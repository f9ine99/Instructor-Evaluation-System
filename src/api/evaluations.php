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

require_once __DIR__ . '/../config/database.php';
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

        case 'course_enrollment_preview':
            if (!AuthService::hasRole('dean', 'admin')) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Insufficient permissions.']);

                return;
            }
            $courseId = (int) ($_GET['course_id'] ?? 0);
            if ($courseId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'course_id is required.']);

                return;
            }
            $restrictDept = null;
            if (AuthService::hasRole('dean')) {
                $did = AuthService::getDepartmentId();
                if (!$did || $did <= 0) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Your account has no department assigned.']);

                    return;
                }
                $restrictDept = (int) $did;
            }
            $listLimit = (int) ($_GET['limit'] ?? 25);
            if ($listLimit < 5) {
                $listLimit = 25;
            }
            if ($listLimit > 150) {
                $listLimit = 150;
            }
            $preview = EvaluationService::getCourseEnrollmentPreview($courseId, $restrictDept, $listLimit);
            if ($preview === null) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Course not found, inactive, or not in your department.']);

                return;
            }
            echo json_encode([
                'success'    => true,
                'count'      => $preview['count'],
                'students'   => $preview['students'],
                'list_limit' => $preview['list_limit'],
            ]);
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

            $startRaw = trim((string) ($input['start_date'] ?? ''));
            $endRaw   = trim((string) ($input['end_date'] ?? ''));
            $hasStart = $startRaw !== '';
            $hasEnd   = $endRaw !== '';

            if ($hasStart xor $hasEnd) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Set both opening and closing date/time, or leave both blank to schedule later.',
                ]);

                return;
            }

            if ($hasStart && $hasEnd) {
                $startNorm = evaluationsNormalizeDatetime($startRaw);
                $endNorm   = evaluationsNormalizeDatetime($endRaw);
                if ($startNorm === null || $endNorm === null) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid opening or closing date/time.']);

                    return;
                }
                $tStart = strtotime($startNorm);
                $tEnd   = strtotime($endNorm);
                if ($tStart === false || $tEnd === false) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Could not read opening or closing time.']);

                    return;
                }
                if ($tEnd <= $tStart) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Closing time must be after opening time (check date and clock).',
                    ]);

                    return;
                }
                $input['start_date'] = $startNorm;
                $input['end_date']   = $endNorm;
            } else {
                $input['start_date'] = null;
                $input['end_date']   = null;
            }

            $courseId = (int) $input['course_id'];
            $instrId  = (int) $input['instructor_id'];
            $deptEv   = (int) $input['department_id'];

            $dbCv = Database::getConnection();
            $stc  = $dbCv->prepare('SELECT department_id, instructor_id, status FROM courses WHERE id = ?');
            $stc->execute([$courseId]);
            $rowC = $stc->fetch(PDO::FETCH_ASSOC);
            if (!$rowC || (($rowC['status'] ?? '') !== 'active')) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid or inactive course. Refresh the course list and try again.']);

                return;
            }
            if ((int) $rowC['department_id'] !== $deptEv) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'That course belongs to another department. Choose a course from your department.']);

                return;
            }
            if ((int) $rowC['instructor_id'] !== $instrId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'The instructor must be the one assigned to this course in the catalog.']);

                return;
            }

            $sheetId = EvaluationService::createSheet($input);
            $created = EvaluationService::getSheet($sheetId);
            $st      = $created['status'] ?? '';
            $enrCount = EvaluationService::countEnrollmentsForCourse($courseId);
            $notice   = null;
            if ($enrCount === 0) {
                $notice = 'No students are enrolled in this class offering yet. An administrator must enroll students (Admin → Manage → Classes → Class roster) before anyone can submit.';
            }
            $msg = $st === 'open'
                ? 'Evaluation created and is open — only enrolled students can respond.'
                : 'Evaluation created as scheduled — use Open in Manage Evaluations when students should access it (or open early if you prefer).';

            $out = [
                'success'                   => true,
                'message'                   => $msg,
                'id'                        => $sheetId,
                'status'                    => $st,
                'course_enrollment_count'   => $enrCount,
                'enrollment_notice'         => $notice,
            ];
            echo json_encode($out);
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
                $out = ['success' => true, 'message' => "Evaluation transitioned to '{$newState}'."];
                if ($newState === 'open') {
                    $sh  = EvaluationService::getSheet($sheetId);
                    $cid = (int) ($sh['course_id'] ?? 0);
                    if ($cid > 0) {
                        $n = EvaluationService::countEnrollmentsForCourse($cid);
                        $out['course_enrollment_count'] = $n;
                        if ($n === 0) {
                            $out['enrollment_notice'] = 'This evaluation is open, but no students are enrolled in the linked class. Administrators should add enrollments (Manage → Classes → Class roster) or nobody can submit.';
                        }
                    }
                }
                echo json_encode($out);
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

/**
 * Accept HTML datetime-local (YYYY-MM-DDTHH:mm) or SQL-like strings; return Y-m-d H:i:s or null.
 */
function evaluationsNormalizeDatetime(string $value): ?string {
    $value = trim(str_replace('T', ' ', $value));
    if ($value === '') {
        return null;
    }
    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $value)) {
        $value .= ':00';
    }
    $t = strtotime($value);

    return $t !== false ? date('Y-m-d H:i:s', $t) : null;
}
