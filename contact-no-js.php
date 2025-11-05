<?php
/**
 * Contact Form Without JavaScript Validation
 * This version disables the problematic JavaScript
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$page_title = "Contact Us - No JS Validation";
$form_success = false;
$form_errors = [];

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_POST && isset($_POST['submit_inquiry'])) {
    // Validate CSRF token
    $csrf_valid = isset($_POST['csrf_token']) && 
                  isset($_SESSION['csrf_token']) && 
                  hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    
    if (!$csrf_valid) {
        $form_errors[] = "Security token mismatch. Please refresh the page and try again.";
    } else {
        // Sanitize and validate form data
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
                    $form_success = true;
                    
                    // Try to send email
                    $subject = "New Inquiry from " . $full_name;
                    $email_body = "Name: $full_name\nEmail: $email\nPhone: $phone\nService: $service_interest\nMessage: $message";
                    @mail(SITE_EMAIL, $subject, $email_body);
                    
                    // Generate new CSRF token
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                } else {
                    $form_errors[] = "Failed to save your inquiry. Please try again.";
                }
                
            } catch (Exception $e) {
                $form_errors[] = "There was an error submitting your inquiry. Please try again.";
            }
        }
    }
}

include 'includes/header.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero contact-hero">
        <div class="container">
            <div class="hero-content text-center">
                <h1 class="hero-title">Get In Touch (No JS Validation)</h1>
                <p class="hero-subtitle">Testing contact form without JavaScript validation interference.</p>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="section contact-main-section">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Information -->
                <div class="contact-info-card">
                    <h2 class="contact-info-title">Contact Information</h2>
                    
                    <div class="contact-method">
                        <div class="contact-method-content">
                            <h3>Phone Numbers</h3>
                            <p><a href="tel:+233541737575">+233-541-737-575</a></p>
                            <p><a href="tel:+233502777704">+233-502-777-704</a></p>
                        </div>
                    </div>

                    <div class="contact-method">
                        <div class="contact-method-content">
                            <h3>Email Address</h3>
                            <p><a href="mailto:alumaster75@gmail.com">alumaster75@gmail.com</a></p>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form-card">
                    <h2 class="contact-form-title">Send Us a Message</h2>

                    <?php if ($form_success): ?>
                        <div class="alert alert-success">
                            <div class="alert-content">
                                <h4>✅ Message Sent Successfully!</h4>
                                <p>Thank you for your inquiry. We'll get back to you within 24 hours.</p>
                                <p><a href="contact-no-js.php">Send another message</a></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($form_errors)): ?>
                        <div class="alert alert-error">
                            <div class="alert-content">
                                <h4>❌ Please correct the following errors:</h4>
                                <ul>
                                    <?php foreach ($form_errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!$form_success): ?>
                        <form class="contact-form-no-js" method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" id="first_name" name="first_name" class="form-input" 
                                           placeholder="Enter your first name" required 
                                           value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" class="form-input" 
                                           placeholder="Enter your last name" required 
                                           value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" id="email" name="email" class="form-input" 
                                       placeholder="Enter your email" required 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-input" 
                                       placeholder="Enter your phone number" required 
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="service_interest" class="form-label">Service Interest</label>
                                <select id="service_interest" name="service_interest" class="form-select">
                                    <option value="">Select a service</option>
                                    <option value="Alucobond Cladding" <?php echo ($_POST['service_interest'] ?? '') === 'Alucobond Cladding' ? 'selected' : ''; ?>>Alucobond Cladding</option>
                                    <option value="Curtain Wall" <?php echo ($_POST['service_interest'] ?? '') === 'Curtain Wall' ? 'selected' : ''; ?>>Curtain Wall</option>
                                    <option value="Spider Glass" <?php echo ($_POST['service_interest'] ?? '') === 'Spider Glass' ? 'selected' : ''; ?>>Spider Glass</option>
                                    <option value="Other" <?php echo ($_POST['service_interest'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="message" class="form-label">Message</label>
                                <textarea id="message" name="message" class="form-textarea" rows="6" 
                                          placeholder="Tell us about your project..." required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                            </div>

                            <div class="form-actions">
                                <button type="submit" name="submit_inquiry" class="btn btn-primary btn-lg btn-full">
                                    Send Message (No JS Validation)
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Disable the problematic JavaScript -->
<script>
// Override the form validation to prevent interference
document.addEventListener('DOMContentLoaded', function() {
    console.log('JavaScript validation disabled for testing');
    
    // Remove any existing form event listeners by cloning and replacing forms
    const forms = document.querySelectorAll('.contact-form-no-js');
    forms.forEach(form => {
        const newForm = form.cloneNode(true);
        form.parentNode.replaceChild(newForm, form);
    });
    
    // Add simple logging
    document.querySelector('.contact-form-no-js').addEventListener('submit', function(e) {
        console.log('Form submitting without JavaScript validation');
        console.log('Submit button name present:', this.querySelector('[name="submit_inquiry"]') ? 'YES' : 'NO');
    });
});
</script>

<?php include 'includes/footer.php'; ?>