<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error_message = '';
$login_attempts = $_SESSION['login_attempts'] ?? 0;
$last_attempt_time = $_SESSION['last_attempt_time'] ?? 0;

// Check for logout or session expiry messages
if (isset($_GET['logged_out'])) {
    $success_message = "You have been logged out successfully.";
} elseif (isset($_GET['expired'])) {
    $error_message = "Your session has expired. Please log in again.";
}

// Check if user is temporarily locked out (5 failed attempts, 15 minute lockout)
$lockout_time = 15 * 60; // 15 minutes
if ($login_attempts >= 5 && (time() - $last_attempt_time) < $lockout_time) {
    $remaining_time = $lockout_time - (time() - $last_attempt_time);
    $error_message = "Too many failed attempts. Please try again in " . ceil($remaining_time / 60) . " minutes.";
}

if ($_POST && isset($_POST['login']) && empty($error_message)) {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = "Please enter both username and password.";
    } else {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND status = 'active'");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Successful login
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_role'] = $admin['role'];
                $_SESSION['admin_name'] = $admin['full_name'];
                
                // Reset login attempts
                unset($_SESSION['login_attempts']);
                unset($_SESSION['last_attempt_time']);
                
                // Update last login
                $stmt = $conn->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
                $stmt->execute([$admin['id']]);
                
                // Log successful login
                $stmt = $conn->prepare("INSERT INTO activity_logs (admin_id, action, details, ip_address, created_at) VALUES (?, 'login', 'Successful login', ?, NOW())");
                $stmt->execute([$admin['id'], $_SERVER['REMOTE_ADDR']]);
                
                // Redirect to intended page or dashboard
                $redirect_to = $_SESSION['redirect_after_login'] ?? 'index.php';
                unset($_SESSION['redirect_after_login']);
                
                header("Location: $redirect_to");
                exit;
            } else {
                // Failed login
                $_SESSION['login_attempts'] = $login_attempts + 1;
                $_SESSION['last_attempt_time'] = time();
                
                // Log failed login attempt
                $stmt = $conn->prepare("INSERT INTO activity_logs (admin_id, action, details, ip_address, created_at) VALUES (NULL, 'failed_login', ?, ?, NOW())");
                $stmt->execute(["Failed login attempt for username: $username", $_SERVER['REMOTE_ADDR']]);
                
                $error_message = "Invalid username or password.";
            }
        } catch (Exception $e) {
            $error_message = "Login system temporarily unavailable. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AluMaster</title>
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
                <h1 class="login-title">Admin Login</h1>
                <p class="login-subtitle">Welcome back! Please sign in to your account.</p>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <div class="alert-icon">
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="alert-content">
                        <?php echo htmlspecialchars($success_message); ?>
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
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                </div>
            <?php endif; ?>

            <form class="login-form" method="POST" action="">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-input" required 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           <?php echo !empty($error_message) && $login_attempts >= 5 ? 'disabled' : ''; ?>>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" class="form-input" required
                               <?php echo !empty($error_message) && $login_attempts >= 5 ? 'disabled' : ''; ?>>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
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
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember_me" class="checkbox-input">
                        <span class="checkbox-custom"></span>
                        <span class="checkbox-text">Remember me</span>
                    </label>
                </div>

                <button type="submit" name="login" class="btn btn-primary btn-lg btn-block"
                        <?php echo !empty($error_message) && $login_attempts >= 5 ? 'disabled' : ''; ?>>
                    Sign In
                </button>
            </form>

            <div class="login-footer">
                <p class="login-security-notice">
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Your session is secured with SSL encryption
                </p>
                <?php if ($login_attempts > 0 && $login_attempts < 5): ?>
                    <p class="login-attempts-warning">
                        <?php echo (5 - $login_attempts); ?> attempts remaining before temporary lockout
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const showIcon = document.querySelector('.password-show');
            const hideIcon = document.querySelector('.password-hide');
            
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
            if (usernameField && !usernameField.disabled) {
                usernameField.focus();
            }
        });
    </script>
</body>
</html>