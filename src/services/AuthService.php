<?php
/**
 * HOPE Evaluation System - Authentication Service
 * 
 * Handles login, logout, session management with industry-standard security.
 */

require_once __DIR__ . '/../config/database.php';

class AuthService {
    
    /**
     * Initialize secure session configuration
     */
    public static function initSession(): void {
        if (session_status() === PHP_SESSION_ACTIVE) return;

        $lifetime = (int) Database::getConfig('SESSION_LIFETIME', '3600');

        // Default session path is often /var/lib/php/sessions (not writable under `php -S` as a normal user).
        $configured = Database::getConfig('SESSION_SAVE_PATH', '');
        $candidates = array_filter([
            $configured !== '' ? $configured : null,
            ini_get('session.save_path') ?: null,
            sys_get_temp_dir(),
        ]);
        foreach ($candidates as $dir) {
            if (is_dir($dir) && is_writable($dir)) {
                session_save_path($dir);
                break;
            }
        }

        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.gc_maxlifetime', (string) $lifetime);

        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path'     => '/',
            'httponly'  => true,
            'samesite'  => 'Strict',
        ]);

        session_start();
    }

    /**
     * Authenticate user with username, password, and expected role.
     * Returns user data array on success, null on failure.
     */
    public static function login(string $username, string $password, string $expectedRole): ?array {
        self::initSession();

        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT id, username, password_hash, full_name, email, role, department_id, status 
             FROM users 
             WHERE username = :username AND role = :role AND status = :status
             LIMIT 1'
        );
        $stmt->execute([
            ':username' => $username,
            ':role'     => $expectedRole,
            ':status'   => 'active',
        ]);

        $user = $stmt->fetch();

        if (!$user) {
            self::logAudit(null, 'login_failed', 'user', null, "Username: {$username}, Role: {$expectedRole}");
            return null;
        }

        if (!password_verify($password, $user['password_hash'])) {
            self::logAudit($user['id'], 'login_failed_password', 'user', $user['id'], "Invalid password attempt");
            return null;
        }

        // Regenerate session ID to prevent fixation attacks
        session_regenerate_id(true);

        // Store user data in session (exclude password hash)
        unset($user['password_hash']);
        $_SESSION['user'] = $user;
        $_SESSION['login_time'] = time();
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // Update last login timestamp
        $updateStmt = $db->prepare('UPDATE users SET last_login = NOW() WHERE id = :id');
        $updateStmt->execute([':id' => $user['id']]);

        self::logAudit($user['id'], 'login_success', 'user', $user['id'], "Role: {$expectedRole}");

        return $user;
    }

    /**
     * Destroy the current session (logout)
     */
    public static function logout(): void {
        self::initSession();

        $userId = $_SESSION['user']['id'] ?? null;
        if ($userId) {
            self::logAudit($userId, 'logout', 'user', $userId, null);
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            $name = session_name();
            $expires = time() - 3600;
            $path = $params['path'] ?? '/';
            $domain = $params['domain'] ?? '';
            $secure = !empty($params['secure']);
            $httponly = !empty($params['httponly']);
            $samesite = $params['samesite'] ?? 'Strict';

            // Must match how the session cookie was set (incl. SameSite) or browsers keep the old cookie.
            if (PHP_VERSION_ID >= 70300) {
                setcookie($name, '', [
                    'expires'  => $expires,
                    'path'     => $path,
                    'domain'   => $domain,
                    'secure'   => $secure,
                    'httponly' => $httponly,
                    'samesite' => $samesite,
                ]);
            } else {
                setcookie($name, '', $expires, $path, $domain, $secure, $httponly);
            }
        }

        session_destroy();
    }

    /**
     * Get the currently logged-in user, or null
     */
    public static function getCurrentUser(): ?array {
        self::initSession();
        return $_SESSION['user'] ?? null;
    }

    /**
     * Check if a user is currently logged in
     */
    public static function isLoggedIn(): bool {
        self::initSession();
        return isset($_SESSION['user']);
    }

    /**
     * Check if the current user has one of the allowed roles
     */
    public static function hasRole(string ...$allowedRoles): bool {
        $user = self::getCurrentUser();
        if (!$user) return false;
        return in_array($user['role'], $allowedRoles, true);
    }

    /**
     * Get the current user's ID
     */
    public static function getUserId(): ?int {
        $user = self::getCurrentUser();
        return $user ? (int) $user['id'] : null;
    }

    /**
     * Get the current user's department ID
     */
    public static function getDepartmentId(): ?int {
        $user = self::getCurrentUser();
        return $user && $user['department_id'] ? (int) $user['department_id'] : null;
    }

    /**
     * Write an entry to the audit log
     */
    public static function logAudit(?int $userId, string $action, ?string $entityType = null, ?int $entityId = null, ?string $details = null): void {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare(
                'INSERT INTO audit_log (user_id, action, entity_type, entity_id, details, ip_address)
                 VALUES (:user_id, :action, :entity_type, :entity_id, :details, :ip)'
            );
            $stmt->execute([
                ':user_id'     => $userId,
                ':action'      => $action,
                ':entity_type' => $entityType,
                ':entity_id'   => $entityId,
                ':details'     => $details,
                ':ip'          => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]);
        } catch (Exception $e) {
            // Audit logging should never break the application
            error_log('Audit log failed: ' . $e->getMessage());
        }
    }
}
