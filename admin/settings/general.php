<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'General Settings';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Settings', 'url' => '#'],
    ['title' => 'General Settings']
];

$success_message = '';
$error_message = '';

// Handle form submission
if ($_POST && isset($_POST['save_settings'])) {
    $settings = [
        'site_title' => sanitize_input($_POST['site_title'] ?? ''),
        'site_tagline' => sanitize_input($_POST['site_tagline'] ?? ''),
        'site_description' => sanitize_input($_POST['site_description'] ?? ''),
        'contact_phone_primary' => sanitize_input($_POST['contact_phone_primary'] ?? ''),
        'contact_phone_secondary' => sanitize_input($_POST['contact_phone_secondary'] ?? ''),
        'contact_email' => sanitize_input($_POST['contact_email'] ?? ''),
        'contact_address' => sanitize_input($_POST['contact_address'] ?? ''),
        'social_facebook' => sanitize_input($_POST['social_facebook'] ?? ''),
        'social_instagram' => sanitize_input($_POST['social_instagram'] ?? ''),
        'social_twitter' => sanitize_input($_POST['social_twitter'] ?? ''),
        'social_tiktok' => sanitize_input($_POST['social_tiktok'] ?? ''),
    ];
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        foreach ($settings as $key => $value) {
            $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value, category) VALUES (?, ?, 'general') ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $stmt->execute([$key, $value]);
        }
        
        $success_message = "Settings saved successfully!";
        
    } catch (Exception $e) {
        $error_message = "Error saving settings: " . $e->getMessage();
    }
}

// Get current settings
$current_settings = [];
try {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT setting_key, setting_value FROM site_settings WHERE category = 'general'");
    $stmt->execute();
    $settings_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($settings_data as $setting) {
        $current_settings[$setting['setting_key']] = $setting['setting_value'];
    }
} catch (Exception $e) {
    // Use default values if database error
}

// Default values
$defaults = [
    'site_title' => SITE_NAME,
    'site_tagline' => SITE_TAGLINE,
    'site_description' => DEFAULT_META_DESCRIPTION,
    'contact_phone_primary' => CONTACT_PHONE_PRIMARY,
    'contact_phone_secondary' => CONTACT_PHONE_SECONDARY,
    'contact_email' => CONTACT_EMAIL,
    'contact_address' => CONTACT_ADDRESS,
    'social_facebook' => SOCIAL_FACEBOOK,
    'social_instagram' => SOCIAL_INSTAGRAM,
    'social_twitter' => SOCIAL_TWITTER,
    'social_tiktok' => SOCIAL_TIKTOK,
];

include '../includes/header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">General Settings</h2>
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
        <div class="form-section">
            <h3 class="form-section-title">Site Information</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="site_title" class="form-label">Site Title</label>
                    <input type="text" id="site_title" name="site_title" class="form-input" 
                           value="<?php echo htmlspecialchars($current_settings['site_title'] ?? $defaults['site_title']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="site_tagline" class="form-label">Site Tagline</label>
                    <input type="text" id="site_tagline" name="site_tagline" class="form-input" 
                           value="<?php echo htmlspecialchars($current_settings['site_tagline'] ?? $defaults['site_tagline']); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="site_description" class="form-label">Site Description</label>
                <textarea id="site_description" name="site_description" class="form-textarea" rows="3"><?php echo htmlspecialchars($current_settings['site_description'] ?? $defaults['site_description']); ?></textarea>
            </div>
        </div>
        
        <div class="form-section">
            <h3 class="form-section-title">Contact Information</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="contact_phone_primary" class="form-label">Primary Phone</label>
                    <input type="text" id="contact_phone_primary" name="contact_phone_primary" class="form-input" 
                           value="<?php echo htmlspecialchars($current_settings['contact_phone_primary'] ?? $defaults['contact_phone_primary']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="contact_phone_secondary" class="form-label">Secondary Phone</label>
                    <input type="text" id="contact_phone_secondary" name="contact_phone_secondary" class="form-input" 
                           value="<?php echo htmlspecialchars($current_settings['contact_phone_secondary'] ?? $defaults['contact_phone_secondary']); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="contact_email" class="form-label">Contact Email</label>
                <input type="email" id="contact_email" name="contact_email" class="form-input" 
                       value="<?php echo htmlspecialchars($current_settings['contact_email'] ?? $defaults['contact_email']); ?>">
            </div>
            
            <div class="form-group">
                <label for="contact_address" class="form-label">Address</label>
                <textarea id="contact_address" name="contact_address" class="form-textarea" rows="2"><?php echo htmlspecialchars($current_settings['contact_address'] ?? $defaults['contact_address']); ?></textarea>
            </div>
        </div>
        
        <div class="form-section">
            <h3 class="form-section-title">Social Media</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="social_facebook" class="form-label">Facebook Username</label>
                    <input type="text" id="social_facebook" name="social_facebook" class="form-input" 
                           value="<?php echo htmlspecialchars($current_settings['social_facebook'] ?? $defaults['social_facebook']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="social_instagram" class="form-label">Instagram Username</label>
                    <input type="text" id="social_instagram" name="social_instagram" class="form-input" 
                           value="<?php echo htmlspecialchars($current_settings['social_instagram'] ?? $defaults['social_instagram']); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="social_twitter" class="form-label">Twitter Username</label>
                    <input type="text" id="social_twitter" name="social_twitter" class="form-input" 
                           value="<?php echo htmlspecialchars($current_settings['social_twitter'] ?? $defaults['social_twitter']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="social_tiktok" class="form-label">TikTok Username</label>
                    <input type="text" id="social_tiktok" name="social_tiktok" class="form-input" 
                           value="<?php echo htmlspecialchars($current_settings['social_tiktok'] ?? $defaults['social_tiktok']); ?>">
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" name="save_settings" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>