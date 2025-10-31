<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'Footer Links';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Navigation', 'url' => '#'],
    ['title' => 'Footer Links']
];

include '../includes/header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">Footer Links Management</h2>
    </div>
    
    <div class="card-content">
        <div class="empty-state">
            <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
            </svg>
            <h3 class="empty-title">Footer Editor Coming Soon</h3>
            <p class="empty-message">
                Footer links management will be available in a future update. This will include managing footer navigation, social links, and contact information.
            </p>
            <div class="empty-actions">
                <a href="../../index.php" target="_blank" class="btn btn-primary">View Current Footer</a>
                <a href="../index.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>