<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'My Profile';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Settings', 'url' => '#'],
    ['title' => 'My Profile']
];

$success_message = '';
$error_message = '';

if ($_POST && isset($_POST['update_profile'])) {
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $first_name = sanitize_input($_POST['first_name'] ?? '');
    $last_name = sanitize_input($_POST['last_name'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    $errors = [];
    
    if (empty($username) || strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters long.";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required.";
    }
    
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    }
    
    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    }
    
    // Password validation (only if changing password)
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors[] = "Current password is required to change password.";
        }
        
        if (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters long.";
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match.";
        }
    }
    
    if (empty($errors)) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            // Verify current password if changing password
            if (!empty($new_password)) {
                $stmt = $conn->prepare("SELECT password FROM admins WHERE id = ?");
                $stmt->execute([$current_admin['id']]);
                $stored_password = $stmt->fetchColumn();
                
                if (!password_verify($current_password, $stored_password)) {
                    $errors[] = "Current password is incorrect.";
                }
            }
            
            if (empty($errors)) {
                // Check if username or email already exists (excluding current user)
                $stmt = $conn->prepare("SELECT COUNT(*) FROM admins WHERE (username = ? OR email = ?) AND id != ?");
                $stmt->execute([$username, $email, $current_admin['id']]);
                $existing_count = $stmt->fetchColumn();
                
                if ($existing_count > 0) {
                    $errors[] = "Username or email already exists.";
                } else {
                    // Update profile
                    if (!empty($new_password)) {
                        // Update with password
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt = $conn->prepare("UPDATE admins SET username = ?, email = ?, first_name = ?, last_name = ?, password = ? WHERE id = ?");
                        $stmt->execute([$username, $email, $first_name, $last_name, $hashed_password, $current_admin['id']]);
                    } else {
                        // Update without password
                        $stmt = $conn->prepare("UPDATE admins SET username = ?, email = ?, first_name = ?, last_name = ? WHERE id = ?");
                        $stmt->execute([$username, $email, $first_name, $last_name, $current_admin['id']]);
                    }
                    
                    $success_message = "Profile updated successfully!";
                    
                    // Update session data
                    $_SESSION['admin_user']['username'] = $username;
                    $_SESSION['admin_user']['email'] = $email;
                    $_SESSION['admin_user']['first_name'] = $first_name;
                    $_SESSION['admin_user']['last_name'] = $last_name;
                    $current_admin = $_SESSION['admin_user'];
                }
            }
        } catch (Exception $e) {
            $error_message = "Error updating profile: " . $e->getMessage();
        }
    }
    
    if (!empty($errors)) {
        $error_message = implode('<br>', $errors);
    }
}

include '../includes/header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">My Profile</h2>
        <a href="../index.php" class="btn btn-outline">Back to Dashboard</a>
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

    <form class="admin-form" method="POST" action="">
        <div class="form-grid">
            <div class="form-group">
                <label for="username" class="form-label">Username *</label>
                <input type="text" id="username" name="username" class="form-input" required 
                       value="<?php echo htmlspecialchars($current_admin['username']); ?>"
                       placeholder="Enter username">
                <div class="form-help">Must be at least 3 characters long</div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address *</label>
                <input type="email" id="email" name="email" class="form-input" required 
                       value="<?php echo htmlspecialchars($current_admin['email']); ?>"
                       placeholder="user@example.com">
            </div>

            <div class="form-group">
                <label for="first_name" class="form-label">First Name *</label>
                <input type="text" id="first_name" name="first_name" class="form-input" required 
                       value="<?php echo htmlspecialchars($current_admin['first_name']); ?>"
                       placeholder="Enter first name">
            </div>

            <div class="form-group">
                <label for="last_name" class="form-label">Last Name *</label>
                <input type="text" id="last_name" name="last_name" class="form-input" required 
                       value="<?php echo htmlspecialchars($current_admin['last_name']); ?>"
                       placeholder="Enter last name">
            </div>
        </div>

        <div class="password-section">
            <h3>Change Password</h3>
            <p class="text-muted">Leave password fields blank to keep your current password.</p>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="current_password" class="form-label">Current Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="current_password" name="current_password" class="form-input"
                               placeholder="Enter current password">
                        <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
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
                    <label for="new_password" class="form-label">New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="new_password" name="new_password" class="form-input"
                               placeholder="Enter new password">
                        <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                            <svg class="icon-sm password-show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg class="icon-sm password-hide" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="form-help">Must be at least 6 characters long</div>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input"
                               placeholder="Confirm new password">
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
            </div>
        </div>

        <div class="account-info-section">
            <h3>Account Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>User ID</label>
                    <span><?php echo $current_admin['id']; ?></span>
                </div>
                <div class="info-item">
                    <label>Role</label>
                    <span class="status-badge <?php echo $current_admin['role'] === 'super_admin' ? 'status-active' : 'status-draft'; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $current_admin['role'])); ?>
                    </span>
                </div>
                <div class="info-item">
                    <label>Last Login</label>
                    <span>
                        <?php if ($current_admin['last_login']): ?>
                            <?php echo date('M j, Y g:i A', strtotime($current_admin['last_login'])); ?>
                        <?php else: ?>
                            Never
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-item">
                    <label>Account Created</label>
                    <span><?php echo date('M j, Y g:i A', strtotime($current_admin['created_at'])); ?></span>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="update_profile" class="btn btn-primary">
                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Update Profile
            </button>
            <a href="../index.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<style>
.password-section, .account-info-section {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

.password-section h3, .account-info-section h3 {
    margin-bottom: 1rem;
    color: #374151;
    font-size: 1.1rem;
    font-weight: 600;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-item label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
}

.info-item span {
    font-size: 0.875rem;
    color: #374151;
}
</style>

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

<?php include '../includes/footer.php'; ?>