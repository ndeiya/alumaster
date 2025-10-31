<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'Service Categories';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Services', 'url' => 'list.php'],
    ['title' => 'Categories']
];

$success_message = '';
$error_message = '';

// Handle add category
if ($_POST && isset($_POST['add_category'])) {
    $name = sanitize_input($_POST['name'] ?? '');
    $description = sanitize_input($_POST['description'] ?? '');
    
    if (!empty($name)) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            $slug = generate_slug($name);
            
            $stmt = $conn->prepare("INSERT INTO service_categories (name, slug, description, is_active) VALUES (?, ?, ?, 1)");
            $stmt->execute([$name, $slug, $description]);
            
            $success_message = "Category added successfully!";
            
        } catch (Exception $e) {
            $error_message = "Error adding category: " . $e->getMessage();
        }
    } else {
        $error_message = "Category name is required.";
    }
}

// Get categories
try {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT * FROM service_categories ORDER BY sort_order, name");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categories = [];
}

include '../includes/header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">Service Categories</h2>
        <button class="btn btn-primary" onclick="toggleAddForm()">Add Category</button>
    </div>
    
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-error">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <!-- Add Category Form -->
    <div id="addCategoryForm" class="add-form" style="display: none;">
        <form method="POST" class="admin-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label">Category Name *</label>
                    <input type="text" id="name" name="name" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-textarea" rows="3"></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                <button type="button" class="btn btn-secondary" onclick="toggleAddForm()">Cancel</button>
            </div>
        </form>
    </div>
    
    <!-- Categories List -->
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Services Count</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="5" class="text-center">No categories found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td><?php echo htmlspecialchars($category['description'] ?: '-'); ?></td>
                            <td>
                                <?php
                                try {
                                    $stmt = $conn->prepare("SELECT COUNT(*) FROM services WHERE category_id = ?");
                                    $stmt->execute([$category['id']]);
                                    echo $stmt->fetchColumn();
                                } catch (Exception $e) {
                                    echo '0';
                                }
                                ?>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $category['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $category['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline">Edit</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleAddForm() {
    const form = document.getElementById('addCategoryForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>

<?php include '../includes/footer.php'; ?>