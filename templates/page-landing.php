<?php include 'includes/header.php'; ?>

<!-- Landing Page Content -->
<div class="landing-page">
    <?php if (!empty($page_data['featured_image'])): ?>
    <section class="landing-hero" style="background-image: url('<?php echo htmlspecialchars($page_data['featured_image']); ?>');">
        <div class="landing-hero-overlay">
            <div class="container">
                <div class="landing-hero-content">
                    <h1><?php echo htmlspecialchars($page_data['title']); ?></h1>
                    <?php if (!empty($page_data['excerpt'])): ?>
                    <p class="landing-subtitle"><?php echo htmlspecialchars($page_data['excerpt']); ?></p>
                    <?php endif; ?>
                    <div class="landing-cta">
                        <a href="contact.php" class="btn btn-primary btn-lg">Get Started</a>
                        <a href="services.php" class="btn btn-secondary btn-lg">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php else: ?>
    <section class="landing-hero-simple">
        <div class="container">
            <div class="landing-hero-content">
                <h1><?php echo htmlspecialchars($page_data['title']); ?></h1>
                <?php if (!empty($page_data['excerpt'])): ?>
                <p class="landing-subtitle"><?php echo htmlspecialchars($page_data['excerpt']); ?></p>
                <?php endif; ?>
                <div class="landing-cta">
                    <a href="contact.php" class="btn btn-primary btn-lg">Get Started</a>
                    <a href="services.php" class="btn btn-secondary btn-lg">Learn More</a>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <section class="landing-content">
        <div class="container">
            <?php echo $page_data['content']; ?>
        </div>
    </section>
</div>

<style>
.landing-page {
    padding: 0;
}

.landing-hero {
    position: relative;
    min-height: 100vh;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
}

.landing-hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(45, 55, 72, 0.8), rgba(49, 130, 206, 0.6));
    display: flex;
    align-items: center;
}

.landing-hero-simple {
    background: linear-gradient(135deg, #2d3748, #3182ce);
    color: white;
    padding: 120px 0;
    text-align: center;
}

.landing-hero-content {
    color: white;
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.landing-hero-content h1 {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    font-weight: 700;
}

.landing-subtitle {
    font-size: 1.25rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.landing-cta {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.landing-content {
    padding: 80px 0;
}

@media (max-width: 768px) {
    .landing-hero-content h1 {
        font-size: 2.5rem;
    }
    
    .landing-cta {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>