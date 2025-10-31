<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$page_title = "About Us - AluMaster Aluminum System";
$page_description = "Learn about AluMaster Aluminum System, Ghana's trusted partner for architectural aluminum and glass solutions since 2008.";

include 'includes/header.php';
?>

<main>
    <!-- About Hero -->
    <section class="hero-section about-hero">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content">
                <nav class="breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-current">About</span>
                </nav>
                <h1 class="hero-title">About AluMaster</h1>
                <p class="hero-subtitle">Ghana's trusted partner for architectural aluminum and glass solutions</p>
            </div>
        </div>
    </section>

    <!-- Company Story -->
    <section class="section">
        <div class="container">
            <div class="about-intro">
                <div class="about-intro-content">
                    <div class="section-eyebrow">Our Story</div>
                    <h2 class="section-title">Building Ghana's Future with Quality Aluminum Solutions</h2>
                    <div class="about-text">
                        <p>Since 2008, AluMaster Aluminum System has been at the forefront of Ghana's architectural transformation, providing premium aluminum and glass solutions that combine international quality standards with local expertise. Based in Madina-Accra, we have grown from a small local business to become one of Ghana's most trusted names in architectural aluminum systems.</p>
                        
                        <p>Our journey began with a simple vision: to make high-quality aluminum and glass solutions accessible and affordable for every Ghanaian project, from residential homes to large-scale commercial developments. Today, we proudly serve architects, contractors, real estate developers, and homeowners across Ghana with our comprehensive range of services.</p>
                        
                        <p>What sets us apart is our commitment to excellence in every project, regardless of size. Whether you're building a single-family home or a multi-story commercial complex, we bring the same level of professionalism, quality materials, and expert craftsmanship to every installation.</p>
                    </div>
                </div>
                <div class="about-intro-image">
                    <img src="assets/images/about-hero.jpg" alt="AluMaster team working on aluminum installation" class="about-image">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="section section-white">
        <div class="container">
            <div class="mission-vision-grid">
                <div class="mission-vision-card">
                    <div class="mission-vision-icon">
                        <svg class="icon-2xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <h3 class="mission-vision-title">Our Mission</h3>
                    <p class="mission-vision-description">To provide exceptional aluminum and glass solutions that enhance Ghana's architectural landscape while maintaining the highest standards of quality, affordability, and customer service. We are committed to being the trusted partner for every construction project, big or small.</p>
                </div>

                <div class="mission-vision-card">
                    <div class="mission-vision-icon">
                        <svg class="icon-2xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <h3 class="mission-vision-title">Our Vision</h3>
                    <p class="mission-vision-description">To be West Africa's leading aluminum and glass solutions provider, recognized for innovation, reliability, and excellence. We envision a future where every building in Ghana benefits from our premium aluminum systems, contributing to sustainable and beautiful architectural development.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="section">
        <div class="container">
            <div class="section-header text-center">
                <div class="section-eyebrow">Why Choose AluMaster</div>
                <h2 class="section-title">What Makes Us Different</h2>
                <p class="section-description">We combine international quality standards with local expertise and competitive pricing</p>
            </div>

            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="benefit-title">15+ Years Experience</h3>
                    <p class="benefit-description">Over a decade of expertise in aluminum and glass installations across Ghana, with hundreds of successful projects completed.</p>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h3 class="benefit-title">Quality Meets Affordability</h3>
                    <p class="benefit-description">Premium materials and expert craftsmanship at competitive prices, making quality aluminum solutions accessible to all.</p>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="benefit-title">Expert Team</h3>
                    <p class="benefit-description">Skilled professionals with extensive training in modern aluminum and glass installation techniques and safety standards.</p>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="benefit-title">Fast Installation</h3>
                    <p class="benefit-description">Efficient project management and installation processes that minimize disruption and deliver results on time.</p>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="benefit-title">Quality Guarantee</h3>
                    <p class="benefit-description">Comprehensive warranty on all installations and ongoing support to ensure long-lasting performance and customer satisfaction.</p>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="benefit-title">Local Expertise</h3>
                    <p class="benefit-description">Deep understanding of Ghana's climate, building codes, and architectural preferences, ensuring optimal solutions for local conditions.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics -->
    <section class="section stats-section">
        <div class="stats-overlay"></div>
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" data-count="15">0</div>
                    <div class="stat-label">Years Experience</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" data-count="500">0</div>
                    <div class="stat-label">Projects Completed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" data-count="100">0</div>
                    <div class="stat-label">Satisfied Clients</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" data-count="24">0</div>
                    <div class="stat-label">Hour Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Overview -->
    <section class="section section-white">
        <div class="container">
            <div class="section-header text-center">
                <div class="section-eyebrow">Our Services</div>
                <h2 class="section-title">Complete Aluminum & Glass Solutions</h2>
                <p class="section-description">From residential to commercial projects, we provide comprehensive aluminum and glass services</p>
            </div>

            <div class="services-overview-grid">
                <div class="service-overview-card">
                    <h3 class="service-overview-title">Cladding & Walls</h3>
                    <ul class="service-overview-list">
                        <li>Alucobond Cladding</li>
                        <li>Curtain Wall Systems</li>
                    </ul>
                </div>

                <div class="service-overview-card">
                    <h3 class="service-overview-title">Glass Systems</h3>
                    <ul class="service-overview-list">
                        <li>Spider Glass Installation</li>
                        <li>Sliding Windows & Doors</li>
                    </ul>
                </div>

                <div class="service-overview-card">
                    <h3 class="service-overview-title">Doors & Windows</h3>
                    <ul class="service-overview-list">
                        <li>Frameless Doors</li>
                        <li>PVC Windows</li>
                    </ul>
                </div>

                <div class="service-overview-card">
                    <h3 class="service-overview-title">Specialty Systems</h3>
                    <ul class="service-overview-list">
                        <li>Sun-breakers</li>
                        <li>Stainless Steel Balustrades</li>
                    </ul>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="services.php" class="btn btn-primary btn-lg">View All Services</a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section cta-section">
        <div class="cta-overlay"></div>
        <div class="container">
            <div class="cta-content text-center">
                <h2 class="cta-title">Ready to Start Your Project?</h2>
                <p class="cta-description">Get a free consultation and quote for your aluminum and glass needs</p>
                <div class="cta-buttons">
                    <a href="contact.php" class="btn btn-primary btn-lg">Get Free Quote</a>
                    <a href="tel:+233541737575" class="btn btn-outline btn-lg">Call Now</a>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/233541737575?text=Hello%20AluMaster,%20I'm%20interested%20in%20your%20services" 
   class="whatsapp-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <svg class="icon-lg" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
    </svg>
</a>

<?php include 'includes/footer.php'; ?>