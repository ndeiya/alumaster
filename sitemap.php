<?php
/**
 * Dynamic XML Sitemap Generator
 * Automatically includes all pages, services, and projects from database
 */

require_once 'includes/config.php';
require_once 'includes/database.php';

// Set XML header
header('Content-Type: application/xml; charset=utf-8');

// Get site URL from config or use current domain
$site_url = rtrim(SITE_URL ?? 'https://' . $_SERVER['HTTP_HOST'], '/');

// Start XML output
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Helper function to add URL
function addUrl($loc, $lastmod = null, $changefreq = 'weekly', $priority = '0.5') {
    global $site_url;
    echo '<url>';
    echo '<loc>' . htmlspecialchars($site_url . $loc) . '</loc>';
    if ($lastmod) {
        echo '<lastmod>' . date('Y-m-d', strtotime($lastmod)) . '</lastmod>';
    }
    echo '<changefreq>' . $changefreq . '</changefreq>';
    echo '<priority>' . $priority . '</priority>';
    echo '</url>';
}

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        // 1. Homepage (highest priority)
        addUrl('/', date('Y-m-d'), 'daily', '1.0');
        
        // 2. Main static pages
        $static_pages = [
            '/about.php' => ['changefreq' => 'monthly', 'priority' => '0.8'],
            '/services.php' => ['changefreq' => 'weekly', 'priority' => '0.9'],
            '/projects.php' => ['changefreq' => 'weekly', 'priority' => '0.9'],
            '/contact.php' => ['changefreq' => 'monthly', 'priority' => '0.8'],
        ];
        
        foreach ($static_pages as $page => $settings) {
            if (file_exists(__DIR__ . $page)) {
                $lastmod = date('Y-m-d', filemtime(__DIR__ . $page));
                addUrl($page, $lastmod, $settings['changefreq'], $settings['priority']);
            }
        }
        
        // 3. Dynamic pages from database
        $stmt = $pdo->query("SELECT slug, updated_at FROM pages WHERE status = 'published' AND is_homepage = 0 ORDER BY updated_at DESC");
        $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($pages as $page) {
            addUrl('/' . $page['slug'] . '.php', $page['updated_at'], 'monthly', '0.7');
        }
        
        // 4. Services
        $stmt = $pdo->query("SELECT id, slug, updated_at FROM services WHERE status = 'active' ORDER BY updated_at DESC");
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($services as $service) {
            addUrl('/service-detail.php?id=' . $service['id'], $service['updated_at'], 'monthly', '0.7');
            // Also add slug-based URL if you implement it
            // addUrl('/services/' . $service['slug'], $service['updated_at'], 'monthly', '0.7');
        }
        
        // 5. Projects
        $stmt = $pdo->query("SELECT id, slug, updated_at FROM projects WHERE status = 'active' ORDER BY updated_at DESC");
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($projects as $project) {
            // Add project detail pages if you have them
            addUrl('/projects.php?id=' . $project['id'], $project['updated_at'], 'monthly', '0.6');
        }
        
    } else {
        // Fallback if database connection fails - add static pages only
        addUrl('/', date('Y-m-d'), 'daily', '1.0');
        addUrl('/about.php', date('Y-m-d'), 'monthly', '0.8');
        addUrl('/services.php', date('Y-m-d'), 'weekly', '0.9');
        addUrl('/projects.php', date('Y-m-d'), 'weekly', '0.9');
        addUrl('/contact.php', date('Y-m-d'), 'monthly', '0.8');
    }
    
} catch (Exception $e) {
    // Fallback if any error occurs
    addUrl('/', date('Y-m-d'), 'daily', '1.0');
    addUrl('/about.php', date('Y-m-d'), 'monthly', '0.8');
    addUrl('/services.php', date('Y-m-d'), 'weekly', '0.9');
    addUrl('/projects.php', date('Y-m-d'), 'weekly', '0.9');
    addUrl('/contact.php', date('Y-m-d'), 'monthly', '0.8');
}

// Close XML
echo '</urlset>';
?>
