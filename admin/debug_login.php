<?php
// Debug Login System - Comprehensive Testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Login System Debug</h1>";

// Test 1: Check if files exist
echo "<h2>1. File Existence Check</h2>";
$files = [
    '../includes/config.php',
    '../includes/database.php', 
    '../includes/functions.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file NOT FOUND<br>";
    }
}

// Test 2: Include files
echo "<h2>2. Include Files Test</h2>";
try {
    require_once '../includes/config.php';
    echo "✅ config.php included<br>";
} catch (Exception $e) {
    echo "❌ config.php error: " . $e->getMessage() . "<br>";
}

try {
    require_once '../includes/database.php';
    echo "✅ database.php included<br>";
} catch (Exception $e) {
    echo "❌ database.php error: " . $e->getMessage() . "<br>";
}

try {
    require_once '../includes/functions.php';
    echo "✅ functions.php included<br>";
} catch (Exception $e) {
    echo "❌ functions.php error: " . $e->getMessage() . "<br>";
}

// Test 3: Database connection
echo "<h2>3. Database Connection Test</h2>";
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if ($conn) {
        echo "✅ Database connection successful<br>";
        
        // Test if admins table exists
        $stmt = $conn->prepare("SHOW TABLES LIKE 'admins'");
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            echo "✅ 'admins' table exists<br>";
            
            // Check table structure
            $stmt = $conn->prepare("DESCRIBE admins");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>Admin Table Structure:</h3>";
            echo "<table border='1'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            foreach ($columns as $col) {
                echo "<tr>";
                echo "<td>" . $col['Field'] . "</td>";
                echo "<td>" . $col['Type'] . "</td>";
                echo "<td>" . $col['Null'] . "</td>";
                echo "<td>" . $col['Key'] . "</td>";
                echo "<td>" . $col['Default'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
        } else {
            echo "❌ 'admins' table does NOT exist<br>";
        }
        
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 4: Check existing admin users
echo "<h2>4. Existing Admin Users</h2>";
try {
    if (isset($conn) && $conn) {
        $stmt = $conn->prepare("SELECT id, username, email, first_name, last_name, role, is_active, login_attempts, locked_until FROM admins");
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($admins)) {
            echo "❌ No admin users found<br>";
        } else {
            echo "✅ Found " . count($admins) . " admin user(s):<br>";
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Name</th><th>Role</th><th>Active</th><th>Login Attempts</th><th>Locked Until</th></tr>";
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
                echo "</tr>";
            }
            echo "</table>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error checking admin users: " . $e->getMessage() . "<br>";
}

// Test 5: Test password verification
echo "<h2>5. Password Hash Test</h2>";
$test_password = 'admin123';
$test_hash = '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe6.Km.kbwEcqCgbF/ghU5NFWJtU7BL.K';

if (password_verify($test_password, $test_hash)) {
    echo "✅ Password verification works - 'admin123' matches the hash<br>";
} else {
    echo "❌ Password verification failed<br>";
}

// Generate a fresh hash
$fresh_hash = password_hash($test_password, PASSWORD_DEFAULT);
echo "Fresh hash for 'admin123': $fresh_hash<br>";

// Test 6: Session check
echo "<h2>6. Session Test</h2>";
session_start();
echo "Session ID: " . session_id() . "<br>";
echo "Login attempts in session: " . ($_SESSION['login_attempts'] ?? 'None') . "<br>";
echo "Last attempt time: " . ($_SESSION['last_attempt_time'] ?? 'None') . "<br>";

// Test 7: Create test admin user
echo "<h2>7. Create Test Admin User</h2>";
if (isset($conn) && $conn) {
    try {
        // Delete existing test user
        $stmt = $conn->prepare("DELETE FROM admins WHERE username = 'testadmin'");
        $stmt->execute();
        
        // Create new test user
        $username = 'testadmin';
        $password = 'test123';
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO admins (username, email, password, role, first_name, last_name, is_active, login_attempts, locked_until) VALUES (?, ?, ?, 'super_admin', 'Test', 'Admin', 1, 0, NULL)");
        $stmt->execute([$username, 'test@example.com', $password_hash]);
        
        echo "✅ Test admin user created:<br>";
        echo "Username: $username<br>";
        echo "Password: $password<br>";
        echo "Hash: $password_hash<br>";
        
    } catch (Exception $e) {
        echo "❌ Error creating test user: " . $e->getMessage() . "<br>";
    }
}

echo "<br><a href='login.php'>Go to Login Page</a>";
?>