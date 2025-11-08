<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'AluMaster Aluminum System - Where Quality Meets Affordability'; ?></title>
    <meta name="description" content="<?php echo $page_description ?? 'Professional aluminum and glass solutions in Ghana. Alucobond cladding, curtain walls, spider glass, and more. Quality meets affordability.'; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/Alumaster-favicon.png">
    <link rel="apple-touch-icon" href="assets/images/Alumaster-favicon.png">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.alumastergh.com/">
    <meta property="og:title" content="<?php echo $page_title ?? 'AluMaster Aluminum System'; ?>">
    <meta property="og:description" content="<?php echo $page_description ?? 'Professional aluminum and glass solutions in Ghana'; ?>">
    <meta property="og:image" content="assets/images/og-image.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://www.alumastergh.com/">
    <meta property="twitter:title" content="<?php echo $page_title ?? 'AluMaster Aluminum System'; ?>">
    <meta property="twitter:description" content="<?php echo $page_description ?? 'Professional aluminum and glass solutions in Ghana'; ?>">
    <meta property="twitter:image" content="assets/images/og-image.jpg">
    
    <!-- Schema.org markup -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "AluMaster Aluminum System",
        "url": "https://www.alumastergh.com",
        "logo": "https://www.alumastergh.com/assets/images/logo.png",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+233-541-737-575",
            "contactType": "customer service",
            "areaServed": "GH",
            "availableLanguage": "en"
        },
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "16 Palace Street",
            "addressLocality": "Madina-Accra",
            "addressCountry": "GH"
        },
        "sameAs": [
            "https://www.facebook.com/alumastergh",
            "https://www.instagram.com/alumaster75",
            "https://twitter.com/alumaster75"
        ]
    }
    </script>
    
    <?php 
    // Allow pages to inject additional head content
    if (isset($additional_head)) {
        echo $additional_head;
    }
    ?>
</head>
<body<?php echo isset($body_class) ? ' class="' . htmlspecialchars($body_class) . '"' : ''; ?>>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="navbar-brand">
                    <img src="assets/images/Alumaster-logo.png" alt="AluMaster Aluminum Systems" class="brand-logo">
                </a>
                
                <ul class="navbar-nav">
                    <li class="mobile-menu-header">
                        <button class="mobile-menu-close" id="mobileMenuClose" aria-label="Close mobile menu">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </li>
                    <?php
                    // Get navigation items from database
                    $nav_items = get_navigation_menu('header');
                    
                    // Fallback to default navigation if no items found
                    if (empty($nav_items)) {
                        $nav_items = [
                            ['title' => 'Home', 'url' => 'index.php', 'target' => '_self'],
                            ['title' => 'Services', 'url' => 'services.php', 'target' => '_self'],
                            ['title' => 'About', 'url' => 'about.php', 'target' => '_self'],
                            ['title' => 'Contact', 'url' => 'contact.php', 'target' => '_self']
                        ];
                    }
                    
                    foreach ($nav_items as $item):
                        $is_active = is_nav_item_active($item);
                        $target = $item['target'] === '_blank' ? ' target="_blank" rel="noopener"' : '';
                    ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($item['url']); ?>" 
                           class="nav-link <?php echo $is_active ? 'active' : ''; ?>"<?php echo $target; ?>>
                            <?php echo htmlspecialchars($item['title']); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <li class="mobile-cta-item">
                        <a href="contact.php" class="btn btn-primary btn-block">Get Quote</a>
                    </li>
                </ul>
                
                <a href="contact.php" class="navbar-cta">Get Quote</a>
                
                <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle mobile menu">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </nav>
        </div>
    </header>