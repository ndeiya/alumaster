<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

// Check if user has admin permissions
if (!check_admin_permission('admin')) {
    header('Location: ../index.php');
    exit;
}

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$user_id) {
    header('Location: users.php');
    exit;
}

// Prevent editing own account from this page
if ($user_id == $current_admin['id']) {
    header('Location: users.php?error=cannot_edit_self');
    exit;
}

$page_title = 'Edit Admin User';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Settings', 'url' => '#'],
    ['title' => 'Admin Users', 'url' => 'users.php'],
    ['title' => 'Edit User']
];

$success_message = '';
$error_message = '';
$user_data = null;

// Get user data
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user_data) {
        header('Location: users.php?error=user_not_found');
        exit;
    }
} catch (Exception $e) {
    header('Location: users.php?error=database_error');
    exit;
}

if ($_POST && isset($_POST['update_admin'])) {
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $first_name = sanitize_input($_POST['first_name'] ?? '');
    $last_name = sanitize_input($_POST['last_name'] ?? '');
    $role = sanitize_input($_POST['role'] ?? 'admin');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
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
    
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    }
    
    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    }
    
    if (!in_array($role, ['admin', 'super_admin'])) {
        $errors[] = "Invalid role selected.";
    }
    
    // Password validation (only if password is provided)
    if (!empty($password)) {
        if (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters long.";
        }
        
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }
    }
    
    if (empty($errors)) {
        try {
            // Check if username or email already exists (excluding current user)
            $stmt = $conn->prepare("SELECT COUNT(*) FROM admins WHERE (username = ? OR email = ?) AND id != ?");
            $stmt->execute([$username, $email, $user_id]);
            $existing_count = $stmt->fetchColumn();
            
            if ($existing_count > 0) {
                $errors[] = "Username or email already exists.";
            } else {
                // Update admin user
                if (!empty($password)) {
                    // Update with password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE admins SET username = ?, email = ?, first_name = ?, last_name = ?, password = ?, role = ?, is_active = ? WHERE id = ?");
                    $stmt->execute([$username, $email, $first_name, $last_name, $hashed_password, $role, $is_active, $user_id]);
                } else {
                    // Update without password
                    $stmt = $conn->prepare("UPDATE admins SET username = ?, email = ?, first_name = ?, last_name = ?, role = ?, is_active = ? WHERE id = ?");
                    $stmt->execute([$username, $email, $first_name, $last_name, $role, $is_active, $user_id]);
                }
                
                $success_message = "Admin user updated successfully!";
                
                // Refresh user data
                $stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
                $stmt->execute([$user_id]);
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            $error_message = "Error updating admin user: " . $e->getMessage();
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
        <h2 class="card-title">Edit Admin User</h2>
        <a href="users.php" class="btn btn-outline">Back to Users</a>
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
                       value="<?php echo htmlspecialchars($user_data['username']); ?>"
                       placeholder="Enter username">
                <div class="form-help">Must be at least 3 characters long</div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address *</label>
                <input type="email" id="email" name="email" class="form-input" required 
                       value="<?php echo htmlspecialchars($user_data['email']); ?>"
                       placeholder="user@example.com">
            </div>

            <div class="form-group">
                <label for="first_name" class="form-label">First Name *</label>
                <input type="text" id="first_name" name="first_name" class="form-input" required 
                       value="<?php echo htmlspecialchars($user_data['first_name']); ?>"
                       placeholder="Enter first name">
            </div>

            <div class="form-group">
                <label for="last_name" class="form-label">Last Name *</label>
                <input type="text" id="last_name" name="last_name" class="form-input" required 
                       value="<?php echo htmlspecialchars($user_data['last_name']); ?>"
                       placeholder="Enter last name">
            </div>

            <div class="form-group">
                <label for="role" class="form-label">Role *</label>
                <select id="role" name="role" class="form-input" required>
                    <option value="admin" <?php echo $user_data['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="super_admin" <?php echo $user_data['role'] === 'super_admin' ? 'selected' : ''; ?>>Super Admin</option>
                </select>
                <div class="form-help">Super Admin has full access to all features</div>
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" <?php echo $user_data['is_active'] ? 'checked' : ''; ?>>
                        <span class="checkbox-text">Active User</span>
                    </label>
                </div>
                <div class="form-help">Inactive users cannot log in</div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">New Password</label>
                <div class="password-input-wrapper">
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="Leave blank to keep current password">
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
                <div class="form-help">Leave blank to keep current password. Must be at least 6 characters if changing.</div>
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

        <div class="user-info-section">
            <h3>Account Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>User ID</label>
                    <span><?php echo $user_data['id']; ?></span>
                </div>
                <div class="info-item">
                    <label>Created</label>
                    <span><?php echo date('M j, Y g:i A', strtotime($user_data['created_at'])); ?></span>
                </div>
                <div class="info-item">
                    <label>Last Login</label>
                    <span>
                        <?php if ($user_data['last_login']): ?>
                            <?php echo date('M j, Y g:i A', strtotime($user_data['last_login'])); ?>
                        <?php else: ?>
                            Never
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-item">
                    <label>Login Attempts</label>
                    <span><?php echo $user_data['login_attempts'] ?? 0; ?></span>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="update_admin" class="btn btn-primary">
                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Update Admin User
            </button>
            <a href="users.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<style>
.user-info-section {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

.user-info-section h3 {
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