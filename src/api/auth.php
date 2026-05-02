<?php
/**
 * HOPE Evaluation System - Auth API Endpoint
 * 
 * Handles login and logout requests via AJAX.
 * 
 * POST /src/api/auth.php
 *   action=login  → { username, password, role }
 *   action=logout → destroys session
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
                echo json_encode([
                    'success'  => true,
                    'message'  => 'Login successful.',
                    'user'     => [
                        'id'        => $user['id'],
                        'username'  => $user['username'],
                        'full_name' => $user['full_name'],
                        'role'      => $user['role'],
                    ],
                    'redirect' => getRedirectUrl($role),
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
