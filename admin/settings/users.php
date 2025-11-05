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

$page_title = 'Admin Users';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Settings', 'url' => '#'],
    ['title' => 'Admin Users']
];

$success_message = '';
$error_message = '';

// Handle URL parameters
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'cannot_edit_self':
            $error_message = "You cannot edit your own account from this page.";
            break;
        case 'user_not_found':
            $error_message = "User not found.";
            break;
        case 'database_error':
            $error_message = "Database error occurred.";
            break;
    }
}

// Handle delete request
if ($_POST && isset($_POST['delete_user'])) {
    $delete_user_id = (int)$_POST['user_id'];
    
    // Prevent deleting own account
    if ($delete_user_id != $current_admin['id']) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            // Check if this is the last super admin
            $stmt = $conn->prepare("SELECT COUNT(*) FROM admins WHERE role = 'super_admin' AND is_active = 1");
            $stmt->execute();
            $super_admin_count = $stmt->fetchColumn();
            
            $stmt = $conn->prepare("SELECT role FROM admins WHERE id = ?");
            $stmt->execute([$delete_user_id]);
            $user_role = $stmt->fetchColumn();
            
            if ($user_role === 'super_admin' && $super_admin_count <= 1) {
                $error_message = "Cannot delete the last active super admin.";
            } else {
                $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
                $stmt->execute([$delete_user_id]);
                $success_message = "Admin user deleted successfully.";
            }
        } catch (Exception $e) {
            $error_message = "Error deleting admin user: " . $e->getMessage();
        }
    }
}

// Get admin users
try {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT id, username, email, first_name, last_name, role, is_active, last_login, created_at FROM admins ORDER BY created_at DESC");
    $stmt->execute();
    $admin_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $admin_users = [];
}

include '../includes/header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">Admin Users</h2>
        <a href="add-user.php" class="btn btn-primary">Add New Admin</a>
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
    
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($admin_users)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No admin users found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($admin_users as $user): ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                                    <div class="user-username text-muted">@<?php echo htmlspecialchars($user['username']); ?></div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $user['role'] === 'super_admin' ? 'status-active' : 'status-draft'; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $user['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['last_login']): ?>
                                    <div class="date-info">
                                        <div class="date-primary"><?php echo date('M j, Y', strtotime($user['last_login'])); ?></div>
                                        <div class="date-secondary"><?php echo date('g:i A', strtotime($user['last_login'])); ?></div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">Never</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="date-info">
                                    <div class="date-primary"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></div>
                                    <div class="date-secondary"><?php echo date('g:i A', strtotime($user['created_at'])); ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($user['id'] != $current_admin['id']): ?>
                                        <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline" title="Edit User">
                                            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <button class="btn btn-sm btn-outline btn-danger" title="Delete User" onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted text-sm">Current User</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.user-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.user-name {
    font-weight: 500;
    color: #374151;
}

.user-username {
    font-size: 12px;
    font-family: monospace;
}

.btn-danger {
    background-color: #dc2626;
    border-color: #dc2626;
    color: white;
}

.btn-danger:hover {
    background-color: #b91c1c;
    border-color: #b91c1c;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}
</style>

<!-- Hidden form for delete functionality -->
<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="delete_user" value="1">
    <input type="hidden" name="user_id" id="deleteUserId">
</form>

<script>
function confirmDelete(userId, username) {
    if (confirm(`Are you sure you want to delete the admin user "${username}"? This action cannot be undone.`)) {
        document.getElementById('deleteUserId').value = userId;
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?php include '../includes/footer.php'; ?>