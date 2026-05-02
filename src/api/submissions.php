<?php
/**
 * HOPE Evaluation System - Submissions API Endpoint
 * 
 * Handles evaluation submissions and retrieval.
 * 
 * POST   → Submit an evaluation
 * GET    → Get eligible evaluations for current student
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/EvaluationService.php';
require_once __DIR__ . '/../services/AnonymizationService.php';

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
    $action = $_GET['action'] ?? 'eligible';

    switch ($action) {
        case 'eligible':
            // Get eligible evaluations for the current student
            if (!AuthService::hasRole('student')) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Students only.']);
                return;
            }

            try {
                AnonymizationService::requireAppSecret();
            } catch (RuntimeException $e) {
                error_log('[submissions/eligible] ' . $e->getMessage());
                http_response_code(503);
                echo json_encode([
                    'success' => false,
                    'message' => 'Application misconfiguration: set APP_SECRET in .env (see .env.example).',
                ]);
                return;
            }

            $evaluations = EvaluationService::getEligibleEvaluations(AuthService::getUserId());
            echo json_encode(['success' => true, 'evaluations' => array_values($evaluations)]);
            break;

        case 'questions':
            // Get questions for a specific evaluation sheet
            $sheetId = (int) ($_GET['sheet_id'] ?? 0);
            if ($sheetId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid sheet ID.']);
                return;
            }

            $questions = EvaluationService::getQuestions($sheetId);
            $sheet = EvaluationService::getSheet($sheetId);
            echo json_encode([
                'success'   => true,
                'questions' => $questions,
                'sheet'     => $sheet,
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown action.']);
            break;
    }
}

function handlePost(): void {
    if (!AuthService::hasRole('student')) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Only students can submit evaluations.']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) $input = $_POST;

    $sheetId = (int) ($input['sheet_id'] ?? 0);
    $ratings = $input['ratings'] ?? [];
    $comment = trim($input['comment'] ?? '');

    if ($sheetId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid evaluation sheet.']);
        return;
    }

    if (empty($ratings) || !is_array($ratings)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ratings are required.']);
        return;
    }

    // Cast ratings to proper types
    $cleanRatings = [];
    foreach ($ratings as $qId => $rating) {
        $cleanRatings[(int) $qId] = (int) $rating;
    }

    $result = EvaluationService::submitEvaluation(
        AuthService::getUserId(),
        $sheetId,
        $cleanRatings,
        $comment
    );

    http_response_code($result['success'] ? 200 : 400);
    echo json_encode($result);
}
