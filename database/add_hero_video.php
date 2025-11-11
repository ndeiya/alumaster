<?php
/**
 * Add video support to hero section
 * This script updates the hero section content to include video fields
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        throw new Exception("Database connection failed");
    }
    
    // Get current hero section content
    $stmt = $pdo->prepare("SELECT content FROM homepage_sections WHERE section_key = 'hero'");
    $stmt->execute();
    $hero = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($hero) {
        $content = json_decode($hero['content'], true);
        
        // Add video fields if they don't exist
        if (!isset($content['video_url'])) {
            $content['video_url'] = '';
        }
        if (!isset($content['video_type'])) {
            $content['video_type'] = 'youtube'; // youtube, vimeo, or direct
        }
        if (!isset($content['show_video'])) {
            $content['show_video'] = false;
        }
        if (!isset($content['video_autoplay'])) {
            $content['video_autoplay'] = true;
        }
        
        // Update the database
        $stmt = $pdo->prepare("UPDATE homepage_sections SET content = ? WHERE section_key = 'hero'");
        $stmt->execute([json_encode($content)]);
        
        echo "✓ Hero section updated with video support\n";
        echo "Video fields added:\n";
        echo "  - video_url: URL of the video\n";
        echo "  - video_type: youtube, vimeo, or direct\n";
        echo "  - show_video: Enable/disable video display\n";
        echo "  - video_autoplay: Autoplay video (muted)\n";
    } else {
        echo "✗ Hero section not found in database\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
