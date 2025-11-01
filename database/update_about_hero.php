<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        throw new Exception("Could not connect to database");
    }

    echo "Updating About page hero section content...\n";
    
    // Update hero section with better organized, professional text
    $hero_content = json_encode([
        'title' => 'About AluMaster',
        'subtitle' => 'Where Quality Meets Affordability',
        'description' => 'Ghana\'s premier provider of architectural aluminum and glass solutions. We deliver excellence in every project with unmatched quality, competitive pricing, and professional craftsmanship that transforms your vision into reality.'
    ]);
    
    $stmt = $pdo->prepare("UPDATE page_sections SET content = ? WHERE page_slug = 'about' AND section_key = 'hero'");
    $stmt->execute([$hero_content]);
    
    echo "Hero section content updated successfully!\n";
    
    // Also update the CTA section for consistency
    $cta_content = json_encode([
        'title' => 'Ready to Work With Us?',
        'description' => 'Transform your project with our expert aluminum and glass solutions. Get started today with a free consultation.',
        'primary_button_text' => 'Get Started',
        'primary_button_link' => 'contact.php',
        'secondary_button_text' => 'Call Now',
        'secondary_button_link' => 'tel:+233541737575'
    ]);
    
    $stmt = $pdo->prepare("UPDATE page_sections SET content = ? WHERE page_slug = 'about' AND section_key = 'cta'");
    $stmt->execute([$cta_content]);
    
    echo "CTA section content updated successfully!\n";
    echo "About page content improvements completed!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>