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

// Get service data from database
$service = null;
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT s.*, sc.name as category_name 
                           FROM services s 
                           LEFT JOIN service_categories sc ON s.category_id = sc.id 
                           WHERE s.slug = ? AND s.status = 'published'");
    $stmt->execute([$service_slug]);
    $service_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($service_data) {
        // Map database fields to expected format
        $service = [
            'name' => $service_data['name'],
            'category' => $service_data['category_name'],
            'short_description' => $service_data['short_description'],
            'description' => $service_data['description'],
            'featured_image' => $service_data['featured_image']
        ];
        
        // Update view count
        $stmt = $conn->prepare("UPDATE services SET views = views + 1 WHERE id = ?");
        $stmt->execute([$service_data['id']]);
    }
    
} catch (Exception $e) {
    error_log("Error loading service: " . $e->getMessage());
}

if (!$service) {
    header('Location: services.php');
    exit;
}

$page_title = $service['name'] . " - AluMaster Aluminum System";
$page_description = $service['short_description'];
$body_class = 'service-detail-page';

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

// Get related services (excluding current service)
$related_services = [];
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if ($conn) {
        $stmt = $conn->prepare("
            SELECT s.*, sc.name as category_name, sc.slug as category_slug 
            FROM services s 
            LEFT JOIN service_categories sc ON s.category_id = sc.id 
            WHERE s.slug != ? AND s.status = 'published' 
            ORDER BY s.sort_order ASC, s.name ASC 
            LIMIT 4
        ");
        $stmt->execute([$service_slug]);
        $related_services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    error_log("Error fetching related services: " . $e->getMessage());
}

// Fallback to hardcoded services if database fails or no services found
if (empty($related_services)) {
    $all_services = [
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
        ]
    ];
    
    // Filter out current service and limit to 4
    $related_services = array_filter($all_services, function($s) use ($service_slug) {
        return $s['slug'] !== $service_slug;
    });
    $related_services = array_slice($related_services, 0, 4);
}

include 'includes/header.php';
?>

