-- ============================================================
-- Wipe all application data except default_questions (rubric templates)
-- Database: ievaluation
-- ============================================================
-- Use after migrating away from old seed data, or to reset smoke tests.
-- Run: mysql -h ... -u ... -p ievaluation < database/clear_data.sql
--
-- Note: MySQL often rejects TRUNCATE on parent tables when child tables
-- reference them (even with FOREIGN_KEY_CHECKS=0). DELETE is used instead.
-- ============================================================

USE ievaluation;

SET FOREIGN_KEY_CHECKS = 0;

UPDATE departments SET head_instructor_id = NULL;

DELETE FROM responses;
DELETE FROM submission_comments;
DELETE FROM submissions;
DELETE FROM questions;
DELETE FROM instructor_reflections;
DELETE FROM evaluation_sheets;
DELETE FROM enrollments;
DELETE FROM hr_decisions;
DELETE FROM courses;
DELETE FROM audit_log;
DELETE FROM users;
DELETE FROM departments;

SET FOREIGN_KEY_CHECKS = 1;
