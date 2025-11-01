<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$menu_id = (int)($_GET['id'] ?? 0);

if (!$menu_id) {
    header('Location: list.php');
    exit;
}

$page_title = 'Edit Navigation Menu';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Navigation', 'url' => 'list.php'],
    ['title' => 'Edit Menu']
];

// Get menu data
$menu = null;
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM navigation_menus WHERE id = ?");
    $stmt->execute([$menu_id]);
    $menu = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$menu) {
        header('Location: list.php');
        exit;
    }
    
    // Get menu items
    $stmt = $conn->prepare("
        SELECT ni.*, p.title as page_title 
        FROM navigation_items ni 
        LEFT JOIN pages p ON ni.page_id = p.id 
        WHERE ni.menu_id = ? 
        ORDER BY ni.sort_order ASC, ni.id ASC
    ");
    $stmt->execute([$menu_id]);
    $menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get available pages for dropdown
    $stmt = $conn->prepare("SELECT id, title, slug FROM pages WHERE status = 'published' ORDER BY title");
    $stmt->execute();
    $available_pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error_message = "Error loading menu: " . $e->getMessage();
}

// Handle menu update
if ($_POST && isset($_POST['update_menu'])) {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $error_message = "Security token mismatch.";
    } else {
        $name = sanitize_input($_POST['name'] ?? '');
        $description = sanitize_input($_POST['description'] ?? '');
        $location = sanitize_input($_POST['location'] ?? 'header');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($name)) {
            $error_message = "Menu name is required.";
        } else {
            try {
                $stmt = $conn->prepare("UPDATE navigation_menus SET name = ?, description = ?, location = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$name, $description, $location, $is_active, $menu_id]);
                
                log_admin_activity('update', "Updated navigation menu: $name", $menu_id);
                $success_message = "Menu updated successfully.";
                
                // Refresh menu data
                $stmt = $conn->prepare("SELECT * FROM navigation_menus WHERE id = ?");
                $stmt->execute([$menu_id]);
                $menu = $stmt->fetch(PDO::FETCH_ASSOC);
                
            } catch (Exception $e) {
                $error_message = "Error updating menu: " . $e->getMessage();
            }
        }
    }
}

