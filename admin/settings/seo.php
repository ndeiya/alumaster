<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'SEO Settings';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Settings', 'url' => '#'],
    ['title' => 'SEO Settings']
];

$success_message = '';
$error_message = '';

// Handle form submission
if ($_POST && isset($_POST['save_seo_settings'])) {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        // SEO settings to save
        $seo_settings = [
            'site_title' => sanitize_input($_POST['site_title'] ?? ''),
            'site_tagline' => sanitize_input($_POST['site_tagline'] ?? ''),
            'site_description' => sanitize_input($_POST['site_description'] ?? ''),
            'meta_keywords' => sanitize_input($_POST['meta_keywords'] ?? ''),
            'google_analytics' => sanitize_input($_POST['google_analytics'] ?? ''),
            'facebook_pixel' => sanitize_input($_POST['facebook_pixel'] ?? ''),
            'google_site_verification' => sanitize_input($_POST['google_site_verification'] ?? ''),
            'bing_site_verification' => sanitize_input($_POST['bing_site_verification'] ?? ''),
            'robots_txt' => sanitize_input($_POST['robots_txt'] ?? ''),
            'canonical_url' => sanitize_input($_POST['canonical_url'] ?? ''),
            'og_image' => sanitize_input($_POST['og_image'] ?? ''),
            'twitter_handle' => sanitize_input($_POST['twitter_handle'] ?? ''),
            'facebook_app_id' => sanitize_input($_POST['facebook_app_id'] ?? ''),
            'schema_organization' => sanitize_input($_POST['schema_organization'] ?? ''),
            'sitemap_enabled' => isset($_POST['sitemap_enabled']) ? '1' : '0',
            'noindex_enabled' => isset($_POST['noindex_enabled']) ? '1' : '0'
        ];
        
        // Save each setting
        foreach ($seo_settings as $key => $value) {
            $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value, category) VALUES (?, ?, 'seo') ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $stmt->execute([$key, $value]);
        }
        
        $success_message = "SEO settings saved successfully!";
        
    } catch (Exception $e) {
        $error_message = "Error saving SEO settings: " . $e->getMessage();
    }
}

// Get current SEO settings
$current_settings = [];
try {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT setting_key, setting_value FROM site_settings WHERE category = 'seo'");
    $stmt->execute();
    $settings_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($settings_data as $setting) {
        $current_settings[$setting['setting_key']] = $setting['setting_value'];
    }
} catch (Exception $e) {
    // Use defaults if database error
}

// Default values
$defaults = [
    'site_title' => 'AluMaster Aluminum System',
    'site_tagline' => 'Where Quality Meets Affordability',
    'site_description' => 'Professional aluminum and glass solutions in Ghana. Alucobond cladding, curtain walls, spider glass, and more. Quality meets affordability.',
    'meta_keywords' => 'aluminum, glass, cladding, curtain wall, spider glass, Ghana, construction, architecture',
    'google_analytics' => '',
    'facebook_pixel' => '',
    'google_site_verification' => '',
    'bing_site_verification' => '',
    'robots_txt' => "User-agent: *\nDisallow: /admin/\nDisallow: /database/\nDisallow: /includes/\nAllow: /\n\nSitemap: https://www.alumastergh.com/sitemap.xml",
    'canonical_url' => 'https://www.alumastergh.com',
    'og_image' => 'assets/images/og-image.jpg',
    'twitter_handle' => '@alumaster75',
    'facebook_app_id' => '',
    'schema_organization' => '',
    'sitemap_enabled' => '1',
    'noindex_enabled' => '0'
];

// Merge current settings with defaults
foreach ($defaults as $key => $default_value) {
    if (!isset($current_settings[$key])) {
        $current_settings[$key] = $default_value;
    }
}

include '../includes/header.php';
?>

<?php if (!empty($success_message)): ?>
    <div class="alert alert-success">
        <div class="alert-icon">
            <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <div class="alert-content">
            <?php echo $success_message; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="alert alert-error">
        <div class="alert-icon">
            <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="alert-content">
            <?php echo $error_message; ?>
        </div>
    </div>
<?php endif; ?>

