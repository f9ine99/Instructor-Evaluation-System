-- ============================================================
-- Sample: Computer Science dept, dean Shewatatek, instructor Ashenafi,
--         two courses (Intro CS + Web Development)
-- Database: ievaluation
-- ============================================================
-- Password for both accounts: password123
-- Run after schema.sql. If departments already exist, remove the INSERT
-- into departments and set @dept_id to an existing id, e.g. SET @dept_id = 1;
-- ============================================================

USE ievaluation;

INSERT INTO departments (name, status) VALUES ('Computer Science', 'active');
SET @dept_id = LAST_INSERT_ID();

-- Dean: Shewatatek
INSERT INTO users (username, password_hash, full_name, email, role, department_id, status) VALUES (
  'shewatatek',
  '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu',
  'Ato Shewatatek Lemma',
  'shewatatek@hope.edu',
  'dean',
  @dept_id,
  'active'
);

-- Instructor: Ashenafi
INSERT INTO users (username, password_hash, full_name, email, role, department_id, status) VALUES (
  'ashenafi',
  '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu',
  'Dr. Ashenafi Worku',
  'ashenafi@hope.edu',
  'instructor',
  @dept_id,
  'active'
);
SET @inst_id = LAST_INSERT_ID();

-- Courses (both in Computer Science; taught by Ashenafi)
INSERT INTO courses (code, title, department_id, program, year_level, semester, academic_year, instructor_id, status) VALUES
(
  'CS101',
  'Introduction to Computer Science',
  @dept_id,
  'BSc Computer Science',
  '1st Year',
  'I',
  '2025',
  @inst_id,
  'active'
),
(
  'WEB201',
  'Web Development',
  @dept_id,
  'BSc Computer Science',
  '2nd Year',
  'I',
  '2025',
  @inst_id,
  'active'
);
