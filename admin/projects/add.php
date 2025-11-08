<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'Add New Project';

// Initialize database
$db = new Database();
$pdo = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $location = trim($_POST['location']);
    $scope = trim($_POST['scope']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $status = $_POST['status'];
    $display_order = (int)$_POST['display_order'];
    
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Project name is required.";
    }
    if (empty($location)) {
        $errors[] = "Location is required.";
    }
    if (empty($scope)) {
        $errors[] = "Scope is required.";
    }
    
    // Handle thumbnail upload
    $thumbnail = '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../assets/images/projects/';
        $file_extension = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = "Thumbnail must be a JPG, PNG, or WEBP image.";
        } else {
            $filename = 'thumb_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_path)) {
                $thumbnail = 'assets/images/projects/' . $filename;
            } else {
                $errors[] = "Failed to upload thumbnail.";
            }
        }
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO projects (name, location, scope, thumbnail, is_featured, status, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $location, $scope, $thumbnail, $is_featured, $status, $display_order]);
            
            $project_id = $pdo->lastInsertId();
            
            // Handle before images
            if (isset($_FILES['before_images'])) {
                $upload_dir = '../../assets/images/projects/';
                foreach ($_FILES['before_images']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['before_images']['error'][$key] === UPLOAD_ERR_OK) {
                        $file_extension = strtolower(pathinfo($_FILES['before_images']['name'][$key], PATHINFO_EXTENSION));
                        $filename = 'before_' . $project_id . '_' . time() . '_' . $key . '.' . $file_extension;
                        $upload_path = $upload_dir . $filename;
                        
                        if (move_uploaded_file($tmp_name, $upload_path)) {
                            $image_path = 'assets/images/projects/' . $filename;
                            $stmt = $pdo->prepare("INSERT INTO project_images (project_id, image_path, image_type, display_order) VALUES (?, ?, 'before', ?)");
                            $stmt->execute([$project_id, $image_path, $key]);
                        }
                    }
                }
            }
            
            // Handle after images
            if (isset($_FILES['after_images'])) {
                $upload_dir = '../../assets/images/projects/';
                foreach ($_FILES['after_images']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['after_images']['error'][$key] === UPLOAD_ERR_OK) {
                        $file_extension = strtolower(pathinfo($_FILES['after_images']['name'][$key], PATHINFO_EXTENSION));
                        $filename = 'after_' . $project_id . '_' . time() . '_' . $key . '.' . $file_extension;
                        $upload_path = $upload_dir . $filename;
                        
                        if (move_uploaded_file($tmp_name, $upload_path)) {
                            $image_path = 'assets/images/projects/' . $filename;
                            $stmt = $pdo->prepare("INSERT INTO project_images (project_id, image_path, image_type, display_order) VALUES (?, ?, 'after', ?)");
                            $stmt->execute([$project_id, $image_path, $key]);
                        }
                    }
                }
            }
            
            header('Location: list.php?success=1');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
?>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <div class="alert-icon">
        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
    <div class="alert-content">
        <strong>Error:</strong>
        <ul style="margin: 0; padding-left: 20px;">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Add New Project</h1>
        <p class="page-description">Create a new project showcase</p>
    </div>
    <div class="page-header-actions">
        <a href="list.php" class="btn btn-secondary">
            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Projects
        </a>
    </div>
</div>

<!-- Project Form -->
<div class="form-card">
    <form method="POST" enctype="multipart/form-data" class="project-form">
        <div class="form-sections">
            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title">Basic Information</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label">Project Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" class="form-input" required 
                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                               placeholder="Enter project name">
                    </div>
                    
                    <div class="form-group">
                        <label for="location" class="form-label">Location <span class="required">*</span></label>
                        <input type="text" id="location" name="location" class="form-input" required 
                               value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>"
                               placeholder="e.g., Kaneshie, Accra">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="scope" class="form-label">Scope Offered <span class="required">*</span></label>
                    <textarea id="scope" name="scope" class="form-textarea" rows="4" required 
                              placeholder="e.g., Alucobond cladding, Spider glass, Curtain wall installation"><?php echo htmlspecialchars($_POST['scope'] ?? ''); ?></textarea>
                    <div class="form-help">Describe the services provided for this project</div>
                </div>
            </div>

            <!-- Images -->
            <div class="form-section">
                <h3 class="form-section-title">Project Images</h3>
                
                <div class="form-group">
                    <label for="thumbnail" class="form-label">Thumbnail Image <span class="required">*</span></label>
                    <input type="file" id="thumbnail" name="thumbnail" class="form-input" accept="image/*" required>
                    <div class="form-help">This will be displayed on the project card (recommended: landscape orientation, min 800x600px)</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="before_images" class="form-label">Before Images</label>
                        <input type="file" id="before_images" name="before_images[]" class="form-input" accept="image/*" multiple>
                        <div class="form-help">Select multiple images showing the project before work</div>
                    </div>

                    <div class="form-group">
                        <label for="after_images" class="form-label">After Images</label>
                        <input type="file" id="after_images" name="after_images[]" class="form-input" accept="image/*" multiple>
                        <div class="form-help">Select multiple images showing the completed project</div>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="form-section">
                <h3 class="form-section-title">Display Settings</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="display_order" class="form-label">Display Order</label>
                        <input type="number" id="display_order" name="display_order" class="form-input" 
                               value="<?php echo (int)($_POST['display_order'] ?? 0); ?>" min="0">
                        <div class="form-help">Lower numbers appear first (0 = highest priority)</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="active" <?php echo (!isset($_POST['status']) || $_POST['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                        <div class="form-help">Inactive projects won't appear on the website</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="is_featured" name="is_featured" class="form-checkbox"
                               <?php echo isset($_POST['is_featured']) ? 'checked' : ''; ?>>
                        <span class="checkbox-text">
                            <strong>Mark as Featured</strong>
                            <small>Displays "MOST RECENT" badge on the project card</small>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Create Project
            </button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
