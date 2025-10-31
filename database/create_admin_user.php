<?php
// Create Admin User Script
require_once '../includes/config.php';
require_once '../includes/database.php';

$username = 'admin';
$password = 'admin123';
$email = 'admin@example.com';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Delete existing admin user if exists
    $stmt = $conn->prepare("DELETE FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    
    // Create password hash
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new admin user
    $stmt = $conn->prepare("INSERT INTO admins (username, email, password, role, first_name, last_name, is_active, login_attempts, locked_until) VALUES (?, ?, ?, 'super_admin', 'Admin', 'User', 1, 0, NULL)");
    $stmt->execute([$username, $email, $password_hash]);
    
    echo "<h2>Admin User Created Successfully!</h2>";
    echo "<p><strong>Username:</strong> $username</p>";
    echo "<p><strong>Password:</strong> $password</p>";
    echo "<p><strong>Password Hash:</strong> $password_hash</p>";
    echo "<br>";
    echo "<a href='../admin/login.php' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login</a>";
    
} catch (Exception $e) {
    echo "Error creating admin user: " . $e->getMessage();
}
?>