<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'All Pages';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Pages', 'url' => 'list.php'],
    ['title' => 'All Pages']
];

// Handle success message from URL
if (isset($_GET['success'])) {
    $success_message = sanitize_input($_GET['success']);
}

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
                $stmt = $conn->prepare("DELETE FROM pages WHERE id IN ($placeholders) AND is_homepage = 0");
                $stmt->execute($selected_ids);
                
                log_admin_activity('bulk_delete', 'Deleted ' . count($selected_ids) . ' pages');
                $success_message = count($selected_ids) . " pages deleted successfully.";
            } elseif ($action === 'publish') {
                $placeholders = str_repeat('?,', count($selected_ids) - 1) . '?';
                $stmt = $conn->prepare("UPDATE pages SET status = 'published' WHERE id IN ($placeholders)");
                $stmt->execute($selected_ids);
                
                log_admin_activity('bulk_publish', 'Published ' . count($selected_ids) . ' pages');
                $success_message = count($selected_ids) . " pages published successfully.";
            } elseif ($action === 'draft') {
                $placeholders = str_repeat('?,', count($selected_ids) - 1) . '?';
                $stmt = $conn->prepare("UPDATE pages SET status = 'draft' WHERE id IN ($placeholders)");
                $stmt->execute($selected_ids);
                
                log_admin_activity('bulk_draft', 'Set ' . count($selected_ids) . ' pages to draft');
                $success_message = count($selected_ids) . " pages set to draft successfully.";
            }
        } catch (Exception $e) {
            $error_message = "Error performing bulk action: " . $e->getMessage();
        }
    }
}

// Handle individual delete
if ($_GET && isset($_GET['delete']) && check_admin_permission('admin')) {
    $page_id = (int)$_GET['delete'];
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Check if it's homepage
        $stmt = $conn->prepare("SELECT title, is_homepage FROM pages WHERE id = ?");
        $stmt->execute([$page_id]);
        $page_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($page_data && !$page_data['is_homepage']) {
            $stmt = $conn->prepare("DELETE FROM pages WHERE id = ?");
            $stmt->execute([$page_id]);
            
            log_admin_activity('delete', "Deleted page: " . $page_data['title'], $page_id);
            $success_message = "Page deleted successfully.";
        } else {
            $error_message = "Cannot delete homepage or page not found.";
        }
    } catch (Exception $e) {
        $error_message = "Error deleting page: " . $e->getMessage();
    }
}

// Get pages with pagination
$page = (int)($_GET['page'] ?? 1);
$per_page = 20;
$offset = ($page - 1) * $per_page;

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Build query conditions
    $conditions = ["1=1"];
    $params = [];
    
    if (!empty($search)) {
        $conditions[] = "(title LIKE ? OR content LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if (!empty($status_filter)) {
        $conditions[] = "status = ?";
        $params[] = $status_filter;
    }
    
    $where_clause = implode(' AND ', $conditions);
    
    // Get total count
    $count_sql = "SELECT COUNT(*) FROM pages WHERE $where_clause";
    $stmt = $conn->prepare($count_sql);
    $stmt->execute($params);
    $total_pages_count = $stmt->fetchColumn();
    
    // Get pages
    $sql = "SELECT * FROM pages WHERE $where_clause ORDER BY is_homepage DESC, sort_order ASC, created_at DESC LIMIT $per_page OFFSET $offset";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total_pages = ceil($total_pages_count / $per_page);
    
} catch (Exception $e) {
    $pages = [];
    $total_pages_count = 0;
    $total_pages = 0;
    $error_message = "Error loading pages: " . $e->getMessage();
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
        <h1 class="page-title">All Pages</h1>
        <p class="page-description">Manage your website pages and content</p>
    </div>
    <div class="page-header-actions">
        <a href="add.php" class="btn btn-primary">
            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Page
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
                       placeholder="Search pages..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            
            <div class="filter-group">
                <label for="status" class="filter-label">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="published" <?php echo $status_filter === 'published' ? 'selected' : ''; ?>>Published</option>
                    <option value="draft" <?php echo $status_filter === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="private" <?php echo $status_filter === 'private' ? 'selected' : ''; ?>>Private</option>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="list.php" class="btn btn-outline">Clear</a>
            </div>
        </div>
    </form>
</div>

<!-- Pages Table -->
<div class="table-card">
    <div class="table-header">
        <div class="table-title">
            Pages (<?php echo $total_pages_count; ?>)
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
    
    <?php if (!empty($pages)): ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="table-checkbox">
                        <input type="checkbox" class="select-all-checkbox">
                    </th>
                    <th>Page</th>
                    <th>Status</th>
                    <th>Type</th>
                    <th>Views</th>
                    <th>Created</th>
                    <th class="table-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $page_item): ?>
                <tr>
                    <td class="table-checkbox">
                        <?php if (!$page_item['is_homepage']): ?>
                        <input type="checkbox" name="selected_items[]" value="<?php echo $page_item['id']; ?>" class="row-checkbox">
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="page-info">
                            <div class="page-name">
                                <a href="edit.php?id=<?php echo $page_item['id']; ?>" class="page-link">
                                    <?php echo htmlspecialchars($page_item['title']); ?>
                                    <?php if ($page_item['is_homepage']): ?>
                                        <span class="homepage-badge">Homepage</span>
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="page-slug">/<?php echo htmlspecialchars($page_item['slug']); ?></div>
                        </div>
                    </td>
                    <td>
                        <span class="status-badge status-<?php echo $page_item['status']; ?>">
                            <?php echo ucfirst($page_item['status']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="type-badge">
                            <?php if ($page_item['is_homepage']): ?>
                                Homepage
                            <?php elseif ($page_item['parent_id']): ?>
                                Sub-page
                            <?php else: ?>
                                Page
                            <?php endif; ?>
                        </span>
                    </td>
                    <td>
                        <span class="views-count"><?php echo number_format($page_item['views']); ?></span>
                    </td>
                    <td>
                        <div class="date-info">
                            <div class="date-primary"><?php echo date('M j, Y', strtotime($page_item['created_at'])); ?></div>
                            <div class="date-secondary"><?php echo date('g:i A', strtotime($page_item['created_at'])); ?></div>
                        </div>
                    </td>
                    <td class="table-actions">
                        <div class="action-buttons">
                            <a href="../../<?php echo $page_item['slug']; ?>.php" target="_blank" class="btn-action" title="View">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="edit.php?id=<?php echo $page_item['id']; ?>" class="btn-action" title="Edit">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <?php if (check_admin_permission('admin') && !$page_item['is_homepage']): ?>
                            <a href="?delete=<?php echo $page_item['id']; ?>" class="btn-action btn-action-danger" 
                               title="Delete" onclick="return confirm('Are you sure you want to delete this page?')">
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
            Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $per_page, $total_pages_count); ?> 
            of <?php echo $total_pages_count; ?> pages
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="empty-title">No pages found</h3>
        <p class="empty-message">
            <?php if (!empty($search) || !empty($status_filter)): ?>
                No pages match your current filters. Try adjusting your search criteria.
            <?php else: ?>
                Get started by adding your first page.
            <?php endif; ?>
        </p>
        <div class="empty-actions">
            <?php if (!empty($search) || !empty($status_filter)): ?>
                <a href="list.php" class="btn btn-secondary">Clear Filters</a>
            <?php endif; ?>
            <a href="add.php" class="btn btn-primary">Add Page</a>
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