<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'Add New Page';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Pages', 'url' => 'list.php'],
    ['title' => 'Add New Page']
];

// Handle form submission
if ($_POST && isset($_POST['add_page'])) {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $error_message = "Security token mismatch.";
    } else {
        $title = sanitize_input($_POST['title'] ?? '');
        $slug = sanitize_input($_POST['slug'] ?? '');
        $content = $_POST['content'] ?? '';
        $excerpt = sanitize_input($_POST['excerpt'] ?? '');
        $featured_image = sanitize_input($_POST['featured_image'] ?? '');
        $template = sanitize_input($_POST['template'] ?? 'default');
        $meta_title = sanitize_input($_POST['meta_title'] ?? '');
        $meta_description = sanitize_input($_POST['meta_description'] ?? '');
        $meta_keywords = sanitize_input($_POST['meta_keywords'] ?? '');
        $status = sanitize_input($_POST['status'] ?? 'draft');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $show_in_nav = isset($_POST['show_in_nav']) ? 1 : 0;
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        
        // Handle image upload
        if (isset($_FILES['featured_image_upload']) && $_FILES['featured_image_upload']['error'] === UPLOAD_ERR_OK) {
            $upload_result = handle_image_upload($_FILES['featured_image_upload'], 'pages');
            if ($upload_result['success']) {
                $featured_image = $upload_result['path'];
            } else {
                $errors[] = "Image upload error: " . $upload_result['message'];
            }
        }
        
        // Validation
        $errors = [];
        if (empty($title)) $errors[] = "Page title is required.";
        if (empty($slug)) $errors[] = "Slug is required.";
        if (empty($content)) $errors[] = "Content is required.";
        
        // Check if slug is unique
        if (!empty($slug)) {
            try {
                $db = new Database();
                $conn = $db->getConnection();
                $stmt = $conn->prepare("SELECT id FROM pages WHERE slug = ?");
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
                $stmt = $conn->prepare("INSERT INTO pages 
                                       (title, slug, content, excerpt, featured_image, template, 
                                        meta_title, meta_description, meta_keywords, status, 
                                        sort_order, show_in_nav, parent_id) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $stmt->execute([
                    $title, $slug, $content, $excerpt, $featured_image, $template,
                    $meta_title, $meta_description, $meta_keywords, $status,
                    $sort_order, $show_in_nav, $parent_id
                ]);
                
                $page_id = $conn->lastInsertId();
                log_admin_activity('create', "Created page: $title", $page_id);
                
                header('Location: list.php?success=Page added successfully');
                exit;
                
            } catch (Exception $e) {
                $error_message = "Error adding page: " . $e->getMessage();
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
    }
}

// Get parent pages for dropdown
try {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT id, title FROM pages WHERE parent_id IS NULL ORDER BY title");
    $stmt->execute();
    $parent_pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $parent_pages = [];
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
        <h1 class="page-title">Add New Page</h1>
        <p class="page-description">Create a new page for your website</p>
    </div>
    <div class="page-header-actions">
        <a href="list.php" class="btn btn-secondary">
            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Pages
        </a>
    </div>
</div>

<!-- Page Form -->
<div class="form-card">
    <form method="POST" action="" class="page-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        
        <div class="form-sections">
            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title">Basic Information</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="title" class="form-label">Page Title <span class="required">*</span></label>
                        <input type="text" id="title" name="title" class="form-input" required 
                               value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                               onkeyup="generateSlug(this.value)">
                    </div>
                    
                    <div class="form-group">
                        <label for="slug" class="form-label">Slug <span class="required">*</span></label>
                        <input type="text" id="slug" name="slug" class="form-input" required 
                               value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>">
                        <div class="form-help">URL-friendly version of the title</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="draft" <?php echo ($_POST['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?php echo ($_POST['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="private" <?php echo ($_POST['status'] ?? '') === 'private' ? 'selected' : ''; ?>>Private</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="template" class="form-label">Template</label>
                        <select id="template" name="template" class="form-select">
                            <option value="default" <?php echo ($_POST['template'] ?? 'default') === 'default' ? 'selected' : ''; ?>>Default</option>
                            <option value="full-width" <?php echo ($_POST['template'] ?? '') === 'full-width' ? 'selected' : ''; ?>>Full Width</option>
                            <option value="landing" <?php echo ($_POST['template'] ?? '') === 'landing' ? 'selected' : ''; ?>>Landing Page</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="excerpt" class="form-label">Excerpt</label>
                    <textarea id="excerpt" name="excerpt" class="form-textarea" rows="3" 
                              placeholder="Brief description of the page..."><?php echo htmlspecialchars($_POST['excerpt'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <!-- Content -->
            <div class="form-section">
                <h3 class="form-section-title">Content</h3>
                
                <div class="form-group">
                    <label for="content" class="form-label">Page Content <span class="required">*</span></label>
                    <textarea id="content" name="content" class="form-textarea content-editor" rows="15" required 
                              placeholder="Enter your page content here..."><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                    <div class="form-help">You can use HTML tags for formatting</div>
                </div>
            </div>
            
            <!-- Media -->
            <div class="form-section">
                <h3 class="form-section-title">Featured Image</h3>
                
                <div class="form-group">
                    <label class="form-label">Featured Image</label>
                    
                    <!-- Image Upload Area -->
                    <div class="image-upload-container">
                        <div class="image-upload-area" id="imageUploadArea">
                            <div class="image-upload-content">
                                <svg class="image-upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="image-upload-text">Click to upload or drag and drop</p>
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
                           value="<?php echo htmlspecialchars($_POST['featured_image'] ?? ''); ?>">
                    
                    <!-- Manual URL input (alternative) -->
                    <div class="form-group mt-3">
                        <label for="manual_image_url" class="form-label">Or enter image URL manually</label>
                        <input type="text" id="manual_image_url" name="manual_image_url" class="form-input" 
                               placeholder="assets/images/pages/page-name.jpg"
                               onchange="setManualImageUrl(this.value)">
                        <div class="form-help">Alternative: Enter image path manually</div>
                    </div>
                </div>
            </div>
            
            <!-- Page Settings -->
            <div class="form-section">
                <h3 class="form-section-title">Page Settings</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="parent_id" class="form-label">Parent Page</label>
                        <select id="parent_id" name="parent_id" class="form-select">
                            <option value="">No Parent (Top Level)</option>
                            <?php foreach ($parent_pages as $parent): ?>
                            <option value="<?php echo $parent['id']; ?>" 
                                    <?php echo ($_POST['parent_id'] ?? '') == $parent['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($parent['title']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order" class="form-input" 
                               value="<?php echo $_POST['sort_order'] ?? '0'; ?>" min="0">
                        <div class="form-help">Lower numbers appear first</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="form-checkbox">
                        <input type="checkbox" id="show_in_nav" name="show_in_nav" value="1" 
                               <?php echo isset($_POST['show_in_nav']) ? 'checked' : 'checked'; ?>>
                        <label for="show_in_nav" class="checkbox-label">Show in Navigation</label>
                    </div>
                </div>
            </div>
            
            <!-- SEO -->
            <div class="form-section">
                <h3 class="form-section-title">SEO Settings</h3>
                
                <div class="form-group">
                    <label for="meta_title" class="form-label">Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" class="form-input" 
                           value="<?php echo htmlspecialchars($_POST['meta_title'] ?? ''); ?>"
                           placeholder="SEO title for search engines">
                </div>
                
                <div class="form-group">
                    <label for="meta_description" class="form-label">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" class="form-textarea" rows="3" 
                              placeholder="SEO description for search engines..."><?php echo htmlspecialchars($_POST['meta_description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                    <input type="text" id="meta_keywords" name="meta_keywords" class="form-input" 
                           value="<?php echo htmlspecialchars($_POST['meta_keywords'] ?? ''); ?>"
                           placeholder="keyword1, keyword2, keyword3">
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" name="add_page" class="btn btn-primary">
                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Page
            </button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
function generateSlug(title) {
    const slug = title.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
    document.getElementById('slug').value = slug;
}

// Image upload functionality (same as services)
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('imageUploadArea');
    const fileInput = document.getElementById('featuredImageUpload');
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');
    const hiddenInput = document.getElementById('featured_image');

    if (uploadArea && fileInput) {
        uploadArea.addEventListener('click', () => fileInput.click());

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

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileUpload(e.target.files[0]);
            }
        });
    }

    function handleFileUpload(file) {
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.src = e.target.result;
            uploadArea.style.display = 'none';
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(file);

        const formData = new FormData();
        formData.append('image', file);

        fetch('../services/upload-image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                hiddenInput.value = data.path;
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