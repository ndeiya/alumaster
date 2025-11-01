<?php
require_once '../includes/auth-check.php';
require_once '../../includes/database.php';

$page_title = "Contact Page Management";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section_key = $_POST['section_key'] ?? '';
    $content = $_POST['content'] ?? '';
    $settings = $_POST['settings'] ?? '{}';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if ($section_key && $content) {
        try {
            $db = new Database();
            $pdo = $db->getConnection();
            
            $stmt = $pdo->prepare("UPDATE page_sections SET content = ?, settings = ?, is_active = ? WHERE page_slug = 'contact' AND section_key = ?");
            $stmt->execute([$content, $settings, $is_active, $section_key]);
            
            $success_message = "Contact page section updated successfully!";
        } catch (PDOException $e) {
            $error_message = "Error updating section: " . $e->getMessage();
        }
    }
}

// Get all contact page sections
try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->query("SELECT * FROM page_sections WHERE page_slug = 'contact' ORDER BY sort_order ASC");
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
        <p>Manage your Contact page content sections</p>
        <div class="content-actions">
            <a href="../../contact.php" target="_blank" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                Preview Page
            </a>
        </div>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <div class="page-sections">
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
                                    <label>Page Title</label>
                                    <input type="text" name="hero_title" value="<?php echo htmlspecialchars($content['title'] ?? ''); ?>" class="form-control">
                                </div>
                                <div class="editor-field">
                                    <label>Subtitle</label>
                                    <input type="text" name="hero_subtitle" value="<?php echo htmlspecialchars($content['subtitle'] ?? ''); ?>" class="form-control">
                                </div>
                                <div class="editor-field">
                                    <label>Breadcrumb Text</label>
                                    <input type="text" name="hero_breadcrumb" value="<?php echo htmlspecialchars($content['breadcrumb'] ?? ''); ?>" class="form-control">
                                </div>
                            <?php elseif ($section['section_key'] === 'contact_form'): ?>
                                <div class="editor-field">
                                    <label>Form Title</label>
                                    <input type="text" name="form_title" value="<?php echo htmlspecialchars($content['title'] ?? ''); ?>" class="form-control">
                                </div>
                                <div class="editor-field">
                                    <label>Form Description</label>
                                    <textarea name="form_description" rows="2" class="form-control"><?php echo htmlspecialchars($content['description'] ?? ''); ?></textarea>
                                </div>
                                <div class="editor-field">
                                    <label>Services List (one per line)</label>
                                    <textarea name="form_services" rows="8" class="form-control"><?php echo implode("\n", $content['services'] ?? []); ?></textarea>
                                </div>
                            <?php elseif ($section['section_key'] === 'map'): ?>
                                <div class="editor-field">
                                    <label>Map Embed URL</label>
                                    <textarea name="map_embed_url" rows="3" class="form-control"><?php echo htmlspecialchars($content['embed_url'] ?? ''); ?></textarea>
                                </div>
                                <div class="editor-field">
                                    <label>Map Title</label>
                                    <input type="text" name="map_title" value="<?php echo htmlspecialchars($content['title'] ?? ''); ?>" class="form-control">
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
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.page-sections {
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

.content-actions {
    display: flex;
    gap: 1rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission for visual editors
    const forms = document.querySelectorAll('.section-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const sectionKey = form.querySelector('input[name="section_key"]').value;
            
            if (sectionKey === 'hero') {
                const content = {
                    title: form.querySelector('input[name="hero_title"]').value,
                    subtitle: form.querySelector('input[name="hero_subtitle"]').value,
                    breadcrumb: form.querySelector('input[name="hero_breadcrumb"]').value
                };
                
                // Create hidden input for content
                let contentInput = form.querySelector('input[name="content"]');
                if (!contentInput) {
                    contentInput = document.createElement('input');
                    contentInput.type = 'hidden';
                    contentInput.name = 'content';
                    form.appendChild(contentInput);
                }
                contentInput.value = JSON.stringify(content);
                
            } else if (sectionKey === 'contact_form') {
                const servicesText = form.querySelector('textarea[name="form_services"]').value;
                const services = servicesText.split('\n').filter(s => s.trim() !== '');
                
                const content = {
                    title: form.querySelector('input[name="form_title"]').value,
                    description: form.querySelector('textarea[name="form_description"]').value,
                    services: services
                };
                
                // Create hidden input for content
                let contentInput = form.querySelector('input[name="content"]');
                if (!contentInput) {
                    contentInput = document.createElement('input');
                    contentInput.type = 'hidden';
                    contentInput.name = 'content';
                    form.appendChild(contentInput);
                }
                contentInput.value = JSON.stringify(content);
                
            } else if (sectionKey === 'map') {
                const content = {
                    embed_url: form.querySelector('textarea[name="map_embed_url"]').value,
                    title: form.querySelector('input[name="map_title"]').value
                };
                
                // Create hidden input for content
                let contentInput = form.querySelector('input[name="content"]');
                if (!contentInput) {
                    contentInput = document.createElement('input');
                    contentInput.type = 'hidden';
                    contentInput.name = 'content';
                    form.appendChild(contentInput);
                }
                contentInput.value = JSON.stringify(content);
            }
        });
    });
    
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
});
</script>

<?php include '../includes/footer.php'; ?>