<!-- Service Hero Section - Blue Gradient -->
<section class="hero" style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); padding: 80px 0; margin-top: -1px;">
    <div class="container">
        <div class="hero-content" style="display: block; text-align: center; max-width: 800px; margin: 0 auto;">
            <!-- Breadcrumb Navigation -->
            <nav class="breadcrumb" style="margin-bottom: 24px;">
                <a href="index.php" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 0.875rem;">Home</a>
                <span style="color: rgba(255, 255, 255, 0.6); margin: 0 8px;">/</span>
                <a href="services.php" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 0.875rem;">Services</a>
                <span style="color: rgba(255, 255, 255, 0.6); margin: 0 8px;">/</span>
                <span style="color: white; font-size: 0.875rem;"><?php echo htmlspecialchars($service['name']); ?></span>
            </nav>
            
            <!-- Service Category Badge -->
            <?php if (!empty($service['category'])): ?>
            <div style="background: rgba(255, 255, 255, 0.2); padding: 8px 16px; border-radius: 50px; display: inline-block; color: white; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 24px;">
                <?php echo htmlspecialchars($service['category']); ?>
            </div>
            <?php endif; ?>
            
            <!-- Service Title -->
            <h1 style="font-size: 3.5rem; font-weight: 700; color: white; margin-bottom: 1.5rem; line-height: 1.1;">
                <?php echo htmlspecialchars($service['name']); ?>
            </h1>
            
            <!-- Service Description -->
            <p style="font-size: 1.25rem; color: rgba(255, 255, 255, 0.9); margin-bottom: 2rem; line-height: 1.6;">
                <?php echo htmlspecialchars($service['short_description']); ?>
            </p>
            
            <!-- Action Buttons -->
            <div class="hero-actions" style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
                <a href="#inquiry-form" 
                   class="btn btn-primary btn-lg" 
                   style="background: white; color: #3b82f6; padding: 16px 32px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 1.125rem; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Get Quote
                </a>
                <a href="tel:+233541737575" 
                   class="btn btn-outline btn-lg" 
                   style="background: transparent; color: white; border: 2px solid white; padding: 16px 32px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 1.125rem; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    +233-541-737-575
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Service Content Section -->
<section class="section" style="background: white; padding: 80px 0;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 64px; align-items: start;">
            <!-- Main Content -->
            <div class="service-detail-main">
                <!-- Service Description -->
                <div style="margin-bottom: 48px;">
                    <h2 style="font-size: 2rem; font-weight: 700; color: #1a202c; margin-bottom: 24px; border-bottom: 3px solid #3b82f6; padding-bottom: 12px; display: inline-block;">
                        Service Overview
                    </h2>
                    <div style="color: #64748b; line-height: 1.8; font-size: 1.125rem;">
                        <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                    </div>
                </div>

                <!-- Key Benefits -->
                <div style="margin-bottom: 48px;">
                    <h2 style="font-size: 2rem; font-weight: 700; color: #1a202c; margin-bottom: 24px; border-bottom: 3px solid #3b82f6; padding-bottom: 12px; display: inline-block;">
                        Key Benefits
                    </h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                        <div style="background: #f8fafc; padding: 24px; border-radius: 12px; border-left: 4px solid #3b82f6;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                <div style="width: 40px; height: 40px; background: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <h3 style="font-size: 1.125rem; font-weight: 600; color: #1a202c; margin: 0;">Premium Quality</h3>
                            </div>
                            <p style="color: #64748b; margin: 0; line-height: 1.6;">High-grade materials and expert craftsmanship ensure long-lasting results.</p>
                        </div>
                        
                        <div style="background: #f8fafc; padding: 24px; border-radius: 12px; border-left: 4px solid #3b82f6;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                <div style="width: 40px; height: 40px; background: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 style="font-size: 1.125rem; font-weight: 600; color: #1a202c; margin: 0;">Cost Effective</h3>
                            </div>
                            <p style="color: #64748b; margin: 0; line-height: 1.6;">Competitive pricing without compromising on quality or service excellence.</p>
                        </div>
                        
                        <div style="background: #f8fafc; padding: 24px; border-radius: 12px; border-left: 4px solid #3b82f6;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                <div style="width: 40px; height: 40px; background: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <h3 style="font-size: 1.125rem; font-weight: 600; color: #1a202c; margin: 0;">Expert Installation</h3>
                            </div>
                            <p style="color: #64748b; margin: 0; line-height: 1.6;">Professional installation by skilled technicians with years of experience.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="service-detail-sidebar">
                <!-- Quick Inquiry Form -->
                <div id="inquiry-form" style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; margin-bottom: 32px;">
                    <h3 style="font-size: 1.5rem; font-weight: 600; color: #1a202c; margin-bottom: 24px; text-align: center;">Get a Quote</h3>
                    
                    <?php if ($form_success): ?>
                        <div style="background: #dcfce7; border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 24px; height: 24px; background: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 style="font-weight: 600; color: #15803d; margin: 0 0 4px 0;">Inquiry Sent!</h4>
                                    <p style="color: #166534; margin: 0; font-size: 0.875rem;">We'll get back to you within 24 hours.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($form_errors)): ?>
                        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
                            <div style="display: flex; align-items: start; gap: 12px;">
                                <div style="width: 24px; height: 24px; background: #dc2626; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <ul style="color: #991b1b; margin: 0; padding-left: 16px;">
                                        <?php foreach ($form_errors as $error): ?>
                                            <li style="font-size: 0.875rem;"><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!$form_success): ?>
                    <form method="POST" action="">
                        <div style="margin-bottom: 20px;">
                            <label for="name" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 0.875rem;">
                                Name <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text" id="name" name="name" required 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                   style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s ease; box-sizing: border-box;">
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label for="email" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 0.875rem;">
                                Email <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="email" id="email" name="email" required 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                   style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s ease; box-sizing: border-box;">
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label for="phone" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 0.875rem;">
                                Phone <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="tel" id="phone" name="phone" required 
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                   style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s ease; box-sizing: border-box;">
                        </div>
                        
                        <div style="margin-bottom: 24px;">
                            <label for="message" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 0.875rem;">
                                Message <span style="color: #dc2626;">*</span>
                            </label>
                            <textarea id="message" name="message" rows="4" required 
                                      placeholder="Tell us about your project..."
                                      style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s ease; resize: vertical; box-sizing: border-box;"><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" name="submit_inquiry" 
                                style="width: 100%; background: #3b82f6; color: white; padding: 16px; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.3s ease;">
                            Send Inquiry
                        </button>
                    </form>
                    <?php endif; ?>
                </div>

                <!-- Contact Info -->
                <div style="background: white; border-radius: 16px; padding: 32px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
                    <h3 style="font-size: 1.5rem; font-weight: 600; color: #1a202c; margin-bottom: 24px; text-align: center;">Contact Information</h3>
                    
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px; padding: 16px; background: #f8fafc; border-radius: 12px;">
                        <div style="width: 48px; height: 48px; background: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; color: #64748b; margin-bottom: 4px;">Phone</div>
                            <a href="tel:+233541737575" style="color: #1a202c; text-decoration: none; font-weight: 600;">+233-541-737-575</a>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px; padding: 16px; background: #f8fafc; border-radius: 12px;">
                        <div style="width: 48px; height: 48px; background: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; color: #64748b; margin-bottom: 4px;">Email</div>
                            <a href="mailto:alumaster75@gmail.com" style="color: #1a202c; text-decoration: none; font-weight: 600;">alumaster75@gmail.com</a>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 16px; padding: 16px; background: #f8fafc; border-radius: 12px;">
                        <div style="width: 48px; height: 48px; background: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; color: #64748b; margin-bottom: 4px;">Location</div>
                            <div style="color: #1a202c; font-weight: 600; line-height: 1.4;">16 Palace Street<br>Madina-Accra, Ghana</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Services Section -->