<form method="POST" class="admin-form">
    <div class="admin-grid">
        <!-- Basic SEO Settings -->
        <div class="admin-card">
            <div class="card-header">
                <h2 class="card-title">Basic SEO Settings</h2>
            </div>
            
            <div class="card-content">
                <div class="form-group">
                <label for="site_title" class="form-label">Site Title</label>
                <input type="text" id="site_title" name="site_title" class="form-input" 
                       value="<?php echo htmlspecialchars($current_settings['site_title']); ?>"
                       placeholder="AluMaster Aluminum System">
                <div class="form-help">The main title of your website (appears in browser tabs and search results)</div>
            </div>

            <div class="form-group">
                <label for="site_tagline" class="form-label">Site Tagline</label>
                <input type="text" id="site_tagline" name="site_tagline" class="form-input" 
                       value="<?php echo htmlspecialchars($current_settings['site_tagline']); ?>"
                       placeholder="Where Quality Meets Affordability">
                <div class="form-help">A short phrase that describes your business</div>
            </div>

            <div class="form-group">
                <label for="site_description" class="form-label">Site Description</label>
                <textarea id="site_description" name="site_description" class="form-textarea" rows="4"
                          placeholder="Professional aluminum and glass solutions in Ghana..."><?php echo htmlspecialchars($current_settings['site_description']); ?></textarea>
                <div class="form-help">Default meta description for your website (150-160 characters recommended)</div>
            </div>

            <div class="form-group">
                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                <input type="text" id="meta_keywords" name="meta_keywords" class="form-input" 
                       value="<?php echo htmlspecialchars($current_settings['meta_keywords']); ?>"
                       placeholder="aluminum, glass, cladding, curtain wall, spider glass, Ghana">
                <div class="form-help">Comma-separated keywords relevant to your business</div>
            </div>

            <div class="form-group">
                <label for="canonical_url" class="form-label">Canonical URL</label>
                <input type="url" id="canonical_url" name="canonical_url" class="form-input" 
                       value="<?php echo htmlspecialchars($current_settings['canonical_url']); ?>"
                       placeholder="https://www.alumastergh.com">
                <div class="form-help">The preferred URL for your website (helps prevent duplicate content issues)</div>
                </div>
            </div>
        </div>

        <!-- Social Media & Open Graph -->
        <div class="admin-card">
            <div class="card-header">
                <h2 class="card-title">Social Media & Open Graph</h2>
            </div>
            
            <div class="card-content">
                <div class="form-group">
                <label for="og_image" class="form-label">Open Graph Image</label>
                <input type="text" id="og_image" name="og_image" class="form-input" 
                       value="<?php echo htmlspecialchars($current_settings['og_image']); ?>"
                       placeholder="assets/images/og-image.jpg">
                <div class="form-help">Image that appears when your site is shared on social media (1200x630px recommended)</div>
            </div>

            <div class="form-group">
                <label for="twitter_handle" class="form-label">Twitter Handle</label>
                <input type="text" id="twitter_handle" name="twitter_handle" class="form-input" 
                       value="<?php echo htmlspecialchars($current_settings['twitter_handle']); ?>"
                       placeholder="@alumaster75">
                <div class="form-help">Your Twitter username (include the @ symbol)</div>
            </div>

            <div class="form-group">
                <label for="facebook_app_id" class="form-label">Facebook App ID</label>
                <input type="text" id="facebook_app_id" name="facebook_app_id" class="form-input" 
                       value="<?php echo htmlspecialchars($current_settings['facebook_app_id']); ?>"
                       placeholder="123456789012345">
                <div class="form-help">Facebook App ID for better social media integration (optional)</div>
                </div>
            </div>
        </div>

        <!-- Analytics & Tracking -->
        <div class="admin-card">
            <div class="card-header">
                <h2 class="card-title">Analytics & Tracking</h2>
            </div>
            
            <div class="card-content">
                <div class="form-group">
                <label for="google_analytics" class="form-label">Google Analytics Tracking ID</label>
                <input type="text" id="google_analytics" name="google_analytics" class="form-input" 
                       value="<?php echo htmlspecialchars($current_settings['google_analytics']); ?>"
                       placeholder="G-XXXXXXXXXX or UA-XXXXXXXX-X">
                <div class="form-help">Your Google Analytics tracking code (GA4 or Universal Analytics)</div>
            </div>

            <div class="form-group">
                <label for="facebook_pixel" class="form-label">Facebook Pixel ID</label>
                <input type="text" id="facebook_pixel" name="facebook_pixel" class="form-input" 
                       value="<?php echo htmlspecialchars($current_settings['facebook_pixel']); ?>"
                       placeholder="123456789012345">
                <div class="form-help">Facebook Pixel ID for tracking conversions and creating audiences</div>
                </div>
            </div>
        </div>

        <!-- Search Engine Verification -->
        <div class="admin-card">
            <div class="card-header">
                <h2 class="card-title">Search Engine Verification</h2>
            </div>
            
            <div class="card-content">
            
            <div class="form-group">
                <label for="google_site_verification" class="form-label">Google Site Verification</label>
                <input type="text" id="google_site_verification" name="google_site_verification" class="form-input" 
                       value="<?php echo htmlspecialchars($current_settings['google_site_verification']); ?>"
                       placeholder="abcdefghijklmnopqrstuvwxyz123456789">
                <div class="form-help">Google Search Console verification meta tag content</div>
            </div>

            <div class="form-group">
                <label for="bing_site_verification" class="form-label">Bing Site Verification</label>
                <input type="text" id="bing_site_verification" name="bing_site_verification" class="form-input" 
                       value="<?php echo htmlspecialchars($current_settings['bing_site_verification']); ?>"
                       placeholder="abcdefghijklmnopqrstuvwxyz123456789">
                <div class="form-help">Bing Webmaster Tools verification meta tag content</div>
                </div>
            </div>
        </div>

        <!-- Advanced Settings -->
        <div class="admin-card">
            <div class="card-header">
                <h2 class="card-title">Advanced SEO Settings</h2>
            </div>
            
            <div class="card-content">
                <div class="form-group">
                <label for="robots_txt" class="form-label">Robots.txt Content</label>
                <textarea id="robots_txt" name="robots_txt" class="form-textarea" rows="8"
                          placeholder="User-agent: *&#10;Disallow: /admin/&#10;Allow: /"><?php echo htmlspecialchars($current_settings['robots_txt']); ?></textarea>
                <div class="form-help">Instructions for search engine crawlers</div>
            </div>

            <div class="form-group">
                <label for="schema_organization" class="form-label">Schema.org Organization JSON-LD</label>
                <textarea id="schema_organization" name="schema_organization" class="form-textarea" rows="6"
                          placeholder='{"@context": "https://schema.org", "@type": "Organization", "name": "AluMaster"}'><?php echo htmlspecialchars($current_settings['schema_organization']); ?></textarea>
                <div class="form-help">Custom Schema.org structured data for your organization (JSON-LD format)</div>
            </div>

            <div class="form-group">
                <label class="form-label">SEO Options</label>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="sitemap_enabled" value="1" <?php echo $current_settings['sitemap_enabled'] ? 'checked' : ''; ?>>
                        <span class="checkbox-text">Enable XML Sitemap Generation</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="noindex_enabled" value="1" <?php echo $current_settings['noindex_enabled'] ? 'checked' : ''; ?>>
                        <span class="checkbox-text">Add noindex to development/staging sites</span>
                    </label>
                </div>
                <div class="form-help">Additional SEO configuration options</div>
                </div>
            </div>
        </div>

        <!-- SEO Tools & Tips -->
        <div class="admin-card">
            <div class="card-header">
                <h2 class="card-title">SEO Tools & Resources</h2>
            </div>
            
            <div class="card-content">
                <div class="seo-tools">
                <h3>Useful SEO Tools</h3>
                <ul class="tool-list">
                    <li><a href="https://search.google.com/search-console" target="_blank">Google Search Console</a> - Monitor your site's search performance</li>
                    <li><a href="https://www.google.com/webmasters/tools/home" target="_blank">Google Webmaster Tools</a> - Submit sitemaps and monitor crawling</li>
                    <li><a href="https://www.bing.com/webmasters" target="_blank">Bing Webmaster Tools</a> - Bing search engine optimization</li>
                    <li><a href="https://developers.facebook.com/tools/debug/" target="_blank">Facebook Sharing Debugger</a> - Test Open Graph tags</li>
                    <li><a href="https://cards-dev.twitter.com/validator" target="_blank">Twitter Card Validator</a> - Test Twitter card markup</li>
                </ul>

                <h3>SEO Best Practices</h3>
                <ul class="tips-list">
                    <li>Keep your site title under 60 characters</li>
                    <li>Write meta descriptions between 150-160 characters</li>
                    <li>Use descriptive, keyword-rich URLs</li>
                    <li>Optimize images with alt text and proper file names</li>
                    <li>Ensure your site loads quickly (under 3 seconds)</li>
                    <li>Make your site mobile-friendly and responsive</li>
                    <li>Create high-quality, original content regularly</li>
                    <li>Build quality backlinks from reputable sites</li>
                </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" name="save_seo_settings" class="btn btn-primary">
            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
            </svg>
            Save SEO Settings
        </button>
        <a href="../index.php" class="btn btn-outline">Back to Dashboard</a>
    </div>
</form>

<style>
.seo-tools h3 {
    color: #374151;
    font-size: 1.1rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.seo-tools h3:first-child {
    margin-top: 0;
}

.tool-list, .tips-list {
    margin-bottom: 1.5rem;
    padding-left: 1.5rem;
}

.tool-list li, .tips-list li {
    margin-bottom: 0.5rem;
    line-height: 1.5;
}

.tool-list a {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}

.tool-list a:hover {
    text-decoration: underline;
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    margin: 0;
}

.checkbox-text {
    font-size: 0.875rem;
    color: #374151;
}
</style>

<?php include '../includes/footer.php'; ?>