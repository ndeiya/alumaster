<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        throw new Exception("Could not connect to database");
    }

    echo "Current navigation items:\n";
    $stmt = $pdo->query("SELECT * FROM navigation_items ORDER BY sort_order");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($items as $item) {
        echo "ID: {$item['id']} - Title: {$item['title']} - URL: {$item['url']}\n";
    }
    
    echo "\nUpdating navigation URLs to point to direct files...\n";
    
    // Update navigation items to point to direct files instead of page.php
    $updates = [
        ['title' => 'Home', 'url' => 'index.php'],
        ['title' => 'About', 'url' => 'about.php'],
        ['title' => 'Services', 'url' => 'services.php'],
        ['title' => 'Contact', 'url' => 'contact.php']
    ];
    
    foreach ($updates as $update) {
        $stmt = $pdo->prepare("UPDATE navigation_items SET url = ? WHERE title = ?");
        $stmt->execute([$update['url'], $update['title']]);
        echo "Updated {$update['title']} to point to {$update['url']}\n";
    }
    
    echo "\nUpdated navigation items:\n";
    $stmt = $pdo->query("SELECT * FROM navigation_items ORDER BY sort_order");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($items as $item) {
        echo "ID: {$item['id']} - Title: {$item['title']} - URL: {$item['url']}\n";
    }
    
    echo "\nNavigation update completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>