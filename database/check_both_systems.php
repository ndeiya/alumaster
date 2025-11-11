<?php
/**
 * Check both homepage systems to understand which is in use
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        die("❌ Database connection failed\n");
    }
    
    echo "=== HOMEPAGE SYSTEMS CHECK ===\n\n";
    
    // Check System 1: homepage_sections table (used by index.php)
    echo "1. HOMEPAGE_SECTIONS TABLE (Used by index.php)\n";
    echo str_repeat("-", 50) . "\n";
    
    $stmt = $pdo->query("SELECT section_key, section_name, is_active FROM homepage_sections ORDER BY sort_order ASC");
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($sections)) {
        foreach ($sections as $section) {
            $status = $section['is_active'] ? '✓ Active' : '✗ Inactive';
            echo "  {$section['section_name']} ({$section['section_key']}) - {$status}\n";
        }
        
        // Get hero content
        $stmt = $pdo->query("SELECT content FROM homepage_sections WHERE section_key = 'hero'");
        $hero = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($hero) {
            $content = json_decode($hero['content'], true);
            echo "\n  Hero Title: " . ($content['title'] ?? 'N/A') . "\n";
            echo "  Hero Highlight: " . ($content['highlight'] ?? 'N/A') . "\n";
        }
    } else {
        echo "  ❌ No sections found\n";
    }
    
    echo "\n\n";
    
    // Check System 2: pages table (CMS pages)
    echo "2. PAGES TABLE (CMS System)\n";
    echo str_repeat("-", 50) . "\n";
    
    $stmt = $pdo->query("SELECT id, title, slug, is_homepage, status FROM pages ORDER BY is_homepage DESC, id ASC LIMIT 5");
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($pages)) {
        foreach ($pages as $page) {
            $homepage_flag = $page['is_homepage'] ? '[HOMEPAGE]' : '';
            $status = $page['status'];
            echo "  ID {$page['id']}: {$page['title']} (/{$page['slug']}) {$homepage_flag} - {$status}\n";
        }
        
        // Get homepage page content
        $stmt = $pdo->query("SELECT title, content FROM pages WHERE is_homepage = 1 LIMIT 1");
        $homepage_page = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($homepage_page) {
            echo "\n  Homepage Page Title: " . $homepage_page['title'] . "\n";
            echo "  Content Preview: " . substr(strip_tags($homepage_page['content']), 0, 100) . "...\n";
        }
    } else {
        echo "  ❌ No pages found\n";
    }
    
    echo "\n\n";
    
    // Check what index.php actually uses
    echo "3. WHAT INDEX.PHP USES\n";
    echo str_repeat("-", 50) . "\n";
    
    $index_content = file_get_contents(__DIR__ . '/../index.php');
    
    if (strpos($index_content, 'homepage_sections') !== false) {
        echo "  ✓ index.php uses HOMEPAGE_SECTIONS table\n";
        echo "  ✓ Managed via: admin/pages/homepage.php\n";
        echo "  ✓ Video embedding: IMPLEMENTED\n";
    } elseif (strpos($index_content, 'FROM pages') !== false) {
        echo "  ✗ index.php uses PAGES table\n";
        echo "  ✗ Managed via: admin/pages/edit.php?id=X\n";
        echo "  ✗ Video embedding: NOT AVAILABLE\n";
    } else {
        echo "  ? index.php uses static content\n";
    }
    
    echo "\n\n";
    
    echo "=== CONCLUSION ===\n";
    echo "The actual homepage (index.php) uses: homepage_sections table\n";
    echo "Edit it via: Admin → Pages → Homepage\n";
    echo "The 'pages' table entry is NOT used by index.php\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
