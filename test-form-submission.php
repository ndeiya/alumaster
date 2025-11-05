<?php
/**
 * Test Form Submission
 * Simulates the exact contact form submission
 */

// Start session like contact.php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    ini_set('session.use_strict_mode', 1);
    session_start();
}

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

echo "Testing Contact Form Submission\n";
echo "==============================\n\n";

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Simulate the exact POST data from the form
$_POST = [
    'submit_inquiry' => '1',
    'csrf_token' => $_SESSION['csrf_token'],
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john.doe@example.com',
    'phone' => '+233-247-439-206',
    'service_interest' => 'Spider Glass',
    'message' => 'I need your service for my project. Please contact me.'
];

echo "Simulating form submission with data:\n";
echo "- Name: {$_POST['first_name']} {$_POST['last_name']}\n";
echo "- Email: {$_POST['email']}\n";
echo "- Phone: {$_POST['phone']}\n";
echo "- Service: {$_POST['service_interest']}\n\n";

// Run the exact same logic as contact.php
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
            echo "❌ Validation errors:\n";
            foreach ($form_errors as $error) {
                echo "  - $error\n";
            }
        } else {
            echo "✅ Validation passed\n";
            
            // If no errors, save to database
            try {
                $db = new Database();
                $conn = $db->getConnection();
                
                if (!$conn) {
                    throw new Exception("Database connection failed");
                }
                
                echo "✅ Database connected\n";
                
                $full_name = $first_name . ' ' . $last_name;
                $stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, service_interest, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $result = $stmt->execute([$full_name, $email, $phone, $service_interest, $message]);
                
                if ($result) {
                    $inquiry_id = $conn->lastInsertId();
                    echo "✅ Database insert successful (ID: $inquiry_id)\n";
                    $form_success = true;
                } else {
                    echo "❌ Database insert failed\n";
                    echo "Error info: " . json_encode($stmt->errorInfo()) . "\n";
                    $form_errors[] = "Failed to save your inquiry. Please try again.";
                }
                
            } catch (Exception $e) {
                $form_errors[] = "There was an error submitting your inquiry. Please try again.";
                echo "❌ Exception: " . $e->getMessage() . "\n";
            }
        }
    }
}

echo "\n=== Results ===\n";
echo "Form Success: " . ($form_success ? 'YES' : 'NO') . "\n";
echo "Form Errors: " . (empty($form_errors) ? 'None' : implode(', ', $form_errors)) . "\n";

// Check if the record was actually inserted
if ($form_success) {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM inquiries WHERE email = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$_POST['email']]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($record) {
            echo "\n✅ Record found in database:\n";
            echo "  - ID: {$record['id']}\n";
            echo "  - Name: {$record['name']}\n";
            echo "  - Email: {$record['email']}\n";
            echo "  - Status: {$record['status']}\n";
            echo "  - Created: {$record['created_at']}\n";
        } else {
            echo "\n❌ Record NOT found in database\n";
        }
    } catch (Exception $e) {
        echo "\n❌ Error checking database: " . $e->getMessage() . "\n";
    }
}
?>