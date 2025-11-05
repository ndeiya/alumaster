<?php
/**
 * Contact Form Without CSRF - For Testing
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$page_title = "Contact Us - No CSRF Test";
$form_success = false;
$form_errors = [];

// Handle form submission WITHOUT CSRF check
if ($_POST && isset($_POST['submit_inquiry'])) {
    error_log("No-CSRF form submitted: " . json_encode($_POST));
    
    // Validate form data
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
    
    // If no errors, save to database
    if (empty($form_errors)) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            if (!$conn) {
                throw new Exception("Database connection failed");
            }
            
            $full_name = $first_name . ' ' . $last_name;
            $stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, service_interest, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $result = $stmt->execute([$full_name, $email, $phone, $service_interest, $message]);
            
            if ($result) {
                error_log("No-CSRF form: Successfully inserted inquiry for " . $full_name);
                $form_success = true;
            } else {
                error_log("No-CSRF form: Failed to insert inquiry for " . $full_name);
                $form_errors[] = "Failed to save your inquiry. Please try again.";
            }
            
        } catch (Exception $e) {
            $form_errors[] = "There was an error submitting your inquiry. Please try again.";
            error_log("No-CSRF form error: " . $e->getMessage());
        }
    }
}

error_log("No-CSRF form processing - Success: " . ($form_success ? 'true' : 'false') . ", Errors: " . count($form_errors));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
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
        .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; border: 1px solid #ccc; font-size: 12px; }
    </style>
</head>
<body>
    <h1>Contact Form Test (No CSRF)</h1>
    <p><a href="contact.php">← Back to main contact form</a></p>
    
    <div class="debug">
        <strong>DEBUG:</strong><br>
        Form Success: <?php echo $form_success ? 'TRUE' : 'FALSE'; ?><br>
        Form Errors: <?php echo empty($form_errors) ? 'None' : count($form_errors) . ' errors'; ?><br>
        POST Data: <?php echo $_POST ? 'Present (' . count($_POST) . ' fields)' : 'None'; ?><br>
    </div>

    <?php if ($form_success): ?>
        <div class="alert alert-success">
            <h4>✅ Success!</h4>
            <p>Your message has been sent successfully! We'll get back to you within 24 hours.</p>
            <p><a href="contact-no-csrf.php">Send another message</a></p>
        </div>
    <?php else: ?>

        <?php if (!empty($form_errors)): ?>
            <div class="alert alert-error">
                <h4>❌ Please fix the following errors:</h4>
                <ul>
                    <?php foreach ($form_errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" required 
                           value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" required 
                           value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" required 
                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="service_interest">Service Interest</label>
                <select id="service_interest" name="service_interest">
                    <option value="">Select a service</option>
                    <option value="Alucobond Cladding" <?php echo ($_POST['service_interest'] ?? '') === 'Alucobond Cladding' ? 'selected' : ''; ?>>Alucobond Cladding</option>
                    <option value="Curtain Wall" <?php echo ($_POST['service_interest'] ?? '') === 'Curtain Wall' ? 'selected' : ''; ?>>Curtain Wall</option>
                    <option value="Spider Glass" <?php echo ($_POST['service_interest'] ?? '') === 'Spider Glass' ? 'selected' : ''; ?>>Spider Glass</option>
                    <option value="Other" <?php echo ($_POST['service_interest'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="message">Message *</label>
                <textarea id="message" name="message" rows="6" required 
                          placeholder="Tell us about your project..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <button type="submit" name="submit_inquiry" class="btn">Send Message (No CSRF)</button>
            </div>
        </form>

    <?php endif; ?>
</body>
</html>