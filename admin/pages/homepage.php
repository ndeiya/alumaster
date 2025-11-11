<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'Homepage Management';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Pages', 'url' => '#'],
    ['title' => 'Homepage']
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section_key = $_POST['section_key'] ?? '';
    $settings = $_POST['settings'] ?? '{}';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Handle different section types
    if ($section_key === 'hero') {
        $content = json_encode([
            'title' => $_POST['hero_title'] ?? '',
            'highlight' => $_POST['hero_highlight'] ?? '',
            'description' => $_POST['hero_description'] ?? '',
            'primary_button_text' => $_POST['hero_primary_btn'] ?? '',
            'primary_button_link' => $_POST['hero_primary_link'] ?? '',
            'secondary_button_text' => $_POST['hero_secondary_btn'] ?? '',
            'secondary_button_link' => $_POST['hero_secondary_link'] ?? '',
            'background_image' => $_POST['hero_bg_image'] ?? '',
            'video_url' => $_POST['hero_video_url'] ?? '',
            'video_type' => $_POST['hero_video_type'] ?? 'youtube',
            'show_video' => isset($_POST['hero_show_video']) ? true : false,
            'video_autoplay' => isset($_POST['hero_video_autoplay']) ? true : false
        ]);
    } else {
        $content = $_POST['content'] ?? '';
    }
    
    if ($section_key && $content) {
        try {
            $db = new Database();
            $pdo = $db->getConnection();
            
            $stmt = $pdo->prepare("UPDATE homepage_sections SET content = ?, settings = ?, is_active = ? WHERE section_key = ?");
            $stmt->execute([$content, $settings, $is_active, $section_key]);
            
            $success_message = "Homepage section updated successfully!";
        } catch (PDOException $e) {
            $error_message = "Error updating section: " . $e->getMessage();
        }
    }
}

$success_message = '';
$error_message = '';

// Get all homepage sections
try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->query("SELECT * FROM homepage_sections ORDER BY sort_order ASC");
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error loading sections: " . $e->getMessage();
    $sections = [];
}

include '../includes/header.php';
?>

