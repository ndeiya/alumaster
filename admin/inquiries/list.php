<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'Inquiries';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Inquiries']
];

// Note: This is a placeholder page since the inquiries table doesn't exist yet
// You'll need to create the inquiries table in your database schema

include '../includes/header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">Customer Inquiries</h2>
    </div>
    
    <div class="card-content">
        <div class="empty-state">
            <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <h3 class="empty-title">Inquiries System Not Set Up</h3>
            <p class="empty-message">
                The inquiries table hasn't been created yet. This feature will be available once you set up the contact form and inquiries database table.
            </p>
            <div class="empty-actions">
                <a href="../index.php" class="btn btn-primary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<!-- 
To enable inquiries, add this table to your database:

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
-->

<?php include '../includes/footer.php'; ?>