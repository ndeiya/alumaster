<?php
/**
 * Email Testing Utility
 * Simple command-line script to test email functionality
 */

require_once 'includes/config.php';

// Load environment variables
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Load PHPMailer
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
    require_once 'includes/mailer.php';
} else {
    die("PHPMailer not installed. Run: php install-phpmailer.php\n");
}

echo "AluMaster Email Test\n";
echo "===================\n\n";

// Get test email address
if (isset($argv[1])) {
    $test_email = $argv[1];
} else {
    echo "Enter test email address: ";
    $test_email = trim(fgets(STDIN));
}

if (!filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address\n");
}

echo "Testing email to: $test_email\n";
echo "Using configuration:\n";
echo "- SMTP Host: " . ($_ENV['SMTP_HOST'] ?? 'Not set') . "\n";
echo "- SMTP Port: " . ($_ENV['SMTP_PORT'] ?? 'Not set') . "\n";
echo "- SMTP Username: " . ($_ENV['SMTP_USERNAME'] ?? 'Not set') . "\n";
echo "- From Email: " . ($_ENV['FROM_EMAIL'] ?? 'Not set') . "\n";
echo "- Encryption: " . ($_ENV['SMTP_ENCRYPTION'] ?? 'Not set') . "\n\n";

// Detect email provider type
$smtp_host = $_ENV['SMTP_HOST'] ?? '';
if (strpos($smtp_host, 'mail.') === 0 || strpos($smtp_host, 'smtp.') === 0) {
    echo "📧 Detected: cPanel/Custom hosting email\n";
} elseif (strpos($smtp_host, 'gmail.com') !== false) {
    echo "📧 Detected: Gmail\n";
} elseif (strpos($smtp_host, 'outlook.com') !== false) {
    echo "📧 Detected: Outlook\n";
} else {
    echo "📧 Email provider: Custom/Unknown\n";
}
echo "\n";

try {
    $emailService = new EmailService();
    
    // Test connection first
    echo "Testing SMTP connection... ";
    if ($emailService->testConnection()) {
        echo "✅ Connected\n";
    } else {
        echo "❌ Connection failed\n";
        exit(1);
    }
    
    // Send test email
    echo "Sending test email... ";
    $result = $emailService->sendCustomEmail(
        $test_email,
        'Email Test - ' . SITE_NAME,
        '<h2>🎉 Email Test Successful!</h2>
         <p>Your email configuration is working correctly.</p>
         <p><strong>Test Details:</strong></p>
         <ul>
             <li>Sent from: ' . SITE_NAME . '</li>
             <li>Timestamp: ' . date('Y-m-d H:i:s') . '</li>
             <li>SMTP Host: ' . ($_ENV['SMTP_HOST'] ?? 'Default') . '</li>
         </ul>
         <p>You can now use the contact form on your website!</p>',
        'Email Test Successful! Your email configuration is working correctly. Sent from: ' . SITE_NAME . ' at ' . date('Y-m-d H:i:s')
    );
    
    if ($result) {
        echo "✅ Sent successfully\n";
        echo "\nTest completed! Check your inbox at $test_email\n";
    } else {
        echo "❌ Failed to send\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test contact form simulation
echo "\nTesting contact form simulation... ";
try {
    $testInquiry = [
        'name' => 'Test Customer',
        'email' => $test_email,
        'phone' => '+233-123-456-789',
        'service_interest' => 'Alucobond Cladding',
        'message' => 'This is a test inquiry to verify the contact form email system is working correctly.'
    ];
    
    $emailService->sendContactInquiry($testInquiry);
    $emailService->sendContactAutoReply($testInquiry);
    
    echo "✅ Contact form emails sent\n";
    echo "\nAll tests completed successfully! 🎉\n";
    
} catch (Exception $e) {
    echo "❌ Contact form test failed: " . $e->getMessage() . "\n";
}
?>