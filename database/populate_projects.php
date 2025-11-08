<?php
require_once '../includes/database.php';

echo "<!DOCTYPE html><html><head><title>Populate Projects</title></head><body>";
echo "<h2>Populating Projects from Folders...</h2>";

$projects_dir = '../assets/images/projects/';

// Skip the alumaster_projects_page folder
$skip_folders = ['alumaster_projects_page'];

try {
    // Get all project folders
    $folders = array_diff(scandir($projects_dir), ['.', '..']);
    
    foreach ($folders as $folder) {
        $folder_path = $projects_dir . $folder;
        
        // Skip if not a directory or in skip list
        if (!is_dir($folder_path) || in_array($folder, $skip_folders)) {
            continue;
        }
        
        echo "<h3>Processing: $folder</h3>";
        
        // Read project_details.txt
        $details_file = $folder_path . '/project_details.txt';
        $name = $folder;
        $location = '';
        $scope = '';
        
        if (file_exists($details_file)) {
            $details = file_get_contents($details_file);
            $lines = explode("\n", trim($details));
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (stripos($line, 'name:') === 0) {
                    $name = trim(substr($line, 5));
                } elseif (stripos($line, 'location:') === 0) {
                    $location = trim(substr($line, 9));
                } elseif (stripos($line, 'scope:') === 0) {
                    $scope = trim(substr($line, 6));
                }
            }
            echo "<p>Details found: Name=$name, Location=$location</p>";
        } else {
            echo "<p>No project_details.txt found, using folder name</p>";
        }
        
        // Find thumbnail from after folder
        $after_folder = $folder_path . '/after';
        $thumbnail = '';
        
        if (is_dir($after_folder)) {
            $after_images = array_diff(scandir($after_folder), ['.', '..']);
            if (!empty($after_images)) {
                $first_image = reset($after_images);
                $thumbnail = 'assets/images/projects/' . $folder . '/after/' . $first_image;
                echo "<p>Thumbnail: $first_image</p>";
            }
        }
        
        // Insert project
        $stmt = $pdo->prepare("INSERT INTO projects (name, location, scope, thumbnail, is_featured, status, display_order) VALUES (?, ?, ?, ?, 0, 'active', 0)");
        $stmt->execute([$name, $location, $scope, $thumbnail]);
        $project_id = $pdo->lastInsertId();
        
        echo "<p>✓ Project created with ID: $project_id</p>";
        
        // Insert before images
        $before_folder = $folder_path . '/before';
        if (is_dir($before_folder)) {
            $before_images = array_diff(scandir($before_folder), ['.', '..']);
            $order = 0;
            foreach ($before_images as $image) {
                if (in_array(strtolower(pathinfo($image, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $image_path = 'assets/images/projects/' . $folder . '/before/' . $image;
                    $stmt = $pdo->prepare("INSERT INTO project_images (project_id, image_path, image_type, display_order) VALUES (?, ?, 'before', ?)");
                    $stmt->execute([$project_id, $image_path, $order++]);
                }
            }
            echo "<p>✓ Added " . count($before_images) . " before images</p>";
        }
        
        // Insert after images
        if (is_dir($after_folder)) {
            $after_images = array_diff(scandir($after_folder), ['.', '..']);
            $order = 0;
            foreach ($after_images as $image) {
                if (in_array(strtolower(pathinfo($image, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $image_path = 'assets/images/projects/' . $folder . '/after/' . $image;
                    $stmt = $pdo->prepare("INSERT INTO project_images (project_id, image_path, image_type, display_order) VALUES (?, ?, 'after', ?)");
                    $stmt->execute([$project_id, $image_path, $order++]);
                }
            }
            echo "<p>✓ Added " . count($after_images) . " after images</p>";
        }
        
        echo "<hr>";
    }
    
    echo "<h3 style='color: green;'>✓ All projects populated successfully!</h3>";
    echo "<p><a href='../admin/projects/list.php'>View Projects in Admin</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>
