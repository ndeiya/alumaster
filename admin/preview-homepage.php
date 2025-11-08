<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Get homepage sections from database
try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        throw new Exception("Could not connect to database");
    }
    
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

$page_title = "Homepage Preview - AluMaster Admin";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .preview-header {
            background: #1a202c;
            color: white;
            padding: 1rem;
            text-align: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .preview-content {
            margin-top: 60px;
        }
        
        .preview-close {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: #3b82f6;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="preview-header">
        <h1>Homepage Preview</h1>
        <a href="pages/homepage.php" class="preview-close">Back to Editor</a>
    </div>
    
    <div class="preview-content">
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
                                <?php if (!empty($hero['primary_button_text'])): ?>
                                    <a href="#" class="btn btn-primary btn-lg">
                                        <?php echo htmlspecialchars($hero['primary_button_text']); ?>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($hero['secondary_button_text'])): ?>
                                    <a href="#" class="btn btn-secondary btn-lg">
                                        <?php echo htmlspecialchars($hero['secondary_button_text']); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="hero-image">
                            <?php if (!empty($hero['background_image'])): ?>
                                <img src="../<?php echo htmlspecialchars($hero['background_image']); ?>" alt="Hero Image">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
        
        <div style="padding: 2rem; text-align: center; background: #f7fafc; color: #2d3748;">
            <p><strong>Preview Mode:</strong> This is how your homepage hero section will look. Other sections are not shown in this preview.</p>
        </div>
    </div>
</body>
</html>
