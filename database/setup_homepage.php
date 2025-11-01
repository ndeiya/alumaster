<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        throw new Exception("Could not connect to database");
    }
    // Create homepage sections table
    $sql = "
    CREATE TABLE IF NOT EXISTS `homepage_sections` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `section_key` varchar(50) NOT NULL,
      `section_name` varchar(100) NOT NULL,
      `content` longtext,
      `settings` json DEFAULT NULL,
      `is_active` tinyint(1) DEFAULT 1,
      `sort_order` int(11) DEFAULT 0,
      `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `section_key` (`section_key`),
      KEY `idx_active_sort` (`is_active`, `sort_order`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    echo "Homepage sections table created successfully.\n";
    
    // Check if data already exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM homepage_sections");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Insert default homepage sections
        $sections = [
            [
                'section_key' => 'hero',
                'section_name' => 'Hero Section',
                'content' => json_encode([
                    'title' => 'Where Quality',
                    'highlight' => 'Meets Affordability',
                    'description' => 'Professional aluminum and glass systems for modern architecture. From curtain walls to specialty installations, we deliver excellence in every project.',
                    'primary_button_text' => 'Call Now',
                    'primary_button_link' => 'tel:+233541737575',
                    'secondary_button_text' => 'View Portfolio',
                    'secondary_button_link' => 'services.php',
                    'background_image' => 'assets/images/hero-building.jpg'
                ]),
                'settings' => json_encode([
                    'background_color' => '#1a1a1a',
                    'text_color' => '#ffffff'
                ]),
                'is_active' => 1,
                'sort_order' => 1
            ],
            [
                'section_key' => 'services',
                'section_name' => 'Our Expertise Section',
                'content' => json_encode([
                    'title' => 'Our Expertise',
                    'subtitle' => 'Comprehensive aluminum and glass solutions for modern construction projects',
                    'services' => [
                        ['name' => 'Alucobond Cladding', 'description' => 'Premium aluminum composite panels for modern facades', 'icon' => 'building'],
                        ['name' => 'Curtain Wall', 'description' => 'Structural glazing systems for commercial buildings', 'icon' => 'grid'],
                        ['name' => 'Spider Glass', 'description' => 'Point-fixed glazing for stunning glass facades', 'icon' => 'lightbulb'],
                        ['name' => 'Sliding Doors', 'description' => 'High-performance sliding window and door systems', 'icon' => 'columns'],
                        ['name' => 'Frameless Door', 'description' => 'Elegant glass door solutions for modern spaces', 'icon' => 'lock'],
                        ['name' => 'PVC Windows', 'description' => 'Energy-efficient PVC window systems', 'icon' => 'clipboard'],
                        ['name' => 'Sun-breakers', 'description' => 'Solar shading solutions for climate control', 'icon' => 'sun'],
                        ['name' => 'Steel Balustrades', 'description' => 'Premium stainless steel railing systems', 'icon' => 'shield']
                    ]
                ]),
                'settings' => json_encode([
                    'background_color' => '#ffffff',
                    'columns' => 4
                ]),
                'is_active' => 1,
                'sort_order' => 2
            ],
            [
                'section_key' => 'why_choose',
                'section_name' => 'Why Choose Us Section',
                'content' => json_encode([
                    'title' => 'Why Choose AluMaster?',
                    'description' => 'With years of experience in the aluminum and glass industry, we have established ourselves as Ghana\'s premier provider of architectural glazing solutions. Our commitment to quality and affordability sets us apart.',
                    'features' => [
                        'Premium quality materials',
                        'Expert installation team',
                        'Competitive pricing',
                        'Timely project completion'
                    ],
                    'image' => 'assets/images/why-choose-us.jpg'
                ]),
                'settings' => json_encode([
                    'background_color' => '#ffffff'
                ]),
                'is_active' => 1,
                'sort_order' => 3
            ],
            [
                'section_key' => 'contact_cta',
                'section_name' => 'Contact CTA Section',
                'content' => json_encode([
                    'title' => 'Get In Touch',
                    'subtitle' => 'Ready to start your project? Contact us today for a free consultation',
                    'contact_items' => [
                        ['title' => 'Call Us', 'icon' => 'phone', 'lines' => ['+233-541-737-575', '+233-502-777-703']],
                        ['title' => 'Visit Us', 'icon' => 'location', 'lines' => ['16 Palace Street', 'Madina-Accra, Ghana']],
                        ['title' => 'Email Us', 'icon' => 'email', 'lines' => ['alumaster75@gmail.com', 'www.alumastergh.com']]
                    ],
                    'social_links' => [
                        ['platform' => 'facebook', 'url' => 'https://www.facebook.com/alumastergh'],
                        ['platform' => 'instagram', 'url' => 'https://www.instagram.com/alumaster75'],
                        ['platform' => 'twitter', 'url' => 'https://twitter.com/alumaster75'],
                        ['platform' => 'tiktok', 'url' => 'https://www.tiktok.com/@alumaster75']
                    ],
                    'cta_button_text' => 'Request Free Quote',
                    'cta_button_link' => 'contact.php'
                ]),
                'settings' => json_encode([
                    'background_color' => '#1a1a1a',
                    'text_color' => '#ffffff'
                ]),
                'is_active' => 1,
                'sort_order' => 4
            ]
        ];
        
        $stmt = $pdo->prepare("
            INSERT INTO homepage_sections (section_key, section_name, content, settings, is_active, sort_order) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($sections as $section) {
            $stmt->execute([
                $section['section_key'],
                $section['section_name'],
                $section['content'],
                $section['settings'],
                $section['is_active'],
                $section['sort_order']
            ]);
        }
        
        echo "Default homepage sections inserted successfully.\n";
    } else {
        echo "Homepage sections already exist. Skipping insert.\n";
    }
    
    echo "Homepage setup completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}