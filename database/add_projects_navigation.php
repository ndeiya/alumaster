<?php
require_once '../includes/config.php';
require_once '../includes/database.php';

echo "<!DOCTYPE html><html><head><title>Add Projects to Navigation</title></head><body>";
echo "<h2>Adding Projects to Navigation</h2>";

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Check if navigation_items table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'navigation_items'");
    if ($stmt->rowCount() > 0) {
        // Check if Projects already exists
        $stmt = $pdo->prepare("SELECT id FROM navigation_items WHERE title = 'Projects' OR url LIKE '%projects.php%'");
        $stmt->execute();
        $result = $stmt->fetch();
        
        if ($result) {
            echo "<p>✓ Projects navigation link already exists!</p>";
        } else {
            // Get the main menu ID
            $stmt = $pdo->prepare("SELECT id, name FROM navigation_menus WHERE location = 'header' LIMIT 1");
            $stmt->execute();
            $menu = $stmt->fetch();
            
            if ($menu) {
                // Get max sort order
                $stmt = $pdo->prepare("SELECT MAX(sort_order) as max_order FROM navigation_items WHERE menu_id = ?");
                $stmt->execute([$menu['id']]);
                $maxOrder = $stmt->fetchColumn() ?: 0;
                $nextOrder = $maxOrder + 1;
                
                // Insert Projects navigation item
                $stmt = $pdo->prepare("INSERT INTO navigation_items (menu_id, title, url, sort_order, is_active) VALUES (?, 'Projects', '/projects.php', ?, 1)");
                $stmt->execute([$menu['id'], $nextOrder]);
                
                echo "<p style='color: green;'>✓ Projects navigation link added successfully!</p>";
                echo "<p><strong>Menu:</strong> {$menu['name']}</p>";
                echo "<p><strong>Sort Order:</strong> $nextOrder</p>";
                echo "<p><a href='../admin/navigation/list.php'>View Navigation</a> | <a href='../projects.php'>View Projects Page</a></p>";
            } else {
                echo "<p style='color: orange;'>⚠ No header menu found.</p>";
                echo "<p>Please add Projects manually through <a href='../admin/navigation/add.php'>Admin > Navigation > Add Menu</a></p>";
            }
        }
    } else {
        echo "<p style='color: orange;'>⚠ Navigation system not found in database.</p>";
        echo "<p>Your projects page is ready at <a href='../projects.php'>projects.php</a></p>";
        echo "<p>You'll need to add the navigation link manually through your admin panel.</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>
