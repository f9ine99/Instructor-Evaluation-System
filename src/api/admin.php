<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../config/database.php';

AuthService::initSession();
if (!AuthService::isLoggedIn() || !AuthService::hasRole('admin')) {
    http_response_code(403); echo json_encode(['success'=>false,'message'=>'Admin access required.']); exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = $method === 'POST' ? (json_decode(file_get_contents('php://input'), true) ?: $_POST) : [];
$action = $input['action'] ?? $_GET['action'] ?? '';
$entity = $input['entity'] ?? $_GET['entity'] ?? '';
$db = Database::getConnection();

if ($method === 'GET' && $action === 'list') {
    switch ($entity) {
        case 'users':
            $stmt = $db->query('SELECT id, username, full_name, email, role, department_id, status, created_at FROM users ORDER BY id');
            echo json_encode(['success'=>true,'data'=>$stmt->fetchAll()]); break;
        case 'courses':
            $stmt = $db->query('SELECT c.*, d.name as department_name, u.full_name as instructor_name FROM courses c JOIN departments d ON c.department_id=d.id JOIN users u ON c.instructor_id=u.id ORDER BY c.id');
            echo json_encode(['success'=>true,'data'=>$stmt->fetchAll()]); break;
        case 'departments':
            $stmt = $db->query('SELECT d.*, u.full_name as head_name, (SELECT COUNT(*) FROM users WHERE department_id=d.id AND role="instructor") as faculty_count FROM departments d LEFT JOIN users u ON d.head_instructor_id=u.id ORDER BY d.id');
            echo json_encode(['success'=>true,'data'=>$stmt->fetchAll()]); break;
        case 'programs':
            $stmt = $db->query('SELECT DISTINCT program, year_level, department_id FROM courses WHERE program IS NOT NULL ORDER BY program');
            echo json_encode(['success'=>true,'data'=>$stmt->fetchAll()]); break;
        default:
            http_response_code(400); echo json_encode(['success'=>false,'message'=>'Unknown entity']); break;
    }
} elseif ($method === 'POST' && $action === 'create') {
    switch ($entity) {
        case 'user':
            $hash = password_hash($input['password'] ?? 'password123', PASSWORD_BCRYPT);
            $stmt = $db->prepare('INSERT INTO users (username, password_hash, full_name, email, role, department_id, status) VALUES (?,?,?,?,?,?,?)');
            $stmt->execute([$input['username'],$hash,$input['full_name'],$input['email']??null,$input['role'],$input['department_id']??null,'active']);
            AuthService::logAudit(AuthService::getUserId(),'user_created','user',(int)$db->lastInsertId(),"Created user: {$input['username']}");
            echo json_encode(['success'=>true,'message'=>'User created.','id'=>$db->lastInsertId()]); break;
        case 'course':
            $stmt = $db->prepare('INSERT INTO courses (code,title,department_id,program,year_level,semester,academic_year,instructor_id) VALUES (?,?,?,?,?,?,?,?)');
            $stmt->execute([$input['code'],$input['title'],$input['department_id'],$input['program']??null,$input['year_level']??null,$input['semester'],$input['academic_year'],$input['instructor_id']]);
            AuthService::logAudit(AuthService::getUserId(),'course_created','course',(int)$db->lastInsertId(),"Created course: {$input['title']}");
            echo json_encode(['success'=>true,'message'=>'Course created.','id'=>$db->lastInsertId()]); break;
        case 'department':
            $stmt = $db->prepare('INSERT INTO departments (name, status) VALUES (?, "active")');
            $stmt->execute([$input['name']]);
            echo json_encode(['success'=>true,'message'=>'Department created.','id'=>$db->lastInsertId()]); break;
        default:
            http_response_code(400); echo json_encode(['success'=>false,'message'=>'Unknown entity']); break;
    }
} elseif ($method === 'POST' && $action === 'delete') {
    $id = (int)($input['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'ID required']); exit; }
    switch ($entity) {
        case 'user':
            $db->prepare('UPDATE users SET status="inactive" WHERE id=?')->execute([$id]);
            AuthService::logAudit(AuthService::getUserId(),'user_deactivated','user',$id,null);
            echo json_encode(['success'=>true,'message'=>'User deactivated.']); break;
        case 'course':
            $db->prepare('UPDATE courses SET status="inactive" WHERE id=?')->execute([$id]);
            echo json_encode(['success'=>true,'message'=>'Course deactivated.']); break;
        case 'department':
            $db->prepare('UPDATE departments SET status="inactive" WHERE id=?')->execute([$id]);
            echo json_encode(['success'=>true,'message'=>'Department deactivated.']); break;
        default:
            http_response_code(400); echo json_encode(['success'=>false,'message'=>'Unknown entity']); break;
    }
} else {
    http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid request']);
}
