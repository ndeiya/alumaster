<?php
// Admin authentication check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Check session timeout (2 hours)
$session_timeout = 2 * 60 * 60; // 2 hours in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_timeout) {
    // Session expired
    session_destroy();
    header('Location: login.php?expired=1');
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Regenerate session ID periodically for security
if (!isset($_SESSION['session_regenerated']) || (time() - $_SESSION['session_regenerated']) > 300) {
    session_regenerate_id(true);
    $_SESSION['session_regenerated'] = time();
}

// Get current admin info
$current_admin = [
    'id' => $_SESSION['admin_id'],
    'username' => $_SESSION['admin_username'],
    'role' => $_SESSION['admin_role'],
    'name' => $_SESSION['admin_name']
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

// CSRF token generation and validation
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

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