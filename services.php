<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

// Page metadata
$page_title = 'Our Services - AluMaster Aluminum System';
$page_description = 'Comprehensive aluminum and glass solutions for modern architecture. Quality meets affordability in every project we deliver.';
$body_class = 'services-page';

// Get services from database
function getServices() {
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        
        if ($pdo) {
            $stmt = $pdo->prepare("
                SELECT s.*, sc.name as category_name, sc.slug as category_slug 
                FROM services s 
                LEFT JOIN service_categories sc ON s.category_id = sc.id 
                WHERE s.status = 'published' 
                ORDER BY s.sort_order ASC, s.name ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        error_log("Error fetching services: " . $e->getMessage());
    }
    
    // Fallback to hardcoded services if database fails
    return [
        [
            'id' => 1,
            'name' => 'Alucobond Cladding',
            'slug' => 'alucobond-cladding',
            'short_description' => 'Premium aluminum composite panels for modern building facades with superior weather resistance.',
            'category_name' => 'Cladding & Walls',
            'featured_image' => 'assets/images/services/alucobond-cladding.jpg'
        ],
        [
            'id' => 2,
            'name' => 'Spider Glass',
            'slug' => 'spider-glass',
            'short_description' => 'Structural glazing systems with spider fittings for seamless glass facades and maximum transparency.',
            'category_name' => 'Glass Systems',
            'featured_image' => 'assets/images/services/spider-glass.jpg'
        ],
        [
            'id' => 3,
            'name' => 'Sliding Windows & Doors',
            'slug' => 'sliding-windows-doors',
            'short_description' => 'High-performance sliding systems with smooth operation and excellent thermal insulation.',
            'category_name' => 'Glass Systems',
            'featured_image' => 'assets/images/services/sliding-doors.jpg'
        ],
        [
            'id' => 4,
            'name' => 'Frameless Door',
            'slug' => 'frameless-door',
            'short_description' => 'Elegant frameless glass doors providing unobstructed views and seamless transitions.',
            'category_name' => 'Doors & Windows',
            'featured_image' => 'assets/images/services/frameless-door.jpg'
        ],
        [
            'id' => 5,
            'name' => 'PVC Windows',
            'slug' => 'pvc-windows',
            'short_description' => 'Energy-efficient PVC window systems with superior insulation and low maintenance requirements.',
            'category_name' => 'Doors & Windows',
            'featured_image' => 'assets/images/services/pvc-windows.jpg'
        ],
        [
            'id' => 6,
            'name' => 'Sun-breakers',
            'slug' => 'sun-breakers',
            'short_description' => 'Advanced solar shading solutions that reduce heat gain while maintaining natural light.',
            'category_name' => 'Specialty Systems',
            'featured_image' => 'assets/images/services/sun-breakers.jpg'
        ],
        [
            'id' => 7,
            'name' => 'Stainless Steel Balustrades',
            'slug' => 'steel-balustrades',
            'short_description' => 'Durable and elegant stainless steel railing systems for safety and aesthetic appeal.',
            'category_name' => 'Specialty Systems',
            'featured_image' => 'assets/images/services/steel-balustrades.jpg'
        ],
        [
            'id' => 8,
            'name' => 'Custom Solutions',
            'slug' => 'custom-solutions',
            'short_description' => 'Need something specific? We create tailored aluminum and glass solutions for unique architectural requirements.',
            'category_name' => 'Custom',
            'featured_image' => 'assets/images/services/custom-solutions.jpg'
        ]
    ];
}

$services = getServices();

include 'includes/header.php';
?>

<!-- Services Hero Section - Single Column -->
<section class="hero" style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); padding: 80px 0; margin-top: -1px;">
    <div class="container">
        <div class="hero-content" style="display: block; text-align: center; max-width: 800px; margin: 0 auto;">
            <h1 style="font-size: 3.5rem; font-weight: 700; color: white; margin-bottom: 1.5rem; line-height: 1.1;">Our Services</h1>
            <p style="font-size: 1.25rem; color: rgba(255, 255, 255, 0.9); margin-bottom: 2rem; line-height: 1.6;">
                Comprehensive aluminum and glass solutions for modern architecture. Where Quality Meets Affordability in every project we deliver.
            </p>
            <div style="background: rgba(255, 255, 255, 0.1); padding: 12px 24px; border-radius: 50px; display: inline-block; color: white; font-weight: 600;">
                8 Specialized Services
            </div>
        </div>
    </div>
