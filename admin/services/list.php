<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'All Services';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Services', 'url' => 'list.php'],
    ['title' => 'All Services']
];

// Handle bulk actions
if ($_POST && isset($_POST['bulk_action']) && isset($_POST['selected_items'])) {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $error_message = "Security token mismatch.";
    } else {
        $action = $_POST['bulk_action'];
        $selected_ids = $_POST['selected_items'];
        
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            if ($action === 'delete' && check_admin_permission('admin')) {
                $placeholders = str_repeat('?,', count($selected_ids) - 1) . '?';
                $stmt = $conn->prepare("UPDATE services SET status = 'deleted' WHERE id IN ($placeholders)");
                $stmt->execute($selected_ids);
                
                log_admin_activity('bulk_delete', 'Deleted ' . count($selected_ids) . ' services');
                $success_message = count($selected_ids) . " services deleted successfully.";
            } elseif ($action === 'publish') {
                $placeholders = str_repeat('?,', count($selected_ids) - 1) . '?';
                $stmt = $conn->prepare("UPDATE services SET status = 'published' WHERE id IN ($placeholders)");
                $stmt->execute($selected_ids);
                
                log_admin_activity('bulk_publish', 'Published ' . count($selected_ids) . ' services');
                $success_message = count($selected_ids) . " services published successfully.";
            } elseif ($action === 'draft') {
                $placeholders = str_repeat('?,', count($selected_ids) - 1) . '?';
                $stmt = $conn->prepare("UPDATE services SET status = 'draft' WHERE id IN ($placeholders)");
                $stmt->execute($selected_ids);
                
                log_admin_activity('bulk_draft', 'Set ' . count($selected_ids) . ' services to draft');
                $success_message = count($selected_ids) . " services set to draft successfully.";
            }
        } catch (Exception $e) {
            $error_message = "Error performing bulk action: " . $e->getMessage();
        }
    }
}

// Handle individual delete
if ($_GET && isset($_GET['delete']) && check_admin_permission('admin')) {
    $service_id = (int)$_GET['delete'];
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Get service name for logging
        $stmt = $conn->prepare("SELECT name FROM services WHERE id = ?");
        $stmt->execute([$service_id]);
        $service_name = $stmt->fetchColumn();
        
        // Soft delete
        $stmt = $conn->prepare("UPDATE services SET status = 'deleted' WHERE id = ?");
        $stmt->execute([$service_id]);
        
        log_admin_activity('delete', "Deleted service: $service_name", $service_id);
        $success_message = "Service deleted successfully.";
    } catch (Exception $e) {
        $error_message = "Error deleting service: " . $e->getMessage();
    }
}

