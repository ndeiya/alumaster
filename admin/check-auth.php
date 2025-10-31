<?php
session_start();

// Check if user is authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Store the intended destination
    $redirect_to = $_GET['redirect'] ?? 'index.php';
    $_SESSION['redirect_after_login'] = $redirect_to;
    
    // Redirect to login
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

// User is authenticated, redirect to the intended file
$redirect_to = $_GET['redirect'] ?? 'index.php';

// Security check - ensure the redirect is within admin directory
if (strpos($redirect_to, '..') !== false || strpos($redirect_to, '/') === 0) {
    $redirect_to = 'index.php';
}

header("Location: $redirect_to?" . $_SERVER['QUERY_STRING']);
exit;
?>