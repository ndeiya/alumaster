<?php
/**
 * Contact Form - Final Working Version
 * This version incorporates all fixes and should work reliably
 */

// Start session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$page_title = "Contact Us - AluMaster Aluminum System";
$page_description = "Get in touch with AluMaster for your aluminum and glass solutions. Located in Madina-Accra, Ghana.";
$body_class = "contact-page";

// Initialize variables
$form_success = false;
$form_errors = [];
$debug_info = [];

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_POST && isset($_POST['submit_inquiry'])) {
    $debug_info[] = "Form submitted via POST";
    
    // Validate CSRF token (with better error handling)
    $csrf_valid = isset($_POST['csrf_token']) && 
                  isset($_SESSION['csrf_token']) && 
                  hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    
    if (!$csrf_valid) {
        $form_errors[] = "Security token mismatch. Please refresh the page and try again.";
        $debug_info[] = "CSRF validation failed";
    } else {
        $debug_info[] = "CSRF validation passed";
        
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
        
        $debug_info[] = "Validation completed - " . count($form_errors) . " errors found";
        
        // If no errors, save to database
        if (empty($form_errors)) {
            try {
                $db = new Database();
                $conn = $db->getConnection();
                
                if (!$conn) {
                    throw new Exception("Database connection failed");
                }
                
                $debug_info[] = "Database connected successfully";
                
                $full_name = $first_name . ' ' . $last_name;
                $stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, service_interest, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $result = $stmt->execute([$full_name, $email, $phone, $service_interest, $message]);
                
                if ($result) {
                    $inquiry_id = $conn->lastInsertId();
                    $form_success = true;
                    $debug_info[] = "Database insert successful - ID: $inquiry_id";
                    
                    // Try to send email (don't fail if email fails)
                    try {
                        $subject = "New Inquiry from " . $full_name;
                        $email_body = "Name: $full_name\nEmail: $email\nPhone: $phone\nService: $service_interest\nMessage: $message\nDate: " . date('Y-m-d H:i:s');
                        
                        if (@mail(SITE_EMAIL, $subject, $email_body)) {
                            $debug_info[] = "Email sent successfully";
                        } else {
                            $debug_info[] = "Email sending failed (but form still successful)";
                        }
                    } catch (Exception $e) {
                        $debug_info[] = "Email error: " . $e->getMessage();
                    }
                    
                    // Generate new CSRF token for security
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    
                } else {
                    $form_errors[] = "Failed to save your inquiry. Please try again.";
                    $debug_info[] = "Database insert failed";
                }
                
            } catch (Exception $e) {
                $form_errors[] = "There was an error submitting your inquiry. Please try again.";
                $debug_info[] = "Exception: " . $e->getMessage();
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Debug Information (remove in production) -->
<?php if (DEBUG_MODE && !empty($debug_info)): ?>
<div style="background: #f0f0f0; padding: 15px; margin: 15px; border: 1px solid #ccc; font-size: 12px;">
    <strong>DEBUG INFO:</strong><br>
    <?php foreach ($debug_info as $info): ?>
        • <?php echo htmlspecialchars($info); ?><br>
    <?php endforeach; ?>
    <br>
    <strong>Form State:</strong><br>
    • Success: <?php echo $form_success ? 'TRUE' : 'FALSE'; ?><br>
    • Errors: <?php echo count($form_errors); ?><br>
    • POST Data: <?php echo $_POST ? 'Present (' . count($_POST) . ' fields)' : 'None'; ?><br>
    • Session ID: <?php echo session_id(); ?><br>
</div>
<?php endif; ?>

<main>
    <!-- Hero Section -->
    <section class="hero contact-hero">
        <div class="container">
            <div class="hero-content text-center">
                <h1 class="hero-title">Get In Touch</h1>
                <p class="hero-subtitle">Ready to transform your architectural vision? Contact AluMaster today and discover where quality meets affordability.</p>
                <div class="hero-feature">
                    <svg class="hero-icon" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21L6.16 11.37a11.045 11.045 0 005.516 5.516l1.983-4.064a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span>Available 24/7 for Your Projects</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Information & Form Section -->
    <section class="section contact-main-section">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Information -->
                <div class="contact-info-card">
                    <h2 class="contact-info-title">Contact Information</h2>
                    
                    <div class="contact-method">
                        <div class="contact-method-icon phone-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21L6.16 11.37a11.045 11.045 0 005.516 5.516l1.983-4.064a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div class="contact-method-content">
                            <h3>Phone Numbers</h3>
                            <p><a href="tel:+233541737575">+233-541-737-575</a></p>
                            <p><a href="tel:+233502777704">+233-502-777-704</a></p>
                        </div>
                    </div>

                    <div class="contact-method">
                        <div class="contact-method-icon email-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="contact-method-content">
                            <h3>Email Address</h3>
                            <p><a href="mailto:alumaster75@gmail.com">alumaster75@gmail.com</a></p>
                        </div>
                    </div>

                    <div class="contact-method">
                        <div class="contact-method-icon location-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="contact-method-content">
                            <h3>Office Location</h3>
                            <p>16 Palace Street<br>Madina-Accra, Ghana</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form-card">
                    <h2 class="contact-form-title">Send Us a Message</h2>

                    <?php if ($form_success): ?>
                        <div class="alert alert-success">
                            <div class="alert-icon">
                                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="alert-content">
                                <h4>Message Sent Successfully!</h4>
                                <p>Thank you for your inquiry. We'll get back to you within 24 hours.</p>
                                <p><a href="contact-final-fix.php" style="color: #155724; text-decoration: underline;">Send another message</a></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($form_errors)): ?>
                        <div class="alert alert-error">
                            <div class="alert-icon">
                                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="alert-content">
                                <h4>Please correct the following errors:</h4>
                                <ul>
                                    <?php foreach ($form_errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!$form_success): ?>
                        <form class="contact-form" method="POST" action="">
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
                                    <option value="PVC Windows" <?php echo ($_POST['service_interest'] ?? '') === 'PVC Windows' ? 'selected' : ''; ?>>PVC Windows</option>
                                    <option value="Sliding Doors" <?php echo ($_POST['service_interest'] ?? '') === 'Sliding Doors' ? 'selected' : ''; ?>>Sliding Doors</option>
                                    <option value="Frameless Door" <?php echo ($_POST['service_interest'] ?? '') === 'Frameless Door' ? 'selected' : ''; ?>>Frameless Door</option>
                                    <option value="Sun-breakers" <?php echo ($_POST['service_interest'] ?? '') === 'Sun-breakers' ? 'selected' : ''; ?>>Sun-breakers</option>
                                    <option value="Steel Balustrades" <?php echo ($_POST['service_interest'] ?? '') === 'Steel Balustrades' ? 'selected' : ''; ?>>Steel Balustrades</option>
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
                                    Send Message
                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>