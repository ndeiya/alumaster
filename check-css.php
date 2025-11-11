<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>CSS Check</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; }
        .success { color: #059669; }
        .error { color: #dc2626; }
        .info { color: #2563eb; }
    </style>
</head>
<body>
    <h1>CSS File Check</h1>

    <?php
    $css_file = __DIR__ . '/assets/css/style.css';
    
    echo "<div class='box'>";
    echo "<h2>1. File Existence</h2>";
    if (file_exists($css_file)) {
        echo "<p class='success'>✓ CSS file exists</p>";
        echo "<p>Path: {$css_file}</p>";
        echo "<p>Size: " . number_format(filesize($css_file)) . " bytes</p>";
        echo "<p>Modified: " . date('Y-m-d H:i:s', filemtime($css_file)) . "</p>";
        
        // Check if file is readable
        if (is_readable($css_file)) {
            echo "<p class='success'>✓ File is readable</p>";
        } else {
            echo "<p class='error'>✗ File is NOT readable - check permissions</p>";
        }
        
        // Check file permissions
        $perms = substr(sprintf('%o', fileperms($css_file)), -4);
        echo "<p>Permissions: {$perms}</p>";
        if ($perms == '0644' || $perms == '0755') {
            echo "<p class='success'>✓ Permissions are correct</p>";
        } else {
            echo "<p class='error'>⚠ Permissions might be wrong (should be 644 or 755)</p>";
        }
        
    } else {
        echo "<p class='error'>✗ CSS file NOT found!</p>";
        echo "<p>Expected location: {$css_file}</p>";
    }
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h2>2. CSS Content Check</h2>";
    if (file_exists($css_file)) {
        $content = file_get_contents($css_file);
        $lines = substr_count($content, "\n");
        echo "<p>Total lines: " . number_format($lines) . "</p>";
        
        // Check for key CSS classes
        $checks = [
            '.hero' => 'Hero section styles',
            '.navbar' => 'Navigation styles',
            '.btn' => 'Button styles',
            '.container' => 'Container styles',
            'font-family' => 'Font definitions'
        ];
        
        foreach ($checks as $search => $desc) {
            if (strpos($content, $search) !== false) {
                echo "<p class='success'>✓ Found: {$desc}</p>";
            } else {
                echo "<p class='error'>✗ Missing: {$desc}</p>";
            }
        }
        
        // Show first 500 characters
        echo "<h3>File Preview:</h3>";
        echo "<pre style='background: #f9f9f9; padding: 10px; overflow-x: auto;'>";
        echo htmlspecialchars(substr($content, 0, 500));
        echo "\n...</pre>";
        
    }
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h2>3. URL Access Test</h2>";
    $css_url = 'assets/css/style.css';
    echo "<p>Try accessing: <a href='{$css_url}' target='_blank'>{$css_url}</a></p>";
    echo "<p class='info'>If the link opens and shows CSS code, the file is accessible.</p>";
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h2>4. Solution</h2>";
    
    if (!file_exists($css_file)) {
        echo "<p class='error'><strong>Problem:</strong> CSS file is missing</p>";
        echo "<p><strong>Solution:</strong> Upload assets/css/style.css to your server</p>";
    } else if (!is_readable($css_file)) {
        echo "<p class='error'><strong>Problem:</strong> CSS file permissions</p>";
        echo "<p><strong>Solution:</strong> Set file permissions to 644</p>";
        echo "<pre>chmod 644 assets/css/style.css</pre>";
    } else {
        echo "<p class='success'><strong>CSS file is OK!</strong></p>";
        echo "<p><strong>Solution:</strong> Clear your browser cache:</p>";
        echo "<ol>";
        echo "<li>Press Ctrl+Shift+Delete</li>";
        echo "<li>Clear 'Cached images and files'</li>";
        echo "<li>Or try Ctrl+F5 (hard refresh)</li>";
        echo "<li>Or open in Incognito/Private window</li>";
        echo "</ol>";
    }
    echo "</div>";
    ?>

    <div class='box'>
        <h2>Quick Links</h2>
        <p><a href="index.php">→ Homepage</a></p>
        <p><a href="assets/css/style.css" target="_blank">→ View CSS File</a></p>
        <p><a href="check-css.php">→ Refresh This Page</a></p>
    </div>

</body>
</html>