<div class="admin-content">
    <div class="content-header">
        <h1><?php echo $page_title; ?></h1>
        <p>Manage your homepage content sections</p>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <div class="homepage-sections">
        <?php foreach ($sections as $section): ?>
            <div class="section-card">
                <div class="section-header">
                    <h3><?php echo htmlspecialchars($section['section_name']); ?></h3>
                    <span class="section-status <?php echo $section['is_active'] ? 'active' : 'inactive'; ?>">
                        <?php echo $section['is_active'] ? 'Active' : 'Inactive'; ?>
                    </span>
                </div>
                
                <form method="POST" class="section-form">
                    <input type="hidden" name="section_key" value="<?php echo htmlspecialchars($section['section_key']); ?>">
                    
                    <div class="form-group">
                        <label>Content</label>
                        <div class="content-editor" data-section="<?php echo htmlspecialchars($section['section_key']); ?>">
                            <?php 
                            $content = json_decode($section['content'], true);
                            if ($section['section_key'] === 'hero'): ?>
                                <div class="editor-field">
                                    <label>Title</label>
                                    <input type="text" name="hero_title" value="<?php echo htmlspecialchars($content['title'] ?? ''); ?>" class="form-control">
                                </div>
                                <div class="editor-field">
                                    <label>Highlight Text</label>
                                    <input type="text" name="hero_highlight" value="<?php echo htmlspecialchars($content['highlight'] ?? ''); ?>" class="form-control">
                                </div>
                                <div class="editor-field">
                                    <label>Description</label>
                                    <textarea name="hero_description" rows="3" class="form-control"><?php echo htmlspecialchars($content['description'] ?? ''); ?></textarea>
                                </div>
                                <div class="editor-field">
                                    <label>Primary Button Text</label>
                                    <input type="text" name="hero_primary_btn" value="<?php echo htmlspecialchars($content['primary_button_text'] ?? ''); ?>" class="form-control">
                                </div>
                                <div class="editor-field">
                                    <label>Primary Button Link</label>
                                    <input type="text" name="hero_primary_link" value="<?php echo htmlspecialchars($content['primary_button_link'] ?? ''); ?>" class="form-control">
                                </div>
                                <div class="editor-field">
                                    <label>Secondary Button Text</label>
                                    <input type="text" name="hero_secondary_btn" value="<?php echo htmlspecialchars($content['secondary_button_text'] ?? ''); ?>" class="form-control">
                                </div>
                                <div class="editor-field">
                                    <label>Secondary Button Link</label>
                                    <input type="text" name="hero_secondary_link" value="<?php echo htmlspecialchars($content['secondary_button_link'] ?? ''); ?>" class="form-control">
                                </div>
                                <div class="editor-field">
                                    <label>Background Image</label>
                                    <input type="text" name="hero_bg_image" value="<?php echo htmlspecialchars($content['background_image'] ?? ''); ?>" class="form-control">
                                    <small class="form-text">Path to background image (shown when video is disabled)</small>
                                </div>
                                
                                <div class="editor-section-divider">
                                    <h4>Video Settings</h4>
                                </div>
                                
                                <div class="editor-field">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="hero_show_video" <?php echo ($content['show_video'] ?? false) ? 'checked' : ''; ?>>
                                        <span class="checkmark"></span>
                                        Enable Video Background
                                    </label>
                                </div>
                                
                                <div class="editor-field">
                                    <label>Video Type</label>
                                    <select name="hero_video_type" class="form-control">
                                        <option value="youtube" <?php echo ($content['video_type'] ?? 'youtube') === 'youtube' ? 'selected' : ''; ?>>YouTube</option>
                                        <option value="vimeo" <?php echo ($content['video_type'] ?? 'youtube') === 'vimeo' ? 'selected' : ''; ?>>Vimeo</option>
                                    </select>
                                </div>
                                
                                <div class="editor-field">
                                    <label>Video URL</label>
                                    <input type="text" name="hero_video_url" value="<?php echo htmlspecialchars($content['video_url'] ?? ''); ?>" class="form-control" placeholder="https://www.youtube.com/watch?v=VIDEO_ID or https://vimeo.com/VIDEO_ID">
                                    <small class="form-text">Full YouTube or Vimeo URL</small>
                                </div>
                                
                                <div class="editor-field">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="hero_video_autoplay" <?php echo ($content['video_autoplay'] ?? true) ? 'checked' : ''; ?>>
                                        <span class="checkmark"></span>
                                        Autoplay Video (muted)
                                    </label>
                                    <small class="form-text">Video will autoplay muted for better UX</small>
                                </div>
                            <?php else: ?>
                                <textarea name="content" rows="10" class="form-control json-editor" required><?php echo htmlspecialchars($section['content']); ?></textarea>
                                <small class="form-text">Edit the JSON content for this section. Be careful with syntax.</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Settings (JSON Format)</label>
                        <textarea name="settings" rows="3" class="form-control json-editor"><?php echo htmlspecialchars($section['settings']); ?></textarea>
                        <small class="form-text">Additional settings like colors, layout options, etc.</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" <?php echo $section['is_active'] ? 'checked' : ''; ?>>
                            <span class="checkmark"></span>
                            Active
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Section</button>
                        <button type="button" class="btn btn-secondary preview-btn" data-section="<?php echo htmlspecialchars($section['section_key']); ?>">Preview</button>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.homepage-sections {
    display: grid;
    gap: 2rem;
    max-width: 1200px;
}

.section-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.07);
    border: 1px solid #e5e7eb;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.25rem;
    border-bottom: 2px solid #e5e7eb;
}

.section-header h3 {
    margin: 0;
    color: #111827;
    font-size: 1.5rem;
    font-weight: 700;
}

.section-status {
    padding: 0.375rem 1rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.section-status.active {
    background-color: #d1fae5;
    color: #065f46;
}

.section-status.inactive {
    background-color: #fee2e2;
    color: #991b1b;
}

.json-editor {
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    line-height: 1.6;
    background-color: #ffffff !important;
    color: #1f2937 !important;
    border: 2px solid #d1d5db !important;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.preview-btn {
    background-color: #6b7280;
}

.preview-btn:hover {
    background-color: #4b5563;
}

.content-editor {
    border: 2px solid #d1d5db;
    border-radius: 8px;
    padding: 1.5rem;
    background-color: #ffffff;
}

.editor-field {
    margin-bottom: 1.25rem;
}

.editor-field:last-child {
    margin-bottom: 0;
}

.editor-field label {
    display: block;
    margin-bottom: 0.625rem;
    font-weight: 600;
    color: #111827;
    font-size: 0.9375rem;
}

.editor-field .form-control,
.editor-field input[type="text"],
.editor-field textarea,
.editor-field select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.9375rem;
    color: #1f2937;
    background-color: #ffffff;
    transition: all 0.2s ease;
}