// Handle AJAX requests for menu items
if ($_POST && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'add_item':
                $title = sanitize_input($_POST['title'] ?? '');
                $url = sanitize_input($_POST['url'] ?? '');
                $page_id = !empty($_POST['page_id']) ? (int)$_POST['page_id'] : null;
                $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
                $target = sanitize_input($_POST['target'] ?? '_self');
                
                if (empty($title) || empty($url)) {
                    echo json_encode(['success' => false, 'message' => 'Title and URL are required']);
                    exit;
                }
                
                // Get next sort order
                $stmt = $conn->prepare("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM navigation_items WHERE menu_id = ?");
                $stmt->execute([$menu_id]);
                $sort_order = $stmt->fetchColumn();
                
                $stmt = $conn->prepare("INSERT INTO navigation_items (menu_id, parent_id, title, url, target, page_id, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$menu_id, $parent_id, $title, $url, $target, $page_id, $sort_order]);
                
                $item_id = $conn->lastInsertId();
                log_admin_activity('create', "Added menu item: $title", $item_id);
                
                echo json_encode(['success' => true, 'item_id' => $item_id]);
                exit;
                
            case 'update_item':
                $item_id = (int)$_POST['item_id'];
                $title = sanitize_input($_POST['title'] ?? '');
                $url = sanitize_input($_POST['url'] ?? '');
                $target = sanitize_input($_POST['target'] ?? '_self');
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                $stmt = $conn->prepare("UPDATE navigation_items SET title = ?, url = ?, target = ?, is_active = ? WHERE id = ? AND menu_id = ?");
                $stmt->execute([$title, $url, $target, $is_active, $item_id, $menu_id]);
                
                echo json_encode(['success' => true]);
                exit;
                
            case 'delete_item':
                $item_id = (int)$_POST['item_id'];
                
                $stmt = $conn->prepare("DELETE FROM navigation_items WHERE id = ? AND menu_id = ?");
                $stmt->execute([$item_id, $menu_id]);
                
                log_admin_activity('delete', "Deleted menu item", $item_id);
                echo json_encode(['success' => true]);
                exit;
                
            case 'reorder_items':
                $items = json_decode($_POST['items'], true);
                
                foreach ($items as $index => $item_id) {
                    $stmt = $conn->prepare("UPDATE navigation_items SET sort_order = ? WHERE id = ? AND menu_id = ?");
                    $stmt->execute([$index + 1, $item_id, $menu_id]);
                }
                
                echo json_encode(['success' => true]);
                exit;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
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
        <h1 class="page-title">Edit Navigation Menu</h1>
        <p class="page-description">Manage menu settings and items</p>
    </div>
    <div class="page-header-actions">
        <a href="list.php" class="btn btn-secondary">
            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Menus
        </a>
    </div>
</div>

<div class="navigation-editor">
    <!-- Menu Settings -->
    <div class="form-card">
        <h3 class="form-section-title">Menu Settings</h3>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label">Menu Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-input" required 
                           value="<?php echo htmlspecialchars($menu['name']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="location" class="form-label">Location</label>
                    <select id="location" name="location" class="form-select">
                        <option value="header" <?php echo $menu['location'] === 'header' ? 'selected' : ''; ?>>Header</option>
                        <option value="footer" <?php echo $menu['location'] === 'footer' ? 'selected' : ''; ?>>Footer</option>
                        <option value="sidebar" <?php echo $menu['location'] === 'sidebar' ? 'selected' : ''; ?>>Sidebar</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-textarea" rows="3" 
                          placeholder="Optional description of this menu..."><?php echo htmlspecialchars($menu['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <div class="form-checkbox">
                    <input type="checkbox" id="is_active" name="is_active" value="1" 
                           <?php echo $menu['is_active'] ? 'checked' : ''; ?>>
                    <label for="is_active" class="checkbox-label">Active</label>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="update_menu" class="btn btn-primary">
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Menu
                </button>
            </div>
        </form>
    </div>
    
    <!-- Menu Builder -->
    <div class="menu-builder">
        <div class="menu-builder-header">
            <h3 class="menu-builder-title">Menu Items</h3>
            <button type="button" class="add-menu-item-btn" onclick="showAddItemForm()">
                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Item
            </button>
        </div>
        
        <!-- Add Item Form -->
        <div id="addItemForm" class="add-item-form" style="display: none;">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Title <span class="required">*</span></label>
                    <input type="text" id="newItemTitle" class="form-input" placeholder="Menu item title">
                </div>
                
                <div class="form-group">
                    <label class="form-label">URL <span class="required">*</span></label>
                    <input type="text" id="newItemUrl" class="form-input" placeholder="/page-url or https://example.com">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Link to Page</label>
                    <select id="newItemPage" class="form-select" onchange="setPageUrl(this)">
                        <option value="">Select a page (optional)</option>
                        <?php foreach ($available_pages as $page): ?>
                        <option value="<?php echo $page['id']; ?>" data-url="/<?php echo $page['slug']; ?>.php">
                            <?php echo htmlspecialchars($page['title']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Target</label>
                    <select id="newItemTarget" class="form-select">
                        <option value="_self">Same Window</option>
                        <option value="_blank">New Window</option>
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-primary" onclick="addMenuItem()">Add Item</button>
                <button type="button" class="btn btn-secondary" onclick="hideAddItemForm()">Cancel</button>
            </div>
        </div>
        
        <!-- Menu Items List -->
        <div class="menu-items-list sortable" id="menuItemsList">
            <?php if (!empty($menu_items)): ?>
                <?php foreach ($menu_items as $item): ?>
                <div class="nav-item-row" data-item-id="<?php echo $item['id']; ?>" data-title="<?php echo htmlspecialchars($item['title']); ?>" data-url="<?php echo htmlspecialchars($item['url']); ?>" data-target="<?php echo $item['target']; ?>" data-active="<?php echo $item['is_active']; ?>">
                    <div class="nav-item-handle">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                        </svg>
                    </div>
                    
                    <div class="nav-item-content">
                        <div class="nav-item-info">
                            <div class="nav-item-title"><?php echo htmlspecialchars($item['title']); ?></div>
                            <div class="nav-item-url"><?php echo htmlspecialchars($item['url']); ?></div>
                        </div>
                    </div>
                    
                    <div class="nav-item-actions">
                        <button type="button" class="nav-item-toggle <?php echo $item['is_active'] ? 'active' : ''; ?>" 
                                onclick="toggleItem(<?php echo $item['id']; ?>)" title="Toggle Active">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                        
                        <button type="button" class="btn-action" onclick="editItem(<?php echo $item['id']; ?>)" title="Edit">
                            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        
                        <button type="button" class="btn-action btn-action-danger" onclick="deleteItem(<?php echo $item['id']; ?>)" title="Delete">
                            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
            <div class="empty-menu">
                <svg class="empty-menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                <p>No menu items yet. Add your first menu item to get started.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
let editingItemId = null;

// Initialize sortable
document.addEventListener('DOMContentLoaded', function() {
    const sortableList = document.getElementById('menuItemsList');
    if (sortableList && sortableList.children.length > 0) {
        new Sortable(sortableList, {
            handle: '.nav-item-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function(evt) {
                updateItemOrder();
            }
        });
    }
});

// Add item form functions
function showAddItemForm() {
    hideEditItemForm();
    document.getElementById('addItemForm').style.display = 'block';
    document.getElementById('newItemTitle').focus();
}

function hideAddItemForm() {
    document.getElementById('addItemForm').style.display = 'none';
    clearAddItemForm();
}

function clearAddItemForm() {
    document.getElementById('newItemTitle').value = '';
    document.getElementById('newItemUrl').value = '';
    document.getElementById('newItemPage').value = '';
    document.getElementById('newItemTarget').value = '_self';
}

function setPageUrl(select) {
    if (select.value) {
        const option = select.options[select.selectedIndex];
        document.getElementById('newItemUrl').value = option.dataset.url;
    }
}

// Menu item functions
function addMenuItem() {
    const title = document.getElementById('newItemTitle').value.trim();
    const url = document.getElementById('newItemUrl').value.trim();
    const pageId = document.getElementById('newItemPage').value;
    const target = document.getElementById('newItemTarget').value;
    
    if (!title || !url) {
        alert('Title and URL are required');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'add_item');
    formData.append('title', title);
    formData.append('url', url);
    formData.append('page_id', pageId);
    formData.append('target', target);
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function toggleItem(itemId) {
    const button = event.target.closest('.nav-item-toggle');
    const isActive = button.classList.contains('active');
    
    const formData = new FormData();
    formData.append('action', 'update_item');
    formData.append('item_id', itemId);
    formData.append('title', button.closest('.nav-item-row').dataset.title);
    formData.append('url', button.closest('.nav-item-row').dataset.url);
    formData.append('target', button.closest('.nav-item-row').dataset.target);
    if (!isActive) {
        formData.append('is_active', '1');
    }
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.classList.toggle('active');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function editItem(itemId) {
    const row = document.querySelector(`[data-item-id="${itemId}"]`);
    const title = row.dataset.title;
    const url = row.dataset.url;
    const target = row.dataset.target;
    
    editingItemId = itemId;
    
    // Create edit form if it doesn't exist
    let editForm = document.getElementById('editItemForm');
    if (!editForm) {
        editForm = createEditForm();
        document.querySelector('.menu-builder').appendChild(editForm);
    }
    
    // Populate form
    document.getElementById('editItemTitle').value = title;
    document.getElementById('editItemUrl').value = url;
    document.getElementById('editItemTarget').value = target;
    
    // Show edit form
    hideAddItemForm();
    editForm.style.display = 'block';
    document.getElementById('editItemTitle').focus();
}

function createEditForm() {
    const form = document.createElement('div');
    form.id = 'editItemForm';
    form.className = 'add-item-form';
    form.style.display = 'none';
    form.innerHTML = `
        <h4 style="color: #e2e8f0; margin-bottom: 16px;">Edit Menu Item</h4>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Title <span class="required">*</span></label>
                <input type="text" id="editItemTitle" class="form-input" placeholder="Menu item title">
            </div>
            
            <div class="form-group">
                <label class="form-label">URL <span class="required">*</span></label>
                <input type="text" id="editItemUrl" class="form-input" placeholder="/page-url or https://example.com">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Target</label>
                <select id="editItemTarget" class="form-select">
                    <option value="_self">Same Window</option>
                    <option value="_blank">New Window</option>
                </select>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="button" class="btn btn-primary" onclick="updateMenuItem()">Update Item</button>
            <button type="button" class="btn btn-secondary" onclick="hideEditItemForm()">Cancel</button>
        </div>
    `;
    return form;
}

function hideEditItemForm() {
    const editForm = document.getElementById('editItemForm');
    if (editForm) {
        editForm.style.display = 'none';
    }
    editingItemId = null;
}

function updateMenuItem() {
    if (!editingItemId) return;
    
    const title = document.getElementById('editItemTitle').value.trim();
    const url = document.getElementById('editItemUrl').value.trim();
    const target = document.getElementById('editItemTarget').value;
    
    if (!title || !url) {
        alert('Title and URL are required');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'update_item');
    formData.append('item_id', editingItemId);
    formData.append('title', title);
    formData.append('url', url);
    formData.append('target', target);
    formData.append('is_active', '1'); // Keep active state
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function deleteItem(itemId) {
    if (!confirm('Are you sure you want to delete this menu item?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete_item');
    formData.append('item_id', itemId);
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function updateItemOrder() {
    const items = Array.from(document.querySelectorAll('.nav-item-row')).map(row => row.dataset.itemId);
    
    const formData = new FormData();
    formData.append('action', 'reorder_items');
    formData.append('items', JSON.stringify(items));
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Failed to update order:', data.message);
            location.reload(); // Reload to restore original order
        }
    })
    .catch(error => {
        console.error('Error:', error);
        location.reload();
    });
}
</script>

<style>
.navigation-editor {
    display: grid;
    gap: 24px;
}

.add-item-form {
    background-color: #1a202c;
    border: 1px solid #4a5568;
    border-radius: 6px;
    padding: 16px;
    margin-bottom: 20px;
}
</style>

<?php include '../includes/footer.php'; ?>