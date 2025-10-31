<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "Starting database population...\n";
    
    // Insert service categories
    $categories = [
        ['name' => 'Cladding & Walls', 'slug' => 'cladding-walls', 'description' => 'Exterior cladding and wall systems', 'sort_order' => 1],
        ['name' => 'Glass Systems', 'slug' => 'glass-systems', 'description' => 'Specialized glass installation systems', 'sort_order' => 2],
        ['name' => 'Doors & Windows', 'slug' => 'doors-windows', 'description' => 'Window and door solutions', 'sort_order' => 3],
        ['name' => 'Specialty Systems', 'slug' => 'specialty-systems', 'description' => 'Specialized architectural systems', 'sort_order' => 4]
    ];
    
    $category_ids = [];
    
    foreach ($categories as $category) {
        // Check if category already exists
        $stmt = $conn->prepare("SELECT id FROM service_categories WHERE slug = ?");
        $stmt->execute([$category['slug']]);
        $existing_id = $stmt->fetchColumn();
        
        if ($existing_id) {
            $category_ids[$category['slug']] = $existing_id;
            echo "Category '{$category['name']}' already exists (ID: $existing_id)\n";
        } else {
            $stmt = $conn->prepare("INSERT INTO service_categories (name, slug, description, sort_order, is_active) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$category['name'], $category['slug'], $category['description'], $category['sort_order']]);
            $category_ids[$category['slug']] = $conn->lastInsertId();
            echo "Created category '{$category['name']}' (ID: {$category_ids[$category['slug']]})\n";
        }
    }
    
    // Insert services
    $services = [
        [
            'category_slug' => 'cladding-walls',
            'name' => 'Alucobond Cladding',
            'slug' => 'alucobond-cladding',
            'short_description' => 'Premium aluminum composite panels for modern facades and building exteriors.',
            'description' => 'Premium aluminum composite panels for modern facades and building exteriors. Our Alucobond cladding systems provide exceptional durability, weather resistance, and aesthetic appeal for commercial and residential buildings.',
            'featured_image' => 'assets/images/services/alucobond-cladding.jpg',
            'sort_order' => 1
        ],
        [
            'category_slug' => 'cladding-walls',
            'name' => 'Curtain Wall',
            'slug' => 'curtain-wall',
            'short_description' => 'Structural glazing systems for commercial buildings and high-rise structures.',
            'description' => 'Structural glazing systems for commercial buildings and high-rise structures. Our curtain wall systems provide superior thermal performance, weather sealing, and architectural flexibility for modern construction projects.',
            'featured_image' => 'assets/images/services/curtain-wall.jpg',
            'sort_order' => 2
        ],
        [
            'category_slug' => 'glass-systems',
            'name' => 'Spider Glass',
            'slug' => 'spider-glass',
            'short_description' => 'Point-fixed glazing systems for stunning glass facades and architectural features.',
            'description' => 'Point-fixed glazing systems for stunning glass facades and architectural features. Our spider glass systems create seamless glass surfaces with minimal structural interference, perfect for modern architectural designs.',
            'featured_image' => 'assets/images/services/spider-glass.jpg',
            'sort_order' => 3
        ],
        [
            'category_slug' => 'doors-windows',
            'name' => 'Sliding Windows & Doors',
            'slug' => 'sliding-windows-doors',
            'short_description' => 'High-performance sliding window and door systems for residential and commercial use.',
            'description' => 'High-performance sliding window and door systems for residential and commercial use. Our sliding systems offer smooth operation, excellent sealing, and energy efficiency for any application.',
            'featured_image' => 'assets/images/services/sliding-doors.jpg',
            'sort_order' => 4
        ],
        [
            'category_slug' => 'doors-windows',
            'name' => 'Frameless Door',
            'slug' => 'frameless-door',
            'short_description' => 'Elegant glass door solutions for modern spaces and commercial entrances.',
            'description' => 'Elegant glass door solutions for modern spaces and commercial entrances. Our frameless door systems provide unobstructed views and seamless integration with modern architectural designs.',
            'featured_image' => 'assets/images/services/frameless-door.jpg',
            'sort_order' => 5
        ],
        [
            'category_slug' => 'doors-windows',
            'name' => 'PVC Windows',
            'slug' => 'pvc-windows',
            'short_description' => 'Energy-efficient PVC window systems for residential and commercial applications.',
            'description' => 'Energy-efficient PVC window systems for residential and commercial applications. Our PVC windows offer excellent thermal insulation, low maintenance, and long-lasting performance.',
            'featured_image' => 'assets/images/services/pvc-windows.jpg',
            'sort_order' => 6
        ],
        [
            'category_slug' => 'specialty-systems',
            'name' => 'Sun-breakers',
            'slug' => 'sun-breakers',
            'short_description' => 'Solar shading solutions for climate control and energy efficiency.',
            'description' => 'Solar shading solutions for climate control and energy efficiency. Our sun-breaker systems reduce heat gain, improve comfort, and enhance the energy performance of buildings.',
            'featured_image' => 'assets/images/services/sun-breakers.jpg',
            'sort_order' => 7
        ],
        [
            'category_slug' => 'specialty-systems',
            'name' => 'Stainless Steel Balustrades',
            'slug' => 'steel-balustrades',
            'short_description' => 'Premium stainless steel railing systems for safety and aesthetic appeal.',
            'description' => 'Premium stainless steel railing systems for safety and aesthetic appeal. Our balustrade systems combine structural integrity with elegant design for both interior and exterior applications.',
            'featured_image' => 'assets/images/services/steel-balustrades.jpg',
            'sort_order' => 8
        ]
    ];
    
    foreach ($services as $service) {
        // Check if service already exists
        $stmt = $conn->prepare("SELECT id FROM services WHERE slug = ?");
        $stmt->execute([$service['slug']]);
        $existing_id = $stmt->fetchColumn();
        
        if ($existing_id) {
            echo "Service '{$service['name']}' already exists (ID: $existing_id)\n";
        } else {
            $category_id = $category_ids[$service['category_slug']];
            $stmt = $conn->prepare("INSERT INTO services (category_id, name, slug, short_description, description, featured_image, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, 'published', ?)");
            $stmt->execute([
                $category_id,
                $service['name'],
                $service['slug'],
                $service['short_description'],
                $service['description'],
                $service['featured_image'],
                $service['sort_order']
            ]);
            $service_id = $conn->lastInsertId();
            echo "Created service '{$service['name']}' (ID: $service_id)\n";
        }
    }
    
    echo "\nDatabase population completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>