<?php
/**
 * HOPE Evaluation System - Analytics Service
 * 
 * Aggregation, trend analysis, and performance alerting
 * for instructor evaluations across semesters.
 */

require_once __DIR__ . '/../config/database.php';

class AnalyticsService {

    // =====================================================
    // INSTRUCTOR-LEVEL ANALYTICS
    // =====================================================

    /**
     * Get aggregated scores for an instructor across all their evaluation sheets.
     * Returns per-question averages and overall average.
     */
    public static function getInstructorAverages(int $instructorId, ?string $academicYear = null, ?string $semester = null): array {
        $db = Database::getConnection();

        $sql = 'SELECT q.question_text, q.question_order,
                       AVG(r.rating) as avg_rating,
                       COUNT(r.id) as response_count
                FROM responses r
                JOIN questions q ON r.question_id = q.id
                JOIN submissions s ON r.submission_id = s.id
                JOIN evaluation_sheets es ON s.evaluation_sheet_id = es.id
                WHERE es.instructor_id = :instructor_id
                  AND es.status IN ("open", "closed", "reviewed", "archived")';

        $params = [':instructor_id' => $instructorId];

        if ($academicYear) {
            $sql .= ' AND es.academic_year = :year';
            $params[':year'] = $academicYear;
        }
        if ($semester) {
            $sql .= ' AND es.semester = :semester';
            $params[':semester'] = $semester;
        }

        $sql .= ' GROUP BY q.question_text, q.question_order ORDER BY q.question_order';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $breakdown = $stmt->fetchAll();

        // Compute overall average
        $totalRating = 0;
        $totalCount = 0;
        foreach ($breakdown as $row) {
            $totalRating += $row['avg_rating'] * $row['response_count'];
            $totalCount += $row['response_count'];
        }

        return [
            'breakdown'       => $breakdown,
            'overall_average' => $totalCount > 0 ? round($totalRating / $totalCount, 2) : 0,
            'total_responses' => $totalCount,
        ];
    }

