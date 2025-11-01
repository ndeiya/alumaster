<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$page_title = "AluMaster Aluminum System - Where Quality Meets Affordability";
$page_description = "Professional aluminum and glass solutions in Ghana. Alucobond cladding, curtain walls, spider glass, and more. Quality meets affordability.";

// Get homepage sections from database
try {
    $stmt = $pdo->query("SELECT * FROM homepage_sections WHERE is_active = 1 ORDER BY sort_order ASC");
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $sections = [];
}

// Convert sections to associative array for easier access
$homepage_content = [];
foreach ($sections as $section) {
    $homepage_content[$section['section_key']] = [
        'content' => json_decode($section['content'], true),
        'settings' => json_decode($section['settings'], true)
    ];
}

include 'includes/header.php';
?>

<?php if (isset($homepage_content['hero'])): 
    $hero = $homepage_content['hero']['content'];
    $hero_settings = $homepage_content['hero']['settings'];
?>
    <!-- Hero Section -->
    <section class="hero" style="background-color: <?php echo $hero_settings['background_color'] ?? '#1a1a1a'; ?>; color: <?php echo $hero_settings['text_color'] ?? '#ffffff'; ?>;">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>
                        <?php echo htmlspecialchars($hero['title'] ?? 'Where Quality'); ?>
                        <span class="highlight"><?php echo htmlspecialchars($hero['highlight'] ?? 'Meets Affordability'); ?></span>
                    </h1>
                    <p><?php echo htmlspecialchars($hero['description'] ?? ''); ?></p>
                    <div class="hero-actions">
                        <?php if (!empty($hero['primary_button_text']) && !empty($hero['primary_button_link'])): ?>
                            <a href="<?php echo htmlspecialchars($hero['primary_button_link']); ?>" class="btn btn-primary btn-lg">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <?php echo htmlspecialchars($hero['primary_button_text']); ?>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($hero['secondary_button_text']) && !empty($hero['secondary_button_link'])): ?>
                            <a href="<?php echo htmlspecialchars($hero['secondary_button_link']); ?>" class="btn btn-secondary btn-lg">
                                <?php echo htmlspecialchars($hero['secondary_button_text']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hero-image">
                    <?php if (!empty($hero['background_image'])): ?>
                        <img src="<?php echo htmlspecialchars($hero['background_image']); ?>" alt="Modern aluminum and glass building">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (isset($homepage_content['services'])): 
    $services = $homepage_content['services']['content'];
    $services_settings = $homepage_content['services']['settings'];
?>
    <!-- Services Section -->
    <section class="section section-white" style="background-color: <?php echo $services_settings['background_color'] ?? '#ffffff'; ?>;">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php echo htmlspecialchars($services['title'] ?? 'Our Expertise'); ?></h2>
                <p class="section-subtitle"><?php echo htmlspecialchars($services['subtitle'] ?? ''); ?></p>
            </div>
            
            <div class="services-grid" style="grid-template-columns: repeat(<?php echo $services_settings['columns'] ?? 4; ?>, 1fr);">
                <?php if (!empty($services['services'])): ?>
                    <?php foreach ($services['services'] as $service): ?>
                        <div class="service-card">
                            <div class="service-icon">
                                <?php echo getServiceIcon($service['icon'] ?? 'default'); ?>
                            </div>
                            <h3><?php echo htmlspecialchars($service['name'] ?? ''); ?></h3>
                            <p><?php echo htmlspecialchars($service['description'] ?? ''); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (isset($homepage_content['why_choose'])): 
    $why_choose = $homepage_content['why_choose']['content'];
    $why_choose_settings = $homepage_content['why_choose']['settings'];
?>
    <!-- Why Choose Us Section -->
    <section class="section section-white why-choose-us" style="background-color: <?php echo $why_choose_settings['background_color'] ?? '#ffffff'; ?>;">
        <div class="container">
            <div class="features-grid">
                <div class="features-content">
                    <h2><?php echo htmlspecialchars($why_choose['title'] ?? 'Why Choose AluMaster?'); ?></h2>
                    <p><?php echo htmlspecialchars($why_choose['description'] ?? ''); ?></p>
                    
                    <?php if (!empty($why_choose['features'])): ?>
                        <ul class="features-list">
                            <?php foreach ($why_choose['features'] as $feature): ?>
                                <li>
                                    <div class="check-icon">✓</div>
                                    <?php echo htmlspecialchars($feature); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                
                <div class="features-image">
                    <?php if (!empty($why_choose['image'])): ?>
                        <img src="<?php echo htmlspecialchars($why_choose['image']); ?>" alt="AluMaster installation team working on aluminum and glass project">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (isset($homepage_content['contact_cta'])): 
    $contact_cta = $homepage_content['contact_cta']['content'];
    $contact_cta_settings = $homepage_content['contact_cta']['settings'];
?>
    <!-- CTA Section -->
    <section class="section cta-section" style="background-color: <?php echo $contact_cta_settings['background_color'] ?? '#1a1a1a'; ?>; color: <?php echo $contact_cta_settings['text_color'] ?? '#ffffff'; ?>;">
        <div class="container">
            <div class="cta-content">
                <h2><?php echo htmlspecialchars($contact_cta['title'] ?? 'Get In Touch'); ?></h2>
                <p><?php echo htmlspecialchars($contact_cta['subtitle'] ?? ''); ?></p>
                
                <?php if (!empty($contact_cta['contact_items'])): ?>
                    <div class="cta-grid">
                        <?php foreach ($contact_cta['contact_items'] as $item): ?>
                            <div class="cta-item">
                                <div class="cta-icon">
                                    <?php echo getContactIcon($item['icon'] ?? 'default'); ?>
                                </div>
                                <h3><?php echo htmlspecialchars($item['title'] ?? ''); ?></h3>
                                <?php if (!empty($item['lines'])): ?>
                                    <?php foreach ($item['lines'] as $line): ?>
                                        <p><?php echo htmlspecialchars($line); ?></p>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($contact_cta['social_links'])): ?>
                    <div class="social-links">
                        <?php foreach ($contact_cta['social_links'] as $social): ?>
                            <a href="<?php echo htmlspecialchars($social['url']); ?>" target="_blank" rel="noopener" class="social-link" aria-label="<?php echo ucfirst($social['platform']); ?>">
                                <?php echo getSocialIcon($social['platform']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($contact_cta['cta_button_text']) && !empty($contact_cta['cta_button_link'])): ?>
                    <a href="<?php echo htmlspecialchars($contact_cta['cta_button_link']); ?>" class="btn btn-primary btn-lg">
                        <?php echo htmlspecialchars($contact_cta['cta_button_text']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>