</section>

<!-- Services Grid Section -->
<section class="section section-white">
    <div class="container">
        <div class="services-grid">
            <?php foreach ($services as $service): ?>
            <div class="service-card" style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; border: 1px solid #e2e8f0;">
                <div class="service-card-image" style="position: relative; height: 240px; overflow: hidden;">
                    <img src="<?php echo htmlspecialchars($service['featured_image'] ?? 'assets/images/services/default-service.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($service['name']); ?>" 
                         class="service-image" 
                         style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;"
                         onerror="this.src='assets/images/services/default-service.jpg'">
                    <div class="service-card-overlay" style="position: absolute; inset: 0; background: rgba(0, 0, 0, 0.7); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
                        <a href="service-detail.php?service=<?php echo urlencode($service['slug']); ?>" 
                           class="service-card-link" 
                           style="color: white; text-decoration: none; padding: 12px; border-radius: 50%; background: rgba(255, 255, 255, 0.2); transition: all 0.3s ease;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="service-card-content" style="padding: 24px;">
                    <?php if (!empty($service['category_name'])): ?>
                    <div class="service-category" style="font-size: 0.75rem; font-weight: 600; color: #3b82f6; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">
                        <?php echo htmlspecialchars($service['category_name']); ?>
                    </div>
                    <?php endif; ?>
                    <h3 class="service-card-title" style="margin-bottom: 12px;">
                        <a href="service-detail.php?service=<?php echo urlencode($service['slug']); ?>" 
                           style="color: #1a202c; text-decoration: none; font-size: 1.25rem; font-weight: 600; transition: color 0.3s ease;">
                            <?php echo htmlspecialchars($service['name']); ?>
                        </a>
                    </h3>
                    <p class="service-card-description" style="color: #64748b; margin-bottom: 24px; line-height: 1.6;">
                        <?php echo htmlspecialchars($service['short_description']); ?>
                    </p>
                    <div class="service-card-actions" style="display: flex; gap: 12px; justify-content: center;">
                        <a href="service-detail.php?service=<?php echo urlencode($service['slug']); ?>" 
                           class="btn btn-primary btn-sm" 
                           style="background: #3b82f6; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.875rem; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;">
                            Learn More
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- Custom Solutions Card - Blue Background -->
            <div class="service-card" style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; border: 1px solid #3b82f6; color: white;">
                <div class="service-card-content" style="padding: 40px 24px; text-align: center; height: 100%; display: flex; flex-direction: column; justify-content: center;">
                    <div class="feature-icon" style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; color: white;">
                        <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.5rem; font-weight: 600; color: white; margin-bottom: 16px;">Custom Solutions</h3>
                    <p style="color: rgba(255, 255, 255, 0.9); margin-bottom: 32px; line-height: 1.6; font-size: 1rem;">
                        Need something specific? We create tailored aluminum and glass solutions for unique architectural requirements.
                    </p>
                    <a href="contact.php" 
                       class="btn" 
                       style="background: white; color: #3b82f6; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.875rem; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px; justify-content: center; margin-top: auto;">
                        Get Quote
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose AluMaster Section -->
<section class="section" style="background: #f8fafc; padding: 80px 0;">
    <div class="container">
        <div class="section-header" style="text-align: center; margin-bottom: 64px;">
            <h2 class="section-title" style="font-size: 2.25rem; font-weight: 700; color: #1a202c; margin-bottom: 16px;">
                Why Choose AluMaster?
            </h2>
            <p class="section-subtitle" style="font-size: 1.125rem; color: #64748b; max-width: 600px; margin: 0 auto;">
                Where Quality Meets Affordability - Our commitment to excellence in every project
            </p>
        </div>
        
        <div class="features-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 32px;">
            <div class="feature-card" style="background: white; padding: 32px; border-radius: 16px; text-align: center; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.3s ease;">
                <div class="feature-icon" style="width: 64px; height: 64px; background: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: white;">
                    <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 600; color: #1a202c; margin-bottom: 12px;">Quality Assurance</h3>
                <p style="color: #64748b; line-height: 1.6;">Premium materials and expert craftsmanship in every installation</p>
            </div>
            
            <div class="feature-card" style="background: white; padding: 32px; border-radius: 16px; text-align: center; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.3s ease;">
                <div class="feature-icon" style="width: 64px; height: 64px; background: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: white;">
                    <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 600; color: #1a202c; margin-bottom: 12px;">Affordable Pricing</h3>
                <p style="color: #64748b; line-height: 1.6;">Competitive rates without compromising on quality or service</p>
            </div>
            
            <div class="feature-card" style="background: white; padding: 32px; border-radius: 16px; text-align: center; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.3s ease;">
                <div class="feature-icon" style="width: 64px; height: 64px; background: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: white;">
                    <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 600; color: #1a202c; margin-bottom: 12px;">Expert Installation</h3>
                <p style="color: #64748b; line-height: 1.6;">Skilled technicians ensuring perfect installation every time</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); padding: 80px 0; text-align: center; margin: 0; border: none; outline: none;">
    <div class="container">
        <h2 style="font-size: 2.25rem; font-weight: 700; color: white; margin-bottom: 16px;">Ready to Start Your Project?</h2>
        <p style="font-size: 1.125rem; color: rgba(255, 255, 255, 0.9); margin-bottom: 32px;">Contact us today for a free consultation and quote</p>
        
        <div class="cta-buttons" style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
            <a href="tel:+233541737575" 
               class="btn btn-outline btn-lg" 
               style="background: transparent; color: white; border: 2px solid white; padding: 16px 32px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 1.125rem; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                +233-541-737-575
            </a>
            <a href="contact.php" 
               class="btn btn-primary btn-lg" 
               style="background: white; color: #3b82f6; padding: 16px 32px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 1.125rem; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Get Quote
            </a>
        </div>
    </div>
