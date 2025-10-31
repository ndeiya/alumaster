<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

// Get service slug from URL
$service_slug = $_GET['service'] ?? '';

if (empty($service_slug)) {
    header('Location: services.php');
    exit;
}

// Static service data (works without database)
$services_data = [
    'alucobond-cladding' => [
        'name' => 'Alucobond Cladding',
        'category' => 'Cladding & Walls',
        'short_description' => 'Premium aluminum composite panels for modern facades',
        'description' => 'Alucobond cladding represents the pinnacle of modern architectural design, offering unparalleled versatility and aesthetic appeal for contemporary building facades. Our premium aluminum composite panels combine durability with stunning visual impact.',
        'features' => [
            'Weather-resistant aluminum composite construction',
            'Wide range of colors and finishes available',
            'Lightweight yet incredibly strong',
            'Fire-resistant properties for safety',
            'Low maintenance requirements',
            'Excellent thermal insulation properties'
        ],
        'applications' => 'Perfect for commercial buildings, residential complexes, shopping centers, and office buildings. Our Alucobond cladding systems provide both functional protection and striking visual appeal.',
        'specifications' => [
            'Material' => 'Aluminum Composite Panel',
            'Thickness' => '3mm, 4mm, 6mm',
            'Standard Size' => '1220mm x 2440mm',
            'Fire Rating' => 'Class A2-s1,d0',
            'Warranty' => '15 years',
            'Colors' => '200+ standard colors'
        ],
        'images' => [
            'assets/images/services/alucobond-1.jpg',
            'assets/images/services/alucobond-2.jpg',
            'assets/images/services/alucobond-3.jpg'
        ]
    ],
    'curtain-wall' => [
        'name' => 'Curtain Wall',
        'category' => 'Cladding & Walls',
        'short_description' => 'Structural glazing systems for commercial buildings',
        'description' => 'Our curtain wall systems provide the perfect solution for modern commercial and high-rise buildings, offering structural integrity combined with stunning glass facades that maximize natural light and energy efficiency.',
        'features' => [
            'Structural glazing technology',
            'Maximum natural light penetration',
            'Energy-efficient thermal performance',
            'Wind and seismic resistance',
            'Customizable design options',
            'Professional installation service'
        ],
        'applications' => 'Ideal for office buildings, shopping centers, hotels, and high-rise residential complexes where maximum natural light and modern aesthetics are desired.',
        'specifications' => [
            'System Type' => 'Structural Glazing',
            'Glass Thickness' => '6mm - 24mm',
            'Frame Material' => 'Aluminum Alloy',
            'Wind Load' => 'Up to 3.0 kPa',
            'Thermal Performance' => 'U-value: 1.2 W/m²K',
            'Water Resistance' => 'Class E1200'
        ]
    ],
    'spider-glass' => [
        'name' => 'Spider Glass',
        'category' => 'Glass Systems',
        'short_description' => 'Point-fixed glazing for stunning glass facades',
        'description' => 'Spider glass systems create breathtaking architectural features with minimal visual obstruction. Our point-fixed glazing technology allows for maximum transparency while maintaining structural integrity and weather resistance.',
        'features' => [
            'Point-fixed glazing system',
            'Minimal visual obstruction',
            'Maximum transparency',
            'Structural integrity maintained',
            'Weather-resistant design',
            'Customizable configurations'
        ],
        'applications' => 'Perfect for atriums, entrance lobbies, showrooms, and architectural features where maximum visual impact and transparency are required.',
        'specifications' => [
            'Glass Type' => 'Tempered/Laminated',
            'Thickness' => '10mm - 19mm',
            'Fixing Points' => 'Stainless Steel Spiders',
            'Maximum Panel Size' => '3000mm x 2000mm',
            'Load Capacity' => 'Up to 2.5 kN per point',
            'Sealant' => 'Structural Glazing Grade'
        ]
    ],
    'sliding-windows-doors' => [
        'name' => 'Sliding Windows & Doors',
        'category' => 'Doors & Windows',
        'short_description' => 'High-performance sliding systems',
        'description' => 'Our premium sliding window and door systems combine smooth operation with excellent thermal and acoustic performance. Perfect for modern homes and commercial spaces requiring large openings and seamless indoor-outdoor connectivity.',
        'features' => [
            'Smooth sliding operation',
            'Excellent thermal performance',
            'Sound insulation properties',
            'Large opening capabilities',
            'Durable hardware systems',
            'Multiple configuration options'
        ],
        'applications' => 'Residential homes, apartments, offices, restaurants, and any space requiring large openings with smooth operation.',
        'specifications' => [
            'Profile System' => 'Multi-chamber aluminum',
            'Glass Options' => 'Single/Double/Triple glazed',
            'Maximum Width' => '6000mm',
            'Maximum Height' => '3000mm',
            'Thermal Break' => 'Yes (Polyamide)',
            'Hardware' => 'German engineering'
        ]
    ],
    'frameless-door' => [
        'name' => 'Frameless Door',
        'category' => 'Doors & Windows',
        'short_description' => 'Elegant glass door solutions',
        'description' => 'Our frameless glass doors provide the ultimate in modern elegance and functionality. These systems offer unobstructed views while maintaining security and weather resistance for both interior and exterior applications.',
        'features' => [
            'Completely frameless design',
            'Unobstructed views',
            'Modern elegant appearance',
            'Security features included',
            'Weather-resistant sealing',
            'Smooth operation mechanisms'
        ],
        'applications' => 'Office entrances, retail stores, hotels, residential entrances, and interior partitions where modern aesthetics are paramount.',
        'specifications' => [
            'Glass Type' => 'Tempered Safety Glass',
            'Thickness' => '12mm - 19mm',
            'Maximum Size' => '2500mm x 3000mm',
            'Hardware' => 'Stainless Steel 316',
            'Locking System' => 'Multi-point locking',
            'Sealing' => 'EPDM weather seals'
        ]
    ],
    'pvc-windows' => [
        'name' => 'PVC Windows',
        'category' => 'Doors & Windows',
        'short_description' => 'Energy-efficient PVC window systems',
        'description' => 'Our PVC window systems offer exceptional energy efficiency, durability, and low maintenance requirements. These windows provide excellent thermal and acoustic insulation while maintaining attractive aesthetics.',
        'features' => [
            'Superior energy efficiency',
            'Low maintenance requirements',
            'Excellent insulation properties',
            'Durable construction',
            'Multiple color options',
            'Cost-effective solution'
        ],
        'applications' => 'Residential buildings, schools, hospitals, and commercial buildings where energy efficiency and cost-effectiveness are priorities.',
        'specifications' => [
            'Profile System' => 'Multi-chamber PVC',
            'Glazing' => 'Double/Triple glazed',
            'U-Value' => '1.1 W/m²K',
            'Air Permeability' => 'Class 4',
            'Water Tightness' => 'Class 9A',
            'Wind Load' => 'Class C5'
        ]
    ],
    'sun-breakers' => [
        'name' => 'Sun-breakers',
        'category' => 'Specialty Systems',
        'short_description' => 'Solar shading solutions for climate control',
        'description' => 'Our sun-breaker systems provide effective solar control while maintaining architectural aesthetics. These systems reduce cooling costs, prevent glare, and protect interior furnishings from UV damage.',
        'features' => [
            'Effective solar control',
            'Reduced cooling costs',
            'Glare prevention',
            'UV protection',
            'Architectural integration',
            'Automated control options'
        ],
        'applications' => 'Office buildings, schools, hospitals, residential buildings, and any structure requiring solar control and energy efficiency.',
        'specifications' => [
            'Material' => 'Aluminum Louvers',
            'Blade Width' => '100mm - 300mm',
            'Control System' => 'Manual/Motorized',
            'Finish Options' => 'Powder coated/Anodized',
            'Wind Resistance' => 'Up to 150 km/h',
            'Solar Control' => 'Up to 90% reduction'
        ]
    ],
    'steel-balustrades' => [
        'name' => 'Stainless Steel Balustrades',
        'category' => 'Specialty Systems',
        'short_description' => 'Premium stainless steel railing systems',
        'description' => 'Our stainless steel balustrade systems combine safety, durability, and modern aesthetics. These systems are perfect for both interior and exterior applications, offering corrosion resistance and minimal maintenance.',
        'features' => [
            'Corrosion-resistant materials',
            'Modern aesthetic appeal',
            'Minimal maintenance required',
            'Safety compliance',
            'Durable construction',
            'Customizable designs'
        ],
        'applications' => 'Staircases, balconies, terraces, walkways, and any area requiring safety barriers with modern aesthetics.',
        'specifications' => [
            'Material Grade' => 'Stainless Steel 316',
            'Handrail Diameter' => '42mm - 50mm',
            'Post Spacing' => 'Maximum 1500mm',
            'Glass Thickness' => '12mm - 17mm (if applicable)',
            'Height Options' => '1000mm - 1200mm',
            'Finish' => 'Brushed/Polished'
        ]
    ]
];

