<?php
/**
 * Detailed Contact Form Debug
 * This will help us identify exactly what's happening
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$debug_info = [];
$form_success = false;
$form_errors = [];

// Log everything about the request
$debug_info[] = "Request Method: " . $_SERVER['REQUEST_METHOD'];
$debug_info[] = "POST Data Count: " . count($_POST);
$debug_info[] = "POST Keys: " . implode(', ', array_keys($_POST));

if ($_POST) {
    $debug_info[] = "POST Data: " . json_encode($_POST);
    
    // Check each expected field
    $expected_fields = ['csrf_token', 'first_name', 'last_name', 'email', 'phone', 'service_interest', 'message', 'submit_inquiry'];
    foreach ($expected_fields as $field) {
        $debug_info[] = "Field '$field': " . (isset($_POST[$field]) ? 'Present (' . strlen($_POST[$field]) . ' chars)' : 'MISSING');
    }
}

// Process form if submit_inquiry is present
if ($_POST && isset($_POST['submit_inquiry'])) {
    $debug_info[] = "=== FORM PROCESSING STARTED ===";
    
    // Simple processing without CSRF for debugging
    $first_name = sanitize_input($_POST['first_name'] ?? '');
    $last_name = sanitize_input($_POST['last_name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $service_interest = sanitize_input($_POST['service_interest'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');
    
    // Validation
    if (empty($first_name)) $form_errors[] = "First name is required.";
    if (empty($last_name)) $form_errors[] = "Last name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_errors[] = "Valid email is required.";
    }
    if (empty($phone)) $form_errors[] = "Phone number is required.";
    if (empty($message)) $form_errors[] = "Message is required.";
    
    $debug_info[] = "Validation errors: " . count($form_errors);
    
    if (empty($form_errors)) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            if ($conn) {
                $full_name = $first_name . ' ' . $last_name;
                $stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, service_interest, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $result = $stmt->execute([$full_name, $email, $phone, $service_interest, $message]);
                
                if ($result) {
                    $form_success = true;
                    $debug_info[] = "Database insert successful - ID: " . $conn->lastInsertId();
                } else {
                    $form_errors[] = "Database insert failed";
                    $debug_info[] = "Database insert failed";
                }
            } else {
                $form_errors[] = "Database connection failed";
                $debug_info[] = "Database connection failed";
            }
        } catch (Exception $e) {
            $form_errors[] = "Exception: " . $e->getMessage();
            $debug_info[] = "Exception: " . $e->getMessage();
        }
    }
} else if ($_POST) {
    $debug_info[] = "=== FORM PROCESSING SKIPPED ===";
    $debug_info[] = "Reason: submit_inquiry field missing from POST data";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Detailed Debug</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        .debug { background: #f0f0f0; padding: 15px; margin: 15px 0; border: 1px solid #ccc; border-radius: 4px; }
        .debug h3 { margin-top: 0; color: #333; }
        .debug ul { margin: 0; padding-left: 20px; }
        .debug li { margin: 5px 0; font-family: monospace; font-size: 12px; }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;
        }
        .form-row { display: flex; gap: 15px; }
        .form-row .form-group { flex: 1; }
        .btn { background: #007cba; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
        .btn:hover { background: #005a87; }
        .alert { padding: 15px; margin: 15px 0; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .test-buttons { margin: 20px 0; }
        .test-buttons button { margin: 5px; padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Contact Form Detailed Debug</h1>
    
    <div class="debug">
        <h3>🔍 Debug Information</h3>
        <ul>
            <?php foreach ($debug_info as $info): ?>
                <li><?php echo htmlspecialchars($info); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="debug">
        <h3>📊 Current State</h3>
        <ul>
            <li>Form Success: <?php echo $form_success ? 'TRUE' : 'FALSE'; ?></li>
            <li>Form Errors: <?php echo count($form_errors); ?></li>
            <li>Session ID: <?php echo session_id(); ?></li>
            <li>CSRF Token: <?php echo substr($_SESSION['csrf_token'], 0, 16); ?>...</li>
        </ul>
    </div>

    <?php if ($form_success): ?>
        <div class="alert alert-success">
            <h4>✅ Success!</h4>
            <p>Form submitted successfully and saved to database!</p>
        </div>
    <?php endif; ?>

    <?php if (!empty($form_errors)): ?>
        <div class="alert alert-error">
            <h4>❌ Errors:</h4>
            <ul>
                <?php foreach ($form_errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="test-buttons">
        <h3>🧪 Test Different Submission Methods</h3>
        <button onclick="testFormSubmission()">Test JavaScript Submit</button>
        <button onclick="testButtonClick()">Test Button Click</button>
        <button onclick="testManualSubmit()">Test Manual Submit</button>
    </div>

    <h2>Contact Form</h2>
    
    <form id="contactForm" method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div class="form-row">
            <div class="form-group">
                <label for="first_name">First Name *</label>
                <input type="text" id="first_name" name="first_name" required 
                       value="<?php echo htmlspecialchars($_POST['first_name'] ?? 'Test'); ?>">
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" required 
                       value="<?php echo htmlspecialchars($_POST['last_name'] ?? 'User'); ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required 
                   value="<?php echo htmlspecialchars($_POST['email'] ?? 'test@example.com'); ?>">
        </div>

        <div class="form-group">
            <label for="phone">Phone Number *</label>
            <input type="tel" id="phone" name="phone" required 
                   value="<?php echo htmlspecialchars($_POST['phone'] ?? '+233-123-456-789'); ?>">
        </div>

        <div class="form-group">
            <label for="service_interest">Service Interest</label>
            <select id="service_interest" name="service_interest">
                <option value="">Select a service</option>
                <option value="Spider Glass" selected>Spider Glass</option>
                <option value="Curtain Wall">Curtain Wall</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="message">Message *</label>
            <textarea id="message" name="message" rows="4" required 
                      placeholder="Test message..."><?php echo htmlspecialchars($_POST['message'] ?? 'This is a test message to debug the contact form.'); ?></textarea>
        </div>

        <div class="form-group">
            <button type="submit" name="submit_inquiry" id="submitBtn" class="btn">
                Send Message (Debug Version)
            </button>
        </div>
    </form>

    <script>
        // Debug JavaScript
        console.log('Contact form debug script loaded');
        
        function testFormSubmission() {
            console.log('Testing form submission via JavaScript');
            const form = document.getElementById('contactForm');
            
            // Add submit_inquiry manually
            const submitInput = document.createElement('input');
            submitInput.type = 'hidden';
            submitInput.name = 'submit_inquiry';
            submitInput.value = '1';
            form.appendChild(submitInput);
            
            form.submit();
        }
        
        function testButtonClick() {
            console.log('Testing button click');
            document.getElementById('submitBtn').click();
        }
        
        function testManualSubmit() {
            console.log('Testing manual form submission');
            const form = document.getElementById('contactForm');
            
            // Create form data manually
            const formData = new FormData(form);
            formData.append('submit_inquiry', '1');
            
            console.log('Form data entries:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Submit via fetch
            fetch('', {
                method: 'POST',
                body: formData
            }).then(response => {
                console.log('Response received:', response.status);
                window.location.reload();
            });
        }
        
        // Monitor form submission
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            console.log('Form submit event triggered');
            console.log('Form data before submission:');
            
            const formData = new FormData(this);
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Check if submit_inquiry is present
            if (!formData.has('submit_inquiry')) {
                console.error('submit_inquiry field is missing!');
                alert('submit_inquiry field is missing from form data!');
            }
        });
        
        // Monitor button clicks
        document.getElementById('submitBtn').addEventListener('click', function(e) {
            console.log('Submit button clicked');
            console.log('Button name:', this.name);
            console.log('Button value:', this.value);
        });
    </script>
</body>
</html>