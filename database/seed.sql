-- ============================================================
-- HOPE Instructor Evaluation System - Seed Data
-- Database: ievaluation
-- ============================================================

USE ievaluation;

-- ============================================================
-- DEPARTMENTS
-- ============================================================
INSERT INTO departments (id, name, status) VALUES
(1, 'Computer Science', 'active'),
(2, 'English Literature', 'active'),
(3, 'Mathematics', 'active'),
(4, 'Architecture', 'active'),
(5, 'Physics', 'active'),
(6, 'Business Administration', 'active'),
(7, 'Electrical Engineering', 'active'),
(8, 'Civil Engineering', 'active');

-- ============================================================
-- USERS (passwords are bcrypt hashed — all default to "password123")
-- hash: $2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu
-- ============================================================

-- System Admin
INSERT INTO users (id, username, password_hash, full_name, email, role, department_id, status) VALUES
(1, 'admin', '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu', 'System Administrator', 'admin@hope.edu', 'admin', NULL, 'active');

-- Deans (one per relevant department)
INSERT INTO users (id, username, password_hash, full_name, email, role, department_id, status) VALUES
(2, 'dean.cs', '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu', 'Dr. Alan Turing', 'turing@hope.edu', 'dean', 1, 'active'),
(3, 'dean.eng', '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu', 'Dr. Jane Austen', 'austen@hope.edu', 'dean', 2, 'active');

-- HR Staff
INSERT INTO users (id, username, password_hash, full_name, email, role, department_id, status) VALUES
(4, 'hr.staff', '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu', 'HR Officer A', 'hr@hope.edu', 'hr', NULL, 'active');

-- Instructors
INSERT INTO users (id, username, password_hash, full_name, email, role, department_id, status) VALUES
(5, 'sarah.j', '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu', 'Dr. Sarah Johnson', 'sarah@hope.edu', 'instructor', 1, 'active'),
(6, 'michael.c', '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu', 'Prof. Michael Chen', 'michael@hope.edu', 'instructor', 2, 'active'),
(7, 'emily.d', '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu', 'Dr. Emily Davis', 'emily@hope.edu', 'instructor', 4, 'active'),
(8, 'robert.w', '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu', 'Mr. Robert Wilson', 'robert@hope.edu', 'instructor', 3, 'active'),
(9, 'lisa.b', '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu', 'Dr. Lisa Brown', 'lisa@hope.edu', 'instructor', 5, 'active'),
(10, 'abebe.k', '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu', 'Dr. Abebe Kebede', 'abebe@hope.edu', 'instructor', 1, 'active');

-- Students
INSERT INTO users (id, username, password_hash, full_name, email, role, department_id, status) VALUES
(11, '2021001', '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu', 'John Doe', 'john@hope.edu', 'student', 1, 'active'),
(12, '2021002', '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu', 'Jane Smith', 'jane@hope.edu', 'student', 1, 'active'),
(13, '2021003', '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu', 'Alice Hailu', 'alice@hope.edu', 'student', 1, 'active'),
(14, '2021045', '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu', 'Bob Tadesse', 'bob@hope.edu', 'student', 2, 'active'),
(15, '2021050', '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu', 'Sara Mengistu', 'sara@hope.edu', 'student', 1, 'active');

-- Update department heads
UPDATE departments SET head_instructor_id = 5 WHERE id = 1;  -- CS: Dr. Sarah Johnson
UPDATE departments SET head_instructor_id = 6 WHERE id = 2;  -- Eng Lit: Prof. Michael Chen

-- ============================================================
-- COURSES
-- ============================================================
INSERT INTO courses (id, code, title, department_id, program, year_level, semester, academic_year, instructor_id, status) VALUES
(1, 'CS101', 'Intro to Programming', 1, 'BSc CS', '1st Year', 'I', '2025', 5, 'active'),
(2, 'ENG202', 'Advanced Composition', 2, 'BA English', '2nd Year', 'II', '2025', 6, 'active'),
(3, 'MATH301', 'Linear Algebra', 3, 'BSc Math', '3rd Year', 'I', '2025', 7, 'active'),
(4, 'CS202', 'Data Structures', 1, 'BSc CS', '2nd Year', 'I', '2025', 10, 'active'),
(5, 'CS305', 'Algorithms', 1, 'BSc CS', '3rd Year', 'I', '2025', 10, 'active'),
(6, 'WEB301', 'Web Programming', 1, 'BSc CS', '3rd Year', 'I', '2025', 10, 'active');

-- ============================================================
-- ENROLLMENTS
-- ============================================================
INSERT INTO enrollments (student_id, course_id) VALUES
-- John Doe (2021001) enrolled in CS101, CS202, Web Programming
(11, 1), (11, 4), (11, 6),
-- Jane Smith (2021002) enrolled in CS101, CS305
(12, 1), (12, 5),
-- Alice Hailu (2021003) enrolled in CS202, Web Programming
(13, 4), (13, 6),
-- Bob Tadesse (2021045) enrolled in ENG202
(14, 2),
-- Sara Mengistu (2021050) enrolled in CS101, CS202, Algorithms
(15, 1), (15, 4), (15, 5);

