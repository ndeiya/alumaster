<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';
require_once 'includes/auth-check.php';

// Only allow admin users
if (!check_admin_permission('admin')) {
    header('Location: index.php');
    exit;
}

$page_title = 'Video Feature Setup';
$status_message = '';
$status_type = '';

// Handle setup action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_setup'])) {
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        
        if (!$pdo) {
            throw new Exception("Database connection failed");
        }
        
        // Check if hero section exists
        $stmt = $pdo->prepare("SELECT content FROM homepage_sections WHERE section_key = 'hero'");
        $stmt->execute();
        $hero = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$hero) {
            throw new Exception("Hero section not found in database. Please run homepage setup first.");
        }
        
        // Get current content
        $content = json_decode($hero['content'], true);
        
        // Add video fields if they don't exist
        $updated = false;
        
        if (!isset($content['video_url'])) {
            $content['video_url'] = '';
            $updated = true;
        }
        
        if (!isset($content['video_type'])) {
            $content['video_type'] = 'youtube';
            $updated = true;
        }
        
        if (!isset($content['show_video'])) {
            $content['show_video'] = false;
            $updated = true;
        }
        
        if (!isset($content['video_autoplay'])) {
            $content['video_autoplay'] = true;
            $updated = true;
        }
        
        if ($updated) {
            // Update the database
            $stmt = $pdo->prepare("UPDATE homepage_sections SET content = ? WHERE section_key = 'hero'");
            $stmt->execute([json_encode($content)]);
            
            $status_message = "✓ Video feature setup completed successfully!";
            $status_type = 'success';
        } else {
            $status_message = "ℹ Video fields already exist. No update needed.";
            $status_type = 'info';
        }
        
    } catch (Exception $e) {
        $status_message = "✗ Error: " . $e->getMessage();
        $status_type = 'error';
    }
}