    /**
     * Get all instructor performance summaries (used by HR and Admin dashboards)
     */
    public static function getAllInstructorSummaries(?string $academicYear = null, ?string $semester = null, ?int $departmentId = null): array {
        $db = Database::getConnection();

        $sql = 'SELECT u.id, u.full_name, d.name as department_name,
                       AVG(r.rating) as avg_rating,
                       COUNT(DISTINCT s.id) as total_submissions,
                       COUNT(DISTINCT es.id) as total_evaluations
                FROM users u
                JOIN departments d ON u.department_id = d.id
                LEFT JOIN evaluation_sheets es ON es.instructor_id = u.id
                    AND es.status IN ("open", "closed", "reviewed", "archived")';

        $params = [];

        if ($academicYear) {
            $sql .= ' AND es.academic_year = :year';
            $params[':year'] = $academicYear;
        }
        if ($semester) {
            $sql .= ' AND es.semester = :semester';
            $params[':semester'] = $semester;
        }

        $sql .= ' LEFT JOIN submissions s ON s.evaluation_sheet_id = es.id
                   LEFT JOIN responses r ON r.submission_id = s.id
                   WHERE u.role = "instructor" AND u.status = "active"';

        if ($departmentId !== null) {
            $sql .= ' AND u.department_id = :dept_id';
            $params[':dept_id'] = $departmentId;
        }

        $sql .= ' GROUP BY u.id, u.full_name, d.name
                   ORDER BY avg_rating DESC';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Active instructors in a department (for dean evaluation form picker — no analytics join).
     */
    public static function listInstructorsInDepartment(int $departmentId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT id, full_name FROM users
             WHERE role = "instructor" AND status = "active" AND department_id = :dept
             ORDER BY full_name ASC'
        );
        $stmt->execute([':dept' => $departmentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Active courses in a department (for dean evaluation form picker).
     */
    public static function listCoursesInDepartment(int $departmentId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT id, code, title, academic_year, semester
             FROM courses
             WHERE department_id = :dept AND status = "active"
             ORDER BY code ASC, academic_year DESC, FIELD(semester, "I", "II", "Summer")'
        );
        $stmt->execute([':dept' => $departmentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================
    // DEPARTMENT-LEVEL ANALYTICS
    // =====================================================

    /**
     * Get performance metrics per department
     */
    public static function getDepartmentPerformance(?string $academicYear = null): array {
        $db = Database::getConnection();

        $sql = 'SELECT d.id, d.name as department_name,
                       AVG(r.rating) as avg_rating,
                       COUNT(DISTINCT es.id) as total_evaluations,
                       COUNT(DISTINCT s.id) as total_submissions
                FROM departments d
                LEFT JOIN evaluation_sheets es ON es.department_id = d.id
                    AND es.status IN ("open", "closed", "reviewed", "archived")';

        $params = [];
        if ($academicYear) {
            $sql .= ' AND es.academic_year = :year';
            $params[':year'] = $academicYear;
        }

        $sql .= ' LEFT JOIN submissions s ON s.evaluation_sheet_id = es.id
                   LEFT JOIN responses r ON r.submission_id = s.id
                   WHERE d.status = "active"
                   GROUP BY d.id, d.name
                   ORDER BY avg_rating DESC';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // =====================================================
    // TREND ANALYSIS
    // =====================================================

    /**
     * Get an instructor's average score per academic_year + semester
     * for trend analysis across time periods.
     */
    public static function getInstructorTrend(int $instructorId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT es.academic_year, es.semester,
                    AVG(r.rating) as avg_rating,
                    COUNT(DISTINCT s.id) as submission_count
             FROM evaluation_sheets es
             JOIN submissions s ON s.evaluation_sheet_id = es.id
             JOIN responses r ON r.submission_id = s.id
             WHERE es.instructor_id = :instructor_id
               AND es.status IN ("open", "closed", "reviewed", "archived")
             GROUP BY es.academic_year, es.semester
             ORDER BY es.academic_year ASC, es.semester ASC'
        );
        $stmt->execute([':instructor_id' => $instructorId]);
        return $stmt->fetchAll();
    }

    /**
     * Calculate the trend change for an instructor (current vs previous period)
     */
    public static function getInstructorTrendChange(int $instructorId): float {
        $trend = self::getInstructorTrend($instructorId);
        if (count($trend) < 2) return 0.0;

        $current = (float) $trend[count($trend) - 1]['avg_rating'];
        $previous = (float) $trend[count($trend) - 2]['avg_rating'];

        return round($current - $previous, 2);
    }

    // =====================================================
    // PERFORMANCE ALERTS (HR Decision Support)
    // =====================================================

    /**
     * Get instructors flagged for attention based on thresholds.
     * 
     * Flags:
     * - Low performance: avg rating < $minRating
     * - Sudden drop: trend change < -$dropThreshold
     */
    public static function getPerformanceAlerts(float $minRating = 3.0, float $dropThreshold = 0.5): array {
        $summaries = self::getAllInstructorSummaries();
        $alerts = [];

        foreach ($summaries as $instructor) {
            $flags = [];
            $avgRating = (float) ($instructor['avg_rating'] ?? 0);

            // Low performance flag
            if ($avgRating > 0 && $avgRating < $minRating) {
                $flags[] = 'Low performance (avg: ' . round($avgRating, 2) . ')';
            }

            // Trend drop flag
            $trendChange = self::getInstructorTrendChange((int) $instructor['id']);
            if ($trendChange < -$dropThreshold) {
                $flags[] = 'Sudden drop (' . $trendChange . ' change)';
            }

            if (!empty($flags)) {
                $alerts[] = [
                    'instructor_id'   => $instructor['id'],
                    'instructor_name' => $instructor['full_name'],
                    'department'      => $instructor['department_name'],
                    'avg_rating'      => $avgRating,
                    'trend_change'    => $trendChange,
                    'flags'           => $flags,
                ];
            }
        }

        return $alerts;
    }

    // =====================================================
    // COMMENTS (anonymized)
    // =====================================================

    /**
     * Get all comments for a specific evaluation sheet (Dean view)
     */
    public static function getSheetComments(int $sheetId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT sc.comment_text, s.submitted_at
             FROM submission_comments sc
             JOIN submissions s ON sc.submission_id = s.id
             WHERE s.evaluation_sheet_id = :sheet_id
             ORDER BY s.submitted_at DESC'
        );
        $stmt->execute([':sheet_id' => $sheetId]);
        return $stmt->fetchAll();
    }

    /**
     * Get recent comments across all evaluations for an instructor
     */
    public static function getInstructorComments(int $instructorId, int $limit = 10): array {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT sc.comment_text, s.submitted_at,
                    c.code as course_code, c.title as course_title
             FROM submission_comments sc
             JOIN submissions s ON sc.submission_id = s.id
             JOIN evaluation_sheets es ON s.evaluation_sheet_id = es.id
             JOIN courses c ON es.course_id = c.id
             WHERE es.instructor_id = :instructor_id
             ORDER BY s.submitted_at DESC
             LIMIT :lim'
        );
        $stmt->bindValue(':instructor_id', $instructorId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // =====================================================
    // SYSTEM-WIDE STATS
    // =====================================================

    /**
     * Per-question averages for one evaluation sheet (dean / admin results view)
     */
    public static function getSheetQuestionStats(int $sheetId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT q.id, q.question_text, q.question_order,
                    AVG(r.rating) AS avg_rating,
                    COUNT(r.id) AS response_count
             FROM questions q
             LEFT JOIN responses r ON r.question_id = q.id
             LEFT JOIN submissions sub ON r.submission_id = sub.id AND sub.evaluation_sheet_id = q.evaluation_sheet_id
             WHERE q.evaluation_sheet_id = :sid
             GROUP BY q.id, q.question_text, q.question_order
             ORDER BY q.question_order'
        );
        $stmt->execute([':sid' => $sheetId]);
        return $stmt->fetchAll();
    }

    /**
     * Department-scoped dashboard stats (dean role)
     */
    public static function getDepartmentStats(int $departmentId): array {
        $db = Database::getConnection();
        $stats = [];

        $stmt = $db->prepare('SELECT COUNT(*) FROM evaluation_sheets WHERE department_id = ? AND status = "open"');
        $stmt->execute([$departmentId]);
        $stats['open_evaluations'] = (int) $stmt->fetchColumn();

        $stmt = $db->prepare(
            'SELECT COUNT(*) FROM submissions s
             INNER JOIN evaluation_sheets es ON s.evaluation_sheet_id = es.id
             WHERE es.department_id = ?'
        );
        $stmt->execute([$departmentId]);
        $stats['total_submissions'] = (int) $stmt->fetchColumn();

        $stmt = $db->prepare('SELECT COUNT(*) FROM evaluation_sheets WHERE department_id = ? AND status = "closed"');
        $stmt->execute([$departmentId]);
        $stats['pending_reviews'] = (int) $stmt->fetchColumn();

        $stmt = $db->prepare(
            'SELECT AVG(r.rating) FROM responses r
             INNER JOIN submissions s ON r.submission_id = s.id
             INNER JOIN evaluation_sheets es ON s.evaluation_sheet_id = es.id
             WHERE es.department_id = ?'
        );
        $stmt->execute([$departmentId]);
        $avg = $stmt->fetchColumn();
        $stats['system_avg_score'] = $avg ? round((float) $avg, 2) : 0;

        return $stats;
    }

    /**
     * Get system-wide statistics for dashboard overview cards
     */
    public static function getSystemStats(): array {
        $db = Database::getConnection();

        $stats = [];

        // Total instructors
        $stmt = $db->query('SELECT COUNT(*) as cnt FROM users WHERE role = "instructor" AND status = "active"');
        $stats['total_instructors'] = (int) $stmt->fetch()['cnt'];

        // Total students
        $stmt = $db->query('SELECT COUNT(*) as cnt FROM users WHERE role = "student" AND status = "active"');
        $stats['total_students'] = (int) $stmt->fetch()['cnt'];

        // Total departments
        $stmt = $db->query('SELECT COUNT(*) as cnt FROM departments WHERE status = "active"');
        $stats['total_departments'] = (int) $stmt->fetch()['cnt'];

        // Total evaluations
        $stmt = $db->query('SELECT COUNT(*) as cnt FROM evaluation_sheets');
        $stats['total_evaluations'] = (int) $stmt->fetch()['cnt'];

        // Total submissions
        $stmt = $db->query('SELECT COUNT(*) as cnt FROM submissions');
        $stats['total_submissions'] = (int) $stmt->fetch()['cnt'];

        // System average score
        $stmt = $db->query('SELECT AVG(r.rating) as avg FROM responses r');
        $avg = $stmt->fetch()['avg'];
        $stats['system_avg_score'] = $avg ? round((float) $avg, 2) : 0;

        // Open evaluations
        $stmt = $db->query('SELECT COUNT(*) as cnt FROM evaluation_sheets WHERE status = "open"');
        $stats['open_evaluations'] = (int) $stmt->fetch()['cnt'];

        // Pending reviews
        $stmt = $db->query('SELECT COUNT(*) as cnt FROM evaluation_sheets WHERE status = "closed"');
        $stats['pending_reviews'] = (int) $stmt->fetch()['cnt'];

        return $stats;
    }

    /**
     * Get recent submissions for activity feeds
     */
    public static function getRecentSubmissions(int $limit = 10, ?int $departmentId = null): array {
        $db = Database::getConnection();

        $sql = 'SELECT s.submitted_at,
                       u.full_name as instructor_name,
                       c.code as course_code, c.title as course_title,
                       d.name as department_name
                FROM submissions s
                JOIN evaluation_sheets es ON s.evaluation_sheet_id = es.id
                JOIN users u ON es.instructor_id = u.id
                JOIN courses c ON es.course_id = c.id
                JOIN departments d ON es.department_id = d.id';

        $params = [];
        if ($departmentId) {
            $sql .= ' WHERE es.department_id = :dept_id';
            $params[':dept_id'] = $departmentId;
        }

        $sql .= ' ORDER BY s.submitted_at DESC LIMIT ' . (int) $limit;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
