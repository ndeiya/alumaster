<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'About Page Settings';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Pages', 'url' => '#'],
    ['title' => 'About Page']
];

include '../includes/header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">About Page Settings</h2>
    </div>
    
    <div class="card-content">
        <div class="empty-state">
            <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="empty-title">About Page Editor Coming Soon</h3>
            <p class="empty-message">
                About page content management will be available in a future update. This will include company information, team members, and history editing.
            </p>
            <div class="empty-actions">
                <a href="../../about.php" target="_blank" class="btn btn-primary">View Current About Page</a>
                <a href="../index.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>