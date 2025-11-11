<?php
// Simple diagnostic - no auth required
require_once 'includes/config.php';
require_once 'includes/database.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Video Setup Check</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #059669; font-weight: bold; }
        .error { color: #dc2626; font-weight: bold; }
        .info { color: #2563eb; }
        pre { background: #1f2937; color: #10b981; padding: 15px; border-radius: 4px; overflow-x: auto; }
        h1 { color: #1f2937; }
        h2 { color: #374151; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>🎬 Video Setup Diagnostic</h1>

<?php
// 1. Database Check
echo "<div class='box'>";
echo "<h2>1. Database Connection</h2>";
try {
    $db = new Database();
    $pdo = $db->getConnection();
    if ($pdo) {
        echo "<p class='success'>✓ Connected</p>";
    } else {
        echo "<p class='error'>✗ Failed</p>";
        echo "</div></body></html>";
        exit;
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div></body></html>";
    exit;
}
echo "</div>";

// 2. Hero Section Check
echo "<div class='box'>";
echo "<h2>2. Hero Section Data</h2>";
try {
    $stmt = $pdo->query("SELECT content FROM homepage_sections WHERE section_key = 'hero'");
    $hero = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($hero) {
        echo "<p class='success'>✓ Hero section exists</p>";
        $content = json_decode($hero['content'], true);
        
        echo "<h3>Video Fields Status:</h3>";
        $has_video_url = isset($content['video_url']);
        $has_video_type = isset($content['video_type']);
        $has_show_video = isset($content['show_video']);
        $has_video_autoplay = isset($content['video_autoplay']);
        
        echo "<p>" . ($has_video_url ? "✓" : "✗") . " video_url</p>";
        echo "<p>" . ($has_video_type ? "✓" : "✗") . " video_type</p>";
        echo "<p>" . ($has_show_video ? "✓" : "✗") . " show_video</p>";
        echo "<p>" . ($has_video_autoplay ? "✓" : "✗") . " video_autoplay</p>";
        
        if (!$has_video_url || !$has_video_type || !$has_show_video || !$has_video_autoplay) {
            echo "<div style='background: #fef3c7; padding: 15px; margin-top: 15px; border-left: 4px solid #f59e0b;'>";
            echo "<p><strong>⚠ Action Required:</strong></p>";
            echo "<p>Run this SQL in phpMyAdmin:</p>";
            echo "<pre>UPDATE homepage_sections 
SET content = JSON_SET(
    content,
    '\$.video_url', '',
    '\$.video_type', 'youtube',
    '\$.show_video', false,
    '\$.video_autoplay', true
)
WHERE section_key = 'hero';</pre>";
            echo "</div>";
        } else {
            echo "<p class='success'>✓ All video fields present in database</p>";
        }
        
        echo "<h3>Current Content:</h3>";
        echo "<pre>" . json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</pre>";
        
    } else {
        echo "<p class='error'>✗ Hero section not found</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// 3. File Check
echo "<div class='box'>";
echo "<h2>3. Required Files</h2>";

$files_to_check = [
    'admin/pages/homepage.php' => ['hero_video_url', 'Video Settings', 'editor-section-divider'],
    'admin/assets/js/editor.js' => ['textarea.json-editor'],
    'index.php' => ['hero-video-container', 'show_video'],
    'assets/css/style.css' => ['hero-video-container', 'video-wrapper']
];

foreach ($files_to_check as $file => $search_terms) {
    echo "<h3>{$file}</h3>";
    if (file_exists($file)) {
        echo "<p class='success'>✓ File exists</p>";
        $file_content = file_get_contents($file);
        
        $all_found = true;
        foreach ($search_terms as $term) {
            $found = strpos($file_content, $term) !== false;
            echo "<p>" . ($found ? "✓" : "✗") . " Contains: {$term}</p>";
            if (!$found) $all_found = false;
        }
        
        if (!$all_found) {
            echo "<p class='error'>⚠ File needs to be updated/uploaded</p>";
        }
        
        echo "<p class='info'>Modified: " . date('Y-m-d H:i:s', filemtime($file)) . "</p>";
    } else {
        echo "<p class='error'>✗ File not found</p>";
    }
}
echo "</div>";

// 4. Summary
echo "<div class='box'>";
echo "<h2>4. Summary & Next Steps</h2>";

$db_ok = $has_video_url && $has_video_type && $has_show_video && $has_video_autoplay;
$homepage_ok = file_exists('admin/pages/homepage.php') && strpos(file_get_contents('admin/pages/homepage.php'), 'hero_video_url') !== false;
$editor_ok = file_exists('admin/assets/js/editor.js') && strpos(file_get_contents('admin/assets/js/editor.js'), 'textarea.json-editor') !== false;

if ($db_ok && $homepage_ok && $editor_ok) {
    echo "<p class='success'>✓ Everything is configured correctly!</p>";
    echo "<p>Clear your browser cache (Ctrl+F5) and visit: <a href='admin/pages/homepage.php'>Homepage Editor</a></p>";
} else {
    echo "<p class='error'>⚠ Issues found:</p>";
    echo "<ul>";
    if (!$db_ok) echo "<li>Database needs SQL update (see section 2 above)</li>";
    if (!$homepage_ok) echo "<li>Upload: admin/pages/homepage.php</li>";
    if (!$editor_ok) echo "<li>Upload: admin/assets/js/editor.js</li>";
    echo "</ul>";
}

echo "</div>";
?>

<div class='box'>
    <h2>Quick Links</h2>
    <p><a href='admin/pages/homepage.php'>→ Homepage Editor</a></p>
    <p><a href='check-video-simple.php'>→ Refresh This Page</a></p>
</div>

</body>
</html>
