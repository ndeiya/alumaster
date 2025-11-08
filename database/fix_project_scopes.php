<?php
require_once '../includes/config.php';
require_once '../includes/database.php';

echo "<!DOCTYPE html><html><head><title>Fix Project Scopes</title></head><body>";
echo "<h2>Fixing Project Scopes</h2>";

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Get all projects
    $stmt = $pdo->query("SELECT id, name FROM projects");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $projectsDir = '../assets/images/projects/';
    $folders = array_diff(scandir($projectsDir), ['.', '..', 'alumaster_projects_page']);
    
    foreach ($projects as $project) {
        $projectId = $project['id'];
        $projectName = $project['name'];
        
        // Find matching folder
        $matchedFolder = null;
        foreach ($folders as $folder) {
            if (stripos($folder, $projectName) !== false || stripos($projectName, $folder) !== false) {
                $matchedFolder = $folder;
                break;
            }
        }
        
        if (!$matchedFolder) {
            // Try partial match
            foreach ($folders as $folder) {
                $folderWords = explode(' ', $folder);
                $nameWords = explode(' ', $projectName);
                $matches = 0;
                foreach ($folderWords as $fw) {
                    foreach ($nameWords as $nw) {
                        if (strlen($fw) > 3 && strlen($nw) > 3 && stripos($fw, $nw) !== false) {
                            $matches++;
                        }
                    }
                }
                if ($matches > 0) {
                    $matchedFolder = $folder;
                    break;
                }
            }
        }
        
        if ($matchedFolder) {
            $detailsFile = $projectsDir . $matchedFolder . '/project_details.txt';
            
            if (file_exists($detailsFile)) {
                $content = file_get_contents($detailsFile);
                $lines = explode("\n", $content);
                
                $scope = '';
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (stripos($line, 'Scope Offered:') === 0) {
                        $scope = trim(str_replace('Scope Offered:', '', $line));
                        break;
                    } elseif (stripos($line, 'System Offered:') === 0) {
                        $scope = trim(str_replace('System Offered:', '', $line));
                        break;
                    }
                }
                
                if (!empty($scope)) {
                    $stmt = $pdo->prepare("UPDATE projects SET scope = ? WHERE id = ?");
                    $stmt->execute([$scope, $projectId]);
                    echo "<p>✓ Updated <strong>" . htmlspecialchars($projectName) . "</strong>: " . htmlspecialchars($scope) . "</p>";
                } else {
                    echo "<p style='color:orange;'>⚠ No scope found for <strong>" . htmlspecialchars($projectName) . "</strong> in folder: $matchedFolder</p>";
                }
            } else {
                echo "<p style='color:red;'>✗ No project_details.txt for <strong>" . htmlspecialchars($projectName) . "</strong></p>";
            }
        } else {
            echo "<p style='color:red;'>✗ No matching folder for <strong>" . htmlspecialchars($projectName) . "</strong></p>";
        }
    }
    
    echo "<br><h3 style='color:green;'>✓ Scope update complete!</h3>";
    echo "<p><a href='../projects.php'>View Projects Page</a> | <a href='../admin/projects/list.php'>Manage Projects</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>
