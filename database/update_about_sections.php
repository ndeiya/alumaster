<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        throw new Exception("Could not connect to database");
    }

    // Clear existing about page sections
    $stmt = $pdo->prepare("DELETE FROM page_sections WHERE page_slug = 'about'");
    $stmt->execute();
    
    echo "Cleared existing about page sections.\n";
    
    // Insert new about page sections matching the screenshot design
    $about_sections = [
        [
            'page_slug' => 'about',
            'section_key' => 'hero',
            'section_name' => 'About Hero Section',
            'content' => json_encode([
                'title' => 'About AluMaster',
                'subtitle' => 'Where Quality Meets Affordability',
                'description' => 'We are Ghana\'s premier provider of architectural aluminum and glass solutions, delivering excellence in every project with unmatched quality and competitive pricing.'
            ]),
            'settings' => json_encode([
                'background_gradient' => 'linear-gradient(135deg, #2d3748 0%, #1a202c 100%)',
                'text_color' => '#ffffff'
            ]),
            'is_active' => 1,
            'sort_order' => 1
        ],
        [
            'page_slug' => 'about',
            'section_key' => 'story',
            'section_name' => 'Our Story Section',
            'content' => json_encode([
                'eyebrow' => 'Our Story',
                'title' => 'Building Excellence Since Day One',
                'paragraphs' => [
                    'AluMaster has been at the forefront of Ghana\'s aluminum and glass industry, providing innovative solutions that combine cutting-edge technology with traditional craftsmanship. Our journey began with a simple mission: to deliver premium quality aluminum and glass installations at affordable prices.',
                    'Over the years, we have built a reputation for excellence, working on projects ranging from residential homes to large commercial complexes. Our team of skilled professionals brings years of experience and expertise to every project, ensuring that our clients receive nothing but the best.',
                    'Today, we continue to lead the industry with our commitment to quality, innovation, and customer satisfaction. Every project we undertake reflects our dedication to excellence and our passion for creating beautiful, functional spaces.'
                ],
                'stats' => [
                    ['number' => '500+', 'label' => 'Projects'],
                    ['number' => '15+', 'label' => 'Years']
                ],
                'image' => 'assets/images/about-building.jpg'
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
                'title' => 'Our Mission & Vision',
                'description' => 'Driving excellence in aluminum and glass solutions across Ghana',
                'mission' => [
                    'title' => 'Our Mission',
                    'description' => 'To provide exceptional aluminum and glass solutions that enhance Ghana\'s architectural landscape while maintaining the highest standards of quality, affordability, and customer service.'
                ],
                'vision' => [
                    'title' => 'Our Vision',
                    'description' => 'To be West Africa\'s leading aluminum and glass solutions provider, recognized for innovation, reliability, and excellence in every project we undertake.'
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
            'section_key' => 'values',
            'section_name' => 'Core Values Section',
            'content' => json_encode([
                'title' => 'Our Core Values',
                'description' => 'The principles that guide everything we do',
                'values' => [
                    ['title' => 'Quality Excellence', 'description' => 'We never compromise on quality, using only premium materials and proven installation techniques.', 'icon' => 'check-circle'],
                    ['title' => 'Affordability', 'description' => 'Premium quality doesn\'t have to break the bank. We offer competitive pricing without sacrificing excellence.', 'icon' => 'dollar-sign'],
                    ['title' => 'Innovation', 'description' => 'We stay ahead of industry trends, bringing the latest technologies and techniques to every project.', 'icon' => 'zap']
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
            'section_key' => 'expertise',
            'section_name' => 'Our Expertise Section',
            'content' => json_encode([
                'title' => 'Our Expertise',
                'description' => 'Comprehensive aluminum and glass solutions for every need',
                'services' => [
                    ['title' => 'Alucobond Cladding', 'description' => 'Premium aluminum composite panels for modern facades and exterior cladding systems.', 'icon' => 'building'],
                    ['title' => 'Curtain Wall', 'description' => 'Structural glazing systems for commercial buildings and high-rise constructions.', 'icon' => 'grid'],
                    ['title' => 'Spider Glass', 'description' => 'Point-fixed glazing systems for stunning glass facades and architectural features.', 'icon' => 'lightbulb'],
                    ['title' => 'Sliding Doors', 'description' => 'High-performance sliding window and door systems for residential and commercial use.', 'icon' => 'columns'],
                    ['title' => 'Frameless Door', 'description' => 'Elegant glass door solutions for modern spaces and commercial entrances.', 'icon' => 'lock'],
                    ['title' => 'PVC Windows', 'description' => 'Energy-efficient PVC window systems for residential and commercial applications.', 'icon' => 'clipboard'],
                    ['title' => 'Sun-breakers', 'description' => 'Solar shading solutions for climate control and energy efficiency.', 'icon' => 'sun'],
                    ['title' => 'Steel Balustrades', 'description' => 'Premium stainless steel railing systems for safety and aesthetic appeal.', 'icon' => 'shield']
                ]
            ]),
            'settings' => json_encode([
                'background_color' => '#f7fafc',
                'columns' => 4
            ]),
            'is_active' => 1,
            'sort_order' => 5
        ],
        [
            'page_slug' => 'about',
            'section_key' => 'team',
            'section_name' => 'Our Team Section',
            'content' => json_encode([
                'title' => 'Our Team',
                'description' => 'Meet the experts behind our success',
                'members' => [
                    ['name' => 'John Mensah', 'role' => 'Managing Director', 'image' => 'assets/images/team-member-1.jpg'],
                    ['name' => 'Sarah Osei', 'role' => 'Project Manager', 'image' => 'assets/images/team-member-2.jpg'],
                    ['name' => 'Michael Asante', 'role' => 'Technical Director', 'image' => 'assets/images/team-member-3.jpg']
                ]
            ]),
            'settings' => json_encode([
                'background_color' => '#ffffff',
                'columns' => 3
            ]),
            'is_active' => 1,
            'sort_order' => 6
        ],
        [
            'page_slug' => 'about',
            'section_key' => 'cta',
            'section_name' => 'Call to Action Section',
            'content' => json_encode([
                'title' => 'Ready to Work With Us?',
                'description' => 'Let\'s discuss your project requirements and bring your vision to life',
                'primary_button_text' => 'Get Started',
                'primary_button_link' => 'contact.php',
                'secondary_button_text' => 'Call Now',
                'secondary_button_link' => 'tel:+233541737575'
            ]),
            'settings' => json_encode([
                'background_gradient' => 'linear-gradient(135deg, #2d3748 0%, #1a202c 100%)',
                'text_color' => '#ffffff'
            ]),
            'is_active' => 1,
            'sort_order' => 7
        ]
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO page_sections (page_slug, section_key, section_name, content, settings, is_active, sort_order) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($about_sections as $section) {
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
    
    echo "New about page sections inserted successfully.\n";
    echo "About page updated to match screenshot design!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>