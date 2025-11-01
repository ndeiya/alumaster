<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "Creating pages table...\n";
    
    // Create pages table
    $sql = "CREATE TABLE IF NOT EXISTS `pages` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(200) NOT NULL,
      `slug` varchar(200) NOT NULL,
      `content` longtext,
      `excerpt` text,
      `featured_image` varchar(255) DEFAULT NULL,
      `template` varchar(50) DEFAULT 'default',
      `meta_title` varchar(200) DEFAULT NULL,
      `meta_description` text,
      `meta_keywords` text,
      `status` enum('published','draft','private') DEFAULT 'draft',
      `sort_order` int(11) DEFAULT 0,
      `is_homepage` tinyint(1) DEFAULT 0,
      `show_in_nav` tinyint(1) DEFAULT 1,
      `parent_id` int(11) DEFAULT NULL,
      `views` int(11) DEFAULT 0,
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `slug` (`slug`),
      KEY `idx_status_sort` (`status`, `sort_order`),
      KEY `idx_parent` (`parent_id`),
      KEY `idx_homepage` (`is_homepage`),
      KEY `idx_nav` (`show_in_nav`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conn->exec($sql);
    echo "Pages table created successfully.\n";
    
    echo "Creating navigation_menus table...\n";
    
    // Create navigation_menus table
    $sql = "CREATE TABLE IF NOT EXISTS `navigation_menus` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL,
      `slug` varchar(100) NOT NULL,
      `description` text,
      `location` varchar(50) DEFAULT 'header',
      `is_active` tinyint(1) DEFAULT 1,
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `slug` (`slug`),
      KEY `idx_location` (`location`),
      KEY `idx_active` (`is_active`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conn->exec($sql);
    echo "Navigation menus table created successfully.\n";
    
    echo "Creating navigation_items table...\n";
    
    // Create navigation_items table
    $sql = "CREATE TABLE IF NOT EXISTS `navigation_items` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `menu_id` int(11) NOT NULL,
      `parent_id` int(11) DEFAULT NULL,
      `title` varchar(200) NOT NULL,
      `url` varchar(500) NOT NULL,
      `target` enum('_self','_blank') DEFAULT '_self',
      `css_class` varchar(100) DEFAULT NULL,
      `icon` varchar(100) DEFAULT NULL,
      `sort_order` int(11) DEFAULT 0,
      `is_active` tinyint(1) DEFAULT 1,
      `page_id` int(11) DEFAULT NULL,
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `fk_nav_items_menu` (`menu_id`),
      KEY `fk_nav_items_parent` (`parent_id`),
      KEY `fk_nav_items_page` (`page_id`),
      KEY `idx_sort_active` (`sort_order`, `is_active`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conn->exec($sql);
    echo "Navigation items table created successfully.\n";
    
    echo "Inserting default navigation menu...\n";
    
    // Insert default navigation menu
    $stmt = $conn->prepare("INSERT IGNORE INTO `navigation_menus` (`name`, `slug`, `description`, `location`, `is_active`) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['Main Navigation', 'main-nav', 'Primary website navigation menu', 'header', 1]);
    echo "Default navigation menu inserted.\n";
    
    echo "Inserting default pages...\n";
    
    // Insert default pages
    $pages = [
        ['Home', 'home', '<h1>Welcome to AluMaster</h1><p>Your premier aluminum and glass solutions provider in Ghana.</p>', 'Welcome to AluMaster - Your premier aluminum and glass solutions provider in Ghana.', 'published', 1, 1, 0, 'AluMaster - Premium Aluminum & Glass Solutions in Ghana', 'Leading provider of aluminum and glass solutions including Alucobond cladding, curtain walls, spider glass, and more in Ghana.'],
        ['About Us', 'about', '<h1>About AluMaster</h1><p>We are Ghana\'s leading provider of premium aluminum and glass solutions.</p>', 'Learn about AluMaster and our commitment to quality aluminum and glass solutions.', 'published', 2, 0, 1, 'About AluMaster - Leading Aluminum & Glass Company in Ghana', 'Learn about AluMaster, Ghana\'s premier aluminum and glass solutions company with years of experience in quality installations.'],
        ['Contact', 'contact', '<h1>Contact Us</h1><p>Get in touch with our team for your aluminum and glass needs.</p>', 'Contact AluMaster for professional aluminum and glass solutions in Ghana.', 'published', 3, 0, 1, 'Contact AluMaster - Get Your Free Quote Today', 'Contact AluMaster for professional aluminum and glass solutions. Call +233-541-737-575 or visit our Madina-Accra office.']
    ];
    
    $stmt = $conn->prepare("INSERT IGNORE INTO `pages` (`title`, `slug`, `content`, `excerpt`, `status`, `sort_order`, `is_homepage`, `show_in_nav`, `meta_title`, `meta_description`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($pages as $page) {
        $stmt->execute($page);
    }
    echo "Default pages inserted.\n";
    
    echo "Inserting default navigation items...\n";
    
    // Insert default navigation items
    $items = [
        [1, 'Home', '/', 1, 1, 1],
        [1, 'About', '/about.php', 2, 1, 2],
        [1, 'Services', '/services.php', 3, 1, NULL],
        [1, 'Contact', '/contact.php', 4, 1, 3]
    ];
    
    $stmt = $conn->prepare("INSERT IGNORE INTO `navigation_items` (`menu_id`, `title`, `url`, `sort_order`, `is_active`, `page_id`) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($items as $item) {
        $stmt->execute($item);
    }
    echo "Default navigation items inserted.\n";
    
    echo "\nAll tables and data created successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>