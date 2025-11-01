<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$service_id = (int)($_GET['id'] ?? 0);

if (!$service_id) {
    header('Location: list.php');
    exit;
}

$page_title = 'Edit Service';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Services', 'url' => 'list.php'],
    ['title' => 'Edit Service']
];

// Get service data
$service = null;
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT s.*, sc.name as category_name 
                           FROM services s 
                           LEFT JOIN service_categories sc ON s.category_id = sc.id 
                           WHERE s.id = ? AND s.status != 'deleted'");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$service) {
        header('Location: list.php');
        exit;
    }
    
    // Get categories for dropdown
    $stmt = $conn->prepare("SELECT * FROM service_categories WHERE is_active = 1 ORDER BY name");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error_message = "Error loading service: " . $e->getMessage();
}

// Handle form submission
if ($_POST && isset($_POST['update_service'])) {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $error_message = "Security token mismatch.";
    } else {
        $name = sanitize_input($_POST['name'] ?? '');
        $slug = sanitize_input($_POST['slug'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $short_description = sanitize_input($_POST['short_description'] ?? '');
        $description = sanitize_input($_POST['description'] ?? '');
        $technical_specs = sanitize_input($_POST['technical_specs'] ?? '');
        $featured_image = sanitize_input($_POST['featured_image'] ?? '');
        
        // Handle image upload
        if (isset($_FILES['featured_image_upload']) && $_FILES['featured_image_upload']['error'] === UPLOAD_ERR_OK) {
            $upload_result = handle_image_upload($_FILES['featured_image_upload'], 'services');
            if ($upload_result['success']) {
                $featured_image = $upload_result['path'];
            } else {
                $errors[] = "Image upload error: " . $upload_result['message'];
            }
        }
        $meta_title = sanitize_input($_POST['meta_title'] ?? '');
        $meta_description = sanitize_input($_POST['meta_description'] ?? '');
        $meta_keywords = sanitize_input($_POST['meta_keywords'] ?? '');
        $status = sanitize_input($_POST['status'] ?? 'draft');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        
        // Validation
        $errors = [];
        if (empty($name)) $errors[] = "Service name is required.";
        if (empty($slug)) $errors[] = "Slug is required.";
        if (!$category_id) $errors[] = "Category is required.";
        if (empty($short_description)) $errors[] = "Short description is required.";
        if (empty($description)) $errors[] = "Description is required.";
        
        // Check if slug is unique (excluding current service)
        if (!empty($slug)) {
            try {
                $stmt = $conn->prepare("SELECT id FROM services WHERE slug = ? AND id != ?");
                $stmt->execute([$slug, $service_id]);
                if ($stmt->fetchColumn()) {
                    $errors[] = "Slug already exists. Please choose a different one.";
                }
            } catch (Exception $e) {
                $errors[] = "Error checking slug uniqueness.";
            }
        }
        
        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("UPDATE services SET 
                                       category_id = ?, name = ?, slug = ?, short_description = ?, 
                                       description = ?, technical_specs = ?, featured_image = ?, 
                                       meta_title = ?, meta_description = ?, meta_keywords = ?, 
                                       status = ?, sort_order = ?, updated_at = CURRENT_TIMESTAMP 
                                       WHERE id = ?");
                
                $stmt->execute([
                    $category_id, $name, $slug, $short_description, $description, 
                    $technical_specs, $featured_image, $meta_title, $meta_description, 
                    $meta_keywords, $status, $sort_order, $service_id
                ]);
                
                log_admin_activity('update', "Updated service: $name", $service_id);
                $success_message = "Service updated successfully.";
                
                // Refresh service data
                $stmt = $conn->prepare("SELECT s.*, sc.name as category_name 
                                       FROM services s 
                                       LEFT JOIN service_categories sc ON s.category_id = sc.id 
                                       WHERE s.id = ?");
                $stmt->execute([$service_id]);
                $service = $stmt->fetch(PDO::FETCH_ASSOC);
                
            } catch (Exception $e) {
                $error_message = "Error updating service: " . $e->getMessage();
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
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
    <div class="alert-content"><?php echo $error_message; ?></div>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Edit Service</h1>
        <p class="page-description">Update service information and settings</p>
    </div>
    <div class="page-header-actions">
        <a href="list.php" class="btn btn-secondary">
            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Services
        </a>
        <a href="../../service-detail.php?service=<?php echo urlencode($service['slug']); ?>" 
           target="_blank" class="btn btn-outline">
            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Preview
        </a>
    </div>
</div>

<!-- Service Form -->
<div class="form-card">
    <form method="POST" action="" class="service-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        
        <div class="form-sections">
            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title">Basic Information</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label">Service Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" class="form-input" required 
                               value="<?php echo htmlspecialchars($service['name']); ?>"
                               onkeyup="generateSlug(this.value)">
                    </div>
                    
                    <div class="form-group">
                        <label for="slug" class="form-label">Slug <span class="required">*</span></label>
                        <input type="text" id="slug" name="slug" class="form-input" required 
                               value="<?php echo htmlspecialchars($service['slug']); ?>">
                        <div class="form-help">URL-friendly version of the name</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id" class="form-label">Category <span class="required">*</span></label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                    <?php echo $service['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="draft" <?php echo $service['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?php echo $service['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="archived" <?php echo $service['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="short_description" class="form-label">Short Description <span class="required">*</span></label>
                    <textarea id="short_description" name="short_description" class="form-textarea" rows="3" required 
                              placeholder="Brief description for service listings..."><?php echo htmlspecialchars($service['short_description']); ?></textarea>
                </div>
            </div>
            
            <!-- Content -->
            <div class="form-section">
                <h3 class="form-section-title">Content</h3>
                
                <div class="form-group">
                    <label for="description" class="form-label">Description <span class="required">*</span></label>
                    <textarea id="description" name="description" class="form-textarea" rows="8" required 
                              placeholder="Detailed service description..."><?php echo htmlspecialchars($service['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="technical_specs" class="form-label">Technical Specifications</label>
                    <textarea id="technical_specs" name="technical_specs" class="form-textarea" rows="6" 
                              placeholder="Technical specifications, features, materials, etc..."><?php echo htmlspecialchars($service['technical_specs']); ?></textarea>
                </div>
            </div>
            
            <!-- Media -->
            <div class="form-section">
                <h3 class="form-section-title">Media</h3>
                
                <div class="form-group">
                    <label class="form-label">Featured Image</label>
                    
                    <!-- Current Image Display -->
                    <?php if (!empty($service['featured_image'])): ?>
                    <div class="current-image-display">
                        <label class="form-label-sm">Current Image:</label>
                        <div class="current-image-preview">
                            <img src="../../<?php echo htmlspecialchars($service['featured_image']); ?>" 
                                 alt="Current featured image" class="current-preview-image"
                                 onerror="this.style.display='none'">
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Image Upload Area -->
                    <div class="image-upload-container">
                        <div class="image-upload-area" id="imageUploadArea">
                            <div class="image-upload-content">
                                <svg class="image-upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="image-upload-text">Click to upload new image or drag and drop</p>
                                <p class="image-upload-hint">PNG, JPG, WebP up to 5MB</p>
                            </div>
                            <input type="file" id="featuredImageUpload" name="featured_image_upload" 
                                   class="image-upload-input" accept="image/*">
                        </div>
                        
                        <!-- Image Preview -->
                        <div class="image-preview" id="imagePreview" style="display: none;">
                            <img id="previewImage" src="" alt="Preview" class="preview-image">
                            <div class="image-preview-actions">
                                <button type="button" class="btn btn-sm btn-secondary" onclick="removeImage()">
                                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden input for image path -->
                    <input type="hidden" id="featured_image" name="featured_image" 
                           value="<?php echo htmlspecialchars($service['featured_image']); ?>">
                    
                    <!-- Manual URL input (alternative) -->
                    <div class="form-group mt-3">
                        <label for="manual_image_url" class="form-label">Or enter image URL manually</label>
                        <input type="text" id="manual_image_url" name="manual_image_url" class="form-input" 
                               value="<?php echo htmlspecialchars($service['featured_image']); ?>"
                               placeholder="assets/images/services/service-name.jpg"
                               onchange="setManualImageUrl(this.value)">
                        <div class="form-help">Alternative: Enter image path manually</div>
                    </div>
                </div>
            </div>
            
            <!-- SEO -->
            <div class="form-section">
                <h3 class="form-section-title">SEO Settings</h3>
                
                <div class="form-group">
                    <label for="meta_title" class="form-label">Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" class="form-input" 
                           value="<?php echo htmlspecialchars($service['meta_title']); ?>"
                           placeholder="SEO title for search engines">
                </div>
                
                <div class="form-group">
                    <label for="meta_description" class="form-label">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" class="form-textarea" rows="3" 
                              placeholder="SEO description for search engines..."><?php echo htmlspecialchars($service['meta_description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                    <input type="text" id="meta_keywords" name="meta_keywords" class="form-input" 
                           value="<?php echo htmlspecialchars($service['meta_keywords']); ?>"
                           placeholder="keyword1, keyword2, keyword3">
                </div>
            </div>
            
            <!-- Settings -->
            <div class="form-section">
                <h3 class="form-section-title">Settings</h3>
                
                <div class="form-group">
                    <label for="sort_order" class="form-label">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-input" 
                           value="<?php echo $service['sort_order']; ?>" min="0">
                    <div class="form-help">Lower numbers appear first</div>
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" name="update_service" class="btn btn-primary">
                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Update Service
            </button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
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

// Image upload functionality
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('imageUploadArea');
    const fileInput = document.getElementById('featuredImageUpload');
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');
    const hiddenInput = document.getElementById('featured_image');

    // Click to upload
    uploadArea.addEventListener('click', () => fileInput.click());

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('drag-over');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('drag-over');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileUpload(files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileUpload(e.target.files[0]);
        }
    });

    function handleFileUpload(file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file');
            return;
        }

        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            return;
        }

        // Show preview immediately
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.src = e.target.result;
            uploadArea.style.display = 'none';
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(file);

        // Upload file via AJAX
        const formData = new FormData();
        formData.append('image', file);

        fetch('upload-image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                hiddenInput.value = data.path;
                console.log('Image uploaded successfully:', data.path);
            } else {
                alert('Upload failed: ' + data.message);
                removeImage();
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            alert('Upload failed. Please try again.');
            removeImage();
        });
    }
});

function removeImage() {
    document.getElementById('imageUploadArea').style.display = 'block';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('featured_image').value = '';
    document.getElementById('featuredImageUpload').value = '';
}

function setManualImageUrl(url) {
    const hiddenInput = document.getElementById('featured_image');
    const previewImage = document.getElementById('previewImage');
    const uploadArea = document.getElementById('imageUploadArea');
    const imagePreview = document.getElementById('imagePreview');
    
    if (url.trim()) {
        hiddenInput.value = url;
        previewImage.src = url;
        uploadArea.style.display = 'none';
        imagePreview.style.display = 'block';
    } else {
        removeImage();
    }
}
</script>

<?php include '../includes/footer.php'; ?>