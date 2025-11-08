<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'Edit Project';

// Initialize database
$db = new Database();
$pdo = $db->getConnection();

// Get project ID
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$project_id) {
    header('Location: list.php');
    exit;
}

// Fetch project
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) {
    header('Location: list.php');
    exit;
}

// Fetch project images
$stmt = $pdo->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY image_type, display_order");
$stmt->execute([$project_id]);
$images = $stmt->fetchAll();

$before_images = array_filter($images, fn($img) => $img['image_type'] === 'before');
$after_images = array_filter($images, fn($img) => $img['image_type'] === 'after');

// Handle form submission
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
    
    $thumbnail = $project['thumbnail'];
    
    // Handle thumbnail upload
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
                // Delete old thumbnail
                if ($thumbnail && file_exists('../../' . $thumbnail)) {
                    unlink('../../' . $thumbnail);
                }
                $thumbnail = 'assets/images/projects/' . $filename;
            } else {
                $errors[] = "Failed to upload thumbnail.";
            }
        }
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE projects SET name = ?, location = ?, scope = ?, thumbnail = ?, is_featured = ?, status = ?, display_order = ? WHERE id = ?");
            $stmt->execute([$name, $location, $scope, $thumbnail, $is_featured, $status, $display_order, $project_id]);
            
            // Handle new before images
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
            
            // Handle new after images
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
            
            header('Location: list.php?updated=1');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Handle image deletion
if (isset($_POST['delete_image'])) {
    $image_id = (int)$_POST['image_id'];
    $stmt = $pdo->prepare("SELECT image_path FROM project_images WHERE id = ? AND project_id = ?");
    $stmt->execute([$image_id, $project_id]);
    $image = $stmt->fetch();
    
    if ($image) {
        // Delete file
        if (file_exists('../../' . $image['image_path'])) {
            unlink('../../' . $image['image_path']);
        }
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM project_images WHERE id = ?");
        $stmt->execute([$image_id]);
        
        header('Location: edit.php?id=' . $project_id . '&deleted=1');
        exit;
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

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success">
    <div class="alert-icon">
        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>
    <div class="alert-content">Image deleted successfully!</div>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Edit Project</h1>
        <p class="page-description">Update project information and manage images</p>
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
                               value="<?php echo htmlspecialchars($project['name']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="location" class="form-label">Location <span class="required">*</span></label>
                        <input type="text" id="location" name="location" class="form-input" required 
                               value="<?php echo htmlspecialchars($project['location']); ?>"
                               placeholder="e.g., Kaneshie, Accra">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="scope" class="form-label">Scope Offered <span class="required">*</span></label>
                    <textarea id="scope" name="scope" class="form-textarea" rows="4" required 
                              placeholder="e.g., Alucobond cladding, Spider glass, Curtain wall installation"><?php echo htmlspecialchars($project['scope']); ?></textarea>
                    <div class="form-help">Describe the services provided for this project</div>
                </div>
            </div>

            <!-- Thumbnail -->
            <div class="form-section">
                <h3 class="form-section-title">Thumbnail Image</h3>
                
                <?php if ($project['thumbnail']): ?>
                <div class="current-thumbnail">
                    <label class="form-label">Current Thumbnail</label>
                    <div class="thumbnail-preview">
                        <img src="../../<?php echo htmlspecialchars($project['thumbnail']); ?>" 
                             alt="Current thumbnail">
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="thumbnail" class="form-label"><?php echo $project['thumbnail'] ? 'Replace' : 'Upload'; ?> Thumbnail</label>
                    <input type="file" id="thumbnail" name="thumbnail" class="form-input" accept="image/*">
                    <div class="form-help">Recommended: landscape orientation, min 800x600px</div>
                </div>
            </div>

            <!-- Settings -->
            <div class="form-section">
                <h3 class="form-section-title">Display Settings</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="display_order" class="form-label">Display Order</label>
                        <input type="number" id="display_order" name="display_order" class="form-input" 
                               value="<?php echo (int)$project['display_order']; ?>" min="0">
                        <div class="form-help">Lower numbers appear first (0 = highest priority)</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="active" <?php echo $project['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $project['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                        <div class="form-help">Inactive projects won't appear on the website</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="is_featured" name="is_featured" class="form-checkbox"
                               <?php echo $project['is_featured'] ? 'checked' : ''; ?>>
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
                Update Project
            </button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<!-- Image Management -->
<div class="image-management-section">
    <!-- Before Images -->
    <div class="image-gallery-card">
        <div class="image-gallery-header">
            <h3 class="image-gallery-title">
                <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Before Images
            </h3>
            <span class="image-count"><?php echo count($before_images); ?> images</span>
        </div>
        
        <div class="image-gallery-body">
            <?php if (!empty($before_images)): ?>
                <div class="image-grid">
                    <?php foreach ($before_images as $image): ?>
                        <div class="image-item">
                            <img src="../../<?php echo htmlspecialchars($image['image_path']); ?>" 
                                 alt="Before">
                            <form method="POST" class="image-delete-form">
                                <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                <button type="submit" name="delete_image" class="image-delete-btn" 
                                        onclick="return confirm('Delete this image?')"
                                        title="Delete image">
                                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-gallery">
                    <svg class="empty-gallery-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p>No before images yet</p>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="image-upload-form">
                <div class="form-group">
                    <label for="before_images" class="form-label">Add More Before Images</label>
                    <input type="file" id="before_images" name="before_images[]" class="form-input" accept="image/*" multiple>
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Upload Before Images
                </button>
            </form>
        </div>
    </div>

    <!-- After Images -->
    <div class="image-gallery-card">
        <div class="image-gallery-header">
            <h3 class="image-gallery-title">
                <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                After Images
            </h3>
            <span class="image-count"><?php echo count($after_images); ?> images</span>
        </div>
        
        <div class="image-gallery-body">
            <?php if (!empty($after_images)): ?>
                <div class="image-grid">
                    <?php foreach ($after_images as $image): ?>
                        <div class="image-item">
                            <img src="../../<?php echo htmlspecialchars($image['image_path']); ?>" 
                                 alt="After">
                            <form method="POST" class="image-delete-form">
                                <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                <button type="submit" name="delete_image" class="image-delete-btn" 
                                        onclick="return confirm('Delete this image?')"
                                        title="Delete image">
                                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-gallery">
                    <svg class="empty-gallery-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p>No after images yet</p>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="image-upload-form">
                <div class="form-group">
                    <label for="after_images" class="form-label">Add More After Images</label>
                    <input type="file" id="after_images" name="after_images[]" class="form-input" accept="image/*" multiple>
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Upload After Images
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Project Info Card -->
<div class="info-card">
    <h3 class="info-card-title">Project Information</h3>
    <div class="info-card-body">
        <div class="info-item">
            <span class="info-label">Created</span>
            <span class="info-value"><?php echo date('M d, Y', strtotime($project['created_at'])); ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Total Images</span>
            <span class="info-value"><?php echo count($images); ?> images</span>
        </div>
        <div class="info-item">
            <span class="info-label">Before Images</span>
            <span class="info-value"><?php echo count($before_images); ?> images</span>
        </div>
        <div class="info-item">
            <span class="info-label">After Images</span>
            <span class="info-value"><?php echo count($after_images); ?> images</span>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
