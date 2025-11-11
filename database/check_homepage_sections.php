<?php
/**
 * Check and display homepage sections status
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        die("❌ Database connection failed\n");
    }
    
    echo "✓ Database connected\n\n";
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'homepage_sections'");
    $table_exists = $stmt->fetch();
    
    if (!$table_exists) {
        echo "❌ Table 'homepage_sections' does not exist\n";
        echo "Run: php database/setup_homepage.php\n";
        exit;
    }
    
    echo "✓ Table 'homepage_sections' exists\n\n";
    
    // Get all sections
    $stmt = $pdo->query("SELECT section_key, section_name, is_active FROM homepage_sections ORDER BY sort_order ASC");
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($sections)) {
        echo "❌ No sections found in database\n";
        echo "Run: php database/setup_homepage.php\n";
        exit;
    }
    
    echo "✓ Found " . count($sections) . " sections:\n\n";
    
    foreach ($sections as $section) {
        $status = $section['is_active'] ? '✓ Active' : '✗ Inactive';
        echo "  - {$section['section_name']} ({$section['section_key']}) - {$status}\n";
    }
    
    echo "\n✓ Homepage sections are configured correctly!\n";
    echo "Access admin panel: admin/pages/homepage.php\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
