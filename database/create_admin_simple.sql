-- Simple Admin User Creation
-- This creates an admin user with a temporary password that you can change

USE `alumaster`;

-- First, delete any existing admin user (optional)
-- DELETE FROM admins WHERE username = 'admin';

-- Create admin user with a simple password
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
    'temppass', -- This will be updated below
    'super_admin',
    'System',
    'Administrator',
    1
);

-- Update with proper password hash for 'admin123'
UPDATE `admins` 
SET `password` = '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm' 
WHERE `username` = 'admin';

-- Verify the admin user
SELECT id, username, email, role, first_name, last_name, is_active, created_at 
FROM admins 
WHERE username = 'admin';