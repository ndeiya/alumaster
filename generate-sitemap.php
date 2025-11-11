<?php
/**
 * Generate Static sitemap.xml file
 * Run this script to create/update sitemap.xml
 * Can be run manually or via cron job
 */

require_once 'includes/config.php';
require_once 'includes/database.php';

echo "Generating sitemap.xml...\n\n";

// Start output buffering
ob_start();

// Include the dynamic sitemap
include 'sitemap.php';

// Get the XML content
$xml_content = ob_get_clean();

// Save to sitemap.xml
$result = file_put_contents(__DIR__ . '/sitemap.xml', $xml_content);

if ($result !== false) {
    echo "✓ sitemap.xml generated successfully!\n";
    echo "✓ File size: " . number_format($result) . " bytes\n";
    echo "✓ Location: " . __DIR__ . "/sitemap.xml\n\n";
    
    // Count URLs
    $url_count = substr_count($xml_content, '<url>');
    echo "✓ Total URLs: {$url_count}\n\n";
    
    echo "Next steps:\n";
    echo "1. Upload sitemap.xml to your website root\n";
    echo "2. Submit to Google Search Console: https://search.google.com/search-console\n";
    echo "3. Submit to Bing Webmaster Tools: https://www.bing.com/webmasters\n";
    echo "4. Add to robots.txt (already done)\n\n";
    
    echo "Sitemap URL: " . (SITE_URL ?? 'https://alumastergh.com') . "/sitemap.xml\n";
} else {
    echo "✗ Error: Could not write sitemap.xml\n";
    echo "Check file permissions for the web root directory.\n";
}
?>
