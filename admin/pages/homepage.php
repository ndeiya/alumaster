<?php
require_once '../includes/auth-check.php';
require_once '../../includes/database.php';

$page_title = "Homepage Management";

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
            'background_image' => $_POST['hero_bg_image'] ?? ''
        ]);
    } else {
        $content = $_POST['content'] ?? '';
    }
    
    if ($section_key && $content) {
        try {
            $stmt = $pdo->prepare("UPDATE homepage_sections SET content = ?, settings = ?, is_active = ? WHERE section_key = ?");
            $stmt->execute([$content, $settings, $is_active, $section_key]);
            
            $success_message = "Homepage section updated successfully!";
        } catch (PDOException $e) {
            $error_message = "Error updating section: " . $e->getMessage();
        }
    }
}

// Get all homepage sections
try {
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
}

.section-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: 1px solid #e5e7eb;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.section-header h3 {
    margin: 0;
    color: #1f2937;
}

.section-status {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 500;
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
    line-height: 1.5;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.preview-btn {
    background-color: #6b7280;
}

.preview-btn:hover {
    background-color: #4b5563;
}

.content-editor {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 1rem;
    background-color: #f9fafb;
}

.editor-field {
    margin-bottom: 1rem;
}

.editor-field:last-child {
    margin-bottom: 0;
}

.editor-field label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
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