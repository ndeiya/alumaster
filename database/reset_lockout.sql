-- Reset Login Lockout SQL Script
-- Run this to clear all login attempt restrictions

USE `alumaster`;

-- Reset login attempts and unlock all admin accounts
UPDATE `admins` 
SET `login_attempts` = 0, 
    `locked_until` = NULL 
WHERE `login_attempts` > 0 OR `locked_until` IS NOT NULL;

-- Show all admin accounts status
SELECT 
    `id`, 
    `username`, 
    `email`, 
    `login_attempts`, 
    `locked_until`, 
    `is_active`,
    `last_login`
FROM `admins`;