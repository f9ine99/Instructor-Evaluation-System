<?php
/**
 * HOPE Evaluation System - Authentication Middleware
 * 
 * Include at the top of any protected page to enforce authentication and RBAC.
 * 
 * Usage:
 *   require_once __DIR__ . '/../../src/middleware/auth.php';
 *   requireAuth('student');            // single role
 *   requireAuth('dean', 'admin');      // multiple allowed roles
 */

require_once __DIR__ . '/../services/AuthService.php';

/**
 * Require the user to be authenticated with one of the given roles.
 * Redirects to the appropriate login page if not authenticated.
 * 
 * @param string ...$allowedRoles  Roles that are permitted to access this page
 */
function requireAuth(string ...$allowedRoles): void {
    AuthService::initSession();

    if (!AuthService::isLoggedIn()) {
        // Not logged in — redirect to the role-appropriate login page
        $targetRole = $allowedRoles[0] ?? 'student';
        redirectToLogin($targetRole);
        exit;
    }

    // Logged in but wrong role
    if (!AuthService::hasRole(...$allowedRoles)) {
        http_response_code(403);
        echo '<!DOCTYPE html><html><head><title>Access Denied</title></head><body>';
        echo '<h1>403 - Access Denied</h1>';
        echo '<p>You do not have permission to access this page.</p>';
        echo '<p><a href="/pages/common/home.php">Return to Home</a></p>';
        echo '</body></html>';
        exit;
    }
}

/**
 * Redirect to the appropriate login page for the given role.
 */
function redirectToLogin(string $role): void {
    $loginPaths = [
        'student'    => '/pages/student/login.php',
        'instructor' => '/pages/instructor/login.php',
        'dean'       => '/pages/dean/login.php',
        'hr'         => '/pages/hr/login.php',
        'admin'      => '/pages/admin/login.php',
    ];

    $path = $loginPaths[$role] ?? $loginPaths['student'];
    header('Location: ' . $path);
    exit;
}

/**
 * Get the base URL for the project (auto-detected from the request).
 * Used for building absolute paths to assets and pages.
 */
function getBaseUrl(): string {
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    // Walk up to find the project root (contains 'public')
    $parts = explode('/', trim($scriptDir, '/'));
    $baseSegments = [];
    foreach ($parts as $part) {
        $baseSegments[] = $part;
        if ($part === 'public') break;
    }
    return '/' . implode('/', $baseSegments);
}

/**
 * Get the path to the assets directory relative to the current page.
 */
function getAssetPath(): string {
    return getBaseUrl() . '/assets';
}
