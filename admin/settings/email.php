<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'Email Settings';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Settings', 'url' => '#'],
    ['title' => 'Email Settings']
];

// Load environment variables if .env file exists
if (file_exists('../../.env')) {
    $lines = file('../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Load PHPMailer if available
$phpmailer_available = false;
if (file_exists('../../vendor/autoload.php')) {
    require_once '../../vendor/autoload.php';
    require_once '../../includes/mailer.php';
    $phpmailer_available = class_exists('EmailService');
}

$success_message = '';
$error_message = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['test_email'])) {
        // Test email functionality
        if ($phpmailer_available) {
            try {
                $emailService = new EmailService();
                $test_email = $_POST['test_email_address'] ?? SITE_EMAIL;
                
                $result = $emailService->sendCustomEmail(
                    $test_email,
                    'Email Test - ' . SITE_NAME,
                    '<h2>Email Test Successful!</h2><p>Your email configuration is working correctly.</p><p>Sent from: ' . SITE_NAME . '</p>',
                    'Email Test Successful! Your email configuration is working correctly. Sent from: ' . SITE_NAME
                );
                
                if ($result) {
                    $success_message = "Test email sent successfully to " . htmlspecialchars($test_email);
                } else {
                    $error_message = "Failed to send test email. Please check your configuration.";
                }
            } catch (Exception $e) {
                $error_message = "Email test failed: " . $e->getMessage();
            }
        } else {
            $error_message = "PHPMailer is not installed. Please run 'composer install' to enable advanced email features.";
        }
    }
    
    if (isset($_POST['save_settings'])) {
        // Save email settings to .env file
        $env_content = "# AluMaster Email Configuration\n";
        $env_content .= "SMTP_HOST=" . ($_POST['smtp_host'] ?? '') . "\n";
        $env_content .= "SMTP_PORT=" . ($_POST['smtp_port'] ?? '587') . "\n";
        $env_content .= "SMTP_USERNAME=" . ($_POST['smtp_username'] ?? '') . "\n";
        $env_content .= "SMTP_PASSWORD=" . ($_POST['smtp_password'] ?? '') . "\n";
        $env_content .= "SMTP_ENCRYPTION=" . ($_POST['smtp_encryption'] ?? 'tls') . "\n";
        $env_content .= "FROM_EMAIL=" . ($_POST['from_email'] ?? SITE_EMAIL) . "\n";
        $env_content .= "FROM_NAME=" . ($_POST['from_name'] ?? SITE_NAME) . "\n";
        $env_content .= "REPLY_TO=" . ($_POST['reply_to'] ?? SITE_EMAIL) . "\n";
        $env_content .= "ADMIN_EMAIL=" . ($_POST['admin_email'] ?? SITE_EMAIL) . "\n";
        
        if (file_put_contents('../../.env', $env_content)) {
            $success_message = "Email settings saved successfully!";
        } else {
            $error_message = "Failed to save email settings. Please check file permissions.";
        }
    }
}

// Get current settings
$current_settings = [
    'smtp_host' => $_ENV['SMTP_HOST'] ?? 'mail.yourdomain.com',
    'smtp_port' => $_ENV['SMTP_PORT'] ?? '587',
    'smtp_username' => $_ENV['SMTP_USERNAME'] ?? '',
    'smtp_password' => $_ENV['SMTP_PASSWORD'] ?? '',
    'smtp_encryption' => $_ENV['SMTP_ENCRYPTION'] ?? 'tls',
    'from_email' => $_ENV['FROM_EMAIL'] ?? SITE_EMAIL,
    'from_name' => $_ENV['FROM_NAME'] ?? SITE_NAME,
    'reply_to' => $_ENV['REPLY_TO'] ?? SITE_EMAIL,
    'admin_email' => $_ENV['ADMIN_EMAIL'] ?? SITE_EMAIL
];

include '../includes/header.php';
?>

<?php if (!empty($success_message)): ?>
    <div class="alert alert-success">
        <div class="alert-icon">
            <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <div class="alert-content">
            <?php echo $success_message; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="alert alert-error">
        <div class="alert-icon">
            <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="alert-content">
            <?php echo $error_message; ?>
        </div>
    </div>
<?php endif; ?>

