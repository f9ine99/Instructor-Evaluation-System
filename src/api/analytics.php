<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/AnalyticsService.php';
require_once __DIR__ . '/../services/EvaluationService.php';

AuthService::initSession();
if (!AuthService::isLoggedIn()) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Auth required']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'GET') { http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit; }

$action = $_GET['action'] ?? 'system_stats';
switch ($action) {
    case 'system_stats':
        if (!AuthService::hasRole('admin','dean','hr')) { http_response_code(403); echo json_encode(['success'=>false]); exit; }
        if (AuthService::hasRole('dean')) {
            $did = AuthService::getDepartmentId();
            if (!$did) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'No department assigned.']);
                exit;
            }
            echo json_encode(['success' => true, 'stats' => AnalyticsService::getDepartmentStats($did), 'scope' => 'department']);
        } else {
            echo json_encode(['success' => true, 'stats' => AnalyticsService::getSystemStats(), 'scope' => 'system']);
        }
        break;
    case 'instructor_averages':
        $id = AuthService::hasRole('instructor') ? AuthService::getUserId() : (int)($_GET['instructor_id']??0);
        echo json_encode(['success'=>true,'data'=>AnalyticsService::getInstructorAverages($id,$_GET['academic_year']??null,$_GET['semester']??null)]); break;
    case 'all_instructors':
        if (!AuthService::hasRole('dean','hr','admin')) { http_response_code(403); echo json_encode(['success'=>false]); exit; }
        $deptFilter = null;
        if (AuthService::hasRole('dean')) {
            $deptFilter = AuthService::getDepartmentId();
        } elseif (!empty($_GET['department_id']) && AuthService::hasRole('admin', 'hr')) {
            $deptFilter = (int) $_GET['department_id'];
        }
        echo json_encode([
            'success' => true,
            'instructors' => AnalyticsService::getAllInstructorSummaries($_GET['academic_year'] ?? null, $_GET['semester'] ?? null, $deptFilter),
        ]);
        break;
    case 'department_instructors':
        if (!AuthService::hasRole('dean', 'hr', 'admin')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            exit;
        }
        $deptId = null;
        if (AuthService::hasRole('dean')) {
            $deptId = AuthService::getDepartmentId();
            if (!$deptId) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'No department assigned.']);
                exit;
            }
        } else {
            $deptId = (int) ($_GET['department_id'] ?? 0);
            if ($deptId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'department_id is required.']);
                exit;
            }
        }
        echo json_encode([
            'success' => true,
            'instructors' => AnalyticsService::listInstructorsInDepartment($deptId),
        ]);
        break;
    case 'department_courses':
        if (!AuthService::hasRole('dean', 'hr', 'admin')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            exit;
        }
        $deptIdCourses = null;
        if (AuthService::hasRole('dean')) {
            $deptIdCourses = AuthService::getDepartmentId();
            if (!$deptIdCourses) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'No department assigned.']);
                exit;
            }
        } else {
            $deptIdCourses = (int) ($_GET['department_id'] ?? 0);
            if ($deptIdCourses <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'department_id is required.']);
                exit;
            }
        }
        echo json_encode([
            'success' => true,
            'courses' => AnalyticsService::listCoursesInDepartment($deptIdCourses),
        ]);
        break;
    case 'department_performance':
        if (!AuthService::hasRole('dean','hr','admin')) { http_response_code(403); echo json_encode(['success'=>false]); exit; }
        echo json_encode(['success'=>true,'departments'=>AnalyticsService::getDepartmentPerformance($_GET['academic_year']??null)]); break;
    case 'instructor_trend':
        $id = AuthService::hasRole('instructor') ? AuthService::getUserId() : (int)($_GET['instructor_id']??0);
        echo json_encode(['success'=>true,'trend'=>AnalyticsService::getInstructorTrend($id),'change'=>AnalyticsService::getInstructorTrendChange($id)]); break;
    case 'performance_alerts':
        if (!AuthService::hasRole('hr','admin')) { http_response_code(403); echo json_encode(['success'=>false]); exit; }
        echo json_encode(['success'=>true,'alerts'=>AnalyticsService::getPerformanceAlerts()]); break;
    case 'instructor_comments':
        $id = AuthService::hasRole('instructor') ? AuthService::getUserId() : (int)($_GET['instructor_id']??0);
        echo json_encode(['success'=>true,'comments'=>AnalyticsService::getInstructorComments($id)]); break;
    case 'sheet_question_stats':
        if (!AuthService::hasRole('dean', 'admin', 'hr')) { http_response_code(403); echo json_encode(['success' => false]); exit; }
        $sheetId = (int) ($_GET['sheet_id'] ?? 0);
        if ($sheetId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'sheet_id required']);
            exit;
        }
        $sheet = EvaluationService::getSheet($sheetId);
        if (!$sheet) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Evaluation not found.']);
            exit;
        }
        if (AuthService::hasRole('dean') && (int) $sheet['department_id'] !== (int) AuthService::getDepartmentId()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            exit;
        }
        echo json_encode(['success' => true, 'questions' => AnalyticsService::getSheetQuestionStats($sheetId)]);
        break;
    case 'sheet_comments':
        if (!AuthService::hasRole('dean', 'admin')) { http_response_code(403); echo json_encode(['success' => false]); exit; }
        $sid = (int) ($_GET['sheet_id'] ?? 0);
        if ($sid <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'sheet_id required']);
            exit;
        }
        $sh = EvaluationService::getSheet($sid);
        if (!$sh) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Evaluation not found.']);
            exit;
        }
        if (AuthService::hasRole('dean') && (int) $sh['department_id'] !== (int) AuthService::getDepartmentId()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            exit;
        }
        echo json_encode(['success' => true, 'comments' => AnalyticsService::getSheetComments($sid)]);
        break;
    case 'recent_submissions':
        $deptId = AuthService::hasRole('dean') ? AuthService::getDepartmentId() : null;
        echo json_encode(['success'=>true,'submissions'=>AnalyticsService::getRecentSubmissions((int)($_GET['limit']??10),$deptId)]); break;
    case 'enrollment_gaps':
        if (!AuthService::hasRole('admin', 'dean')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            exit;
        }
        $gapDept = null;
        if (AuthService::hasRole('dean')) {
            $gapDept = AuthService::getDepartmentId();
            if (!$gapDept || (int) $gapDept <= 0) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'No department assigned.']);
                exit;
            }
        }
        $gaps = AnalyticsService::getEvaluationEnrollmentGaps($gapDept);
        echo json_encode(['success' => true, 'gaps' => $gaps, 'count' => count($gaps)]);
        break;
    default:
        http_response_code(400); echo json_encode(['success'=>false,'message'=>'Unknown action']); break;
}
