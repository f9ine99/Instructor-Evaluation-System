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
            $roleFilter = trim((string) ($_GET['role'] ?? ''));
            $allowedFilterRoles = ['student', 'instructor', 'dean', 'hr', 'admin'];
            if ($roleFilter !== '' && !in_array($roleFilter, $allowedFilterRoles, true)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid role filter.']);
                exit;
            }
            if ($roleFilter === '') {
                $stmt = $db->query('SELECT id, username, full_name, email, role, department_id, status, created_at FROM users ORDER BY id');
                $rows = $stmt->fetchAll();
            } else {
                $stmt = $db->prepare(
                    'SELECT id, username, full_name, email, role, department_id, status, created_at FROM users WHERE role = ? ORDER BY id'
                );
                $stmt->execute([$roleFilter]);
                $rows = $stmt->fetchAll();
            }
            echo json_encode(['success' => true, 'data' => $rows, 'role_filter' => $roleFilter === '' ? null : $roleFilter]);
            break;
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
            $username = trim($input['username'] ?? '');
            $fullName = trim($input['full_name'] ?? '');
            $role = $input['role'] ?? '';
            $allowedRoles = ['student', 'instructor', 'dean', 'hr', 'admin'];
            if ($username === '' || $fullName === '' || !in_array($role, $allowedRoles, true)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Username, full name, and a valid role are required.']);
                exit;
            }
            if (empty($input['password']) || strlen((string) $input['password']) < 8) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
                exit;
            }
            $deptId = $input['department_id'] ?? null;
            $deptId = ($deptId === '' || $deptId === null) ? null : (int) $deptId;

            $needsDept = in_array($role, ['student', 'instructor', 'dean'], true);
            if ($needsDept && ($deptId === null || $deptId <= 0)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Department is required for students, instructors, and deans.']);
                exit;
            }

            // Pre-validate enrollment courses for students (enrollments table) before insert
            $courseIds = [];
            if ($role === 'student' && !empty($input['course_ids']) && is_array($input['course_ids'])) {
                $courseIds = array_values(array_unique(array_filter(array_map('intval', $input['course_ids']), function ($id) {
                    return $id > 0;
                })));
                $chk = $db->prepare('SELECT id, department_id FROM courses WHERE id = ? AND status = "active"');
                foreach ($courseIds as $cid) {
                    $chk->execute([$cid]);
                    $courseRow = $chk->fetch(PDO::FETCH_ASSOC);
                    if (!$courseRow) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Invalid or inactive course ID: ' . $cid]);
                        exit;
                    }
                    if ($deptId !== null && (int) $courseRow['department_id'] !== (int) $deptId) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Each selected course must belong to the student\'s department.']);
                        exit;
                    }
                }
            }

            $hash = password_hash((string) $input['password'], PASSWORD_BCRYPT);
            $stmt = $db->prepare('INSERT INTO users (username, password_hash, full_name, email, role, department_id, status) VALUES (?,?,?,?,?,?,?)');
            $db->beginTransaction();
            try {
                try {
                    $stmt->execute([$username, $hash, $fullName, $input['email'] ?? null, $role, $deptId, 'active']);
                } catch (PDOException $e) {
                    if ($e->getCode() === '23000' || strpos($e->getMessage(), 'Duplicate') !== false) {
                        $db->rollBack();
                        http_response_code(409);
                        echo json_encode(['success' => false, 'message' => 'That username is already taken.']);
                        exit;
                    }
                    throw $e;
                }

                $newId = (int) $db->lastInsertId();

                if ($role === 'student' && count($courseIds) > 0) {
                    $insEn = $db->prepare('INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)');
                    foreach ($courseIds as $cid) {
                        if ($cid <= 0) {
                            continue;
                        }
                        try {
                            $insEn->execute([$newId, $cid]);
                        } catch (PDOException $e) {
                            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                                continue;
                            }
                            throw $e;
                        }
                    }
                }

                $db->commit();
            } catch (Throwable $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                throw $e;
            }

            AuthService::logAudit(AuthService::getUserId(), 'user_created', 'user', $newId, "Created user: {$username}");
            echo json_encode(['success' => true, 'message' => 'User created.', 'id' => $newId]);
            break;
        case 'course':
            $code = trim($input['code'] ?? '');
            $title = trim($input['title'] ?? '');
            $dept = (int) ($input['department_id'] ?? 0);
            $sem = $input['semester'] ?? '';
            $year = trim($input['academic_year'] ?? '');
            $inst = (int) ($input['instructor_id'] ?? 0);
            $allowedSem = ['I', 'II', 'Summer'];
            if ($code === '' || $title === '' || $dept <= 0 || !in_array($sem, $allowedSem, true) || $year === '' || $inst <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'All course fields except program/year level are required.']);
                exit;
            }
            $stmt = $db->prepare('INSERT INTO courses (code,title,department_id,program,year_level,semester,academic_year,instructor_id) VALUES (?,?,?,?,?,?,?,?)');
            $stmt->execute([
                $code,
                $title,
                $dept,
                $input['program'] ?? null,
                $input['year_level'] ?? null,
                $sem,
                $year,
                $inst,
            ]);
            AuthService::logAudit(AuthService::getUserId(),'course_created','course',(int)$db->lastInsertId(),"Created course: {$title}");
            echo json_encode(['success'=>true,'message'=>'Course created.','id'=>$db->lastInsertId()]); break;
        case 'department':
            $name = trim($input['name'] ?? '');
            if ($name === '') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Department name is required.']);
                exit;
            }
            $stmt = $db->prepare('INSERT INTO departments (name, status) VALUES (?, "active")');
            try {
                $stmt->execute([$name]);
            } catch (PDOException $e) {
                if ($e->getCode() === '23000' || strpos($e->getMessage(), 'Duplicate') !== false) {
                    http_response_code(409);
                    echo json_encode(['success' => false, 'message' => 'A department with that name already exists.']);
                    exit;
                }
                throw $e;
            }
            $newDeptId = (int) $db->lastInsertId();
            AuthService::logAudit(AuthService::getUserId(), 'department_created', 'department', $newDeptId, "Created department: {$name}");
            echo json_encode(['success' => true, 'message' => 'Department created.', 'id' => $newDeptId]);
            break;
        default:
            http_response_code(400); echo json_encode(['success'=>false,'message'=>'Unknown entity']); break;
    }
} elseif ($method === 'POST' && $action === 'delete') {
    $id = (int)($input['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'ID required']); exit; }
    switch ($entity) {
        case 'user':
            if ($id === AuthService::getUserId()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'You cannot deactivate your own account.']);
                exit;
            }
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
} elseif ($method === 'POST' && $action === 'bulk_import_students') {
    $deptId = (int) ($input['department_id'] ?? 0);
    $sharedPassword = (string) ($input['shared_password'] ?? '');
    $raw = $input['students'] ?? $input['lines'] ?? null;
    $maxRows = 500;

    if ($deptId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Select an active department.']);
        exit;
    }
    if (strlen($sharedPassword) < 8) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Shared password must be at least 8 characters.']);
        exit;
    }

    if (is_string($raw)) {
        $lines = preg_split('/\r\n|\r|\n/', $raw);
    } elseif (is_array($raw)) {
        $lines = $raw;
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Provide student IDs (one per line) or a students array.']);
        exit;
    }

    $parsed = [];
    $seenLocal = [];

    foreach ($lines as $lineNum => $line) {
        $line = trim((string) $line);
        if ($line === '') {
            continue;
        }
        if (count($parsed) >= $maxRows) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Too many rows (max ' . $maxRows . ').']);
            exit;
        }

        $cells = str_getcsv($line);
        $username = trim((string) ($cells[0] ?? ''));
        $fullName = isset($cells[1]) ? trim((string) $cells[1]) : '';
        $email = isset($cells[2]) ? trim((string) $cells[2]) : '';

        if ($username === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Line ' . ($lineNum + 1) . ': username is empty.']);
            exit;
        }
        if (strlen($username) > 50) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Line ' . ($lineNum + 1) . ': username too long (max 50 characters).']);
            exit;
        }
        // Username/student ID is login-only; display name must not default to the ID — use CSV for real names.
        if ($fullName === '') {
            $fullName = 'New student';
        }
        if (strlen($fullName) > 100) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Line ' . ($lineNum + 1) . ': full name too long (max 100 characters).']);
            exit;
        }
        $emailFinal = ($email !== '') ? $email : null;
        if ($emailFinal !== null && strlen($emailFinal) > 150) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Line ' . ($lineNum + 1) . ': email too long.']);
            exit;
        }

        $dedupeKey = strtolower($username);
        if (isset($seenLocal[$dedupeKey])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Duplicate username in list: ' . $username]);
            exit;
        }
        $seenLocal[$dedupeKey] = true;

        $parsed[] = ['username' => $username, 'full_name' => $fullName, 'email' => $emailFinal];
    }

    if ($parsed === []) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Add at least one student ID (one per line).']);
        exit;
    }

    $dst = $db->prepare('SELECT id FROM departments WHERE id = ? AND status = ? LIMIT 1');
    $dst->execute([$deptId, 'active']);
    if (!$dst->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid or inactive department.']);
        exit;
    }

    $usernames = array_column($parsed, 'username');
    $placeholders = implode(',', array_fill(0, count($usernames), '?'));
    $chkDup = $db->prepare('SELECT username FROM users WHERE username IN (' . $placeholders . ')');
    $chkDup->execute($usernames);
    $existing = $chkDup->fetchAll(PDO::FETCH_COLUMN);
    if ($existing !== []) {
        $sample = array_slice($existing, 0, 10);
        http_response_code(409);
        echo json_encode([
            'success'  => false,
            'message'  => 'Some usernames already exist: ' . implode(', ', $sample) . (count($existing) > 10 ? ' …' : ''),
            'existing' => $existing,
        ]);
        exit;
    }

    $hash = password_hash($sharedPassword, PASSWORD_BCRYPT);
    $ins = $db->prepare(
        'INSERT INTO users (username, password_hash, full_name, email, role, department_id, must_change_password, status) VALUES (?,?,?,?,?,?,1,?)'
    );

    $db->beginTransaction();
    try {
        foreach ($parsed as $row) {
            $ins->execute([
                $row['username'],
                $hash,
                $row['full_name'],
                $row['email'],
                'student',
                $deptId,
                'active',
            ]);
        }
        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        if ($e instanceof PDOException && ($e->getCode() === '23000' || strpos($e->getMessage(), 'Duplicate') !== false)) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'A username conflicted during import (remove duplicates or resolve conflicts).']);
            exit;
        }
        throw $e;
    }

    $n = count($parsed);
    AuthService::logAudit(
        AuthService::getUserId(),
        'bulk_students_imported',
        'user',
        null,
        "Imported {$n} students (department_id {$deptId})"
    );
    echo json_encode(['success' => true, 'message' => 'Imported ' . $n . ' student(s).', 'count' => $n]);
} else {
    http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid request']);
}
