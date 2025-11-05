<?php
/**
 * cPanel Email Settings Detection Utility
 * Helps detect the correct SMTP settings for your cPanel hosting
 */

echo "AluMaster cPanel Email Detection\n";
echo "===============================\n\n";

// Get domain from current URL or ask user
$domain = '';
if (isset($_SERVER['HTTP_HOST'])) {
    $domain = $_SERVER['HTTP_HOST'];
    $domain = str_replace('www.', '', $domain);
} else {
    echo "Enter your domain name (e.g., yourdomain.com): ";
    $domain = trim(fgets(STDIN));
}

echo "Domain: $domain\n\n";

// Common cPanel SMTP configurations to test
$smtp_configs = [
    [
        'name' => 'Standard cPanel',
        'host' => "mail.$domain",
        'port' => 587,
        'encryption' => 'tls'
    ],
    [
        'name' => 'Alternative cPanel',
        'host' => "smtp.$domain", 
        'port' => 587,
        'encryption' => 'tls'
    ],
    [
        'name' => 'Direct Domain',
        'host' => $domain,
        'port' => 587,
        'encryption' => 'tls'
    ],
    [
        'name' => 'SSL Configuration',
        'host' => "mail.$domain",
        'port' => 465,
        'encryption' => 'ssl'
    ]
];

echo "Testing SMTP configurations for $domain:\n";
echo "========================================\n\n";

foreach ($smtp_configs as $config) {
    echo "Testing: {$config['name']}\n";
    echo "Host: {$config['host']}\n";
    echo "Port: {$config['port']}\n";
    echo "Encryption: {$config['encryption']}\n";
    
    // Test if host resolves
    $ip = gethostbyname($config['host']);
    if ($ip !== $config['host']) {
        echo "✅ Host resolves to: $ip\n";
        
        // Test if port is open
        $connection = @fsockopen($config['host'], $config['port'], $errno, $errstr, 5);
        if ($connection) {
            echo "✅ Port {$config['port']} is open\n";
            fclose($connection);
            
            echo "🎉 This configuration looks promising!\n";
            echo "\nRecommended .env settings:\n";
            echo "SMTP_HOST={$config['host']}\n";
            echo "SMTP_PORT={$config['port']}\n";
            echo "SMTP_ENCRYPTION={$config['encryption']}\n";
            echo "SMTP_USERNAME=alumaster75@$domain\n";
            echo "SMTP_PASSWORD=your-email-password\n\n";
        } else {
            echo "❌ Port {$config['port']} is not accessible\n";
        }
    } else {
        echo "❌ Host does not resolve\n";
    }
    
    echo "---\n\n";
}

// Additional checks
echo "Additional Information:\n";
echo "======================\n";

// Check if we can determine the hosting provider
$whois_info = '';
if (function_exists('exec')) {
    $whois_output = [];
    @exec("nslookup $domain", $whois_output);
    if (!empty($whois_output)) {
        echo "DNS Information:\n";
        foreach ($whois_output as $line) {
            if (strpos($line, 'Address:') !== false || strpos($line, 'Name:') !== false) {
                echo "  $line\n";
            }
        }
    }
}

echo "\nNext Steps:\n";
echo "1. Try the configurations marked with ✅\n";
echo "2. Create email account in cPanel: alumaster75@$domain\n";
echo "3. Update your .env file with the working configuration\n";
echo "4. Test with: php test-email.php\n\n";

echo "If none work, check your cPanel for exact SMTP settings or contact your hosting provider.\n";
?>