-- ============================================================
-- DEFAULT EVALUATION QUESTIONS (the 13 standard questions)
-- ============================================================
INSERT INTO default_questions (question_text, question_order, is_active) VALUES
('The course instructor gives course outlines at the beginning of the semester.', 1, 1),
('The course instructor teaches effectively by preparation using useful instructional materials and available technology.', 2, 1),
('The course instructor is knowledgeable enough on his course.', 3, 1),
('The course instructor arrives on time.', 4, 1),
('The course instructor leaves on time.', 5, 1),
('The course instructor manages and maintains appropriate discipline in the class.', 6, 1),
('The course instructor covered all chapters in the course outline.', 7, 1),
('The course instructor gives immediate feedback on students\' progress and performances.', 8, 1),
('The course instructor encourages student\'s interaction/participation.', 9, 1),
('Assessment covers fairly all contents and learning experiences.', 10, 1),
('The course instructor uses his/her office/consultation hours to render academic support and advice to the students.', 11, 1),
('The course instructor returns the graded script within reasonable time.', 12, 1),
('The course instructor serves as a role model through high moral standards in class and on campus, as well as encouraging high professional standards.', 13, 1);

-- ============================================================
-- SAMPLE EVALUATION SHEET (already open for demo)
-- ============================================================
INSERT INTO evaluation_sheets (id, title, description, department_id, course_id, instructor_id, created_by, status, start_date, end_date, academic_year, semester) VALUES
(1, 'CS101 Instructor Evaluation - Semester I 2025', 'Evaluate the teaching effectiveness of Dr. Sarah Johnson for Intro to Programming.', 1, 1, 5, 2, 'open', '2025-04-01 00:00:00', '2027-12-31 23:59:59', '2025', 'I'),
(2, 'Web Programming Evaluation - Semester I 2025', 'Evaluate Dr. Abebe Kebede for Web Programming.', 1, 6, 10, 2, 'open', '2025-04-01 00:00:00', '2027-12-31 23:59:59', '2025', 'I'),
(3, 'ENG202 Evaluation - Semester II 2025', 'Evaluate Prof. Michael Chen for Advanced Composition.', 2, 2, 6, 3, 'draft', NULL, NULL, '2025', 'II');

-- Seed questions for the open evaluation sheets (copy from defaults)
INSERT INTO questions (evaluation_sheet_id, question_text, question_order)
SELECT 1, question_text, question_order FROM default_questions WHERE is_active = 1;

INSERT INTO questions (evaluation_sheet_id, question_text, question_order)
SELECT 2, question_text, question_order FROM default_questions WHERE is_active = 1;

INSERT INTO questions (evaluation_sheet_id, question_text, question_order)
SELECT 3, question_text, question_order FROM default_questions WHERE is_active = 1;

-- ============================================================
-- SAMPLE SUBMISSIONS (anonymized, for demo data in dashboards)
-- These tokens are pre-computed for demonstration purposes
-- ============================================================

-- Submission 1: Anonymous student evaluated CS101 (sheet 1)
INSERT INTO submissions (id, evaluation_sheet_id, submission_token, submitted_at) VALUES
(1, 1, 'a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6abcd', '2025-04-15 10:30:00'),
(2, 1, 'b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6abcdef', '2025-04-16 14:20:00'),
(3, 2, 'c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6abcdefab', '2025-04-17 09:15:00');

-- Responses: resolve question_id via evaluation_sheet_id + question_order (portable AUTO_INCREMENT ids)
INSERT INTO responses (submission_id, question_id, rating)
SELECT 1, q.id,
  CASE q.question_order
    WHEN 1 THEN 5 WHEN 2 THEN 4 WHEN 3 THEN 5 WHEN 4 THEN 5 WHEN 5 THEN 4
    WHEN 6 THEN 4 WHEN 7 THEN 5 WHEN 8 THEN 4 WHEN 9 THEN 5 WHEN 10 THEN 4
    WHEN 11 THEN 4 WHEN 12 THEN 5 WHEN 13 THEN 5
  END
FROM questions q
WHERE q.evaluation_sheet_id = 1;

INSERT INTO responses (submission_id, question_id, rating)
SELECT 2, q.id,
  CASE q.question_order
    WHEN 1 THEN 4 WHEN 2 THEN 5 WHEN 3 THEN 5 WHEN 4 THEN 4 WHEN 5 THEN 5
    WHEN 6 THEN 4 WHEN 7 THEN 4 WHEN 8 THEN 5 WHEN 9 THEN 4 WHEN 10 THEN 5
    WHEN 11 THEN 4 WHEN 12 THEN 4 WHEN 13 THEN 5
  END
FROM questions q
WHERE q.evaluation_sheet_id = 1;

INSERT INTO responses (submission_id, question_id, rating)
SELECT 3, q.id,
  CASE q.question_order
    WHEN 1 THEN 5 WHEN 2 THEN 5 WHEN 3 THEN 4 WHEN 4 THEN 5 WHEN 5 THEN 5
    WHEN 6 THEN 4 WHEN 7 THEN 5 WHEN 8 THEN 4 WHEN 9 THEN 5 WHEN 10 THEN 4
    WHEN 11 THEN 5 WHEN 12 THEN 5 WHEN 13 THEN 4
  END
FROM questions q
WHERE q.evaluation_sheet_id = 2;

-- Sample comments
INSERT INTO submission_comments (submission_id, comment_text) VALUES
(1, 'Great explanation of complex topics. Really enjoyed the practical examples.'),
(2, 'The workload was a bit heavy this semester, but learned a lot.'),
(3, 'Dr. Abebe is an excellent instructor. Very approachable and knowledgeable.');

-- ============================================================
-- SAMPLE HR DECISION
-- ============================================================
INSERT INTO hr_decisions (instructor_id, decision_type, justification, decided_by) VALUES
(5, 'Commendation', 'Consistently excellent evaluation scores across all courses. Recommended for teaching excellence award.', 4);
