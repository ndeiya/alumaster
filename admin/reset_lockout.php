<?php
// Reset Login Lockout Script
// Run this to clear session-based login attempt restrictions

session_start();

// Clear session-based lockout
unset($_SESSION['login_attempts']);
unset($_SESSION['last_attempt_time']);

// Destroy the entire session to be safe
session_destroy();

echo "<h2>Session Lockout Reset Complete</h2>";
echo "<p>Session-based login lockout has been cleared.</p>";
echo "<p><strong>Note:</strong> If you're still locked out, run the SQL script to clear database lockouts.</p>";
echo "<br>";
echo "<a href='login.php' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a>";
echo "<br><br>";
echo "<p><strong>SQL Script to run if still locked out:</strong></p>";
echo "<code>UPDATE admins SET login_attempts = 0, locked_until = NULL;</code>";
?>