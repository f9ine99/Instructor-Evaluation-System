-- ============================================================
-- HOPE Instructor Evaluation System - Database Schema
-- Database: ievaluation
-- ============================================================

CREATE DATABASE IF NOT EXISTS ievaluation;
USE ievaluation;

-- ============================================================
-- 1. DEPARTMENTS
-- ============================================================
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    head_instructor_id INT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. USERS (all roles: student, instructor, dean, hr, admin)
-- ============================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    role ENUM('student', 'instructor', 'dean', 'hr', 'admin') NOT NULL,
    department_id INT DEFAULT NULL,
    must_change_password TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    INDEX idx_role (role),
    INDEX idx_status (status),
    INDEX idx_dept (department_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add FK for department head after users table exists
ALTER TABLE departments
    ADD CONSTRAINT fk_dept_head
    FOREIGN KEY (head_instructor_id) REFERENCES users(id) ON DELETE SET NULL;

-- ============================================================
-- 3. COURSES
-- ============================================================
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL,
    title VARCHAR(150) NOT NULL,
    department_id INT NOT NULL,
    program VARCHAR(100) DEFAULT NULL,
    year_level VARCHAR(20) DEFAULT NULL,
    semester ENUM('I', 'II', 'Summer') NOT NULL,
    academic_year VARCHAR(10) NOT NULL,
    instructor_id INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_semester_year (semester, academic_year),
    INDEX idx_instructor (instructor_id),
    INDEX idx_dept (department_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. ENROLLMENTS
-- ============================================================
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_enrollment (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_student (student_id),
    INDEX idx_course (course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. EVALUATION SHEETS (with lifecycle state machine)
-- States: draft → scheduled → open → closed → reviewed → archived
-- ============================================================
CREATE TABLE evaluation_sheets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT DEFAULT NULL,
    department_id INT NOT NULL,
    course_id INT NOT NULL,
    instructor_id INT NOT NULL,
    created_by INT NOT NULL,
    status ENUM('draft', 'scheduled', 'open', 'closed', 'reviewed', 'archived')
        DEFAULT 'draft',
    start_date DATETIME DEFAULT NULL,
    end_date DATETIME DEFAULT NULL,
    academic_year VARCHAR(10) NOT NULL,
    semester ENUM('I', 'II', 'Summer') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_semester_year (semester, academic_year),
    INDEX idx_instructor (instructor_id),
    INDEX idx_dept (department_id),
    INDEX idx_course (course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. QUESTIONS (linked to evaluation sheets, DB-driven)
-- ============================================================
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evaluation_sheet_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_order INT NOT NULL DEFAULT 0,

    FOREIGN KEY (evaluation_sheet_id) REFERENCES evaluation_sheets(id) ON DELETE CASCADE,
    INDEX idx_sheet (evaluation_sheet_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. SUBMISSIONS (anonymized — NO student_id stored)
-- submission_token = hash(student_id + sheet_id + secret)
-- Used for deduplication without revealing identity
-- ============================================================
CREATE TABLE submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evaluation_sheet_id INT NOT NULL,
    submission_token VARCHAR(64) NOT NULL UNIQUE,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (evaluation_sheet_id) REFERENCES evaluation_sheets(id) ON DELETE CASCADE,
    INDEX idx_sheet (evaluation_sheet_id),
    INDEX idx_token (submission_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. RESPONSES (linked to submission, NOT to student)
-- ============================================================
CREATE TABLE responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    question_id INT NOT NULL,
    rating TINYINT NOT NULL,

    FOREIGN KEY (submission_id) REFERENCES submissions(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    INDEX idx_submission (submission_id),
    INDEX idx_question (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. SUBMISSION COMMENTS (optional text feedback)
-- ============================================================
CREATE TABLE submission_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    comment_text TEXT NOT NULL,

    FOREIGN KEY (submission_id) REFERENCES submissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. AUDIT LOG (data integrity & abuse prevention)
-- ============================================================
CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) DEFAULT NULL,
    entity_id INT DEFAULT NULL,
    details TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. INSTRUCTOR REFLECTIONS
-- ============================================================
CREATE TABLE instructor_reflections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evaluation_sheet_id INT NOT NULL,
    instructor_id INT NOT NULL,
    agreement ENUM('agree', 'disagree') NOT NULL,
    went_well TEXT DEFAULT NULL,
    areas_improvement TEXT DEFAULT NULL,
    action_plan TEXT DEFAULT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (evaluation_sheet_id) REFERENCES evaluation_sheets(id) ON DELETE CASCADE,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_reflection (evaluation_sheet_id, instructor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. HR DECISIONS
-- ============================================================
CREATE TABLE hr_decisions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    instructor_id INT NOT NULL,
    decision_type VARCHAR(100) NOT NULL,
    justification TEXT NOT NULL,
    decided_by INT NOT NULL,
    decided_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (decided_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_instructor (instructor_id),
    INDEX idx_decided_by (decided_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 13. DEFAULT QUESTION TEMPLATES (for seeding new evaluations)
-- ============================================================
CREATE TABLE default_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    question_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Standard rubric copied into each new evaluation sheet (no demo users/courses here)
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
