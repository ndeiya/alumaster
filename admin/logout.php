<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';

// Log logout activity if user was logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("INSERT INTO activity_logs (admin_id, action, details, ip_address, created_at) VALUES (?, 'logout', 'User logged out', ?, NOW())");
        $stmt->execute([$_SESSION['admin_id'], $_SERVER['REMOTE_ADDR']]);
    } catch (Exception $e) {
        // Ignore logging errors during logout
    }
}

// Destroy session
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to login page
header('Location: login.php?logged_out=1');
exit;
?>