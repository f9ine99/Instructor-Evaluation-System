<?php
/**
 * HOPE Evaluation System - Auth API Endpoint
 * 
 * Handles login and logout requests via AJAX.
 * 
 * POST /src/api/auth.php
 *   action=login           → { username, password, role }
 *   action=logout          → destroys session
 *   action=session_check   → { user } when session valid, else 401
 *   action=change_password → { current_password, new_password }; students stay signed in (session rotated); other roles logged out on success
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../services/AuthService.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    // Fall back to form-encoded data
    $input = $_POST;
}

try {
    $action = $input['action'] ?? '';

    switch ($action) {
        case 'login':
            $username = trim($input['username'] ?? '');
            $password = $input['password'] ?? '';
            $role     = trim($input['role'] ?? '');

            if (empty($username) || empty($password) || empty($role)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Username, password, and role are required.']);
                exit;
            }

            $validRoles = ['student', 'instructor', 'dean', 'hr', 'admin'];
            if (!in_array($role, $validRoles, true)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid role.']);
                exit;
            }

            $user = AuthService::login($username, $password, $role);

            if ($user) {
                $mustChange = !empty($user['must_change_password']);
                $redirect = getRedirectUrl($role);
                if ($role === 'student' && $mustChange) {
                    $redirect = '/pages/student/first-login-password.php';
                }
                echo json_encode([
                    'success'               => true,
                    'message'               => 'Login successful.',
                    'user'                  => [
                        'id'                     => $user['id'],
                        'username'               => $user['username'],
                        'full_name'              => $user['full_name'],
                        'role'                   => $user['role'],
                        'must_change_password'   => $mustChange,
                    ],
                    'redirect'              => $redirect,
                    'must_change_password'    => $mustChange,
                ]);
            } else {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Invalid credentials or account is not active.']);
            }
            break;

        case 'logout':
            AuthService::logout();
            echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
            break;

        case 'session_check':
            AuthService::initSession();
            $profile = AuthService::getSanitizedUserHydratedFromDb();
            if (!$profile) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Session expired, account inactive, or not signed in.',
                ]);
                exit;
            }
            echo json_encode(['success' => true, 'user' => $profile]);
            break;

        case 'change_password':
            AuthService::initSession();
            if (!AuthService::isLoggedIn()) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Session expired or not signed in.']);
                exit;
            }
            $currentPassword = (string) ($input['current_password'] ?? '');
            $newPassword     = (string) ($input['new_password'] ?? '');
            if ($currentPassword === '' || $newPassword === '') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Current password and new password are required.']);
                exit;
            }
            $result = AuthService::changeOwnPassword($currentPassword, $newPassword);
            if (!$result['success']) {
                $code = $result['code'] ?? 'error';
                $status = ($code === 'auth') ? 401 : (($code === 'server') ? 500 : 400);
                http_response_code($status);
                echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Unable to change password.']);
                exit;
            }
            echo json_encode([
                'success'       => true,
                'message'       => $result['message'] ?? 'Password updated.',
                'sign_in_again' => !empty($result['sign_in_again']),
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown action.']);
            break;
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error while processing login.',
        'error'   => $e->getMessage(),
    ]);
}

/**
 * Get the dashboard redirect URL for a given role
 */
function getRedirectUrl(string $role): string {
    $paths = [
        'student'    => '/pages/student/evaluate.php',
        'instructor' => '/pages/instructor/dashboard.php',
        'dean'       => '/pages/dean/dashboard.php',
        'hr'         => '/pages/hr/dashboard.php',
        'admin'      => '/pages/admin/dashboard.php',
    ];
    return $paths[$role] ?? '/pages/common/home.php';
}
