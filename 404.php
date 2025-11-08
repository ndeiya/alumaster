<?php
$page_title = "Page Not Found - AluMaster Aluminum System";
$page_description = "The page you're looking for could not be found. Browse our aluminum and glass solutions or contact us for assistance.";

include 'includes/header.php';
?>

<!-- 404 Error Page -->
<section class="error-page">
    <div class="container">
        <div class="error-content">
            <div class="error-illustration">
                <svg width="200" height="200" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="100" r="80" stroke="#e2e8f0" stroke-width="2" fill="none"/>
                    <path d="M70 70L130 130M130 70L70 130" stroke="#e53e3e" stroke-width="3" stroke-linecap="round"/>
                    <text x="100" y="180" text-anchor="middle" fill="#718096" font-size="24" font-weight="bold">404</text>
                </svg>
            </div>
            
            <div class="error-text">
                <h1>Page Not Found</h1>
                <p>Sorry, the page you're looking for doesn't exist or has been moved. Let's get you back on track.</p>
                
                <div class="error-actions">
                    <a href="index.php" class="btn btn-primary">Go Home</a>
                    <a href="services.php" class="btn btn-secondary">View Services</a>
                    <a href="contact.php" class="btn btn-outline">Contact Us</a>
                </div>
            </div>
        </div>
        
        <!-- Helpful Links -->
        <div class="helpful-links">
            <h3>Popular Pages</h3>
            <div class="links-grid">
                <div class="link-group">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="service/alucobond-cladding">Alucobond Cladding</a></li>
                        <li><a href="service/curtain-wall">Curtain Wall</a></li>
                        <li><a href="service/spider-glass">Spider Glass</a></li>
                        <li><a href="services.php">All Services</a></li>
                    </ul>
                </div>
                
                <div class="link-group">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="index.php#projects">Projects</a></li>
                        <li><a href="index.php#testimonials">Testimonials</a></li>
                    </ul>
                </div>
                
                <div class="link-group">
                    <h4>Get Help</h4>
                    <ul>
                        <li><a href="tel:+233541737575">Call: +233-541-737-575</a></li>
                        <li><a href="mailto:contact@alumastergh.com">Email Us</a></li>
                        <li><a href="contact.php">Get Quote</a></li>
                        <li><a href="https://wa.me/233541737575" target="_blank">WhatsApp</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.error-page {
    padding: 80px 0;
    min-height: 60vh;
    display: flex;
    align-items: center;
}

.error-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
    margin-bottom: 60px;
}

.error-illustration {
    text-align: center;
}

.error-text {
    text-align: left;
}

.error-text h1 {
    font-size: 3rem;
    color: #2d3748;
    margin-bottom: 1rem;
}

.error-text p {
    font-size: 1.125rem;
    color: #718096;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.error-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.helpful-links {
    border-top: 1px solid #e2e8f0;
    padding-top: 40px;
}

.helpful-links h3 {
    font-size: 1.5rem;
    color: #2d3748;
    margin-bottom: 2rem;
    text-align: center;
}

.links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.link-group h4 {
    font-size: 1.125rem;
    color: #2d3748;
    margin-bottom: 1rem;
    border-bottom: 2px solid #3182ce;
    padding-bottom: 0.5rem;
}

.link-group ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.link-group li {
    margin-bottom: 0.5rem;
}

.link-group a {
    color: #718096;
    text-decoration: none;
    transition: color 0.2s ease;
}

.link-group a:hover {
    color: #3182ce;
}

@media (max-width: 768px) {
    .error-content {
        grid-template-columns: 1fr;
        gap: 40px;
        text-align: center;
    }
    
    .error-text {
        text-align: center;
    }
    
    .error-text h1 {
        font-size: 2rem;
    }
    
    .error-actions {
        justify-content: center;
    }
    
    .links-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>