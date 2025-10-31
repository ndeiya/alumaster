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
        <a href="../setup.php" class="btn btn-primary">Add New Admin</a>
    </div>
    
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
                                        <button class="btn btn-sm btn-outline" title="Edit User">
                                            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
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
</style>

<?php include '../includes/footer.php'; ?>