</section>

<style>
/* Remove any potential overlays or borders */
section {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
}

body {
    margin: 0 !important;
    padding: 0 !important;
}

/* Hover effects */
.service-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    border-color: #3b82f6 !important;
}

.service-card:hover .service-image {
    transform: scale(1.05);
}

.service-card:hover .service-card-overlay {
    opacity: 1;
}

.service-card:hover .service-card-link {
    background: #3b82f6 !important;
    transform: scale(1.1);
}

.service-card-title a:hover {
    color: #3b82f6 !important;
}

.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    border-color: #3b82f6 !important;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn-outline:hover {
    background: white !important;
    color: #3b82f6 !important;
}

.btn-primary:hover {
    background: #2563eb !important;
}

/* Custom Solutions Card Hover Effects */
.service-card:has(.feature-icon) {
    min-height: 400px;
}

.service-card:has(.feature-icon):hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.3), 0 10px 10px -5px rgba(59, 130, 246, 0.2) !important;
}

.service-card:has(.feature-icon) .btn:hover {
    background: rgba(255, 255, 255, 0.9) !important;
    transform: translateY(-1px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero h1 {
        font-size: 2.5rem !important;
    }
    
    .hero p {
        font-size: 1rem !important;
    }
    
    .section-title {
        font-size: 1.875rem !important;
    }
    
    .services-grid {
        grid-template-columns: 1fr !important;
        gap: 24px !important;
    }
    
    .features-grid {
        grid-template-columns: 1fr !important;
        gap: 24px !important;
    }
    
    .cta-buttons {
        flex-direction: column !important;
        align-items: center !important;
    }
    
    .cta-buttons .btn {
        width: 100% !important;
        max-width: 300px !important;
        justify-content: center !important;
    }
    
    .service-card-content {
        padding: 20px !important;
    }
    
    .feature-card {
        padding: 24px !important;
    }
}

@media (max-width: 480px) {
    .hero {
        padding: 60px 0 !important;
    }
    
    .hero h1 {
        font-size: 2rem !important;
    }
    
    .section {
        padding: 60px 0 !important;
    }
    
    .service-card-content {
        padding: 16px !important;
    }
    
    .feature-card {
        padding: 20px !important;
    }
}
</style>

<?php include 'includes/footer.php'; ?>