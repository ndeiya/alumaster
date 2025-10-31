-- Populate services and categories from frontend hardcoded data
-- This script migrates the hardcoded services from services.php to the database

USE `alumaster`;

-- Insert service categories
INSERT INTO `service_categories` (`name`, `slug`, `description`, `sort_order`, `is_active`) VALUES
('Cladding & Walls', 'cladding-walls', 'Exterior cladding and wall systems', 1, 1),
('Glass Systems', 'glass-systems', 'Specialized glass installation systems', 2, 1),
('Doors & Windows', 'doors-windows', 'Window and door solutions', 3, 1),
('Specialty Systems', 'specialty-systems', 'Specialized architectural systems', 4, 1);

-- Insert services
INSERT INTO `services` (`category_id`, `name`, `slug`, `short_description`, `description`, `featured_image`, `status`, `sort_order`) VALUES
(1, 'Alucobond Cladding', 'alucobond-cladding', 'Premium aluminum composite panels for modern facades and building exteriors.', 'Premium aluminum composite panels for modern facades and building exteriors. Our Alucobond cladding systems provide exceptional durability, weather resistance, and aesthetic appeal for commercial and residential buildings.', 'assets/images/services/alucobond-cladding.jpg', 'published', 1),

(1, 'Curtain Wall', 'curtain-wall', 'Structural glazing systems for commercial buildings and high-rise structures.', 'Structural glazing systems for commercial buildings and high-rise structures. Our curtain wall systems provide superior thermal performance, weather sealing, and architectural flexibility for modern construction projects.', 'assets/images/services/curtain-wall.jpg', 'published', 2),

(2, 'Spider Glass', 'spider-glass', 'Point-fixed glazing systems for stunning glass facades and architectural features.', 'Point-fixed glazing systems for stunning glass facades and architectural features. Our spider glass systems create seamless glass surfaces with minimal structural interference, perfect for modern architectural designs.', 'assets/images/services/spider-glass.jpg', 'published', 3),

(3, 'Sliding Windows & Doors', 'sliding-windows-doors', 'High-performance sliding window and door systems for residential and commercial use.', 'High-performance sliding window and door systems for residential and commercial use. Our sliding systems offer smooth operation, excellent sealing, and energy efficiency for any application.', 'assets/images/services/sliding-doors.jpg', 'published', 4),

(3, 'Frameless Door', 'frameless-door', 'Elegant glass door solutions for modern spaces and commercial entrances.', 'Elegant glass door solutions for modern spaces and commercial entrances. Our frameless door systems provide unobstructed views and seamless integration with modern architectural designs.', 'assets/images/services/frameless-door.jpg', 'published', 5),

(3, 'PVC Windows', 'pvc-windows', 'Energy-efficient PVC window systems for residential and commercial applications.', 'Energy-efficient PVC window systems for residential and commercial applications. Our PVC windows offer excellent thermal insulation, low maintenance, and long-lasting performance.', 'assets/images/services/pvc-windows.jpg', 'published', 6),

(4, 'Sun-breakers', 'sun-breakers', 'Solar shading solutions for climate control and energy efficiency.', 'Solar shading solutions for climate control and energy efficiency. Our sun-breaker systems reduce heat gain, improve comfort, and enhance the energy performance of buildings.', 'assets/images/services/sun-breakers.jpg', 'published', 7),

(4, 'Stainless Steel Balustrades', 'steel-balustrades', 'Premium stainless steel railing systems for safety and aesthetic appeal.', 'Premium stainless steel railing systems for safety and aesthetic appeal. Our balustrade systems combine structural integrity with elegant design for both interior and exterior applications.', 'assets/images/services/steel-balustrades.jpg', 'published', 8);