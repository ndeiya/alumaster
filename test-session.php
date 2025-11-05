<?php
/**
 * Test Session Handling
 */

echo "Testing session handling...\n";

// Start session like contact.php does
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    ini_set('session.use_strict_mode', 1);
    session_start();
}

echo "✅ Session started successfully\n";
echo "Session ID: " . session_id() . "\n";

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

echo "✅ Files loaded successfully\n";

// Test CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

echo "✅ CSRF token: " . substr($_SESSION['csrf_token'], 0, 16) . "...\n";

echo "\n✅ Session test completed successfully!\n";
?>