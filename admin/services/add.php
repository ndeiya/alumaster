<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'Add New Service';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Services', 'url' => 'list.php'],
    ['title' => 'Add New Service']
];

$success_message = '';
$error_message = '';

if ($_POST && isset($_POST['add_service'])) {
    $name = sanitize_input($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $short_description = sanitize_input($_POST['short_description'] ?? '');
    $description = $_POST['description'] ?? '';
    $technical_specs = $_POST['technical_specs'] ?? '';
    
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Service name is required.";
    }
    
    if ($category_id <= 0) {
        $errors[] = "Please select a category.";
    }
    
    if (empty($short_description)) {
        $errors[] = "Short description is required.";
    }
    
    if (empty($errors)) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            $slug = generate_slug($name);
            
            $stmt = $conn->prepare("INSERT INTO services (category_id, name, slug, short_description, description, technical_specs, status) VALUES (?, ?, ?, ?, ?, ?, 'draft')");
            $stmt->execute([$category_id, $name, $slug, $short_description, $description, $technical_specs]);
            
            $success_message = "Service added successfully!";
            
        } catch (Exception $e) {
            $error_message = "Error adding service: " . $e->getMessage();
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

// Get categories
try {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT * FROM service_categories WHERE is_active = 1 ORDER BY sort_order, name");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categories = [];
}

include '../includes/header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">Add New Service</h2>
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
    
    <form method="POST" class="admin-form">
        <div class="form-row">
            <div class="form-group">
                <label for="name" class="form-label">Service Name *</label>
                <input type="text" id="name" name="name" class="form-input" required 
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="category_id" class="form-label">Category *</label>
                <select id="category_id" name="category_id" class="form-select" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" 
                                <?php echo (($_POST['category_id'] ?? '') == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="short_description" class="form-label">Short Description *</label>
            <textarea id="short_description" name="short_description" class="form-textarea" rows="3" required><?php echo htmlspecialchars($_POST['short_description'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="description" class="form-label">Full Description</label>
            <textarea id="description" name="description" class="form-textarea" rows="8"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="technical_specs" class="form-label">Technical Specifications</label>
            <textarea id="technical_specs" name="technical_specs" class="form-textarea" rows="6"><?php echo htmlspecialchars($_POST['technical_specs'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" name="add_service" class="btn btn-primary">Add Service</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>