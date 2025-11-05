<?php
/**
 * PHPMailer Installation Script
 * Simple script to download and install PHPMailer without Composer
 */

echo "AluMaster Email System Setup\n";
echo "============================\n\n";

// Check if Composer is available
if (shell_exec('composer --version') !== null) {
    echo "Composer detected! Installing PHPMailer via Composer...\n";
    
    // Create composer.json if it doesn't exist
    if (!file_exists('composer.json')) {
        $composer_config = [
            "require" => [
                "phpmailer/phpmailer" => "^6.8"
            ]
        ];
        file_put_contents('composer.json', json_encode($composer_config, JSON_PRETTY_PRINT));
        echo "Created composer.json\n";
    }
    
    // Run composer install
    $output = shell_exec('composer install 2>&1');
    echo $output;
    
    if (file_exists('vendor/autoload.php')) {
        echo "\n✅ PHPMailer installed successfully via Composer!\n";
    } else {
        echo "\n❌ Composer installation failed. Trying manual installation...\n";
        manualInstall();
    }
} else {
    echo "Composer not found. Installing PHPMailer manually...\n";
    manualInstall();
}

function manualInstall() {
    $phpmailer_url = 'https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.8.1.zip';
    $temp_file = 'phpmailer.zip';
    $extract_dir = 'vendor/phpmailer/phpmailer';
    
    echo "Downloading PHPMailer...\n";
    
    // Create vendor directory
    if (!is_dir('vendor')) {
        mkdir('vendor', 0755, true);
    }
    if (!is_dir('vendor/phpmailer')) {
        mkdir('vendor/phpmailer', 0755, true);
    }
    
    // Download PHPMailer
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $phpmailer_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    
    if (curl_error($ch)) {
        echo "❌ Download failed: " . curl_error($ch) . "\n";
        curl_close($ch);
        return false;
    }
    curl_close($ch);
    
    file_put_contents($temp_file, $data);
    echo "Downloaded PHPMailer archive\n";
    
    // Extract archive
    $zip = new ZipArchive;
    if ($zip->open($temp_file) === TRUE) {
        $zip->extractTo('vendor/phpmailer/');
        $zip->close();
        
        // Move files to correct location
        $extracted_folder = 'vendor/phpmailer/PHPMailer-6.8.1';
        if (is_dir($extracted_folder)) {
            rename($extracted_folder, $extract_dir);
        }
        
        unlink($temp_file);
        echo "Extracted PHPMailer\n";
        
        // Create simple autoloader
        createAutoloader();
        
        echo "\n✅ PHPMailer installed manually!\n";
        return true;
    } else {
        echo "❌ Failed to extract archive\n";
        unlink($temp_file);
        return false;
    }
}

function createAutoloader() {
    $autoloader_content = '<?php
// Simple PHPMailer autoloader
spl_autoload_register(function ($class) {
    $prefix = "PHPMailer\\PHPMailer\\";
    $base_dir = __DIR__ . "/phpmailer/phpmailer/src/";
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace("\\", "/", $relative_class) . ".php";
    
    if (file_exists($file)) {
        require $file;
    }
});
';
    
    file_put_contents('vendor/autoload.php', $autoloader_content);
    echo "Created autoloader\n";
}

echo "\nNext steps:\n";
echo "1. Copy .env.example to .env\n";
echo "2. Update .env with your email credentials\n";
echo "3. Visit /admin/settings/email.php to configure and test\n";
echo "\nFor Gmail users:\n";
echo "- Enable 2-factor authentication\n";
echo "- Generate an App Password\n";
echo "- Use the App Password in your .env file\n";
?>