.editor-field .form-control:focus,
.editor-field input[type="text"]:focus,
.editor-field textarea:focus,
.editor-field select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.editor-field textarea {
    min-height: 100px;
    resize: vertical;
    font-family: inherit;
    line-height: 1.6;
}

.editor-field small.form-text {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.8125rem;
    color: #6b7280;
    font-style: italic;
}

.editor-section-divider {
    margin: 2rem -1.5rem 1.5rem;
    padding: 2rem 1.5rem 1.5rem;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
    border: 2px solid #2563eb;
}

.editor-section-divider h4 {
    margin: 0 0 1rem 0;
    color: #ffffff;
    font-size: 1.25rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.editor-section-divider h4::before {
    content: "🎬";
    font-size: 1.5rem;
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.2));
}

/* Video section specific styling */
.editor-section-divider .editor-field label {
    color: #ffffff !important;
    font-weight: 600;
}

.editor-section-divider .editor-field small.form-text {
    color: #e0e7ff !important;
    font-weight: 500;
}

.editor-section-divider .checkbox-label {
    background-color: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 1rem;
    transition: all 0.2s ease;
}

.editor-section-divider .checkbox-label:hover {
    background-color: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.3);
}

.editor-section-divider .checkbox-label span:not(.checkmark) {
    color: #ffffff !important;
    font-weight: 600;
}

.editor-section-divider input[type="text"],
.editor-section-divider select,
.editor-section-divider textarea {
    background-color: #ffffff !important;
    border: 2px solid #e5e7eb !important;
    color: #1f2937 !important;
}

.editor-section-divider input[type="text"]:focus,
.editor-section-divider select:focus,
.editor-section-divider textarea:focus {
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3) !important;
}

.editor-section-divider input[type="checkbox"] {
    width: 1.5rem;
    height: 1.5rem;
    accent-color: #ffffff;
    cursor: pointer;
}

/* Checkbox styling */
.editor-field .checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    padding: 0.75rem;
    border-radius: 6px;
    transition: background-color 0.2s ease;
}

.editor-field .checkbox-label:hover {
    background-color: #f9fafb;
}

.editor-field .checkbox-label input[type="checkbox"] {
    width: 1.25rem;
    height: 1.25rem;
    cursor: pointer;
    accent-color: #3b82f6;
}

.editor-field .checkbox-label span:not(.checkmark) {
    font-weight: 500;
    color: #1f2937;
}

/* Form group styling */
.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.625rem;
    font-weight: 600;
    color: #111827;
    font-size: 0.9375rem;
}

.form-group .form-control,
.form-group textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.9375rem;
    color: #1f2937;
    background-color: #ffffff;
    transition: all 0.2s ease;
}

.form-group .form-control:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Button improvements */
.btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.btn-primary {
    background-color: #3b82f6;
    color: white;
    border: none;
}

.btn-primary:hover {
    background-color: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
}

.btn-secondary {
    background-color: #6b7280;
    color: white;
    border: none;
}

.btn-secondary:hover {
    background-color: #4b5563;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // JSON validation for textareas
    const jsonEditors = document.querySelectorAll('.json-editor');
    
    jsonEditors.forEach(editor => {
        editor.addEventListener('blur', function() {
            try {
                JSON.parse(this.value);
                this.style.borderColor = '#d1d5db';
            } catch (e) {
                this.style.borderColor = '#ef4444';
                console.error('Invalid JSON:', e.message);
            }
        });
    });
    
    // Preview functionality
    const previewBtns = document.querySelectorAll('.preview-btn');
    
    previewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const section = this.dataset.section;
            window.open('../../index.php#' + section, '_blank');
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>