<div class="admin-grid">
    <!-- Email Configuration -->
    <div class="admin-card">
        <div class="card-header">
            <h2 class="card-title">SMTP Configuration</h2>
        </div>
        
        <?php if (!$phpmailer_available): ?>
            <div class="alert alert-warning">
                <div class="alert-icon">
                    <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="alert-content">
                    <strong>PHPMailer not installed!</strong><br>
                    To enable advanced email features, run: <code>composer install</code>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" class="admin-form">
            <div class="form-group">
                <label for="smtp_host" class="form-label">SMTP Host</label>
                <input type="text" id="smtp_host" name="smtp_host" class="form-input"
                       value="<?php echo htmlspecialchars($current_settings['smtp_host']); ?>" 
                       placeholder="mail.yourdomain.com">
                <div class="form-help">cPanel: mail.yourdomain.com | Gmail: smtp.gmail.com | Outlook: smtp-mail.outlook.com</div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="smtp_port" class="form-label">SMTP Port</label>
                    <input type="number" id="smtp_port" name="smtp_port" class="form-input"
                           value="<?php echo htmlspecialchars($current_settings['smtp_port']); ?>" 
                           placeholder="587">
                </div>
                <div class="form-group">
                    <label for="smtp_encryption" class="form-label">Encryption</label>
                    <select id="smtp_encryption" name="smtp_encryption" class="form-input">
                        <option value="tls" <?php echo $current_settings['smtp_encryption'] === 'tls' ? 'selected' : ''; ?>>TLS</option>
                        <option value="ssl" <?php echo $current_settings['smtp_encryption'] === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="smtp_username" class="form-label">SMTP Username</label>
                <input type="email" id="smtp_username" name="smtp_username" class="form-input"
                       value="<?php echo htmlspecialchars($current_settings['smtp_username']); ?>" 
                       placeholder="alumaster75@yourdomain.com">
                <div class="form-help">For cPanel: use full email address | For Gmail: use full email address</div>
            </div>

            <div class="form-group">
                <label for="smtp_password" class="form-label">SMTP Password</label>
                <input type="password" id="smtp_password" name="smtp_password" class="form-input"
                       value="<?php echo htmlspecialchars($current_settings['smtp_password']); ?>" 
                       placeholder="Your email password">
                <div class="form-help">For cPanel: use email account password | For Gmail: use App Password</div>
            </div>

            <h3>Email Settings</h3>

            <div class="form-group">
                <label for="from_email" class="form-label">From Email</label>
                <input type="email" id="from_email" name="from_email" class="form-input"
                       value="<?php echo htmlspecialchars($current_settings['from_email']); ?>" 
                       placeholder="alumaster75@yourdomain.com">
            </div>

            <div class="form-group">
                <label for="from_name" class="form-label">From Name</label>
                <input type="text" id="from_name" name="from_name" class="form-input"
                       value="<?php echo htmlspecialchars($current_settings['from_name']); ?>" 
                       placeholder="AluMaster Aluminum System">
            </div>

            <div class="form-group">
                <label for="reply_to" class="form-label">Reply-To Email</label>
                <input type="email" id="reply_to" name="reply_to" class="form-input"
                       value="<?php echo htmlspecialchars($current_settings['reply_to']); ?>" 
                       placeholder="alumaster75@yourdomain.com">
            </div>

            <div class="form-group">
                <label for="admin_email" class="form-label">Admin Email (receives notifications)</label>
                <input type="email" id="admin_email" name="admin_email" class="form-input"
                       value="<?php echo htmlspecialchars($current_settings['admin_email']); ?>" 
                       placeholder="alumaster75@yourdomain.com">
            </div>

            <div class="form-actions">
                <button type="submit" name="save_settings" class="btn btn-primary">
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Email Test -->
    <div class="admin-card">
        <div class="card-header">
            <h2 class="card-title">Test Email</h2>
        </div>
        
        <p>Send a test email to verify your configuration is working correctly.</p>
        
        <form method="POST" class="admin-form">
            <div class="form-group">
                <label for="test_email_address" class="form-label">Test Email Address</label>
                <input type="email" id="test_email_address" name="test_email_address" class="form-input"
                       value="<?php echo htmlspecialchars($current_settings['admin_email']); ?>" 
                       required>
            </div>

            <div class="form-actions">
                <button type="submit" name="test_email" class="btn btn-secondary" 
                        <?php echo !$phpmailer_available ? 'disabled' : ''; ?>>
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Send Test Email
                </button>
            </div>
        </form>

        <?php if (!$phpmailer_available): ?>
            <div class="alert alert-info">
                <div class="alert-icon">
                    <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="alert-content">
                    Install PHPMailer to enable email testing: <code>composer install</code>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Setup Instructions -->
    <div class="admin-card">
        <div class="card-header">
            <h2 class="card-title">Setup Instructions</h2>
        </div>
        
        <div class="setup-instructions">
            <h3>cPanel Email Setup (Recommended)</h3>
            <ol>
                <li>Login to your cPanel hosting control panel</li>
                <li>Go to "Email Accounts" section</li>
                <li>Create email account: alumaster75@yourdomain.com</li>
                <li>Use these settings:
                    <ul>
                        <li><strong>SMTP Host:</strong> mail.yourdomain.com</li>
                        <li><strong>Port:</strong> 587 (TLS) or 465 (SSL)</li>
                        <li><strong>Username:</strong> full email address</li>
                        <li><strong>Password:</strong> email account password</li>
                    </ul>
                </li>
            </ol>

            <h3>Alternative cPanel SMTP Hosts</h3>
            <p>If mail.yourdomain.com doesn't work, try:</p>
            <ul>
                <li>smtp.yourdomain.com</li>
                <li>yourdomain.com</li>
                <li>Check your cPanel for the exact server name</li>
            </ul>

            <h3>Other Email Providers</h3>
            <ul>
                <li><strong>Gmail:</strong> smtp.gmail.com, port 587, TLS (requires App Password)</li>
                <li><strong>Outlook:</strong> smtp-mail.outlook.com, port 587, TLS</li>
                <li><strong>Yahoo:</strong> smtp.mail.yahoo.com, port 587, TLS</li>
            </ul>

            <h3>Installation</h3>
            <p>To enable advanced email features, install PHPMailer via Composer:</p>
            <code>composer install</code>
        </div>
    </div>
</div>

<style>
.setup-instructions h3 {
    color: #374151;
    font-size: 1.1rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.setup-instructions h3:first-child {
    margin-top: 0;
}

.setup-instructions ol, .setup-instructions ul {
    margin-bottom: 1rem;
    padding-left: 1.5rem;
}

.setup-instructions li {
    margin-bottom: 0.5rem;
}

.setup-instructions ul ul {
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}

code {
    background: #f3f4f6;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
    font-size: 0.875rem;
    color: #374151;
}
</style>

<?php include '../includes/footer.php'; ?>