<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Calculate the path to admin root from current file
$current_path = $_SERVER['SCRIPT_NAME'];
$admin_pos = strpos($current_path, '/admin/');
if ($admin_pos !== false) {
    $after_admin = substr($current_path, $admin_pos + 7); // +7 for '/admin/'
    $depth = substr_count($after_admin, '/');
    $admin_root = str_repeat('../', $depth);
} else {
    $admin_root = '';
}

// Admin authentication check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ' . $admin_root . 'login.php');
    exit;
}

// Check session timeout (2 hours)
$session_timeout = 2 * 60 * 60; // 2 hours in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_timeout) {
    // Session expired
    session_destroy();
    header('Location: ' . $admin_root . 'login.php?expired=1');
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Regenerate session ID periodically for security
if (!isset($_SESSION['session_regenerated']) || (time() - $_SESSION['session_regenerated']) > 300) {
    session_regenerate_id(true);
    $_SESSION['session_regenerated'] = time();
}

// Get current admin info with defaults to prevent undefined array key warnings
$current_admin = [
    'id' => $_SESSION['admin_id'] ?? 0,
    'username' => $_SESSION['admin_username'] ?? 'Unknown',
    'role' => $_SESSION['admin_role'] ?? 'editor',
    'name' => $_SESSION['admin_name'] ?? 'Unknown User',
    'email' => $_SESSION['admin_email'] ?? '',
    'first_name' => $_SESSION['admin_first_name'] ?? '',
    'last_name' => $_SESSION['admin_last_name'] ?? '',
    'last_login' => $_SESSION['admin_last_login'] ?? null,
    'created_at' => $_SESSION['admin_created_at'] ?? date('Y-m-d H:i:s')
];

// Role-based access control helper function
function check_admin_permission($required_role = 'editor') {
    global $current_admin;
    
    $role_hierarchy = [
        'editor' => 1,
        'admin' => 2,
        'super_admin' => 3
    ];
    
    $user_level = $role_hierarchy[$current_admin['role']] ?? 0;
    $required_level = $role_hierarchy[$required_role] ?? 1;
    
    return $user_level >= $required_level;
}

// CSRF token functions are available from includes/functions.php

// Activity logging function
function log_admin_activity($action, $details = '', $target_id = null) {
    global $current_admin;
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("INSERT INTO activity_logs (admin_id, action, details, target_id, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $current_admin['id'],
            $action,
            $details,
            $target_id,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    } catch (Exception $e) {
        // Log error but don't break the application
        error_log("Failed to log admin activity: " . $e->getMessage());
    }
}
?>