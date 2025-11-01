-- Create homepage sections table for editable content
USE `alumaster`;

-- Table structure for table `homepage_sections`
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

-- Insert default homepage sections
INSERT INTO `homepage_sections` (`section_key`, `section_name`, `content`, `settings`, `is_active`, `sort_order`) VALUES
('hero', 'Hero Section', '{"title": "Where Quality", "highlight": "Meets Affordability", "description": "Professional aluminum and glass systems for modern architecture. From curtain walls to specialty installations, we deliver excellence in every project.", "primary_button_text": "Call Now", "primary_button_link": "tel:+233541737575", "secondary_button_text": "View Portfolio", "secondary_button_link": "services.php", "background_image": "assets/images/hero-building.jpg"}', '{"background_color": "#1a1a1a", "text_color": "#ffffff"}', 1, 1),

('services', 'Our Expertise Section', '{"title": "Our Expertise", "subtitle": "Comprehensive aluminum and glass solutions for modern construction projects", "services": [{"name": "Alucobond Cladding", "description": "Premium aluminum composite panels for modern facades", "icon": "building"}, {"name": "Curtain Wall", "description": "Structural glazing systems for commercial buildings", "icon": "grid"}, {"name": "Spider Glass", "description": "Point-fixed glazing for stunning glass facades", "icon": "lightbulb"}, {"name": "Sliding Doors", "description": "High-performance sliding window and door systems", "icon": "columns"}, {"name": "Frameless Door", "description": "Elegant glass door solutions for modern spaces", "icon": "lock"}, {"name": "PVC Windows", "description": "Energy-efficient PVC window systems", "icon": "clipboard"}, {"name": "Sun-breakers", "description": "Solar shading solutions for climate control", "icon": "sun"}, {"name": "Steel Balustrades", "description": "Premium stainless steel railing systems", "icon": "shield"}]}', '{"background_color": "#ffffff", "columns": 4}', 1, 2),

('why_choose', 'Why Choose Us Section', '{"title": "Why Choose AluMaster?", "description": "With years of experience in the aluminum and glass industry, we have established ourselves as Ghana premier provider of architectural glazing solutions. Our commitment to quality and affordability sets us apart.", "features": ["Premium quality materials", "Expert installation team", "Competitive pricing", "Timely project completion"], "image": "assets/images/why-choose-us.jpg"}', '{"background_color": "#ffffff"}', 1, 3),

('contact_cta', 'Contact CTA Section', '{"title": "Get In Touch", "subtitle": "Ready to start your project? Contact us today for a free consultation", "contact_items": [{"title": "Call Us", "icon": "phone", "lines": ["+233-541-737-575", "+233-502-777-703"]}, {"title": "Visit Us", "icon": "location", "lines": ["16 Palace Street", "Madina-Accra, Ghana"]}, {"title": "Email Us", "icon": "email", "lines": ["alumaster75@gmail.com", "www.alumastergh.com"]}], "social_links": [{"platform": "facebook", "url": "https://www.facebook.com/alumastergh"}, {"platform": "instagram", "url": "https://www.instagram.com/alumaster75"}, {"platform": "twitter", "url": "https://twitter.com/alumaster75"}, {"platform": "tiktok", "url": "https://www.tiktok.com/@alumaster75"}], "cta_button_text": "Request Free Quote", "cta_button_link": "contact.php"}', '{"background_color": "#1a1a1a", "text_color": "#ffffff"}', 1, 4);