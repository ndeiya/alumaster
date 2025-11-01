<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "Fixing navigation URLs...\n";
    
    // Update navigation items with correct URLs
    $updates = [
        [1, 'index.php'],  // Home
        [2, 'about.php'],  // About - use existing about.php
        [3, 'services.php'], // Services
        [4, 'contact.php']   // Contact
    ];
    
    $stmt = $conn->prepare("UPDATE navigation_items SET url = ? WHERE id = ?");
    
    foreach ($updates as $update) {
        $stmt->execute([$update[1], $update[0]]);
        echo "Updated item {$update[0]} to URL: {$update[1]}\n";
    }
    
    echo "Navigation URLs fixed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>