<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "Updating page content...\n";
    
    // Update Home page content
    $home_content = '
    <div class="hero-section">
        <h1>Welcome to AluMaster Aluminum System</h1>
        <p class="hero-subtitle">Ghana\'s Premier Aluminum and Glass Solutions Provider</p>
        <p>Since 2008, we have been transforming Ghana\'s architectural landscape with premium aluminum and glass solutions that combine international quality standards with local expertise.</p>
    </div>
    
    <div class="features-section">
        <h2>Why Choose AluMaster?</h2>
        <div class="features-grid">
            <div class="feature">
                <h3>Quality Materials</h3>
                <p>We use only premium-grade aluminum and glass materials from trusted suppliers.</p>
            </div>
            <div class="feature">
                <h3>Expert Installation</h3>
                <p>Our skilled technicians ensure precise installation and perfect finishing.</p>
            </div>
            <div class="feature">
                <h3>Competitive Pricing</h3>
                <p>Quality doesn\'t have to be expensive. We offer the best value in Ghana.</p>
            </div>
        </div>
    </div>
    
    <div class="cta-section">
        <h2>Ready to Start Your Project?</h2>
        <p>Get a free consultation and quote for your aluminum and glass needs.</p>
        <a href="contact.php" class="btn btn-primary">Get Free Quote</a>
    </div>';
    
    $stmt = $conn->prepare("UPDATE pages SET content = ? WHERE slug = 'home'");
    $stmt->execute([$home_content]);
    echo "Updated Home page content.\n";
    
    // Update About page content
    $about_content = '
    <div class="about-hero">
        <h1>About AluMaster Aluminum System</h1>
        <p class="about-subtitle">Ghana\'s trusted partner for architectural aluminum and glass solutions since 2008.</p>
    </div>
    
    <div class="about-story">
        <h2>Our Story</h2>
        <p>Since 2008, AluMaster Aluminum System has been at the forefront of Ghana\'s architectural transformation, providing premium aluminum and glass solutions that combine international quality standards with local expertise.</p>
        
        <p>Based in Madina-Accra, we have grown from a small local business to become one of Ghana\'s most trusted names in architectural aluminum systems.</p>
        
        <p>Our journey began with a simple vision: to make high-quality aluminum and glass solutions accessible and affordable for every Ghanaian project, from residential homes to large-scale commercial developments.</p>
    </div>
    
    <div class="mission-vision">
        <div class="mission">
            <h3>Our Mission</h3>
            <p>To provide exceptional aluminum and glass solutions that enhance Ghana\'s architectural landscape while maintaining the highest standards of quality, affordability, and customer service.</p>
        </div>
        
        <div class="vision">
            <h3>Our Vision</h3>
            <p>To be West Africa\'s leading aluminum and glass solutions provider, recognized for innovation, reliability, and excellence.</p>
        </div>
    </div>';
    
    $stmt = $conn->prepare("UPDATE pages SET content = ? WHERE slug = 'about'");
    $stmt->execute([$about_content]);
    echo "Updated About page content.\n";
    
    // Update Contact page content
    $contact_content = '
    <div class="contact-hero">
        <h1>Contact AluMaster</h1>
        <p class="contact-subtitle">Get in touch with our team for your aluminum and glass needs.</p>
    </div>
    
    <div class="contact-info">
        <h2>Get In Touch</h2>
        <div class="contact-details">
            <div class="contact-item">
                <h3>Phone</h3>
                <p><a href="tel:+233541737575">+233-541-737-575</a></p>
            </div>
            
            <div class="contact-item">
                <h3>Email</h3>
                <p><a href="mailto:contact@alumastergh.com">contact@alumastergh.com</a></p>
            </div>
            
            <div class="contact-item">
                <h3>Location</h3>
                <p>16 Palace Street<br>Madina-Accra, Ghana</p>
            </div>
            
            <div class="contact-item">
                <h3>WhatsApp</h3>
                <p><a href="https://wa.me/233541737575" target="_blank">Chat with us</a></p>
            </div>
        </div>
    </div>
    
    <div class="services-cta">
        <h2>Our Services</h2>
        <p>We provide comprehensive aluminum and glass solutions including:</p>
        <ul>
            <li>Alucobond Cladding</li>
            <li>Curtain Wall Systems</li>
            <li>Spider Glass Installation</li>
            <li>Sliding Windows & Doors</li>
            <li>Frameless Doors</li>
            <li>PVC Windows</li>
            <li>Sun-breakers</li>
            <li>Stainless Steel Balustrades</li>
        </ul>
        <a href="services.php" class="btn btn-primary">View All Services</a>
    </div>';
    
    $stmt = $conn->prepare("UPDATE pages SET content = ? WHERE slug = 'contact'");
    $stmt->execute([$contact_content]);
    echo "Updated Contact page content.\n";
    
    echo "All page content updated successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>