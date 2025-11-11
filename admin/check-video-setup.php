<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once 'includes/auth-check.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Video Setup Diagnostic</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #0f0; }
        .success { color: #0f0; }
        .error { color: #f00; }
        .info { color: #ff0; }
        pre { background: #000; padding: 10px; border: 1px solid #333; }
        h2 { color: #0ff; }
    </style>
</head>
<body>
    <h1>🎬 Video Setup Diagnostic</h1>

<?php
echo "<h2>1. Checking Database Connection...</h2>";
try {
    $db = new Database();
    $pdo = $db->getConnection();
    if ($pdo) {
        echo "<p class='success'>✓ Database connected</p>";
    } else {
        echo "<p class='error'>✗ Database connection failed</p>";
        exit;
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

echo "<h2>2. Checking Hero Section in Database...</h2>";
try {
    $stmt = $pdo->query("SELECT content FROM homepage_sections WHERE section_key = 'hero'");
    $hero = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($hero) {
        echo "<p class='success'>✓ Hero section found</p>";
        $content = json_decode($hero['content'], true);
        
        echo "<h3>Current Hero Content:</h3>";
        echo "<pre>" . json_encode($content, JSON_PRETTY_PRINT) . "</pre>";
        
        echo "<h3>Video Fields Check:</h3>";
        $fields = ['video_url', 'video_type', 'show_video', 'video_autoplay'];
        foreach ($fields as $field) {
            if (isset($content[$field])) {
                echo "<p class='success'>✓ {$field}: " . json_encode($content[$field]) . "</p>";
            } else {
                echo "<p class='error'>✗ {$field}: MISSING</p>";
            }
        }
    } else {
        echo "<p class='error'>✗ Hero section not found</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>3. Checking homepage.php File...</h2>";
$homepage_file = __DIR__ . '/pages/homepage.php';
if (file_exists($homepage_file)) {
    echo "<p class='success'>✓ File exists: {$homepage_file}</p>";
    
    $content = file_get_contents($homepage_file);
    
    // Check for video fields in the code
    $checks = [
        'hero_video_url' => strpos($content, 'hero_video_url') !== false,
        'hero_video_type' => strpos($content, 'hero_video_type') !== false,
        'hero_show_video' => strpos($content, 'hero_show_video') !== false,
        'hero_video_autoplay' => strpos($content, 'hero_video_autoplay') !== false,
        'Video Settings' => strpos($content, 'Video Settings') !== false,
        'editor-section-divider' => strpos($content, 'editor-section-divider') !== false,
    ];
    
    echo "<h3>Code Check:</h3>";
    foreach ($checks as $item => $found) {
        if ($found) {
            echo "<p class='success'>✓ Found: {$item}</p>";
        } else {
            echo "<p class='error'>✗ Missing: {$item}</p>";
        }
    }
    
    // Check file modification time
    $mod_time = filemtime($homepage_file);
    echo "<p class='info'>ℹ Last modified: " . date('Y-m-d H:i:s', $mod_time) . "</p>";
    
} else {
    echo "<p class='error'>✗ File not found: {$homepage_file}</p>";
}

echo "<h2>4. Checking editor.js File...</h2>";
$editor_file = __DIR__ . '/assets/js/editor.js';
if (file_exists($editor_file)) {
    echo "<p class='success'>✓ File exists: {$editor_file}</p>";
    
    $content = file_get_contents($editor_file);
    
    if (strpos($content, 'textarea.json-editor') !== false) {
        echo "<p class='success'>✓ Editor.js updated correctly (targets textarea.json-editor)</p>";
    } else if (strpos($content, '.content-editor') !== false) {
        echo "<p class='error'>✗ Editor.js NOT updated (still targets .content-editor)</p>";
        echo "<p class='info'>ℹ This will cause the rich text editor to override the form fields</p>";
    }
    
    $mod_time = filemtime($editor_file);
    echo "<p class='info'>ℹ Last modified: " . date('Y-m-d H:i:s', $mod_time) . "</p>";
} else {
    echo "<p class='error'>✗ File not found: {$editor_file}</p>";
}

echo "<h2>5. Solution:</h2>";
echo "<div style='background: #333; padding: 15px; border-left: 4px solid #0ff;'>";

$all_good = true;
if (!isset($content) || !isset($content['video_url'])) {
    echo "<p class='error'>⚠ Database needs update</p>";
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
    $all_good = false;
}

if (!file_exists($homepage_file) || !strpos(file_get_contents($homepage_file), 'hero_video_url')) {
    echo "<p class='error'>⚠ homepage.php needs to be uploaded</p>";
    echo "<p>Upload: <code>admin/pages/homepage.php</code> from your local files</p>";
    $all_good = false;
}

if (file_exists($editor_file) && strpos(file_get_contents($editor_file), '.content-editor') !== false) {
    echo "<p class='error'>⚠ editor.js needs to be uploaded</p>";
    echo "<p>Upload: <code>admin/assets/js/editor.js</code> from your local files</p>";
    $all_good = false;
}

if ($all_good) {
    echo "<p class='success'>✓ Everything looks good!</p>";
    echo "<p>Try clearing your browser cache (Ctrl+F5) and reload the homepage editor.</p>";
}

echo "</div>";

echo "<h2>6. Quick Actions:</h2>";
echo "<p><a href='pages/homepage.php' style='color: #0ff;'>→ Go to Homepage Editor</a></p>";
echo "<p><a href='setup-video.php' style='color: #0ff;'>→ Run Video Setup Tool</a></p>";
echo "<p><a href='check-video-setup.php' style='color: #0ff;'>→ Refresh This Page</a></p>";
?>

</body>
</html>
