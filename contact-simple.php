<?php
/**
 * Simple Contact Form - Minimal Version for Testing
 */

// Start session at the very beginning
session_start();

// Include required files
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$form_success = false;
$form_errors = [];

// Handle form submission
if ($_POST && isset($_POST['submit_inquiry'])) {
    // Skip CSRF for simplicity in testing
    
    // Get and sanitize form data
    $first_name = sanitize_input($_POST['first_name'] ?? '');
    $last_name = sanitize_input($_POST['last_name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $service_interest = sanitize_input($_POST['service_interest'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');
    
    // Basic validation
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
            
            if ($conn) {
                $full_name = $first_name . ' ' . $last_name;
                $stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, service_interest, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $result = $stmt->execute([$full_name, $email, $phone, $service_interest, $message]);
                
                if ($result) {
                    $form_success = true;
                    
                    // Try to send email (optional)
                    $subject = "New Inquiry from " . $full_name;
                    $email_body = "Name: $full_name\nEmail: $email\nPhone: $phone\nService: $service_interest\nMessage: $message";
                    @mail(SITE_EMAIL, $subject, $email_body);
                } else {
                    $form_errors[] = "Failed to save inquiry. Please try again.";
                }
            } else {
                $form_errors[] = "Database connection failed. Please try again.";
            }
        } catch (Exception $e) {
            $form_errors[] = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - AluMaster (Simple)</title>
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
        .back-link { display: inline-block; margin-bottom: 20px; color: #007cba; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <a href="contact.php" class="back-link">← Back to Main Contact Page</a>
    
    <h1>Contact AluMaster (Simple Form)</h1>
    <p>This is a simplified version for testing. All functionality should work here.</p>

    <?php if ($form_success): ?>
        <div class="alert alert-success">
            <h4>✅ Success!</h4>
            <p>Your message has been sent successfully! We'll get back to you within 24 hours.</p>
            <p><a href="contact-simple.php">Send another message</a></p>
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
                    <option value="PVC Windows" <?php echo ($_POST['service_interest'] ?? '') === 'PVC Windows' ? 'selected' : ''; ?>>PVC Windows</option>
                    <option value="Other" <?php echo ($_POST['service_interest'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="message">Message *</label>
                <textarea id="message" name="message" rows="6" required 
                          placeholder="Tell us about your project..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <button type="submit" name="submit_inquiry" class="btn">Send Message</button>
            </div>
        </form>

    <?php endif; ?>

    <hr style="margin: 40px 0;">
    
    <h3>Recent Inquiries (Last 3)</h3>
    <?php
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        if ($conn) {
            $stmt = $conn->prepare("SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 3");
            $stmt->execute();
            $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($inquiries) {
                echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
                echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Created</th></tr>";
                foreach ($inquiries as $inquiry) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($inquiry['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($inquiry['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($inquiry['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($inquiry['created_at']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No inquiries found.</p>";
            }
        } else {
            echo "<p>Database connection failed.</p>";
        }
    } catch (Exception $e) {
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    ?>
</body>
</html>