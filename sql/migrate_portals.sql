-- Upgrade an existing ShikkhaBridge database to the student / teacher / admin portals.
-- Run once (phpMyAdmin > Import, or: mysql -u root shikkhabridge < sql/migrate_portals.sql).
-- Fresh installs do not need this: schema.sql already has the new columns.
USE shikkhabridge;

-- 1) allow both names while we move mentors over to teachers
ALTER TABLE users MODIFY role ENUM('student','mentor','teacher','admin') NOT NULL DEFAULT 'student';
UPDATE users SET role = 'teacher' WHERE role = 'mentor';
ALTER TABLE users MODIFY role ENUM('student','teacher','admin') NOT NULL DEFAULT 'student';

-- 2) account status: teachers who sign up wait for an admin; admins can suspend anyone
ALTER TABLE users ADD COLUMN status ENUM('pending','active','suspended') NOT NULL DEFAULT 'active' AFTER role;