// Check current status
$video_status = [];
try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        $stmt = $pdo->prepare("SELECT content FROM homepage_sections WHERE section_key = 'hero'");
        $stmt->execute();
        $hero = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($hero) {
            $content = json_decode($hero['content'], true);
            $video_status = [
                'video_url' => isset($content['video_url']),
                'video_type' => isset($content['video_type']),
                'show_video' => isset($content['show_video']),
                'video_autoplay' => isset($content['video_autoplay'])
            ];
        }
    }
} catch (Exception $e) {
    // Ignore errors for status check
}

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="content-header">
        <h1><?php echo $page_title; ?></h1>
        <p>Setup video embedding feature for your homepage hero section</p>
    </div>

    <?php if ($status_message): ?>
        <div class="alert alert-<?php echo $status_type; ?>">
            <div class="alert-icon">
                <?php if ($status_type === 'success'): ?>
                    <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                <?php elseif ($status_type === 'error'): ?>
                    <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                <?php else: ?>
                    <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                <?php endif; ?>
            </div>
            <div class="alert-content">
                <?php echo $status_message; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Current Status Card -->
    <div class="setup-card">
        <div class="setup-card-header">
            <h2>Current Status</h2>
        </div>
        <div class="setup-card-body">
            <div class="status-grid">
                <div class="status-item">
                    <div class="status-icon <?php echo !empty($video_status['video_url']) ? 'status-success' : 'status-pending'; ?>">
                        <?php if (!empty($video_status['video_url'])): ?>
                            ✓
                        <?php else: ?>
                            ○
                        <?php endif; ?>
                    </div>
                    <div class="status-info">
                        <div class="status-label">Video URL Field</div>
                        <div class="status-value"><?php echo !empty($video_status['video_url']) ? 'Configured' : 'Not Configured'; ?></div>
                    </div>
                </div>

                <div class="status-item">
                    <div class="status-icon <?php echo !empty($video_status['video_type']) ? 'status-success' : 'status-pending'; ?>">
                        <?php if (!empty($video_status['video_type'])): ?>
                            ✓
                        <?php else: ?>
                            ○
                        <?php endif; ?>
                    </div>
                    <div class="status-info">
                        <div class="status-label">Video Type Field</div>
                        <div class="status-value"><?php echo !empty($video_status['video_type']) ? 'Configured' : 'Not Configured'; ?></div>
                    </div>
                </div>

                <div class="status-item">
                    <div class="status-icon <?php echo !empty($video_status['show_video']) ? 'status-success' : 'status-pending'; ?>">
                        <?php if (!empty($video_status['show_video'])): ?>
                            ✓
                        <?php else: ?>
                            ○
                        <?php endif; ?>
                    </div>
                    <div class="status-info">
                        <div class="status-label">Show Video Field</div>
                        <div class="status-value"><?php echo !empty($video_status['show_video']) ? 'Configured' : 'Not Configured'; ?></div>
                    </div>
                </div>

                <div class="status-item">
                    <div class="status-icon <?php echo !empty($video_status['video_autoplay']) ? 'status-success' : 'status-pending'; ?>">
                        <?php if (!empty($video_status['video_autoplay'])): ?>
                            ✓
                        <?php else: ?>
                            ○
                        <?php endif; ?>
                    </div>
                    <div class="status-info">
                        <div class="status-label">Autoplay Field</div>
                        <div class="status-value"><?php echo !empty($video_status['video_autoplay']) ? 'Configured' : 'Not Configured'; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Setup Instructions Card -->
    <div class="setup-card">
        <div class="setup-card-header">
            <h2>What This Does</h2>
        </div>
        <div class="setup-card-body">
            <p>This setup will add video embedding fields to your homepage hero section, allowing you to:</p>
            <ul class="setup-list">
                <li>✓ Add YouTube or Vimeo videos to your hero section</li>
                <li>✓ Enable/disable video display</li>
                <li>✓ Control autoplay settings</li>
                <li>✓ Choose between video or image background</li>
            </ul>
            <p style="margin-top: 1rem;"><strong>Note:</strong> This is safe to run multiple times. It will only add missing fields.</p>
        </div>
    </div>

    <!-- Run Setup Card -->
    <div class="setup-card">
        <div class="setup-card-header">
            <h2>Run Setup</h2>
        </div>
        <div class="setup-card-body">
            <?php if (array_filter($video_status)): ?>
                <div class="alert alert-info" style="margin-bottom: 1.5rem;">
                    <div class="alert-icon">
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="alert-content">
                        Video fields appear to be already configured. You can run setup again to ensure all fields are present.
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" onsubmit="return confirm('Run video feature setup?');">
                <button type="submit" name="run_setup" class="btn btn-primary btn-lg">
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 0.5rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Run Video Setup
                </button>
            </form>
        </div>
    </div>

    <!-- Next Steps Card -->
    <?php if (array_filter($video_status)): ?>
    <div class="setup-card">
        <div class="setup-card-header">
            <h2>Next Steps</h2>
        </div>
        <div class="setup-card-body">
            <ol class="setup-steps">
                <li>
                    <strong>Go to Homepage Editor</strong>
                    <p>Navigate to <a href="pages/homepage.php">Pages → Homepage</a></p>
                </li>
                <li>
                    <strong>Find Video Settings Section</strong>
                    <p>Scroll to the blue "Video Settings" section in the Hero Section</p>
                </li>
                <li>
                    <strong>Add Your Video</strong>
                    <p>Check "Enable Video Background" and paste your YouTube or Vimeo URL</p>
                </li>
                <li>
                    <strong>Save and Test</strong>
                    <p>Click "Update Section" and visit your homepage to see the video</p>
                </li>
            </ol>
            <div style="margin-top: 1.5rem;">
                <a href="pages/homepage.php" class="btn btn-primary">
                    Go to Homepage Editor
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-left: 0.5rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.setup-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
    border: 1px solid #e5e7eb;
}

.setup-card-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e5e7eb;
}

.setup-card-header h2 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
}

.setup-card-body {
    padding: 2rem;
}

.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.status-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    flex-shrink: 0;
}

.status-icon.status-success {
    background-color: #d1fae5;
    color: #065f46;
}

.status-icon.status-pending {
    background-color: #fee2e2;
    color: #991b1b;
}

.status-info {
    flex: 1;
}

.status-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.status-value {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
}

.setup-list {
    list-style: none;
    padding: 0;
    margin: 1rem 0;
}

.setup-list li {
    padding: 0.75rem 0;
    font-size: 1rem;
    color: #4b5563;
}

.setup-steps {
    list-style: none;
    counter-reset: step-counter;
    padding: 0;
}

.setup-steps li {
    counter-increment: step-counter;
    margin-bottom: 1.5rem;
    padding-left: 3rem;
    position: relative;
}

.setup-steps li::before {
    content: counter(step-counter);
    position: absolute;
    left: 0;
    top: 0;
    width: 2rem;
    height: 2rem;
    background: #3b82f6;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
}

.setup-steps li strong {
    display: block;
    color: #1f2937;
    margin-bottom: 0.25rem;
    font-size: 1.0625rem;
}

.setup-steps li p {
    color: #6b7280;
    margin: 0;
}

.setup-steps li a {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}

.setup-steps li a:hover {
    text-decoration: underline;
}

.alert-info {
    background-color: #dbeafe;
    border: 1px solid #3b82f6;
    color: #1e40af;
}
</style>

<?php include 'includes/footer.php'; ?>
