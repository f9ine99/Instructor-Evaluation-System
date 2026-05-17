-- ============================================================
-- Bootstrap admin user (run after schema.sql on database ievaluation)
-- Plaintext password matches seed_dean_instructor.sql sample accounts:
--   password123
-- ============================================================

USE ievaluation;

INSERT INTO users (username, password_hash, full_name, email, role, department_id, must_change_password, status) VALUES (
  'admin',
  '$2y$10$WfKz/cZiwqwYwCCy1AK.vu4UvL8v0xMBWnh/rCu3yKbpzkeU0fZXu',
  'System Administrator',
  NULL,
  'admin',
  NULL,
  0,
  'active'
);
