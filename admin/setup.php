<?php
// Admin Setup Script - Run this once to create initial admin user
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Check if setup has already been run
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT COUNT(*) FROM admins WHERE status = 'active'");
    $stmt->execute();
    $admin_count = $stmt->fetchColumn();
    
    if ($admin_count > 0) {
        die('Setup has already been completed. Admin users exist in the database.');
    }
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

$success_message = '';
$error_message = '';

if ($_POST && isset($_POST['create_admin'])) {
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $full_name = sanitize_input($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    $errors = [];
    
    if (empty($username) || strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters long.";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required.";
    }
    
    if (empty($full_name)) {
        $errors[] = "Full name is required.";
    }
    
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    
    if (empty($errors)) {
        try {
            // Create admin user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO admins (username, email, full_name, password, role, status, created_at) VALUES (?, ?, ?, ?, 'super_admin', 'active', NOW())");
            $stmt->execute([$username, $email, $full_name, $hashed_password]);
            
            $success_message = "Admin user created successfully! You can now <a href='login.php'>login</a> with your credentials.";
            
            // Log the setup
            $admin_id = $conn->lastInsertId();
            $stmt = $conn->prepare("INSERT INTO activity_logs (admin_id, action, details, ip_address, created_at) VALUES (?, 'setup', 'Initial admin user created', ?, NOW())");
            $stmt->execute([$admin_id, $_SERVER['REMOTE_ADDR']]);
            
        } catch (Exception $e) {
            $error_message = "Error creating admin user: " . $e->getMessage();
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Setup - AluMaster</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-login-page">
    <div class="login-container">
        <div class="login-background"></div>
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <img src="../assets/images/logo.png" alt="AluMaster" class="logo-image">
                </div>
                <h1 class="login-title">Admin Setup</h1>
                <p class="login-subtitle">Create your first admin user to get started.</p>
            </div>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <div class="alert-icon">
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="alert-content">
                        <?php echo $success_message; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error">
                    <div class="alert-icon">
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="alert-content">
                        <?php echo $error_message; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($success_message)): ?>
            <form class="login-form" method="POST" action="">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-input" required 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           placeholder="admin">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           placeholder="admin@alumastergh.com">
                </div>

                <div class="form-group">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-input" required 
                           value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                           placeholder="Administrator">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" class="form-input" required
                               placeholder="Minimum 6 characters">
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <svg class="icon-sm password-show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg class="icon-sm password-hide" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" required
                               placeholder="Confirm your password">
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                            <svg class="icon-sm password-show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg class="icon-sm password-hide" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" name="create_admin" class="btn btn-primary btn-lg btn-block">
                    Create Admin User
                </button>
            </form>
            <?php endif; ?>

            <div class="login-footer">
                <p class="login-security-notice">
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    This setup page will be disabled after creating the first admin user
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const showIcon = passwordInput.parentElement.querySelector('.password-show');
            const hideIcon = passwordInput.parentElement.querySelector('.password-hide');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                showIcon.style.display = 'none';
                hideIcon.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                showIcon.style.display = 'block';
                hideIcon.style.display = 'none';
            }
        }

        // Auto-focus username field
        document.addEventListener('DOMContentLoaded', function() {
            const usernameField = document.getElementById('username');
            if (usernameField) {
                usernameField.focus();
            }
        });
    </script>
</body>
</html>