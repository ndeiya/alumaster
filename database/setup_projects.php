<?php
require_once '../includes/config.php';
require_once '../includes/database.php';

$db = new Database();
$pdo = $db->getConnection();

echo "<!DOCTYPE html><html><head><title>Setup Projects</title></head><body>";
echo "<h2>Setting up Projects Tables and Data</h2>";

// Create tables
$sql = file_get_contents('create_projects_tables.sql');
$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $statement) {
    if (!empty($statement)) {
        try {
            $pdo->exec($statement);
            echo "✓ Executed: " . substr($statement, 0, 50) . "...<br>";
        } catch (PDOException $e) {
            echo "✗ Error: " . $e->getMessage() . "<br>";
        }
    }
}

// Scan project folders and populate database
$projectsDir = '../assets/images/projects/';
$folders = array_diff(scandir($projectsDir), array('.', '..', 'alumaster_projects_page'));

$displayOrder = 1;
foreach ($folders as $folder) {
    $folderPath = $projectsDir . $folder;
    
    if (!is_dir($folderPath)) continue;
    
    $detailsFile = $folderPath . '/project_details.txt';
    
    if (!file_exists($detailsFile)) {
        echo "⚠ Skipping $folder - no project_details.txt found<br>";
        continue;
    }
    
    // Parse project details
    $content = file_get_contents($detailsFile);
    $lines = explode("\n", $content);
    
    $name = '';
    $location = '';
    $scope = '';
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        if (strpos($line, 'Project:') === 0) {
            $name = trim(str_replace('Project:', '', $line));
        } elseif (strpos($line, 'Location:') === 0) {
            $location = trim(str_replace('Location:', '', $line));
        } elseif (strpos($line, 'Scope Offered:') === 0 || strpos($line, 'Scope:') === 0) {
            $scope = trim(str_replace(['Scope Offered:', 'Scope:'], '', $line));
        }
    }
    
    if (empty($name)) {
        $name = $folder;
    }
    
    // Find thumbnail (first after image)
    $afterDir = $folderPath . '/After';
    $thumbnail = '';
    
    if (is_dir($afterDir)) {
        $afterImages = array_diff(scandir($afterDir), array('.', '..'));
        $afterImages = array_values(array_filter($afterImages, function($file) {
            return preg_match('/\.(jpg|jpeg|png|gif)$/i', $file);
        }));
        
        if (!empty($afterImages)) {
            sort($afterImages);
            $thumbnail = 'assets/images/projects/' . $folder . '/After/' . $afterImages[0];
        }
    }
    
    // Set first project as featured
    $isFeatured = ($displayOrder === 1) ? 1 : 0;
    
    // Insert project
    try {
        $stmt = $pdo->prepare("INSERT INTO projects (name, location, scope, thumbnail, is_featured, display_order, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$name, $location, $scope, $thumbnail, $isFeatured, $displayOrder]);
        $projectId = $pdo->lastInsertId();
        echo "✓ Added project: $name<br>";
        
        // Insert before images
        $beforeDir = $folderPath . '/Before';
        if (is_dir($beforeDir)) {
            $beforeImages = array_diff(scandir($beforeDir), array('.', '..'));
            $beforeImages = array_values(array_filter($beforeImages, function($file) {
                return preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file);
            }));
            
            sort($beforeImages);
            $imgOrder = 1;
            foreach ($beforeImages as $image) {
                $imagePath = 'assets/images/projects/' . $folder . '/Before/' . $image;
                $imgStmt = $pdo->prepare("INSERT INTO project_images (project_id, image_path, image_type, display_order) VALUES (?, ?, 'before', ?)");
                $imgStmt->execute([$projectId, $imagePath, $imgOrder]);
                $imgOrder++;
            }
            echo "&nbsp;&nbsp;→ Added " . count($beforeImages) . " before images<br>";
        }
        
        // Insert after images
        if (is_dir($afterDir)) {
            $afterImages = array_diff(scandir($afterDir), array('.', '..'));
            $afterImages = array_values(array_filter($afterImages, function($file) {
                return preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file);
            }));
            
            sort($afterImages);
            $imgOrder = 1;
            foreach ($afterImages as $image) {
                $imagePath = 'assets/images/projects/' . $folder . '/After/' . $image;
                $imgStmt = $pdo->prepare("INSERT INTO project_images (project_id, image_path, image_type, display_order) VALUES (?, ?, 'after', ?)");
                $imgStmt->execute([$projectId, $imagePath, $imgOrder]);
                $imgOrder++;
            }
            echo "&nbsp;&nbsp;→ Added " . count($afterImages) . " after images<br>";
        }
    } catch (PDOException $e) {
        echo "✗ Error adding project $name: " . $e->getMessage() . "<br>";
    }
    
    $displayOrder++;
}

// Add Projects to navigation
try {
    // Check if navigation_items table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'navigation_items'");
    if ($stmt->rowCount() > 0) {
        // Check if Projects already exists
        $stmt = $pdo->prepare("SELECT id FROM navigation_items WHERE title = 'Projects' OR url LIKE '%projects.php%'");
        $stmt->execute();
        $result = $stmt->fetch();
        
        if (!$result) {
            // Get the main menu ID
            $stmt = $pdo->prepare("SELECT id FROM navigation_menus WHERE location = 'header' LIMIT 1");
            $stmt->execute();
            $menu = $stmt->fetch();
            
            if ($menu) {
                // Get max sort order
                $stmt = $pdo->prepare("SELECT MAX(sort_order) as max_order FROM navigation_items WHERE menu_id = ?");
                $stmt->execute([$menu['id']]);
                $maxOrder = $stmt->fetchColumn() ?: 0;
                
                // Insert Projects navigation item
                $stmt = $pdo->prepare("INSERT INTO navigation_items (menu_id, title, url, sort_order, is_active) VALUES (?, 'Projects', '/projects.php', ?, 1)");
                $stmt->execute([$menu['id'], $maxOrder + 1]);
                echo "<br>✓ Added 'Projects' to navigation<br>";
            } else {
                echo "<br>⚠ No header menu found. Please add Projects manually through Admin > Navigation<br>";
            }
        } else {
            echo "<br>ℹ 'Projects' already exists in navigation<br>";
        }
    } else {
        echo "<br>ℹ Navigation system not found. Projects page is ready but you'll need to add the link manually.<br>";
    }
} catch (PDOException $e) {
    echo "<br>⚠ Could not add to navigation (non-critical): " . $e->getMessage() . "<br>";
    echo "<br>ℹ You can add Projects to navigation manually through Admin > Navigation > Add Menu<br>";
}

echo "<br><strong>Setup Complete!</strong>";
echo "<p><a href='../projects.php'>View Projects Page</a> | <a href='../admin/projects/list.php'>Manage Projects</a></p>";
echo "</body></html>";
?>
