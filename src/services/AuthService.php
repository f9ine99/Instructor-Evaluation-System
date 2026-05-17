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
            'SELECT id, username, password_hash, full_name, email, role, department_id,
                    COALESCE(must_change_password, 0) AS must_change_password, status
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
        $user['must_change_password'] = (int) ($user['must_change_password'] ?? 0) === 1;
        unset($user['status']);
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
     * Non-sensitive session user for API responses (no password_hash).
     */
    public static function getSanitizedSessionUser(): ?array {
        $user = self::getCurrentUser();
        if (!$user) {
            return null;
        }

        return [
            'id'          => (int) $user['id'],
            'username'    => $user['username'],
            'full_name'   => $user['full_name'],
            'email'       => $user['email'] ?? '',
            'role'        => $user['role'],
        ];
    }

    /**
     * Re-load the signed-in user from the database by session user id (must be active).
     * Updates $_SESSION['user'] so it matches the DB (no stale name/email). If missing or inactive, logs out.
     *
     * @return array{id:int, username:string, full_name:string, email:string, role:string, must_change_password:bool}|null
     */
    public static function getSanitizedUserHydratedFromDb(): ?array {
        self::initSession();
        $sess = self::getCurrentUser();
        if (!$sess || empty($sess['id'])) {
            return null;
        }

        try {
            $db = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT id, username, full_name, email, role, department_id,
                        COALESCE(must_change_password, 0) AS must_change_password, status
                 FROM users WHERE id = ? LIMIT 1'
            );
            $stmt->execute([(int) $sess['id']]);
            $row = $stmt->fetch();

            if (!$row || (($row['status'] ?? '') !== 'active')) {
                self::logout();

                return null;
            }

            unset($row['status']);
            $fresh = $_SESSION['user'];
            foreach (['id', 'username', 'full_name', 'email', 'role', 'department_id'] as $k) {
                if (array_key_exists($k, $row)) {
                    $fresh[$k] = $row[$k];
                }
            }
            if (array_key_exists('must_change_password', $row)) {
                $fresh['must_change_password'] = (int) $row['must_change_password'] === 1;
            }
            unset($fresh['password_hash']);
            $_SESSION['user'] = $fresh;

            return [
                'id'                    => (int) $fresh['id'],
                'username'              => $fresh['username'],
                'full_name'             => $fresh['full_name'],
                'email'                 => $fresh['email'] ?? '',
                'role'                  => $fresh['role'],
                'must_change_password'  => !empty($fresh['must_change_password']),
            ];
        } catch (\Throwable $e) {
            error_log('Session hydration failed: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Change password for the currently logged-in active user after verifying current password.
     *
     * @return array{success:bool, message?:string, code?:string}
     */
    public static function changeOwnPassword(string $currentPassword, string $newPassword): array {
        self::initSession();
        $user = self::getCurrentUser();

        if (!$user) {
            return ['success' => false, 'code' => 'auth', 'message' => 'Not signed in.'];
        }

        if ($newPassword === '' || strlen($newPassword) < 8) {
            return ['success' => false, 'code' => 'validation', 'message' => 'New password must be at least 8 characters.'];
        }

        try {
            $db = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT password_hash, COALESCE(must_change_password, 0) AS must_change_password
                 FROM users WHERE id = ? AND status = ? LIMIT 1'
            );
            $stmt->execute([(int) $user['id'], 'active']);
            $row = $stmt->fetch();

            if (!$row) {
                return ['success' => false, 'code' => 'auth', 'message' => 'Account not found or inactive.'];
            }

            $hadMustChange = ((int) ($row['must_change_password'] ?? 0)) === 1;

            if (!password_verify($currentPassword, $row['password_hash'])) {
                self::logAudit((int) $user['id'], 'password_change_failed', 'user', (int) $user['id'], 'Invalid current password');

                return ['success' => false, 'code' => 'current', 'message' => 'Current password is incorrect.'];
            }

            if (password_verify($newPassword, $row['password_hash'])) {
                return ['success' => false, 'code' => 'validation', 'message' => 'Choose a password different from your current one.'];
            }

            $hash = password_hash($newPassword, PASSWORD_BCRYPT);
            $up = $db->prepare('UPDATE users SET password_hash = ?, must_change_password = 0 WHERE id = ?');
            $up->execute([$hash, (int) $user['id']]);

            self::logAudit((int) $user['id'], 'password_changed', 'user', (int) $user['id'], null);

            // Students stay signed in (session rotated); other roles sign out after password change.
            if (($user['role'] ?? '') === 'student') {
                session_regenerate_id(true);
                $_SESSION['user']['must_change_password'] = false;

                return [
                    'success'       => true,
                    'sign_in_again' => false,
                    'message'       => $hadMustChange
                        ? 'Your password has been updated. You can continue.'
                        : 'Your password has been updated.',
                ];
            }

            self::logout();

            return [
                'success'        => true,
                'sign_in_again'  => true,
                'message'        => 'Password updated. You have been signed out — sign in again with your new password.',
            ];
        } catch (\Throwable $e) {
            error_log('Password change failed: ' . $e->getMessage());

            return ['success' => false, 'code' => 'server', 'message' => 'Could not update password. Try again later.'];
        }
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
