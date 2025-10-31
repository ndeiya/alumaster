<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'Homepage Settings';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Pages', 'url' => '#'],
    ['title' => 'Homepage']
];

include '../includes/header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">Homepage Settings</h2>
    </div>
    
    <div class="card-content">
        <div class="empty-state">
            <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v3H8V5z"></path>
            </svg>
            <h3 class="empty-title">Homepage Editor Coming Soon</h3>
            <p class="empty-message">
                Homepage content management will be available in a future update. This will include hero section, featured services, and content blocks editing.
            </p>
            <div class="empty-actions">
                <a href="../../index.php" target="_blank" class="btn btn-primary">View Current Homepage</a>
                <a href="../index.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>