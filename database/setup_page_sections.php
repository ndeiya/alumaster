<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        throw new Exception("Could not connect to database");
    }

    // Create page_sections table for editable page content
    $sql = "
    CREATE TABLE IF NOT EXISTS `page_sections` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `page_slug` varchar(50) NOT NULL,
      `section_key` varchar(50) NOT NULL,
      `section_name` varchar(100) NOT NULL,
      `content` longtext,
      `settings` json DEFAULT NULL,
      `is_active` tinyint(1) DEFAULT 1,
      `sort_order` int(11) DEFAULT 0,
      `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `page_section` (`page_slug`, `section_key`),
      KEY `idx_page_active_sort` (`page_slug`, `is_active`, `sort_order`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    echo "Page sections table created successfully.\n";
    
    // Check if data already exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM page_sections");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Insert default About page sections
        $about_sections = [
            [
                'page_slug' => 'about',
                'section_key' => 'hero',
                'section_name' => 'About Hero Section',
                'content' => json_encode([
                    'title' => 'About AluMaster',
                    'subtitle' => 'Ghana\'s trusted partner for architectural aluminum and glass solutions',
                    'breadcrumb' => 'About'
                ]),
                'settings' => json_encode([
                    'background_image' => 'assets/images/about-hero.jpg',
                    'overlay_opacity' => 0.7
                ]),
                'is_active' => 1,
                'sort_order' => 1
            ],
            [
                'page_slug' => 'about',
                'section_key' => 'story',
                'section_name' => 'Company Story Section',
                'content' => json_encode([
                    'eyebrow' => 'Our Story',
                    'title' => 'Building Ghana\'s Future with Quality Aluminum Solutions',
                    'paragraphs' => [
                        'Since 2008, AluMaster Aluminum System has been at the forefront of Ghana\'s architectural transformation, providing premium aluminum and glass solutions that combine international quality standards with local expertise. Based in Madina-Accra, we have grown from a small local business to become one of Ghana\'s most trusted names in architectural aluminum systems.',
                        'Our journey began with a simple vision: to make high-quality aluminum and glass solutions accessible and affordable for every Ghanaian project, from residential homes to large-scale commercial developments. Today, we proudly serve architects, contractors, real estate developers, and homeowners across Ghana with our comprehensive range of services.',
                        'What sets us apart is our commitment to excellence in every project, regardless of size. Whether you\'re building a single-family home or a multi-story commercial complex, we bring the same level of professionalism, quality materials, and expert craftsmanship to every installation.'
                    ],
                    'image' => 'assets/images/about-hero.jpg'
                ]),
                'settings' => json_encode([
                    'background_color' => '#ffffff'
                ]),
                'is_active' => 1,
                'sort_order' => 2
            ],
            [
                'page_slug' => 'about',
                'section_key' => 'mission_vision',
                'section_name' => 'Mission & Vision Section',
                'content' => json_encode([
                    'mission' => [
                        'title' => 'Our Mission',
                        'description' => 'To provide exceptional aluminum and glass solutions that enhance Ghana\'s architectural landscape while maintaining the highest standards of quality, affordability, and customer service. We are committed to being the trusted partner for every construction project, big or small.'
                    ],
                    'vision' => [
                        'title' => 'Our Vision',
                        'description' => 'To be West Africa\'s leading aluminum and glass solutions provider, recognized for innovation, reliability, and excellence. We envision a future where every building in Ghana benefits from our premium aluminum systems, contributing to sustainable and beautiful architectural development.'
                    ]
                ]),
                'settings' => json_encode([
                    'background_color' => '#f7fafc'
                ]),
                'is_active' => 1,
                'sort_order' => 3
            ],
            [
                'page_slug' => 'about',
                'section_key' => 'benefits',
                'section_name' => 'Why Choose Us Section',
                'content' => json_encode([
                    'eyebrow' => 'Why Choose AluMaster',
                    'title' => 'What Makes Us Different',
                    'description' => 'We combine international quality standards with local expertise and competitive pricing',
                    'benefits' => [
                        ['title' => '15+ Years Experience', 'description' => 'Over a decade of expertise in aluminum and glass installations across Ghana, with hundreds of successful projects completed.', 'icon' => 'check-circle'],
                        ['title' => 'Quality Meets Affordability', 'description' => 'Premium materials and expert craftsmanship at competitive prices, making quality aluminum solutions accessible to all.', 'icon' => 'dollar-sign'],
                        ['title' => 'Expert Team', 'description' => 'Skilled professionals with extensive training in modern aluminum and glass installation techniques and safety standards.', 'icon' => 'users'],
                        ['title' => 'Fast Installation', 'description' => 'Efficient project management and installation processes that minimize disruption and deliver results on time.', 'icon' => 'zap'],
                        ['title' => 'Quality Guarantee', 'description' => 'Comprehensive warranty on all installations and ongoing support to ensure long-lasting performance and customer satisfaction.', 'icon' => 'shield'],
                        ['title' => 'Local Expertise', 'description' => 'Deep understanding of Ghana\'s climate, building codes, and architectural preferences, ensuring optimal solutions for local conditions.', 'icon' => 'globe']
                    ]
                ]),
                'settings' => json_encode([
                    'background_color' => '#ffffff',
                    'columns' => 3
                ]),
                'is_active' => 1,
                'sort_order' => 4
            ],
            [
                'page_slug' => 'about',
                'section_key' => 'stats',
                'section_name' => 'Statistics Section',
                'content' => json_encode([
                    'stats' => [
                        ['number' => 15, 'label' => 'Years Experience'],
                        ['number' => 500, 'label' => 'Projects Completed'],
                        ['number' => 100, 'label' => 'Satisfied Clients'],
                        ['number' => 24, 'label' => 'Hour Support']
                    ]
                ]),
                'settings' => json_encode([
                    'background_image' => 'assets/images/stats-bg.jpg',
                    'overlay_opacity' => 0.8
                ]),
                'is_active' => 1,
                'sort_order' => 5
            ]
        ];
        
        // Insert default Contact page sections
        $contact_sections = [
            [
                'page_slug' => 'contact',
                'section_key' => 'hero',
                'section_name' => 'Contact Hero Section',
                'content' => json_encode([
                    'title' => 'Get In Touch',
                    'subtitle' => 'Ready to start your project? Contact us for a free consultation and quote.',
                    'breadcrumb' => 'Contact'
                ]),
                'settings' => json_encode([
                    'background_image' => 'assets/images/contact-hero.jpg',
                    'overlay_opacity' => 0.7
                ]),
                'is_active' => 1,
                'sort_order' => 1
            ],
            [
                'page_slug' => 'contact',
                'section_key' => 'contact_methods',
                'section_name' => 'Contact Methods Section',
                'content' => json_encode([
                    'methods' => [
                        [
                            'icon' => 'phone',
                            'label' => 'Call Us',
                            'values' => ['+233-541-737-575', '+233-502-777-703']
                        ],
                        [
                            'icon' => 'email',
                            'label' => 'Email Us',
                            'values' => ['alumaster75@gmail.com']
                        ],
                        [
                            'icon' => 'location',
                            'label' => 'Visit Us',
                            'values' => ['16 Palace Street', 'Madina-Accra, Ghana']
                        ],
                        [
                            'icon' => 'clock',
                            'label' => 'Business Hours',
                            'values' => ['Mon - Fri: 8:00 AM - 6:00 PM', 'Sat: 9:00 AM - 4:00 PM', 'Sun: Closed']
                        ]
                    ]
                ]),
                'settings' => json_encode([
                    'background_color' => '#ffffff'
                ]),
                'is_active' => 1,
                'sort_order' => 2
            ],
            [
                'page_slug' => 'contact',
                'section_key' => 'contact_form',
                'section_name' => 'Contact Form Section',
                'content' => json_encode([
                    'title' => 'Send Us a Message',
                    'description' => 'Fill out the form below and we\'ll get back to you within 24 hours.',
                    'services' => [
                        'Alucobond Cladding',
                        'Curtain Wall',
                        'Spider Glass',
                        'Sliding Windows And Doors',
                        'Frameless Door',
                        'PVC Windows',
                        'Sun-breakers',
                        'Stainless Steel Balustrades'
                    ]
                ]),
                'settings' => json_encode([
                    'background_color' => '#f7fafc'
                ]),
                'is_active' => 1,
                'sort_order' => 3
            ],
            [
                'page_slug' => 'contact',
                'section_key' => 'map',
                'section_name' => 'Map Section',
                'content' => json_encode([
                    'embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3970.8267739384!2d-0.1677!3d5.6037!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNcKwMzYnMTMuMyJOIDDCsDEwJzAzLjciVw!5e0!3m2!1sen!2sgh!4v1635789012345!5m2!1sen!2sgh',
                    'title' => 'AluMaster Location - 16 Palace Street, Madina-Accra'
                ]),
                'settings' => json_encode([
                    'height' => 400
                ]),
                'is_active' => 1,
                'sort_order' => 4
            ]
        ];
        
        // Combine all sections
        $all_sections = array_merge($about_sections, $contact_sections);
        
        $stmt = $pdo->prepare("
            INSERT INTO page_sections (page_slug, section_key, section_name, content, settings, is_active, sort_order) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($all_sections as $section) {
            $stmt->execute([
                $section['page_slug'],
                $section['section_key'],
                $section['section_name'],
                $section['content'],
                $section['settings'],
                $section['is_active'],
                $section['sort_order']
            ]);
        }
        
        echo "Default page sections inserted successfully.\n";
    } else {
        echo "Page sections already exist. Skipping insert.\n";
    }
    
    echo "Page sections setup completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>