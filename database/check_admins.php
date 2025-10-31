<?php
// Check existing admin users
require_once '../includes/config.php';
require_once '../includes/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT id, username, email, first_name, last_name, role, is_active, login_attempts, locked_until, created_at FROM admins");
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Current Admin Users</h2>";
    if (empty($admins)) {
        echo "<p>No admin users found in database.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Name</th><th>Role</th><th>Active</th><th>Login Attempts</th><th>Locked Until</th><th>Created</th></tr>";
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>" . $admin['id'] . "</td>";
            echo "<td>" . $admin['username'] . "</td>";
            echo "<td>" . $admin['email'] . "</td>";
            echo "<td>" . $admin['first_name'] . " " . $admin['last_name'] . "</td>";
            echo "<td>" . $admin['role'] . "</td>";
            echo "<td>" . ($admin['is_active'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . $admin['login_attempts'] . "</td>";
            echo "<td>" . ($admin['locked_until'] ?: 'Not locked') . "</td>";
            echo "<td>" . $admin['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>