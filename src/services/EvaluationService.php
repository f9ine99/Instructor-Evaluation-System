<?php
/**
 * HOPE Evaluation System - Evaluation Service
 * 
 * Manages the evaluation lifecycle (state machine) and submission logic.
 * 
 * States: draft → scheduled → open → closed → reviewed → archived
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/AnonymizationService.php';
require_once __DIR__ . '/AuthService.php';

class EvaluationService {

    /**
     * Valid state transitions for the evaluation lifecycle
     */
    private const STATE_TRANSITIONS = [
        'draft'     => ['scheduled', 'open'],       // Can schedule or publish immediately
        'scheduled' => ['open', 'draft'],            // Can open or revert to draft
        'open'      => ['closed'],                   // Can only close
        'closed'    => ['reviewed'],                  // Can only mark reviewed
        'reviewed'  => ['archived'],                  // Can only archive
        'archived'  => [],                            // Terminal state
    ];

    // =====================================================
    // EVALUATION SHEET CRUD
    // =====================================================

    /**
     * Create a new evaluation sheet (Dean only)
     */
    public static function createSheet(array $data): int {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            'INSERT INTO evaluation_sheets 
             (title, description, department_id, course_id, instructor_id, created_by, status, start_date, end_date, academic_year, semester)
             VALUES (:title, :description, :dept_id, :course_id, :instructor_id, :created_by, :status, :start_date, :end_date, :academic_year, :semester)'
        );

        // Students only see status=open. No window → open immediately. Window with future start → scheduled until dean opens;
        // window that has already started → open.
        $startVal = $data['start_date'] ?? null;
        $endVal   = $data['end_date'] ?? null;
        $hasStart = $startVal !== null && $startVal !== '';
        $hasEnd   = $endVal !== null && $endVal !== '';
        if (!$hasStart && !$hasEnd) {
            $status = 'open';
        } else {
            $tStart = strtotime((string) $startVal);
            $status = ($tStart !== false && $tStart > time()) ? 'scheduled' : 'open';
        }

        $stmt->execute([
            ':title'         => $data['title'],
            ':description'   => $data['description'] ?? null,
            ':dept_id'       => $data['department_id'],
            ':course_id'     => $data['course_id'],
            ':instructor_id' => $data['instructor_id'],
            ':created_by'    => $data['created_by'],
            ':status'        => $status,
            ':start_date'    => $data['start_date'] ?? null,
            ':end_date'      => $data['end_date'] ?? null,
            ':academic_year' => $data['academic_year'],
            ':semester'      => $data['semester'],
        ]);

        $sheetId = (int) $db->lastInsertId();

        // Copy default questions into this evaluation sheet
        self::seedQuestionsForSheet($sheetId);

        AuthService::logAudit(
            $data['created_by'], 'evaluation_created', 'evaluation_sheet', $sheetId,
            "Title: {$data['title']}"
        );

        return $sheetId;
    }

    /**
     * Copy the default questions into a new evaluation sheet
     */
    private static function seedQuestionsForSheet(int $sheetId): void {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'INSERT INTO questions (evaluation_sheet_id, question_text, question_order)
             SELECT :sheet_id, question_text, question_order 
             FROM default_questions 
             WHERE is_active = 1
             ORDER BY question_order'
        );
        $stmt->execute([':sheet_id' => $sheetId]);
    }

    /**
     * Get a single evaluation sheet by ID
     */
    public static function getSheet(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT es.*, 
                    c.code as course_code, c.title as course_title, c.program, c.year_level,
                    d.name as department_name,
                    u.full_name as instructor_name,
                    creator.full_name as created_by_name
             FROM evaluation_sheets es
             JOIN courses c ON es.course_id = c.id
             JOIN departments d ON es.department_id = d.id
             JOIN users u ON es.instructor_id = u.id
             JOIN users creator ON es.created_by = creator.id
             WHERE es.id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * List evaluation sheets with optional filters
     */
    public static function listSheets(array $filters = []): array {
        $db = Database::getConnection();

        $sql = 'SELECT es.*,
                       c.code as course_code, c.title as course_title,
                       d.name as department_name,
                       u.full_name as instructor_name,
                       (SELECT COUNT(*) FROM submissions s WHERE s.evaluation_sheet_id = es.id) as submission_count,
                       (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = es.course_id) as course_enrollment_count
                FROM evaluation_sheets es
                JOIN courses c ON es.course_id = c.id
                JOIN departments d ON es.department_id = d.id
                JOIN users u ON es.instructor_id = u.id
                WHERE 1=1';

        $params = [];

        if (!empty($filters['department_id'])) {
            $sql .= ' AND es.department_id = :dept_id';
            $params[':dept_id'] = $filters['department_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= ' AND es.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['instructor_id'])) {
            $sql .= ' AND es.instructor_id = :instructor_id';
            $params[':instructor_id'] = $filters['instructor_id'];
        }
        if (!empty($filters['academic_year'])) {
            $sql .= ' AND es.academic_year = :year';
            $params[':year'] = $filters['academic_year'];
        }
        if (!empty($filters['semester'])) {
            $sql .= ' AND es.semester = :semester';
            $params[':semester'] = $filters['semester'];
        }

        $sql .= ' ORDER BY es.updated_at DESC';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // =====================================================
    // STATE MACHINE TRANSITIONS
    // =====================================================

    /**
     * Transition an evaluation sheet to a new state
     */
    public static function transitionState(int $sheetId, string $newState, int $userId): bool {
        $sheet = self::getSheet($sheetId);
        if (!$sheet) return false;

        $currentState = $sheet['status'];
        $allowed = self::STATE_TRANSITIONS[$currentState] ?? [];

        if (!in_array($newState, $allowed, true)) {
            return false; // Invalid transition
        }

        $db = Database::getConnection();
        $stmt = $db->prepare('UPDATE evaluation_sheets SET status = :status WHERE id = :id');
        $stmt->execute([':status' => $newState, ':id' => $sheetId]);

        AuthService::logAudit(
            $userId, 'evaluation_state_change', 'evaluation_sheet', $sheetId,
            "State: {$currentState} → {$newState}"
        );

        return true;
    }

    /**
     * Publish an evaluation sheet (draft/scheduled → open)
     */
    public static function publishSheet(int $sheetId, int $userId): bool {
        return self::transitionState($sheetId, 'open', $userId);
    }

    /**
     * Close an evaluation sheet (open → closed)
     */
    public static function closeSheet(int $sheetId, int $userId): bool {
        return self::transitionState($sheetId, 'closed', $userId);
    }

    /**
     * Mark an evaluation sheet as reviewed (closed → reviewed)
     */
    public static function markReviewed(int $sheetId, int $userId): bool {
        return self::transitionState($sheetId, 'reviewed', $userId);
    }

    /**
     * Archive an evaluation sheet (reviewed → archived)
     */
    public static function archiveSheet(int $sheetId, int $userId): bool {
        return self::transitionState($sheetId, 'archived', $userId);
    }

    // =====================================================
    // STUDENT-FACING METHODS
    // =====================================================

    /**
     * Enrolled students preview for a course (dean/admin create-eval UI).
     * Deans pass their department id — course must belong to that department.
     *
     * @return array{count:int,students:list<array{id:int,full_name:string,username:string}>,list_limit:int}|null
     */
    public static function getCourseEnrollmentPreview(int $courseId, ?int $restrictToDepartmentId, int $studentListLimit = 25): ?array {
        $db = Database::getConnection();
        $chk = $db->prepare('SELECT department_id, status FROM courses WHERE id = ?');
        $chk->execute([$courseId]);
        $row = $chk->fetch(PDO::FETCH_ASSOC);
        if (!$row || ($row['status'] ?? '') !== 'active') {
            return null;
        }
        if ($restrictToDepartmentId !== null && (int) $row['department_id'] !== (int) $restrictToDepartmentId) {
            return null;
        }

        $cntStmt = $db->prepare('SELECT COUNT(*) as c FROM enrollments WHERE course_id = ?');
        $cntStmt->execute([$courseId]);
        $total = (int) ($cntStmt->fetch()['c'] ?? 0);

        $studentListLimit = max(5, min(150, $studentListLimit));

        $listStmt = $db->prepare(
            'SELECT u.id, u.full_name, u.username
             FROM enrollments e
             INNER JOIN users u ON u.id = e.student_id AND u.role = \'student\' AND u.status = \'active\'
             WHERE e.course_id = ?
             ORDER BY u.full_name ASC, u.username ASC
             LIMIT ' . $studentListLimit
        );
        $listStmt->execute([$courseId]);
        $students = $listStmt->fetchAll(PDO::FETCH_ASSOC);

        return ['count' => $total, 'students' => $students, 'list_limit' => $studentListLimit];
    }

    /**
     * Total enrollment rows for a course offering (basis for evaluation eligibility).
     */
    public static function countEnrollmentsForCourse(int $courseId): int {
        if ($courseId <= 0) {
            return 0;
        }
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT COUNT(*) AS c FROM enrollments WHERE course_id = ?');
        $stmt->execute([$courseId]);

        return (int) (($stmt->fetch(PDO::FETCH_ASSOC) ?: [])['c'] ?? 0);
    }

    /**
     * Get all open evaluations that a student is eligible to submit
     */
    public static function getEligibleEvaluations(int $studentId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT es.*, 
                    c.code as course_code, c.title as course_title, c.program, c.year_level,
                    d.name as department_name,
                    u.full_name as instructor_name
             FROM evaluation_sheets es
             JOIN courses c ON es.course_id = c.id
             JOIN departments d ON es.department_id = d.id
             JOIN users u ON es.instructor_id = u.id
             JOIN enrollments e ON e.course_id = c.id AND e.student_id = :student_id
             WHERE es.status = :status
               AND c.status = \'active\'
               AND (es.end_date IS NULL OR es.end_date >= NOW())
             ORDER BY es.end_date ASC'
        );
        $stmt->execute([
            ':student_id' => $studentId,
            ':status'     => 'open',
        ]);

        $evaluations = $stmt->fetchAll();

        // Filter out already-submitted ones
        return array_filter($evaluations, function ($eval) use ($studentId) {
            return !AnonymizationService::hasSubmitted($studentId, (int) $eval['id']);
        });
    }

    /**
     * Get questions for a specific evaluation sheet
     */
    public static function getQuestions(int $sheetId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT id, question_text, question_order 
             FROM questions 
             WHERE evaluation_sheet_id = :sheet_id 
             ORDER BY question_order ASC'
        );
        $stmt->execute([':sheet_id' => $sheetId]);
        return $stmt->fetchAll();
    }

    /**
     * Submit an evaluation (anonymized).
     * 
     * @param int    $studentId        The student's user ID (used only for token generation)
     * @param int    $sheetId          The evaluation sheet ID
     * @param array  $ratings          [question_id => rating(1-5)]
     * @param string $comment          Optional comment text
     * 
     * @return array ['success' => bool, 'message' => string]
     */
    public static function submitEvaluation(int $studentId, int $sheetId, array $ratings, string $comment = ''): array {
        // 1. Validate eligibility
        $eligibility = AnonymizationService::validateEligibility($studentId, $sheetId);
        if (!$eligibility['eligible']) {
            return ['success' => false, 'message' => $eligibility['reason']];
        }

        // 2. Validate ratings
        $questions = self::getQuestions($sheetId);
        $questionIds = array_column($questions, 'id');

        foreach ($ratings as $qId => $rating) {
            if (!in_array((int) $qId, array_map('intval', $questionIds), true)) {
                return ['success' => false, 'message' => 'Invalid question ID in submission.'];
            }
            if ($rating < 1 || $rating > 5) {
                return ['success' => false, 'message' => 'All ratings must be between 1 and 5.'];
            }
        }

        if (count($ratings) !== count($questions)) {
            return ['success' => false, 'message' => 'Please answer all evaluation questions.'];
        }

        // 3. Generate anonymous token
        $token = AnonymizationService::generateSubmissionToken($studentId, $sheetId);

        // 4. Create submission (within a transaction)
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            // Insert submission (NO student_id stored)
            $subStmt = $db->prepare(
                'INSERT INTO submissions (evaluation_sheet_id, submission_token)
                 VALUES (:sheet_id, :token)'
            );
            $subStmt->execute([':sheet_id' => $sheetId, ':token' => $token]);
            $submissionId = (int) $db->lastInsertId();

            // Insert responses
            $respStmt = $db->prepare(
                'INSERT INTO responses (submission_id, question_id, rating)
                 VALUES (:sub_id, :q_id, :rating)'
            );

            foreach ($ratings as $questionId => $rating) {
                $respStmt->execute([
                    ':sub_id' => $submissionId,
                    ':q_id'   => (int) $questionId,
                    ':rating' => (int) $rating,
                ]);
            }

            // Insert comment (if provided)
            if (!empty(trim($comment))) {
                $commentStmt = $db->prepare(
                    'INSERT INTO submission_comments (submission_id, comment_text)
                     VALUES (:sub_id, :comment)'
                );
                $commentStmt->execute([
                    ':sub_id'  => $submissionId,
                    ':comment' => trim($comment),
                ]);
            }

            $db->commit();

            // Log the submission (without student identity)
            AuthService::logAudit(
                null, 'evaluation_submitted', 'submission', $submissionId,
                "Sheet: {$sheetId} (anonymized)"
            );

            return ['success' => true, 'message' => 'Your evaluation has been submitted successfully. Thank you!'];

        } catch (Exception $e) {
            $db->rollBack();
            error_log('Evaluation submission failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while submitting your evaluation. Please try again.'];
        }
    }

    /**
     * Get submission count and eligible student count for an evaluation sheet
     */
    public static function getResponseRate(int $sheetId): array {
        $db = Database::getConnection();

        // Submissions count
        $subStmt = $db->prepare('SELECT COUNT(*) as cnt FROM submissions WHERE evaluation_sheet_id = :id');
        $subStmt->execute([':id' => $sheetId]);
        $submitted = (int) $subStmt->fetch()['cnt'];

        // Eligible students (enrolled in the course)
        $eligStmt = $db->prepare(
            'SELECT COUNT(*) as cnt FROM enrollments e
             JOIN evaluation_sheets es ON es.course_id = e.course_id
             WHERE es.id = :id'
        );
        $eligStmt->execute([':id' => $sheetId]);
        $eligible = (int) $eligStmt->fetch()['cnt'];

        return [
            'submitted' => $submitted,
            'eligible'  => $eligible,
            'rate'      => $eligible > 0 ? round(($submitted / $eligible) * 100) : 0,
        ];
    }
}
