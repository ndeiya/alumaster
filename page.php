<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

// Get page slug from URL
$page_slug = $_GET['slug'] ?? '';

if (empty($page_slug)) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Get page data from database
$page_data = null;
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published'");
    $stmt->execute([$page_slug]);
    $page_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$page_data) {
        header('HTTP/1.0 404 Not Found');
        include '404.php';
        exit;
    }
    
    // Update view count
    $stmt = $conn->prepare("UPDATE pages SET views = views + 1 WHERE id = ?");
    $stmt->execute([$page_data['id']]);
    
} catch (Exception $e) {
    error_log("Error loading page: " . $e->getMessage());
    header('HTTP/1.0 500 Internal Server Error');
    include '500.php';
    exit;
}

// Set page variables
$page_title = !empty($page_data['meta_title']) ? $page_data['meta_title'] : $page_data['title'] . " - AluMaster Aluminum System";
$page_description = !empty($page_data['meta_description']) ? $page_data['meta_description'] : $page_data['excerpt'];
$page_keywords = $page_data['meta_keywords'] ?? '';

// Load appropriate template
$template = $page_data['template'] ?? 'default';

switch ($template) {
    case 'full-width':
        include 'templates/page-full-width.php';
        break;
    case 'landing':
        include 'templates/page-landing.php';
        break;
    default:
        include 'templates/page-default.php';
        break;
}
?>