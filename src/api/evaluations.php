<?php
/**
 * HOPE Evaluation System - Evaluations API Endpoint
 * 
 * CRUD for evaluation sheets (Dean role).
 * State transitions for the evaluation lifecycle.
 * 
 * GET    → List/get evaluation sheets
 * POST   → Create or transition state
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/EvaluationService.php';

AuthService::initSession();

if (!AuthService::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet();
        break;
    case 'POST':
        handlePost();
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

function handleGet(): void {
    $action = $_GET['action'] ?? 'list';

    switch ($action) {
        case 'list':
            $filters = [];

            // Deans can only see their own department
            if (AuthService::hasRole('dean')) {
                $filters['department_id'] = AuthService::getDepartmentId();
            }

            if (!empty($_GET['status'])) $filters['status'] = $_GET['status'];
            if (!empty($_GET['instructor_id'])) $filters['instructor_id'] = (int) $_GET['instructor_id'];
            if (!empty($_GET['academic_year'])) $filters['academic_year'] = $_GET['academic_year'];
            if (!empty($_GET['semester'])) $filters['semester'] = $_GET['semester'];

            // Instructors can only see their own evaluations
            if (AuthService::hasRole('instructor')) {
                $filters['instructor_id'] = AuthService::getUserId();
            }

            $sheets = EvaluationService::listSheets($filters);
            echo json_encode(['success' => true, 'sheets' => $sheets]);
            break;

        case 'get':
            $id = (int) ($_GET['id'] ?? 0);
            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
                return;
            }

            $sheet = EvaluationService::getSheet($id);
            if (!$sheet) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Evaluation sheet not found.']);
                return;
            }

            // Add response rate info
            $responseRate = EvaluationService::getResponseRate($id);
            $sheet['response_rate'] = $responseRate;

            echo json_encode(['success' => true, 'sheet' => $sheet]);
            break;

        case 'response_rate':
            $id = (int) ($_GET['id'] ?? 0);
            $rate = EvaluationService::getResponseRate($id);
            echo json_encode(['success' => true, 'response_rate' => $rate]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown action.']);
            break;
    }
}

function handlePost(): void {
    // Only deans and admins can modify evaluation sheets
    if (!AuthService::hasRole('dean', 'admin')) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Insufficient permissions.']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) $input = $_POST;

    $action = $input['action'] ?? 'create';

    switch ($action) {
        case 'create':
            $required = ['title', 'department_id', 'course_id', 'instructor_id', 'academic_year', 'semester'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => "Missing required field: {$field}"]);
                    return;
                }
            }

            $input['created_by'] = AuthService::getUserId();

            // Enforce dean's department scope
            if (AuthService::hasRole('dean')) {
                $input['department_id'] = AuthService::getDepartmentId();
            }

            $sheetId = EvaluationService::createSheet($input);
            echo json_encode(['success' => true, 'message' => 'Evaluation sheet created.', 'id' => $sheetId]);
            break;

        case 'transition':
            $sheetId = (int) ($input['sheet_id'] ?? 0);
            $newState = $input['new_state'] ?? '';

            if ($sheetId <= 0 || empty($newState)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Sheet ID and new state are required.']);
                return;
            }

            $result = EvaluationService::transitionState($sheetId, $newState, AuthService::getUserId());
            if ($result) {
                echo json_encode(['success' => true, 'message' => "Evaluation transitioned to '{$newState}'."]);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid state transition.']);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown action.']);
            break;
    }
}
