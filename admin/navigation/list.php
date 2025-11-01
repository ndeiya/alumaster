<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'Navigation Menus';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Navigation', 'url' => 'list.php'],
    ['title' => 'All Menus']
];

// Handle success message from URL
if (isset($_GET['success'])) {
    $success_message = sanitize_input($_GET['success']);
}

// Handle delete
if ($_GET && isset($_GET['delete']) && check_admin_permission('admin')) {
    $menu_id = (int)$_GET['delete'];
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Get menu name for logging
        $stmt = $conn->prepare("SELECT name FROM navigation_menus WHERE id = ?");
        $stmt->execute([$menu_id]);
        $menu_name = $stmt->fetchColumn();
        
        if ($menu_name) {
            // Delete menu (cascade will handle menu items)
            $stmt = $conn->prepare("DELETE FROM navigation_menus WHERE id = ?");
            $stmt->execute([$menu_id]);
            
            log_admin_activity('delete', "Deleted navigation menu: $menu_name", $menu_id);
            $success_message = "Navigation menu deleted successfully.";
        } else {
            $error_message = "Menu not found.";
        }
    } catch (Exception $e) {
        $error_message = "Error deleting menu: " . $e->getMessage();
    }
}

// Get navigation menus
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("
        SELECT nm.*, 
               COUNT(ni.id) as item_count
        FROM navigation_menus nm 
        LEFT JOIN navigation_items ni ON nm.id = ni.menu_id 
        GROUP BY nm.id 
        ORDER BY nm.created_at DESC
    ");
    $stmt->execute();
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $menus = [];
    $error_message = "Error loading navigation menus: " . $e->getMessage();
}

include '../includes/header.php';
?>

<?php if (isset($success_message)): ?>
<div class="alert alert-success">
    <div class="alert-icon">
        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>
    <div class="alert-content"><?php echo htmlspecialchars($success_message); ?></div>
</div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
<div class="alert alert-error">
    <div class="alert-icon">
        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
    <div class="alert-content"><?php echo htmlspecialchars($error_message); ?></div>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Navigation Menus</h1>
        <p class="page-description">Manage your website navigation menus</p>
    </div>
    <div class="page-header-actions">
        <a href="add.php" class="btn btn-primary">
            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Menu
        </a>
    </div>
</div>

<!-- Menus Grid -->
<div class="menus-grid">
    <?php if (!empty($menus)): ?>
        <?php foreach ($menus as $menu): ?>
        <div class="menu-card">
            <div class="menu-card-header">
                <h3 class="menu-card-title"><?php echo htmlspecialchars($menu['name']); ?></h3>
                <div class="menu-card-status">
                    <span class="status-badge status-<?php echo $menu['is_active'] ? 'published' : 'draft'; ?>">
                        <?php echo $menu['is_active'] ? 'Active' : 'Inactive'; ?>
                    </span>
                </div>
            </div>
            
            <div class="menu-card-content">
                <div class="menu-card-meta">
                    <div class="menu-meta-item">
                        <span class="menu-meta-label">Location:</span>
                        <span class="menu-meta-value"><?php echo ucfirst($menu['location']); ?></span>
                    </div>
                    <div class="menu-meta-item">
                        <span class="menu-meta-label">Items:</span>
                        <span class="menu-meta-value"><?php echo $menu['item_count']; ?></span>
                    </div>
                    <div class="menu-meta-item">
                        <span class="menu-meta-label">Slug:</span>
                        <span class="menu-meta-value"><?php echo htmlspecialchars($menu['slug']); ?></span>
                    </div>
                </div>
                
                <?php if (!empty($menu['description'])): ?>
                <p class="menu-card-description"><?php echo htmlspecialchars($menu['description']); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="menu-card-actions">
                <a href="edit.php?id=<?php echo $menu['id']; ?>" class="btn btn-sm btn-primary">
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Menu
                </a>
                
                <?php if (check_admin_permission('admin')): ?>
                <a href="?delete=<?php echo $menu['id']; ?>" class="btn btn-sm btn-danger" 
                   onclick="return confirm('Are you sure you want to delete this menu? All menu items will also be deleted.')">
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
    <div class="empty-state">
        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
        </svg>
        <h3 class="empty-title">No navigation menus found</h3>
        <p class="empty-message">Get started by creating your first navigation menu.</p>
        <div class="empty-actions">
            <a href="add.php" class="btn btn-primary">Add Menu</a>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.menus-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 24px;
    margin-top: 24px;
}

.menu-card {
    background-color: #2d3748;
    border: 1px solid #4a5568;
    border-radius: 8px;
    padding: 20px;
    transition: all 0.2s ease;
}

.menu-card:hover {
    border-color: #718096;
    transform: translateY(-2px);
}

.menu-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.menu-card-title {
    font-size: 18px;
    font-weight: 600;
    color: #e2e8f0;
    margin: 0;
}

.menu-card-content {
    margin-bottom: 20px;
}

.menu-card-meta {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
}

.menu-meta-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.menu-meta-label {
    font-size: 12px;
    color: #a0aec0;
    font-weight: 500;
}

.menu-meta-value {
    font-size: 12px;
    color: #e2e8f0;
    font-weight: 500;
}

.menu-card-description {
    font-size: 14px;
    color: #cbd5e0;
    line-height: 1.4;
    margin: 0;
}

.menu-card-actions {
    display: flex;
    gap: 8px;
}

.btn-danger {
    background-color: #e53e3e;
    color: white;
    border: 1px solid #e53e3e;
}

.btn-danger:hover {
    background-color: #c53030;
    border-color: #c53030;
}
</style>

<?php include '../includes/footer.php'; ?>