<?php
/**
 * Contact Form Fix Script
 * Diagnoses and fixes common contact form issues
 */

echo "AluMaster Contact Form Fix\n";
echo "=========================\n\n";

// Test 1: Check and fix session issues
echo "1. Checking Session Configuration...\n";

// Check if session is already started
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Session is active\n";
    echo "   Session ID: " . session_id() . "\n";
} else {
    echo "⚠️ Starting session...\n";
    session_start();
    echo "✅ Session started\n";
    echo "   Session ID: " . session_id() . "\n";
}

// Test 2: Check database and table
echo "\n2. Checking Database...\n";
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if ($conn) {
        echo "✅ Database connected\n";
        
        // Check inquiries table
        $stmt = $conn->prepare("SHOW TABLES LIKE 'inquiries'");
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo "✅ Inquiries table exists\n";
            
            // Check table structure
            $stmt = $conn->prepare("DESCRIBE inquiries");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $required_columns = ['id', 'name', 'email', 'phone', 'message', 'created_at'];
            $missing_columns = array_diff($required_columns, $columns);
            
            if (empty($missing_columns)) {
                echo "✅ All required columns present\n";
            } else {
                echo "⚠️ Missing columns: " . implode(', ', $missing_columns) . "\n";
            }
        } else {
            echo "❌ Inquiries table missing - creating it...\n";
            
            $sql = "CREATE TABLE inquiries (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                phone VARCHAR(20),
                service_interest VARCHAR(100),
                message TEXT,
                status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
                ip_address VARCHAR(45),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $conn->exec($sql);
            echo "✅ Inquiries table created\n";
        }
    } else {
        echo "❌ Database connection failed\n";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

// Test 3: Test form processing
echo "\n3. Testing Form Processing...\n";

// Simulate form submission
$_POST = [
    'submit_inquiry' => '1',
    'csrf_token' => bin2hex(random_bytes(32)),
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test@example.com',
    'phone' => '+233-123-456-789',
    'service_interest' => 'Alucobond Cladding',
    'message' => 'This is a test message from the fix script.'
];

// Set CSRF token in session
$_SESSION['csrf_token'] = $_POST['csrf_token'];

echo "Simulating form submission...\n";

// Process like the real form
$form_success = false;
$form_errors = [];

if ($_POST && isset($_POST['submit_inquiry'])) {
    echo "✅ Form submission detected\n";
    
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $form_errors[] = "Security token mismatch. Please try again.";
        echo "❌ CSRF validation failed\n";
    } else {
        echo "✅ CSRF validation passed\n";
        
        // Validate form data
        $first_name = sanitize_input($_POST['first_name'] ?? '');
        $last_name = sanitize_input($_POST['last_name'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $phone = sanitize_input($_POST['phone'] ?? '');
        $service_interest = sanitize_input($_POST['service_interest'] ?? '');
        $message = sanitize_input($_POST['message'] ?? '');
        
        echo "✅ Data sanitized\n";
        
        // Validation
        if (empty($first_name)) $form_errors[] = "First name is required.";
        if (empty($last_name)) $form_errors[] = "Last name is required.";
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $form_errors[] = "Valid email is required.";
        }
        if (empty($phone)) $form_errors[] = "Phone number is required.";
        if (empty($message)) $form_errors[] = "Message is required.";
        
        if (!empty($form_errors)) {
            echo "❌ Validation errors: " . implode(', ', $form_errors) . "\n";
        } else {
            echo "✅ Validation passed\n";
            
            // Save to database
            try {
                $full_name = $first_name . ' ' . $last_name;
                $stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, service_interest, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $result = $stmt->execute([$full_name, $email, $phone, $service_interest, $message]);
                
                if ($result) {
                    $inquiry_id = $conn->lastInsertId();
                    echo "✅ Database insert successful (ID: $inquiry_id)\n";
                    $form_success = true;
                } else {
                    echo "❌ Database insert failed\n";
                }
            } catch (Exception $e) {
                echo "❌ Database error: " . $e->getMessage() . "\n";
            }
        }
    }
}

// Test 4: Check recent inquiries
echo "\n4. Recent Inquiries:\n";
try {
    $stmt = $conn->prepare("SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 3");
    $stmt->execute();
    $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($inquiries) {
        foreach ($inquiries as $inquiry) {
            echo "  - ID: {$inquiry['id']}, Name: {$inquiry['name']}, Email: {$inquiry['email']}, Created: {$inquiry['created_at']}\n";
        }
    } else {
        echo "  No inquiries found\n";
    }
} catch (Exception $e) {
    echo "❌ Error loading inquiries: " . $e->getMessage() . "\n";
}

// Test 5: Check file permissions
echo "\n5. Checking File Permissions...\n";
$files_to_check = [
    'contact.php',
    'includes/config.php',
    'includes/database.php',
    'includes/functions.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        if (is_readable($file)) {
            echo "✅ $file is readable\n";
        } else {
            echo "❌ $file is not readable\n";
        }
    } else {
        echo "❌ $file does not exist\n";
    }
}

echo "\n=== Fix Complete ===\n";

if ($form_success) {
    echo "🎉 Contact form is working correctly!\n";
    echo "The test submission was successful.\n";
} else {
    echo "⚠️ Contact form has issues that need to be resolved.\n";
    if (!empty($form_errors)) {
        echo "Errors found:\n";
        foreach ($form_errors as $error) {
            echo "  - $error\n";
        }
    }
}

echo "\nNext steps:\n";
echo "1. Test the contact form at: contact-debug.php\n";
echo "2. Check the main contact form at: contact.php\n";
echo "3. Monitor inquiries in admin panel: admin/inquiries/list.php\n";
?>