// Get service data
$service = $services_data[$service_slug] ?? null;

if (!$service) {
    header('Location: services.php');
    exit;
}

$page_title = $service['name'] . " - AluMaster Aluminum System";
$page_description = $service['short_description'];

// Handle inquiry form submission
$form_success = false;
$form_errors = [];

if ($_POST && isset($_POST['submit_inquiry'])) {
    // Validate form data
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');
    
    // Validation
    if (empty($name)) $form_errors[] = "Name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_errors[] = "Valid email is required.";
    }
    if (empty($phone)) $form_errors[] = "Phone number is required.";
    if (empty($message)) $form_errors[] = "Message is required.";
    
    // If no errors, show success (in real app, would save to database)
    if (empty($form_errors)) {
        $form_success = true;
        
        // In a real application, you would:
        // 1. Save to database
        // 2. Send email notification
        // For now, we just show success message
    }
}

include 'includes/header.php';
?>

    <!-- Service Hero -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <nav class="breadcrumb">
                        <a href="index.php">Home</a>
                        <span class="breadcrumb-separator">/</span>
                        <a href="services.php">Services</a>
                        <span class="breadcrumb-separator">/</span>
                        <span class="breadcrumb-current"><?php echo htmlspecialchars($service['name']); ?></span>
                    </nav>
                    <div class="service-category-badge"><?php echo htmlspecialchars($service['category']); ?></div>
                    <h1><?php echo htmlspecialchars($service['name']); ?></h1>
                    <p><?php echo htmlspecialchars($service['short_description']); ?></p>
                    <div class="hero-actions">
                        <a href="#inquiry-form" class="btn btn-primary btn-lg">Get Quote</a>
                        <a href="tel:+233541737575" class="btn btn-secondary btn-lg">Call Now</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="assets/images/services/<?php echo $service_slug; ?>-hero.jpg" 
                         alt="<?php echo htmlspecialchars($service['name']); ?>" 
                         onerror="this.src='assets/images/service-placeholder.jpg'">
                </div>
            </div>
        </div>
    </section>

    <!-- Service Content -->
    <section class="section section-white">
        <div class="container">
            <div class="service-detail-layout">
                <div class="service-detail-main">
                    <!-- Service Description -->
                    <div class="service-description">
                        <h2 class="service-section-title">Service Overview</h2>
                        <div class="service-content">
                            <p><?php echo htmlspecialchars($service['description']); ?></p>
                        </div>
                    </div>

                    <!-- Key Features -->
                    <?php if (isset($service['features'])): ?>
                    <div class="service-features">
                        <h2 class="service-section-title">Key Features</h2>
                        <ul class="features-list">
                            <?php foreach ($service['features'] as $feature): ?>
                            <li>
                                <div class="check-icon">✓</div>
                                <?php echo htmlspecialchars($feature); ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Applications -->
                    <?php if (isset($service['applications'])): ?>
                    <div class="service-applications">
                        <h2 class="service-section-title">Applications</h2>
                        <div class="service-content">
                            <p><?php echo htmlspecialchars($service['applications']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Technical Specifications -->
                    <?php if (isset($service['specifications'])): ?>
                    <div class="service-specifications">
                        <h2 class="service-section-title">Technical Specifications</h2>
                        <div class="specs-table-wrapper">
                            <table class="specs-table">
                                <?php foreach ($service['specifications'] as $spec => $value): ?>
                                <tr>
                                    <td class="spec-label"><?php echo htmlspecialchars($spec); ?></td>
                                    <td class="spec-value"><?php echo htmlspecialchars($value); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="service-detail-sidebar">
                    <!-- Quick Inquiry Form -->
                    <div class="sidebar-card" id="inquiry-form">
                        <h3 class="sidebar-card-title">Get a Quote</h3>
                        
                        <?php if ($form_success): ?>
                            <div class="alert alert-success">
                                <div class="alert-icon">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div class="alert-content">
                                    <h4>Inquiry Sent!</h4>
                                    <p>We'll get back to you within 24 hours.</p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($form_errors)): ?>
                            <div class="alert alert-error">
                                <div class="alert-icon">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="alert-content">
                                    <ul>
                                        <?php foreach ($form_errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!$form_success): ?>
                        <form class="inquiry-form" method="POST" action="">
                            <div class="form-group">
                                <label for="name" class="form-label">Name <span class="required">*</span></label>
                                <input type="text" id="name" name="name" class="form-input" required 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="email" class="form-label">Email <span class="required">*</span></label>
                                <input type="email" id="email" name="email" class="form-input" required 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone <span class="required">*</span></label>
                                <input type="tel" id="phone" name="phone" class="form-input" required 
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="message" class="form-label">Message <span class="required">*</span></label>
                                <textarea id="message" name="message" class="form-textarea" rows="4" required 
                                          placeholder="Tell us about your project..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                            </div>

                            <button type="submit" name="submit_inquiry" class="btn btn-primary btn-block">
                                Send Inquiry
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>

                    <!-- Contact Info -->
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">Contact Information</h3>
                        <div class="contact-info">
                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <div class="contact-info-content">
                                    <div class="contact-info-label">Phone</div>
                                    <a href="tel:+233541737575" class="contact-info-value">+233-541-737-575</a>
                                </div>
                            </div>

                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="contact-info-content">
                                    <div class="contact-info-label">Email</div>
                                    <a href="mailto:alumaster75@gmail.com" class="contact-info-value">alumaster75@gmail.com</a>
                                </div>
                            </div>

                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div class="contact-info-content">
                                    <div class="contact-info-label">Location</div>
                                    <div class="contact-info-value">16 Palace Street<br>Madina-Accra, Ghana</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Services -->
    <section class="section cta-section">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">Other Services</h2>
                <p class="section-subtitle">Explore our complete range of aluminum and glass solutions</p>
            </div>
            
            <div class="cta-buttons">
                <a href="services.php" class="btn btn-primary btn-lg">View All Services</a>
                <a href="contact.php" class="btn btn-secondary btn-lg">Contact Us</a>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/233541737575?text=Hello%20AluMaster,%20I'm%20interested%20in%20<?php echo urlencode($service['name']); ?>" 
   class="whatsapp-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
    </svg>
</a>