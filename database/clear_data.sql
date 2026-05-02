-- ============================================================
-- Wipe all application data except default_questions (rubric templates)
-- Database: ievaluation
-- ============================================================
-- Use after migrating away from old seed data, or to reset smoke tests.
-- Run: mysql -h ... -u ... -p ievaluation < database/clear_data.sql
-- ============================================================

USE ievaluation;

SET FOREIGN_KEY_CHECKS = 0;

UPDATE departments SET head_instructor_id = NULL;

TRUNCATE TABLE responses;
TRUNCATE TABLE submission_comments;
TRUNCATE TABLE submissions;
TRUNCATE TABLE questions;
TRUNCATE TABLE instructor_reflections;
TRUNCATE TABLE evaluation_sheets;
TRUNCATE TABLE enrollments;
TRUNCATE TABLE hr_decisions;
TRUNCATE TABLE courses;
TRUNCATE TABLE audit_log;
TRUNCATE TABLE users;
TRUNCATE TABLE departments;

SET FOREIGN_KEY_CHECKS = 1;
