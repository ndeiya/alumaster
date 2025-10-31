-- Add Inquiries Table to AluMaster Database
-- Run this to enable the inquiries system

USE `alumaster`;

-- Create inquiries table
CREATE TABLE IF NOT EXISTS `inquiries` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `company` varchar(100) DEFAULT NULL,
    `service_interest` varchar(100) DEFAULT NULL,
    `message` text,
    `status` enum('unread','read','replied') DEFAULT 'unread',
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_status` (`status`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert some sample inquiries for testing
INSERT INTO `inquiries` (`name`, `email`, `phone`, `company`, `service_interest`, `message`, `status`, `ip_address`) VALUES
('John Doe', 'john@example.com', '+233-541-123-456', 'ABC Construction', 'Curtain Wall Systems', 'I am interested in your curtain wall solutions for a new office building project.', 'unread', '192.168.1.1'),
('Sarah Johnson', 'sarah@techcorp.com', '+233-502-789-012', 'TechCorp Ghana', 'Alucobond Cladding', 'We need alucobond cladding for our headquarters. Please send quotation.', 'read', '192.168.1.2'),
('Michael Asante', 'masante@gmail.com', '+233-244-567-890', NULL, 'Spider Glass Systems', 'Looking for spider glass installation for residential project in Accra.', 'unread', '192.168.1.3');

-- Verify the table was created
SELECT COUNT(*) as total_inquiries FROM inquiries;
SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 5;