<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'SEO Settings';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Settings', 'url' => '#'],
    ['title' => 'SEO Settings']
];

include '../includes/header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">SEO Settings</h2>
    </div>
    
    <div class="card-content">
        <div class="empty-state">
            <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h3 class="empty-title">SEO Settings Coming Soon</h3>
            <p class="empty-message">
                SEO settings management will be available in a future update. This will include meta tags, Open Graph settings, and search engine optimization tools.
            </p>
            <div class="empty-actions">
                <a href="../settings/general.php" class="btn btn-primary">General Settings</a>
                <a href="../index.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>