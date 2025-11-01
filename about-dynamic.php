<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$page_title = "About Us - AluMaster Aluminum System";
$page_description = "Learn about AluMaster Aluminum System, Ghana's trusted partner for architectural aluminum and glass solutions since 2008.";

// Get page sections from database
$page_content = getPageSections('about');

include 'includes/header.php';
?>

<main>
    <?php if (isset($page_content['hero'])): 
        $hero = $page_content['hero']['content'];
        $hero_settings = $page_content['hero']['settings'];
    ?>
    <!-- About Hero -->
    <section class="hero-section about-hero" style="background-image: url('<?php echo htmlspecialchars($hero_settings['background_image'] ?? ''); ?>');">
        <div class="hero-overlay" style="opacity: <?php echo $hero_settings['overlay_opacity'] ?? 0.7; ?>;"></div>
        <div class="container">
            <div class="hero-content">
                <nav class="breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-current"><?php echo htmlspecialchars($hero['breadcrumb'] ?? 'About'); ?></span>
                </nav>
                <h1 class="hero-title"><?php echo htmlspecialchars($hero['title'] ?? 'About AluMaster'); ?></h1>
                <p class="hero-subtitle"><?php echo htmlspecialchars($hero['subtitle'] ?? ''); ?></p>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (isset($page_content['story'])): 
        $story = $page_content['story']['content'];
        $story_settings = $page_content['story']['settings'];
    ?>
    <!-- Company Story -->
    <section class="section" style="background-color: <?php echo $story_settings['background_color'] ?? '#ffffff'; ?>;">
        <div class="container">
            <div class="about-intro">
                <div class="about-intro-content">
                    <div class="section-eyebrow"><?php echo htmlspecialchars($story['eyebrow'] ?? 'Our Story'); ?></div>
                    <h2 class="section-title"><?php echo htmlspecialchars($story['title'] ?? ''); ?></h2>
                    <div class="about-text">
                        <?php if (!empty($story['paragraphs'])): ?>
                            <?php foreach ($story['paragraphs'] as $paragraph): ?>
                                <p><?php echo htmlspecialchars($paragraph); ?></p>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="about-intro-image">
                    <?php if (!empty($story['image'])): ?>
                        <img src="<?php echo htmlspecialchars($story['image']); ?>" alt="AluMaster team working on aluminum installation" class="about-image">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (isset($page_content['mission_vision'])): 
        $mission_vision = $page_content['mission_vision']['content'];
        $mission_vision_settings = $page_content['mission_vision']['settings'];
    ?>
    <!-- Mission & Vision -->
    <section class="section section-white" style="background-color: <?php echo $mission_vision_settings['background_color'] ?? '#f7fafc'; ?>;">
        <div class="container">
            <div class="mission-vision-grid">
                <div class="mission-vision-card">
                    <div class="mission-vision-icon">
                        <svg class="icon-2xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <h3 class="mission-vision-title"><?php echo htmlspecialchars($mission_vision['mission']['title'] ?? 'Our Mission'); ?></h3>
                    <p class="mission-vision-description"><?php echo htmlspecialchars($mission_vision['mission']['description'] ?? ''); ?></p>
                </div>

                <div class="mission-vision-card">
                    <div class="mission-vision-icon">
                        <svg class="icon-2xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <h3 class="mission-vision-title"><?php echo htmlspecialchars($mission_vision['vision']['title'] ?? 'Our Vision'); ?></h3>
                    <p class="mission-vision-description"><?php echo htmlspecialchars($mission_vision['vision']['description'] ?? ''); ?></p>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (isset($page_content['benefits'])): 
        $benefits = $page_content['benefits']['content'];
        $benefits_settings = $page_content['benefits']['settings'];
    ?>
    <!-- Why Choose Us -->
    <section class="section" style="background-color: <?php echo $benefits_settings['background_color'] ?? '#ffffff'; ?>;">
        <div class="container">
            <div class="section-header text-center">
                <div class="section-eyebrow"><?php echo htmlspecialchars($benefits['eyebrow'] ?? 'Why Choose AluMaster'); ?></div>
                <h2 class="section-title"><?php echo htmlspecialchars($benefits['title'] ?? 'What Makes Us Different'); ?></h2>
                <p class="section-description"><?php echo htmlspecialchars($benefits['description'] ?? ''); ?></p>
            </div>

            <div class="benefits-grid" style="grid-template-columns: repeat(<?php echo $benefits_settings['columns'] ?? 3; ?>, 1fr);">
                <?php if (!empty($benefits['benefits'])): ?>
                    <?php foreach ($benefits['benefits'] as $benefit): ?>
                        <div class="benefit-card">
                            <div class="benefit-icon">
                                <?php echo getBenefitIcon($benefit['icon'] ?? 'default'); ?>
                            </div>
                            <h3 class="benefit-title"><?php echo htmlspecialchars($benefit['title'] ?? ''); ?></h3>
                            <p class="benefit-description"><?php echo htmlspecialchars($benefit['description'] ?? ''); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (isset($page_content['stats'])): 
        $stats = $page_content['stats']['content'];
        $stats_settings = $page_content['stats']['settings'];
    ?>
    <!-- Statistics -->
    <section class="section stats-section" style="background-image: url('<?php echo htmlspecialchars($stats_settings['background_image'] ?? ''); ?>');">
        <div class="stats-overlay" style="opacity: <?php echo $stats_settings['overlay_opacity'] ?? 0.8; ?>;"></div>
        <div class="container">
            <div class="stats-grid">
                <?php if (!empty($stats['stats'])): ?>
                    <?php foreach ($stats['stats'] as $stat): ?>
                        <div class="stat-card">
                            <div class="stat-number" data-count="<?php echo htmlspecialchars($stat['number'] ?? 0); ?>">0</div>
                            <div class="stat-label"><?php echo htmlspecialchars($stat['label'] ?? ''); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

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