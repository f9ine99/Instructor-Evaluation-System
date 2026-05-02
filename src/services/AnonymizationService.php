<?php
/**
 * HOPE Evaluation System - Anonymization Service
 * 
 * Ensures student identity is decoupled from evaluation submissions.
 * Uses HMAC-SHA256 with a server-side secret to generate deterministic
 * but non-reversible submission tokens for deduplication.
 */

require_once __DIR__ . '/../config/database.php';

class AnonymizationService {

    /**
     * Generate a deterministic submission token for a student + evaluation sheet pair.
     * 
     * Token = HMAC-SHA256(studentId:sheetId, APP_SECRET)
     * 
     * This ensures:
     * - Same student + sheet always produces the same token (deduplication)
     * - Token cannot be reversed to find the student ID
     * - Different secrets produce different tokens (environment isolation)
     */
    public static function generateSubmissionToken(int $studentId, int $evaluationSheetId): string {
        $secret = Database::getConfig('APP_SECRET', 'default-insecure-secret-change-me');
        $payload = $studentId . ':' . $evaluationSheetId;
        return hash_hmac('sha256', $payload, $secret);
    }

    /**
     * Check if a student has already submitted for a given evaluation sheet.
     * This is done by computing the expected token and checking if it exists.
     */
    public static function hasSubmitted(int $studentId, int $evaluationSheetId): bool {
        $token = self::generateSubmissionToken($studentId, $evaluationSheetId);

        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT COUNT(*) as cnt FROM submissions WHERE submission_token = :token'
        );
        $stmt->execute([':token' => $token]);
        $row = $stmt->fetch();

        return (int) $row['cnt'] > 0;
    }

    /**
     * Validate that a student is eligible to submit a specific evaluation.
     * 
     * Checks:
     * 1. Student is enrolled in the course linked to the evaluation sheet
     * 2. Evaluation sheet is in 'open' status
     * 3. If end_date is set, it has not passed (start_date does not block after go-live)
     * 4. Student has not already submitted
     * 
     * Returns: ['eligible' => bool, 'reason' => string]
     */
    public static function validateEligibility(int $studentId, int $evaluationSheetId): array {
        $db = Database::getConnection();

        // 1. Get the evaluation sheet
        $stmt = $db->prepare(
            'SELECT es.*, c.id as course_id 
             FROM evaluation_sheets es
             JOIN courses c ON es.course_id = c.id
             WHERE es.id = :sheet_id'
        );
        $stmt->execute([':sheet_id' => $evaluationSheetId]);
        $sheet = $stmt->fetch();

        if (!$sheet) {
            return ['eligible' => false, 'reason' => 'Evaluation sheet not found.'];
        }

        // 2. Check status is 'open'
        if ($sheet['status'] !== 'open') {
            return ['eligible' => false, 'reason' => 'This evaluation is not currently accepting submissions.'];
        }

        // 3. Deadline only — opening time does not block once the sheet is "open".
        // (start_date schedules initial status when created; transitioning to open is the dean’s go-live.)
        $now = new DateTime('now');
        if (!empty($sheet['end_date'])) {
            $end = new DateTime($sheet['end_date']);
            if ($end < $now) {
                return ['eligible' => false, 'reason' => 'This evaluation has expired.'];
            }
        }

        // 4. Check enrollment
        $enrollStmt = $db->prepare(
            'SELECT COUNT(*) as cnt FROM enrollments 
             WHERE student_id = :student_id AND course_id = :course_id'
        );
        $enrollStmt->execute([
            ':student_id' => $studentId,
            ':course_id'  => $sheet['course_id'],
        ]);
        $enrolled = (int) $enrollStmt->fetch()['cnt'];

        if ($enrolled === 0) {
            return ['eligible' => false, 'reason' => 'You are not enrolled in the course for this evaluation.'];
        }

        // 5. Check for duplicate submission
        if (self::hasSubmitted($studentId, $evaluationSheetId)) {
            return ['eligible' => false, 'reason' => 'You have already submitted this evaluation.'];
        }

        return ['eligible' => true, 'reason' => 'Eligible to submit.'];
    }
}