<section style="background: #f8fafc; padding: 80px 0;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 64px;">
            <h2 style="font-size: 2.25rem; font-weight: 700; color: #1a202c; margin-bottom: 16px;">
                Other Services
            </h2>
            <p style="font-size: 1.125rem; color: #64748b; max-width: 600px; margin: 0 auto;">
                Explore our complete range of aluminum and glass solutions
            </p>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 32px; margin-bottom: 48px;">
            <?php foreach ($related_services as $related_service): ?>
            <div class="related-service-card" style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; border: 1px solid #e2e8f0;">
                <div style="position: relative; height: 200px; overflow: hidden;">
                    <img src="<?php echo htmlspecialchars($related_service['featured_image'] ?? 'assets/images/services/default-service.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($related_service['name']); ?>" 
                         style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;"
                         onerror="this.src='assets/images/services/default-service.jpg'">
                    <div style="position: absolute; inset: 0; background: rgba(0, 0, 0, 0.7); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;" class="service-overlay">
                        <a href="service-detail.php?service=<?php echo urlencode($related_service['slug']); ?>" 
                           style="color: white; text-decoration: none; padding: 12px; border-radius: 50%; background: rgba(255, 255, 255, 0.2); transition: all 0.3s ease;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <div style="padding: 24px;">
                    <?php if (!empty($related_service['category_name'])): ?>
                    <div style="font-size: 0.75rem; font-weight: 600; color: #3b82f6; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">
                        <?php echo htmlspecialchars($related_service['category_name']); ?>
                    </div>
                    <?php endif; ?>
                    <h3 style="margin-bottom: 12px;">
                        <a href="service-detail.php?service=<?php echo urlencode($related_service['slug']); ?>" 
                           style="color: #1a202c; text-decoration: none; font-size: 1.25rem; font-weight: 600; transition: color 0.3s ease;">
                            <?php echo htmlspecialchars($related_service['name']); ?>
                        </a>
                    </h3>
                    <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6; font-size: 0.875rem;">
                        <?php echo htmlspecialchars($related_service['short_description']); ?>
                    </p>
                    <div style="display: flex; gap: 12px; justify-content: center;">
                        <a href="service-detail.php?service=<?php echo urlencode($related_service['slug']); ?>" 
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
        </div>
        
        <!-- View All Services Button -->
        <div style="text-align: center;">
            <a href="services.php" 
               style="background: #3b82f6; color: white; padding: 16px 32px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 1.125rem; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                View All Services
            </a>
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
<sty
le>
/* Service Detail Page Styles */
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

