<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$page_title = "About AluMaster - Premium Aluminum & Glass Solutions";
$page_description = "Learn about AluMaster, Ghana's leading provider of aluminum and glass solutions. Quality meets affordability in every project.";
$body_class = "about-page";

include 'includes/header.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero about-hero">
        <div class="container">
            <div class="hero-content text-center">
                <h1 class="hero-title">About AluMaster</h1>
                <p class="hero-subtitle">Where Quality Meets Affordability</p>
                <p class="hero-description">Leading provider of high-quality architectural aluminum and glass solutions, delivering superior products and services without compromising on cost-effectiveness.</p>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="section section-white">
        <div class="container">
            <div class="story-grid">
                <div class="story-content">
                    <div class="section-eyebrow">Our Story</div>
                    <h2 class="section-title">Building Excellence Since Day One</h2>
                    <div class="story-text">
                        <p>AluMaster has been at the forefront of Ghana's aluminum and glass industry, providing innovative solutions that combine cutting-edge technology with traditional craftsmanship. Our journey began with a simple mission: to deliver premium quality aluminum and glass installations at affordable prices.</p>
                        
                        <p>Over the years, we have built a reputation for excellence, working on projects ranging from residential homes to large commercial complexes. Our team of skilled professionals brings years of experience and expertise to every project, ensuring that our clients receive nothing but the best.</p>
                        
                        <p>Today, we continue to lead the industry with our commitment to quality, innovation, and customer satisfaction. Every project we undertake reflects our dedication to excellence and our passion for creating beautiful, functional spaces.</p>
                    </div>
                    
                    <div class="story-stats">
                        <div class="story-stat">
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Projects</div>
                        </div>
              
                <div class="story-stat">
                            <div class="stat-number">15+</div>
                            <div class="stat-label">Years</div>
                        </div>
                    </div>
                </div>
                
                <div class="story-image">
                    <img src="assets/images/about-building.jpg" alt="Modern aluminum and glass building" class="story-img">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="section section-gray">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">Our Mission & Vision</h2>
                <p class="section-description">Driving excellence in aluminum and glass solutions across Ghana</p>
            </div>
            
            <div class="mission-vision-grid">
                <div class="mission-vision-card">
                    <div class="mission-vision-icon">
                        <div class="icon-circle green">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="mission-vision-title">Our Mission</h3>
                    <p class="mission-vision-description">To provide exceptional aluminum and glass solutions that enhance Ghana's architectural landscape while maintaining the highest standards of quality, affordability, and customer service.</p>
                </div>

                <div class="mission-vision-card">
                    <div class="mission-vision-icon">
                        <div class="icon-circle green">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="mission-vision-title">Our Vision</h3>
                    <p class="mission-vision-description">To be West Africa's leading aluminum and glass solutions provider, recognized for innovation, reliability, and excellence in every project we undertake.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values Section -->
    <section class="section section-white">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">Our Core Values</h2>
                <p class="section-description">The principles that guide everything we do</p>
            </div>
            
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">
                        <div class="icon-circle green">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="value-title">Quality Excellence</h3>
                    <p class="value-description">We never compromise on quality, using only premium materials and proven installation techniques.</p>
                </div>

                <div class="value-card">
                    <div class="value-icon">
                        <div class="icon-circle green">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="value-title">Affordability</h3>
                    <p class="value-description">Premium quality doesn't have to break the bank. We offer competitive pricing without sacrificing excellence.</p>
                </div>

                <div class="value-card">
                    <div class="value-icon">
                        <div class="icon-circle green">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="value-title">Innovation</h3>
                    <p class="value-description">We stay ahead of industry trends, bringing the latest technologies and techniques to every project.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Expertise Section -->
    <section class="section section-gray">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">Our Expertise</h2>
                <p class="section-description">Comprehensive aluminum and glass solutions for every need</p>
            </div>
            
            <div class="expertise-grid">
                <div class="expertise-card">
                    <div class="expertise-icon">
                        <div class="icon-circle green">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="expertise-title">Alucobond Cladding</h3>
                    <p class="expertise-description">Premium aluminum composite panels for modern facades and exterior cladding systems.</p>
                </div>

                <div class="expertise-card">
                    <div class="expertise-icon">
                        <div class="icon-circle green">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="expertise-title">Curtain Wall</h3>
                    <p class="expertise-description">Structural glazing systems for commercial buildings and high-rise constructions.</p>
                </div>

                <div class="expertise-card">
                    <div class="expertise-icon">
                        <div class="icon-circle green">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="expertise-title">Spider Glass</h3>
                    <p class="expertise-description">Point-fixed glazing systems for stunning glass facades and architectural features.</p>
                </div>

                <div class="expertise-card">
                    <div class="expertise-icon">
                        <div class="icon-circle green">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="expertise-title">Sliding Doors</h3>
                    <p class="expertise-description">High-performance sliding window and door systems for residential and commercial use.</p>
                </div>

                <div class="expertise-card">
                    <div class="expertise-icon">
                        <div class="icon-circle green">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="expertise-title">Frameless Door</h3>
                    <p class="expertise-description">Elegant glass door solutions for modern spaces and commercial entrances.</p>
                </div>

                <div class="expertise-card">
                    <div class="expertise-icon">
                        <div class="icon-circle green">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8h4M10 12h4"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="expertise-title">PVC Windows</h3>
                    <p class="expertise-description">Energy-efficient PVC window systems for residential and commercial applications.</p>
                </div>

                <div class="expertise-card">
                    <div class="expertise-icon">
                        <div class="icon-circle green">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="expertise-title">Sun-breakers</h3>
                    <p class="expertise-description">Solar shading solutions for climate control and energy efficiency.</p>
                </div>

                <div class="expertise-card">
                    <div class="expertise-icon">
                        <div class="icon-circle green">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="expertise-title">Steel Balustrades</h3>
                    <p class="expertise-description">Premium stainless steel railing systems for safety and aesthetic appeal.</p>
                </div>
            </div>
        </div>
    </section>



    <!-- CTA Section -->
    <section class="section about-cta-section">
        <div class="container">
            <div class="cta-content text-center">
                <h2 class="cta-title">Ready to Work With Us?</h2>
                <p class="cta-description">Let's discuss your project requirements and bring your vision to life</p>
                <div class="cta-buttons">
                    <a href="contact.php" class="btn btn-primary btn-lg">Get Started</a>
                    <a href="tel:+233541737575" class="btn btn-outline btn-lg">Call Now</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>