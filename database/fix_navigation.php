<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        throw new Exception("Could not connect to database");
    }

    echo "Current navigation items with page_id:\n";
    $stmt = $pdo->query("SELECT id, title, url, page_id FROM navigation_items ORDER BY sort_order");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($items as $item) {
        echo "ID: {$item['id']} - Title: {$item['title']} - URL: {$item['url']} - Page ID: " . ($item['page_id'] ?? 'NULL') . "\n";
    }
    
    echo "\nClearing page_id values to prevent URL override...\n";
    
    // Clear page_id values so URLs won't be overridden
    $stmt = $pdo->prepare("UPDATE navigation_items SET page_id = NULL");
    $stmt->execute();
    
    echo "Cleared all page_id values.\n";
    
    echo "\nUpdated navigation items:\n";
    $stmt = $pdo->query("SELECT id, title, url, page_id FROM navigation_items ORDER BY sort_order");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($items as $item) {
        echo "ID: {$item['id']} - Title: {$item['title']} - URL: {$item['url']} - Page ID: " . ($item['page_id'] ?? 'NULL') . "\n";
    }
    
    echo "\nNavigation fix completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>