/* Form Input Focus States */
input:focus, textarea:focus {
    border-color: #3b82f6 !important;
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
}

/* Button Hover Effects */
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

button[type="submit"]:hover {
    background: #2563eb !important;
    transform: translateY(-1px);
}

/* Breadcrumb Hover Effects */
.breadcrumb a:hover {
    color: white !important;
}

/* Contact Info Hover Effects */
.contact-info a:hover {
    color: #3b82f6 !important;
}

/* Related Services Card Hover Effects */
.related-service-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    border-color: #3b82f6 !important;
}

.related-service-card:hover img {
    transform: scale(1.05);
}

.related-service-card:hover .service-overlay {
    opacity: 1 !important;
}

.related-service-card:hover .service-overlay a {
    background: #3b82f6 !important;
    transform: scale(1.1);
}

.related-service-card h3 a:hover {
    color: #3b82f6 !important;
}

.related-service-card a[style*="background: #3b82f6"]:hover {
    background: #2563eb !important;
    transform: translateY(-1px);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .service-detail-main {
        grid-template-columns: 1fr !important;
        gap: 32px !important;
    }
}

@media (max-width: 768px) {
    .hero h1 {
        font-size: 2.5rem !important;
    }
    
    .hero p {
        font-size: 1rem !important;
    }
    
    .hero-actions {
        flex-direction: column !important;
        align-items: center !important;
    }
    
    .hero-actions .btn {
        width: 100% !important;
        max-width: 300px !important;
        justify-content: center !important;
    }
    
    .service-detail-main {
        grid-template-columns: 1fr !important;
        gap: 32px !important;
    }
    
    .service-detail-main h2 {
        font-size: 1.5rem !important;
    }
    
    .service-detail-sidebar {
        order: -1 !important;
    }
    
    .service-detail-sidebar > div {
        padding: 24px !important;
    }
    
    /* Related Services Grid */
    .related-service-card {
        margin-bottom: 24px !important;
    }
    
    .related-service-card > div:last-child {
        padding: 20px !important;
    }
    
    /* Key Benefits Grid */
    .service-detail-main > div:nth-child(2) > div {
        grid-template-columns: 1fr !important;
        gap: 16px !important;
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
    
    .service-detail-sidebar > div {
        padding: 20px !important;
    }
    
    .hero-actions .btn {
        padding: 12px 24px !important;
        font-size: 1rem !important;
    }
    
    /* Related Services Mobile */
    .related-service-card > div:last-child {
        padding: 16px !important;
    }
    
    .related-service-card h3 {
        font-size: 1.125rem !important;
    }
}

/* Animation for smooth scrolling to form */
html {
    scroll-behavior: smooth;
}

/* Loading animation for form submission */
button[type="submit"]:active {
    transform: scale(0.98);
}

/* Enhanced focus states for accessibility */
a:focus, button:focus, input:focus, textarea:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Print styles */
@media print {
    .hero-actions, .service-detail-sidebar, .cta-buttons {
        display: none !important;
    }
    
    .hero {
        background: white !important;
        color: black !important;
    }
    
    .hero h1, .hero p {
        color: black !important;
    }
}
</style>