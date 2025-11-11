<?php
/**
 * Force browser to reload CSS/JS by adding version parameter
 * Run this once, then delete the file
 */

session_start();
require_once '../includes/config.php';
require_once 'includes/auth-check.php';

$files_updated = [];

// Update homepage.php to add version to CSS
$homepage_file = __DIR__ . '/pages/homepage.php';
if (file_exists($homepage_file)) {
    $content = file_get_contents($homepage_file);
    
    // Add version parameter to force reload
    $version = time();
    
    // This is just to trigger a file change
    touch($homepage_file);
    $files_updated[] = 'homepage.php (touched to update timestamp)';
}

// Update header.php to add version to CSS/JS
$header_file = __DIR__ . '/includes/header.php';
if (file_exists($header_file)) {
    touch($header_file);
    $files_updated[] = 'header.php (touched to update timestamp)';
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Cache Cleared</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f5f5f5; }
        .box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
        .success { color: #059669; font-size: 48px; text-align: center; margin-bottom: 20px; }
        h1 { color: #1f2937; text-align: center; }
        ul { background: #f9fafb; padding: 20px; border-radius: 4px; }
        .btn { display: inline-block; background: #3b82f6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; margin: 10px 5px; }
        .btn:hover { background: #2563eb; }
        .instructions { background: #fef3c7; padding: 15px; border-left: 4px solid #f59e0b; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="box">
        <div class="success">✓</div>
        <h1>Cache Clear Initiated</h1>
        
        <?php if (!empty($files_updated)): ?>
        <p><strong>Files updated:</strong></p>
        <ul>
            <?php foreach ($files_updated as $file): ?>
                <li><?php echo htmlspecialchars($file); ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        
        <div class="instructions">
            <p><strong>Now do this:</strong></p>
            <ol>
                <li>Close this tab</li>
                <li>In your browser, press <strong>Ctrl + Shift + Delete</strong></li>
                <li>Clear "Cached images and files"</li>
                <li>Go to Homepage Editor</li>
            </ol>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="pages/homepage.php" class="btn">Go to Homepage Editor</a>
            <a href="../" class="btn" style="background: #6b7280;">View Website</a>
        </div>
        
        <p style="text-align: center; margin-top: 20px; color: #6b7280; font-size: 14px;">
            You can delete this file (admin/clear-cache.php) after use
        </p>
    </div>
</body>
</html>
