<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'Media Library';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Media Library']
];

$success_message = '';
$error_message = '';

// Handle file upload
if ($_POST && isset($_POST['upload_media']) && isset($_FILES['media_files'])) {
    $upload_dir = '../../uploads/media/';
    
    // Create upload directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $uploaded_files = [];
    $errors = [];
    
    foreach ($_FILES['media_files']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['media_files']['error'][$key] === UPLOAD_ERR_OK) {
            $file_name = $_FILES['media_files']['name'][$key];
            $file_size = $_FILES['media_files']['size'][$key];
            $file_type = $_FILES['media_files']['type'][$key];
            
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file_type, $allowed_types)) {
                $errors[] = "File $file_name: Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.";
                continue;
            }
            
            // Validate file size (5MB max)
            if ($file_size > 5 * 1024 * 1024) {
                $errors[] = "File $file_name: File size too large. Maximum 5MB allowed.";
                continue;
            }
            
            // Generate unique filename
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $unique_name = uniqid() . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $unique_name;
            
            if (move_uploaded_file($tmp_name, $upload_path)) {
                $uploaded_files[] = [
                    'original_name' => $file_name,
                    'file_name' => $unique_name,
                    'file_path' => 'uploads/media/' . $unique_name,
                    'file_size' => $file_size,
                    'file_type' => $file_type
                ];
            } else {
                $errors[] = "Failed to upload $file_name";
            }
        }
    }
    
    if (!empty($uploaded_files)) {
        $success_message = count($uploaded_files) . " file(s) uploaded successfully!";
    }
    
    if (!empty($errors)) {
        $error_message = implode('<br>', $errors);
    }
}

// Get uploaded media files
$media_files = [];
$media_dir = '../../uploads/media/';
if (is_dir($media_dir)) {
    $files = scandir($media_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && is_file($media_dir . $file)) {
            $file_path = $media_dir . $file;
            $media_files[] = [
                'name' => $file,
                'path' => 'uploads/media/' . $file,
                'size' => filesize($file_path),
                'modified' => filemtime($file_path),
                'type' => mime_content_type($file_path)
            ];
        }
    }
    
    // Sort by modification time (newest first)
    usort($media_files, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });
}

include '../includes/header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">Media Library</h2>
        <button class="btn btn-primary" onclick="toggleUploadForm()">Upload Media</button>
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
    
    <!-- Upload Form -->
    <div id="uploadForm" class="add-form" style="display: none;">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="media_files" class="form-label">Select Files</label>
                <input type="file" id="media_files" name="media_files[]" class="form-input" 
                       multiple accept="image/*" required>
                <small class="form-help">Select one or more image files (JPEG, PNG, GIF, WebP). Maximum 5MB per file.</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="upload_media" class="btn btn-primary">Upload Files</button>
                <button type="button" class="btn btn-secondary" onclick="toggleUploadForm()">Cancel</button>
            </div>
        </form>
    </div>
    
    <!-- Media Grid -->
    <div class="media-grid">
        <?php if (empty($media_files)): ?>
            <div class="empty-state">
                <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="empty-message">No media files found</p>
                <button class="btn btn-primary" onclick="toggleUploadForm()">Upload Your First File</button>
            </div>
        <?php else: ?>
            <?php foreach ($media_files as $file): ?>
                <div class="media-item">
                    <?php if (strpos($file['type'], 'image/') === 0): ?>
                        <div class="media-thumbnail">
                            <img src="../../<?php echo $file['path']; ?>" alt="<?php echo htmlspecialchars($file['name']); ?>">
                        </div>
                    <?php else: ?>
                        <div class="media-thumbnail media-file">
                            <svg class="file-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    <?php endif; ?>
                    
                    <div class="media-info">
                        <div class="media-name" title="<?php echo htmlspecialchars($file['name']); ?>">
                            <?php echo htmlspecialchars(strlen($file['name']) > 20 ? substr($file['name'], 0, 20) . '...' : $file['name']); ?>
                        </div>
                        <div class="media-details">
                            <span class="media-size"><?php echo number_format($file['size'] / 1024, 1); ?> KB</span>
                            <span class="media-date"><?php echo date('M j, Y', $file['modified']); ?></span>
                        </div>
                    </div>
                    
                    <div class="media-actions">
                        <button class="btn btn-sm btn-outline" onclick="copyUrl('<?php echo SITE_URL . '/' . $file['path']; ?>')">Copy URL</button>
                        <a href="../../<?php echo $file['path']; ?>" target="_blank" class="btn btn-sm btn-outline">View</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleUploadForm() {
    const form = document.getElementById('uploadForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function copyUrl(url) {
    navigator.clipboard.writeText(url).then(function() {
        alert('URL copied to clipboard!');
    });
}
</script>



<?php include '../includes/footer.php'; ?>