// Get services with pagination
$page = (int)($_GET['page'] ?? 1);
$per_page = 20;
$offset = ($page - 1) * $per_page;

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$category_filter = $_GET['category'] ?? '';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Build query conditions
    $conditions = ["s.status != 'deleted'"];
    $params = [];
    
    if (!empty($search)) {
        $conditions[] = "(s.name LIKE ? OR s.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if (!empty($status_filter)) {
        $conditions[] = "s.status = ?";
        $params[] = $status_filter;
    }
    
    if (!empty($category_filter)) {
        $conditions[] = "s.category_id = ?";
        $params[] = $category_filter;
    }
    
    $where_clause = implode(' AND ', $conditions);
    
    // Get total count
    $count_sql = "SELECT COUNT(*) FROM services s WHERE $where_clause";
    $stmt = $conn->prepare($count_sql);
    $stmt->execute($params);
    $total_services = $stmt->fetchColumn();
    
    // Get services
    $sql = "SELECT s.*, sc.name as category_name 
            FROM services s 
            LEFT JOIN service_categories sc ON s.category_id = sc.id 
            WHERE $where_clause 
            ORDER BY s.created_at DESC 
            LIMIT $per_page OFFSET $offset";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get categories for filter
    $stmt = $conn->prepare("SELECT * FROM service_categories ORDER BY name");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total_pages = ceil($total_services / $per_page);
    
} catch (Exception $e) {
    $services = [];
    $categories = [];
    $total_services = 0;
    $total_pages = 0;
    $error_message = "Error loading services: " . $e->getMessage();
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
        <h1 class="page-title">All Services</h1>
        <p class="page-description">Manage your service offerings</p>
    </div>
    <div class="page-header-actions">
        <a href="add.php" class="btn btn-primary">
            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Service
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filters-card">
    <form method="GET" class="filters-form">
        <div class="filters-row">
            <div class="filter-group">
                <label for="search" class="filter-label">Search</label>
                <input type="text" id="search" name="search" class="form-input" 
                       placeholder="Search services..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            
            <div class="filter-group">
                <label for="status" class="filter-label">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="published" <?php echo $status_filter === 'published' ? 'selected' : ''; ?>>Published</option>
                    <option value="draft" <?php echo $status_filter === 'draft' ? 'selected' : ''; ?>>Draft</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="category" class="filter-label">Category</label>
                <select id="category" name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" 
                            <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="list.php" class="btn btn-outline">Clear</a>
            </div>
        </div>
    </form>
</div>

<!-- Services Table -->
<div class="table-card">
    <div class="table-header">
        <div class="table-title">
            Services (<?php echo $total_services; ?>)
        </div>
        <div class="bulk-actions" style="display: none;">
            <form method="POST" class="bulk-actions-form">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <select name="bulk_action" class="form-select form-select-sm">
                    <option value="">Bulk Actions</option>
                    <option value="publish">Publish</option>
                    <option value="draft">Set to Draft</option>
                    <?php if (check_admin_permission('admin')): ?>
                    <option value="delete">Delete</option>
                    <?php endif; ?>
                </select>
                <button type="submit" class="btn btn-sm btn-secondary" 
                        onclick="return confirm('Are you sure you want to perform this action?')">Apply</button>
            </form>
        </div>
    </div>
    
    <?php if (!empty($services)): ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="table-checkbox">
                        <input type="checkbox" class="select-all-checkbox">
                    </th>
                    <th>Service</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="table-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                <tr>
                    <td class="table-checkbox">
                        <input type="checkbox" name="selected_items[]" value="<?php echo $service['id']; ?>" class="row-checkbox">
                    </td>
                    <td>
                        <div class="service-info">
                            <div class="service-name">
                                <a href="edit.php?id=<?php echo $service['id']; ?>" class="service-link">
                                    <?php echo htmlspecialchars($service['name']); ?>
                                </a>
                            </div>
                            <div class="service-slug">/service/<?php echo htmlspecialchars($service['slug']); ?></div>
                        </div>
                    </td>
                    <td>
                        <span class="category-badge">
                            <?php echo htmlspecialchars($service['category_name'] ?: 'Uncategorized'); ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-<?php echo $service['status']; ?>">
                            <?php echo ucfirst($service['status']); ?>
                        </span>
                    </td>
                    <td>
                        <div class="date-info">
                            <div class="date-primary"><?php echo date('M j, Y', strtotime($service['created_at'])); ?></div>
                            <div class="date-secondary"><?php echo date('g:i A', strtotime($service['created_at'])); ?></div>
                        </div>
                    </td>
                    <td class="table-actions">
                        <div class="action-buttons">
                            <a href="../../service-detail.php?service=<?php echo urlencode($service['slug']); ?>" 
                               target="_blank" class="btn-action" title="View">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="edit.php?id=<?php echo $service['id']; ?>" class="btn-action" title="Edit">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <?php if (check_admin_permission('admin')): ?>
                            <a href="?delete=<?php echo $service['id']; ?>" class="btn-action btn-action-danger" 
                               title="Delete" onclick="return confirm('Are you sure you want to delete this service?')">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="table-pagination">
        <div class="pagination-info">
            Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $per_page, $total_services); ?> 
            of <?php echo $total_services; ?> services
        </div>
        <div class="pagination-controls">
            <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&<?php echo http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)); ?>" 
               class="pagination-btn">Previous</a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
            <a href="?page=<?php echo $i; ?>&<?php echo http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)); ?>" 
               class="pagination-btn <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&<?php echo http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)); ?>" 
               class="pagination-btn">Next</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="empty-state">
        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
        <h3 class="empty-title">No services found</h3>
        <p class="empty-message">
            <?php if (!empty($search) || !empty($status_filter) || !empty($category_filter)): ?>
                No services match your current filters. Try adjusting your search criteria.
            <?php else: ?>
                Get started by adding your first service.
            <?php endif; ?>
        </p>
        <div class="empty-actions">
            <?php if (!empty($search) || !empty($status_filter) || !empty($category_filter)): ?>
                <a href="list.php" class="btn btn-secondary">Clear Filters</a>
            <?php endif; ?>
            <a href="add.php" class="btn btn-primary">Add Service</a>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Initialize table functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeTableSelection();
});
</script>

<?php include '../includes/footer.php'; ?>