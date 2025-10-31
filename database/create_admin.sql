-- Create Admin User Script
-- Run this to create a new admin user directly in the database

USE `alumaster`;

-- Insert a new super admin user
-- Default credentials: admin / password123
-- IMPORTANT: Change these credentials after first login!

INSERT INTO `admins` (
    `username`, 
    `email`, 
    `password`, 
    `role`, 
    `first_name`, 
    `last_name`, 
    `is_active`
) VALUES (
    'admin',
    'admin@alumastergh.com',
    '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', -- password: password123
    'super_admin',
    'System',
    'Administrator',
    1
);

-- Verify the admin user was created
SELECT id, username, email, role, first_name, last_name, is_active, created_at 
FROM admins 
WHERE username = 'admin';