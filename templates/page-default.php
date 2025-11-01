<?php include 'includes/header.php'; ?>

<!-- Page Hero -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <nav class="breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-current"><?php echo htmlspecialchars($page_data['title']); ?></span>
                </nav>
                <h1><?php echo htmlspecialchars($page_data['title']); ?></h1>
                <?php if (!empty($page_data['excerpt'])): ?>
                <p><?php echo htmlspecialchars($page_data['excerpt']); ?></p>
                <?php endif; ?>
            </div>
            <?php if (!empty($page_data['featured_image'])): ?>
            <div class="hero-image">
                <img src="<?php echo htmlspecialchars($page_data['featured_image']); ?>" 
                     alt="<?php echo htmlspecialchars($page_data['title']); ?>" 
                     onerror="this.style.display='none'">
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Page Content -->
<section class="section section-white">
    <div class="container">
        <div class="page-content">
            <?php echo $page_data['content']; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>