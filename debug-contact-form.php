<?php
/**
 * Contact Form Debug Script
 * Tests all components of the contact form system
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "AluMaster Contact Form Debug\n";
echo "===========================\n\n";

// Test 1: Check required files
echo "1. Checking Required Files:\n";
$required_files = [
    'includes/config.php',
    'includes/database.php', 
    'includes/functions.php',
    '.env'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists\n";
    } else {
        echo "❌ $file missing\n";
    }
}

// Test 2: Load configuration
echo "\n2. Loading Configuration:\n";
try {
    require_once 'includes/config.php';
    echo "✅ Config loaded\n";
    echo "   - Site Email: " . SITE_EMAIL . "\n";
    echo "   - Database: " . DB_NAME . "\n";
} catch (Exception $e) {
    echo "❌ Config error: " . $e->getMessage() . "\n";
}

// Test 3: Load environment variables
echo "\n3. Loading Environment Variables:\n";
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env_count = 0;
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
        $env_count++;
    }
    echo "✅ Loaded $env_count environment variables\n";
} else {
    echo "❌ .env file not found\n";
}

// Test 4: Database connection
echo "\n4. Testing Database Connection:\n";
try {
    require_once 'includes/database.php';
    $db = new Database();
    $conn = $db->getConnection();
    echo "✅ Database connected\n";
    
    // Check if inquiries table exists
    $stmt = $conn->prepare("SHOW TABLES LIKE 'inquiries'");
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo "✅ Inquiries table exists\n";
        
        // Check table structure
        $stmt = $conn->prepare("DESCRIBE inquiries");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "   - Columns: " . implode(', ', $columns) . "\n";
        
        // Check existing records
        $stmt = $conn->prepare("SELECT COUNT(*) FROM inquiries");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        echo "   - Existing records: $count\n";
    } else {
        echo "❌ Inquiries table missing\n";
        echo "   Run: mysql -u root -p alumaster < database/add_inquiries_table.sql\n";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

// Test 5: Functions
echo "\n5. Testing Functions:\n";
try {
    require_once 'includes/functions.php';
    echo "✅ Functions loaded\n";
    
    if (function_exists('sanitize_input')) {
        $test = sanitize_input('<script>alert("test")</script>');
        echo "✅ sanitize_input works: " . $test . "\n";
    } else {
        echo "❌ sanitize_input function missing\n";
    }
} catch (Exception $e) {
    echo "❌ Functions error: " . $e->getMessage() . "\n";
}

// Test 6: PHPMailer
echo "\n6. Testing Email System:\n";
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
    echo "✅ Composer autoloader found\n";
    
    if (file_exists('includes/mailer.php')) {
        require_once 'includes/mailer.php';
        echo "✅ Mailer class found\n";
        
        if (class_exists('EmailService')) {
            echo "✅ EmailService class available\n";
        } else {
            echo "❌ EmailService class not found\n";
        }
    } else {
        echo "❌ Mailer file missing\n";
    }
} else {
    echo "❌ PHPMailer not installed (run: composer install)\n";
}

// Test 7: Session
echo "\n7. Testing Session:\n";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Session active\n";
} else {
    session_start();
    echo "✅ Session started\n";
}

// Test 8: Simulate form submission
echo "\n8. Simulating Form Submission:\n";
try {
    // Simulate POST data
    $_POST = [
        'submit_inquiry' => '1',
        'csrf_token' => bin2hex(random_bytes(32)),
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'phone' => '+233-123-456-789',
        'service_interest' => 'Alucobond Cladding',
        'message' => 'This is a test message from the debug script.'
    ];
    
    // Set CSRF token in session
    $_SESSION['csrf_token'] = $_POST['csrf_token'];
    
    echo "✅ POST data prepared\n";
    echo "   - Name: {$_POST['first_name']} {$_POST['last_name']}\n";
    echo "   - Email: {$_POST['email']}\n";
    echo "   - Service: {$_POST['service_interest']}\n";
    
    // Test database insertion
    if (isset($conn)) {
        $full_name = $_POST['first_name'] . ' ' . $_POST['last_name'];
        $stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, service_interest, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $result = $stmt->execute([
            $full_name,
            $_POST['email'],
            $_POST['phone'],
            $_POST['service_interest'],
            $_POST['message']
        ]);
        
        if ($result) {
            echo "✅ Test inquiry inserted successfully\n";
            echo "   - Inquiry ID: " . $conn->lastInsertId() . "\n";
        } else {
            echo "❌ Failed to insert test inquiry\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Form simulation error: " . $e->getMessage() . "\n";
}

// Test 9: Check logs directory
echo "\n9. Checking Logs:\n";
if (!is_dir('logs')) {
    mkdir('logs', 0755, true);
    echo "✅ Created logs directory\n";
} else {
    echo "✅ Logs directory exists\n";
}

if (file_exists('logs/email.log')) {
    $log_size = filesize('logs/email.log');
    echo "✅ Email log exists ($log_size bytes)\n";
} else {
    echo "ℹ️  Email log not created yet\n";
}

echo "\n=== Debug Complete ===\n";
echo "If all tests pass, the contact form should work.\n";
echo "If there are errors, fix them before testing the contact form.\n";
?>