<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'Add Navigation Menu';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Navigation', 'url' => 'list.php'],
    ['title' => 'Add Menu']
];

// Handle form submission
if ($_POST && isset($_POST['add_menu'])) {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $error_message = "Security token mismatch.";
    } else {
        $name = sanitize_input($_POST['name'] ?? '');
        $slug = sanitize_input($_POST['slug'] ?? '');
        $description = sanitize_input($_POST['description'] ?? '');
        $location = sanitize_input($_POST['location'] ?? 'header');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Validation
        $errors = [];
        if (empty($name)) $errors[] = "Menu name is required.";
        if (empty($slug)) $errors[] = "Slug is required.";
        
        // Check if slug is unique
        if (!empty($slug)) {
            try {
                $db = new Database();
                $conn = $db->getConnection();
                $stmt = $conn->prepare("SELECT id FROM navigation_menus WHERE slug = ?");
                $stmt->execute([$slug]);
                if ($stmt->fetchColumn()) {
                    $errors[] = "Slug already exists. Please choose a different one.";
                }
            } catch (Exception $e) {
                $errors[] = "Error checking slug uniqueness.";
            }
        }
        
        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("INSERT INTO navigation_menus (name, slug, description, location, is_active) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $slug, $description, $location, $is_active]);
                
                $menu_id = $conn->lastInsertId();
                log_admin_activity('create', "Created navigation menu: $name", $menu_id);
                
                header('Location: edit.php?id=' . $menu_id . '&success=Menu created successfully');
                exit;
                
            } catch (Exception $e) {
                $error_message = "Error adding menu: " . $e->getMessage();
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
    }
}

include '../includes/header.php';
?>

<?php if (isset($error_message)): ?>
<div class="alert alert-error">
    <div class="alert-icon">
        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
    <div class="alert-content"><?php echo $error_message; ?></div>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Add Navigation Menu</h1>
        <p class="page-description">Create a new navigation menu for your website</p>
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

<!-- Menu Form -->
<div class="form-card">
    <form method="POST" action="" class="menu-form">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        
        <div class="form-sections">
            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title">Menu Information</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label">Menu Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" class="form-input" required 
                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                               onkeyup="generateSlug(this.value)">
                    </div>
                    
                    <div class="form-group">
                        <label for="slug" class="form-label">Slug <span class="required">*</span></label>
                        <input type="text" id="slug" name="slug" class="form-input" required 
                               value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>">
                        <div class="form-help">URL-friendly identifier for this menu</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-textarea" rows="3" 
                              placeholder="Optional description of this menu..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <!-- Menu Settings -->
            <div class="form-section">
                <h3 class="form-section-title">Menu Settings</h3>
                
                <div class="form-group">
                    <label for="location" class="form-label">Location</label>
                    <select id="location" name="location" class="form-select">
                        <option value="header" <?php echo ($_POST['location'] ?? 'header') === 'header' ? 'selected' : ''; ?>>Header</option>
                        <option value="footer" <?php echo ($_POST['location'] ?? '') === 'footer' ? 'selected' : ''; ?>>Footer</option>
                        <option value="sidebar" <?php echo ($_POST['location'] ?? '') === 'sidebar' ? 'selected' : ''; ?>>Sidebar</option>
                        <option value="mobile" <?php echo ($_POST['location'] ?? '') === 'mobile' ? 'selected' : ''; ?>>Mobile Menu</option>
                    </select>
                    <div class="form-help">Where this menu will be displayed on your website</div>
                </div>
                
                <div class="form-group">
                    <div class="form-checkbox">
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                               <?php echo isset($_POST['is_active']) ? 'checked' : 'checked'; ?>>
                        <label for="is_active" class="checkbox-label">Active</label>
                    </div>
                    <div class="form-help">Inactive menus won't be displayed on the website</div>
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" name="add_menu" class="btn btn-primary">
                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Menu
            </button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<div class="info-card">
    <div class="info-card-header">
        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3>Next Steps</h3>
    </div>
    <div class="info-card-content">
        <p>After creating your menu, you'll be able to:</p>
        <ul>
            <li>Add menu items and organize them</li>
            <li>Link to pages, services, or external URLs</li>
            <li>Reorder items with drag and drop</li>
            <li>Create nested menu structures</li>
        </ul>
    </div>
</div>

<script>
function generateSlug(name) {
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
    document.getElementById('slug').value = slug;
}
</script>

<style>
.info-card {
    background-color: #2d3748;
    border: 1px solid #4a5568;
    border-radius: 8px;
    padding: 20px;
    margin-top: 24px;
}

.info-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    color: #3182ce;
}

.info-card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.info-card-content {
    color: #e2e8f0;
}

.info-card-content p {
    margin-bottom: 12px;
}

.info-card-content ul {
    margin: 0;
    padding-left: 20px;
}

.info-card-content li {
    margin-bottom: 8px;
    line-height: 1.5;
}
</style>

<?php include '../includes/footer.php'; ?>