<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$page_title = "Our Services - AluMaster Aluminum System";
$page_description = "Comprehensive aluminum and glass solutions including Alucobond cladding, curtain walls, spider glass, sliding doors, and more in Ghana.";

// Get all services (in a real app, this would come from database)
$services = [
    [
        'id' => 1,
        'name' => 'Alucobond Cladding',
        'slug' => 'alucobond-cladding',
        'category' => 'Cladding & Walls',
        'description' => 'Premium aluminum composite panels for modern facades and building exteriors.',
        'image' => 'assets/images/services/alucobond-cladding.jpg'
    ],
    [
        'id' => 2,
        'name' => 'Curtain Wall',
        'slug' => 'curtain-wall',
        'category' => 'Cladding & Walls',
        'description' => 'Structural glazing systems for commercial buildings and high-rise structures.',
        'image' => 'assets/images/services/curtain-wall.jpg'
    ],
    [
        'id' => 3,
        'name' => 'Spider Glass',
        'slug' => 'spider-glass',
        'category' => 'Glass Systems',
        'description' => 'Point-fixed glazing systems for stunning glass facades and architectural features.',
        'image' => 'assets/images/services/spider-glass.jpg'
    ],
    [
        'id' => 4,
        'name' => 'Sliding Windows & Doors',
        'slug' => 'sliding-windows-doors',
        'category' => 'Doors & Windows',
        'description' => 'High-performance sliding window and door systems for residential and commercial use.',
        'image' => 'assets/images/services/sliding-doors.jpg'
    ],
    [
        'id' => 5,
        'name' => 'Frameless Door',
        'slug' => 'frameless-door',
        'category' => 'Doors & Windows',
        'description' => 'Elegant glass door solutions for modern spaces and commercial entrances.',
        'image' => 'assets/images/services/frameless-door.jpg'
    ],
    [
        'id' => 6,
        'name' => 'PVC Windows',
        'slug' => 'pvc-windows',
        'category' => 'Doors & Windows',
        'description' => 'Energy-efficient PVC window systems for residential and commercial applications.',
        'image' => 'assets/images/services/pvc-windows.jpg'
    ],
    [
        'id' => 7,
        'name' => 'Sun-breakers',
        'slug' => 'sun-breakers',
        'category' => 'Specialty Systems',
        'description' => 'Solar shading solutions for climate control and energy efficiency.',
        'image' => 'assets/images/services/sun-breakers.jpg'
    ],
    [
        'id' => 8,
        'name' => 'Stainless Steel Balustrades',
        'slug' => 'steel-balustrades',
        'category' => 'Specialty Systems',
        'description' => 'Premium stainless steel railing systems for safety and aesthetic appeal.',
        'image' => 'assets/images/services/steel-balustrades.jpg'
    ]
];

// Get unique categories for filtering
$categories = array_unique(array_column($services, 'category'));

// Filter services if category is selected
$selected_category = $_GET['category'] ?? '';
if ($selected_category) {
    $services = array_filter($services, function($service) use ($selected_category) {
        return $service['category'] === $selected_category;
    });
}

include 'includes/header.php';
?>

    <!-- Services Hero -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <nav class="breadcrumb">
                        <a href="index.php">Home</a>
                        <span class="breadcrumb-separator">/</span>
                        <span class="breadcrumb-current">Services</span>
                    </nav>
                    <h1>Our Services</h1>
                    <p>Comprehensive aluminum and glass solutions for modern construction projects. From residential installations to large-scale commercial developments.</p>
                </div>
                <div class="hero-image">
                    <img src="assets/images/services-hero.jpg" alt="AluMaster services showcase">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Filter -->
    <section class="section section-white">
        <div class="container">
            <!-- Desktop Filter -->
            <div class="services-filter desktop-filter">
                <h2 class="filter-title">Filter by Category</h2>
                <div class="filter-buttons">
                    <a href="services.php" class="filter-btn <?php echo empty($selected_category) ? 'active' : ''; ?>">
                        All Services
                    </a>
                    <?php foreach ($categories as $category): ?>
                    <a href="services.php?category=<?php echo urlencode($category); ?>" 
                       class="filter-btn <?php echo $selected_category === $category ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($category); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Mobile Filter -->
            <div class="mobile-filter">
                <label for="mobileFilterSelect" class="mobile-filter-label">
                    <svg class="filter-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M3 12h18M3 20h18"></path>
                    </svg>
                    Filter by Category
                </label>
                <select id="mobileFilterSelect" class="mobile-filter-select" onchange="window.location.href=this.value">
                    <option value="services.php" <?php echo empty($selected_category) ? 'selected' : ''; ?>>
                        All Services
                    </option>
                    <?php foreach ($categories as $category): ?>
                    <option value="services.php?category=<?php echo urlencode($category); ?>" 
                            <?php echo $selected_category === $category ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Services Grid -->
            <div class="services-grid">
                <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <div class="service-card-image">
                        <img src="<?php echo htmlspecialchars($service['image']); ?>" 
                             alt="<?php echo htmlspecialchars($service['name']); ?>" 
                             class="service-image">
                        <div class="service-card-overlay">
                            <a href="service-detail.php?service=<?php echo urlencode($service['slug']); ?>" 
                               class="service-card-link" aria-label="View <?php echo htmlspecialchars($service['name']); ?>">
                                <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="service-card-content">
                        <div class="service-category"><?php echo htmlspecialchars($service['category']); ?></div>
                        <h3 class="service-card-title">
                            <a href="service-detail.php?service=<?php echo urlencode($service['slug']); ?>">
                                <?php echo htmlspecialchars($service['name']); ?>
                            </a>
                        </h3>
                        <p class="service-card-description">
                            <?php echo htmlspecialchars($service['description']); ?>
                        </p>
                        <div class="service-card-actions">
                            <a href="service-detail.php?service=<?php echo urlencode($service['slug']); ?>" 
                               class="btn btn-primary btn-sm">Learn More</a>
                            <a href="contact.php?service=<?php echo urlencode($service['name']); ?>" 
                               class="btn btn-secondary btn-sm">Get Quote</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($services)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3>No services found</h3>
                <p>No services match the selected category. Please try a different filter.</p>
                <a href="services.php" class="btn btn-primary">View All Services</a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Why Choose Our Services -->
    <section class="section section-white">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">Why Choose Our Services?</h2>
                <p class="section-subtitle">We deliver exceptional quality and value in every project</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3>Quality Materials</h3>
                    <p>We use only premium-grade aluminum and glass materials from trusted suppliers.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3>Expert Installation</h3>
                    <p>Our skilled technicians ensure precise installation and perfect finishing.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h3>Competitive Pricing</h3>
                    <p>Quality doesn't have to be expensive. We offer the best value in Ghana.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3>Warranty & Support</h3>
                    <p>Comprehensive warranty coverage and ongoing support for all installations.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section cta-section">
        <div class="container">
            <div class="cta-content text-center">
                <h2>Ready to Start Your Project?</h2>
                <p>Get a free consultation and quote for your aluminum and glass needs</p>
                <div class="cta-buttons">
                    <a href="contact.php" class="btn btn-primary btn-lg">Get Free Quote</a>
                    <a href="tel:+233541737575" class="btn btn-secondary btn-lg">Call Now</a>
                </div>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>