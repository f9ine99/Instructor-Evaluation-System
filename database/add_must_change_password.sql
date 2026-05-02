-- Run once on existing databases (column may already exist — ignore duplicate column error).
ALTER TABLE users
    ADD COLUMN must_change_password TINYINT(1) NOT NULL DEFAULT 0
    AFTER department_id;
