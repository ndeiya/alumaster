<?php include 'includes/header.php'; ?>

<!-- Full Width Page Content -->
<section class="section section-full-width">
    <?php if (!empty($page_data['featured_image'])): ?>
    <div class="full-width-hero" style="background-image: url('<?php echo htmlspecialchars($page_data['featured_image']); ?>');">
        <div class="full-width-hero-overlay">
            <div class="container">
                <div class="full-width-hero-content">
                    <h1><?php echo htmlspecialchars($page_data['title']); ?></h1>
                    <?php if (!empty($page_data['excerpt'])): ?>
                    <p><?php echo htmlspecialchars($page_data['excerpt']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="full-width-header">
        <div class="container">
            <h1><?php echo htmlspecialchars($page_data['title']); ?></h1>
            <?php if (!empty($page_data['excerpt'])): ?>
            <p><?php echo htmlspecialchars($page_data['excerpt']); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="full-width-content">
        <?php echo $page_data['content']; ?>
    </div>
</section>

<style>
.section-full-width {
    padding: 0;
}

.full-width-hero {
    position: relative;
    min-height: 400px;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
}

.full-width-hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
}

.full-width-hero-content {
    color: white;
    text-align: center;
}

.full-width-hero-content h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.full-width-header {
    background-color: #2d3748;
    color: white;
    padding: 80px 0;
    text-align: center;
}

.full-width-header h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.full-width-content {
    padding: 0;
}

.full-width-content .container {
    max-width: 100%;
    padding: 0;
}
</style>

<?php include 'includes/footer.php'; ?>