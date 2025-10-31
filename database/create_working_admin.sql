-- Create Working Admin User
-- This will create a simple admin user that definitely works

USE `alumaster`;

-- Delete any existing admin user to start fresh
DELETE FROM `admins` WHERE `username` = 'admin';

-- Create a new admin user with simple credentials
-- Username: admin
-- Password: admin123
INSERT INTO `admins` (
    `username`, 
    `email`, 
    `password`, 
    `role`, 
    `first_name`, 
    `last_name`, 
    `is_active`,
    `login_attempts`,
    `locked_until`
) VALUES (
    'admin',
    'admin@example.com',
    '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe6.Km.kbwEcqCgbF/ghU5NFWJtU7BL.K', -- admin123
    'super_admin',
    'Admin',
    'User',
    1,
    0,
    NULL
);

-- Verify the user was created
SELECT 
    id, 
    username, 
    email, 
    first_name, 
    last_name, 
    role, 
    is_active, 
    login_attempts,
    locked_until,
    created_at 
FROM `admins` 
WHERE `username` = 'admin';