<?php
// Simple Login Test - No Lockout
session_start();

// Clear any existing lockout
unset($_SESSION['login_attempts']);
unset($_SESSION['last_attempt_time']);

require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$message = '';

if ($_POST && isset($_POST['test_login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "<h2>Login Attempt Debug</h2>";
    echo "Username entered: '$username'<br>";
    echo "Password entered: '$password'<br>";
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        if (!$conn) {
            echo "❌ Database connection failed<br>";
        } else {
            echo "✅ Database connected<br>";
            
            $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND is_active = 1");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin) {
                echo "✅ User found in database<br>";
                echo "User ID: " . $admin['id'] . "<br>";
                echo "Username: " . $admin['username'] . "<br>";
                echo "Email: " . $admin['email'] . "<br>";
                echo "Role: " . $admin['role'] . "<br>";
                echo "Active: " . ($admin['is_active'] ? 'Yes' : 'No') . "<br>";
                echo "Stored hash: " . $admin['password'] . "<br>";
                
                if (password_verify($password, $admin['password'])) {
                    echo "✅ Password verification SUCCESS!<br>";
                    
                    // Set session variables
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_role'] = $admin['role'];
                    $_SESSION['admin_name'] = $admin['first_name'] . ' ' . $admin['last_name'];
                    
                    echo "<br><strong>Login successful! Redirecting...</strong><br>";
                    echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 2000);</script>";
                    
                } else {
                    echo "❌ Password verification FAILED<br>";
                    
                    // Test with a fresh hash
                    $fresh_hash = password_hash($password, PASSWORD_DEFAULT);
                    echo "Fresh hash for entered password: $fresh_hash<br>";
                }
            } else {
                echo "❌ User NOT found in database<br>";
                
                // Show all users for debugging
                $stmt = $conn->prepare("SELECT username, is_active FROM admins");
                $stmt->execute();
                $all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "Available users:<br>";
                foreach ($all_users as $user) {
                    echo "- " . $user['username'] . " (active: " . ($user['is_active'] ? 'Yes' : 'No') . ")<br>";
                }
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin: 10px 0; }
        input[type="text"], input[type="password"] { padding: 8px; width: 200px; }
        button { padding: 10px 20px; background: #007cba; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Login System Test</h1>
    
    <form method="POST">
        <div class="form-group">
            <label>Username:</label><br>
            <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? 'testadmin'); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Password:</label><br>
            <input type="password" name="password" value="<?php echo htmlspecialchars($_POST['password'] ?? 'test123'); ?>" required>
        </div>
        
        <button type="submit" name="test_login">Test Login</button>
    </form>
    
    <br>
    <a href="debug_login.php">Run Full Debug</a> | 
    <a href="login.php">Normal Login</a>
    
    <?php if (!empty($message)): ?>
        <div style="margin-top: 20px; padding: 10px; background: #f0f0f0;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
</body>
</html>