<?php
/**
 * Contact Us Page - AluMaster Aluminum System
 * Clean implementation without debugging code
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'includes/anti-spam.php';

// Load environment variables for email
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Load PHPMailer if available
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
    require_once 'includes/mailer.php';
}

$page_title = "Contact Us - AluMaster Aluminum System";
$page_description = "Get in touch with AluMaster for your aluminum and glass solutions. Located in Madina-Accra, Ghana.";
$body_class = "contact-page";

// Initialize form variables
$form_success = false;
$form_errors = [];

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Generate form timestamp for time-based validation
if (!isset($_SESSION['form_timestamp'])) {
    $_SESSION['form_timestamp'] = time();
}

// Debug logging
error_log("=== CONTACT FORM DEBUG START ===");
error_log("POST data: " . json_encode($_POST));
error_log("Session ID: " . session_id());
error_log("CSRF token in session: " . ($_SESSION['csrf_token'] ?? 'NOT SET'));

// Handle form submission
if ($_POST && isset($_POST['submit_inquiry'])) {
    error_log("Form processing started - submit_inquiry found");
    
    // Get IP address
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // Check if IP is blocked
    if (AntiSpam::isIPBlocked($ip_address)) {
        error_log("Blocked IP attempt: {$ip_address}");
        $form_errors[] = "Your request could not be processed at this time.";
    } else {
    
    // Validate CSRF token
    $csrf_valid = isset($_POST['csrf_token']) && 
                  isset($_SESSION['csrf_token']) && 
                  hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    
    if (!$csrf_valid) {
        $form_errors[] = "Security token mismatch. Please refresh the page and try again.";
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate new token
    } else {
        
        // ANTI-SPAM MEASURES
        
        // 1. Honeypot check - bots fill hidden fields
        if (!empty($_POST['website']) || !empty($_POST['company_url'])) {
            AntiSpam::logSpamAttempt($ip_address, 'HONEYPOT', 'Hidden field filled');
            // Silently reject - don't tell spammer why
            $form_success = true; // Fake success
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            // Don't process further
            goto skip_processing;
        }
        
        // 2. Time-based validation - ensure minimum 3 seconds on page
        $form_load_time = intval($_POST['form_timestamp'] ?? 0);
        $time_spent = time() - $form_load_time;
        if ($time_spent < 3) {
            AntiSpam::logSpamAttempt($ip_address, 'TOO_FAST', "Submitted in {$time_spent}s");
            $form_errors[] = "Please take a moment to review your message before submitting.";
        }
        
        // 3. Rate limiting - prevent rapid successive submissions
        $rate_limit_key = 'rate_limit_' . md5($ip_address);
        $last_submission = $_SESSION[$rate_limit_key] ?? 0;
        $time_since_last = time() - $last_submission;
        
        if ($time_since_last < 60) { // 60 seconds between submissions
            AntiSpam::logSpamAttempt($ip_address, 'RATE_LIMIT', "Too many requests ({$time_since_last}s since last)");
            $form_errors[] = "Please wait a moment before submitting another message. Try again in " . (60 - $time_since_last) . " seconds.";
        }
        
        // Check for excessive submissions from this IP
        $submission_count = AntiSpam::getSubmissionCount($ip_address, 60);
        if ($submission_count > 5) {
            AntiSpam::logSpamAttempt($ip_address, 'EXCESSIVE_SUBMISSIONS', "$submission_count submissions in 60 minutes");
            AntiSpam::blockIP($ip_address, 'Excessive submissions');
            $form_errors[] = "Too many submission attempts. Please contact us directly.";
        }
        
        // 4. Check for spam patterns in message content
        $message_text = $_POST['message'] ?? '';
        if (AntiSpam::containsSpamPatterns($message_text)) {
            AntiSpam::logSpamAttempt($ip_address, 'SPAM_PATTERN', 'Message contains spam keywords');
            $form_errors[] = "Your message contains content that appears to be spam. Please revise and try again.";
        }
        
        // 5. Check for excessive links in message
        $link_count = AntiSpam::countURLs($message_text);
        if ($link_count > 2) {
            AntiSpam::logSpamAttempt($ip_address, 'EXCESSIVE_LINKS', "{$link_count} links in message");
            $form_errors[] = "Please remove some links from your message (maximum 2 allowed).";
        }
        
        // Only proceed if anti-spam checks passed
        if (empty($form_errors)) {
        
            // Sanitize form data
            $first_name = sanitize_input($_POST['first_name'] ?? '');
            $last_name = sanitize_input($_POST['last_name'] ?? '');
            $email = sanitize_input($_POST['email'] ?? '');
            $phone = sanitize_input($_POST['phone'] ?? '');
            $service_interest = sanitize_input($_POST['service_interest'] ?? '');
            $message = sanitize_input($_POST['message'] ?? '');
        
            // Validate required fields
            if (empty($first_name)) $form_errors[] = "First name is required.";
            if (empty($last_name)) $form_errors[] = "Last name is required.";
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $form_errors[] = "Valid email is required.";
            }
            if (empty($phone)) $form_errors[] = "Phone number is required.";
            if (empty($message)) $form_errors[] = "Message is required.";
            
            // Additional validation - check field lengths
            if (strlen($first_name) > 50) $form_errors[] = "First name is too long.";
            if (strlen($last_name) > 50) $form_errors[] = "Last name is too long.";
            if (strlen($message) < 10) $form_errors[] = "Please provide a more detailed message (at least 10 characters).";
            if (strlen($message) > 2000) $form_errors[] = "Message is too long (maximum 2000 characters).";
            
            // Check for suspicious email
            if (AntiSpam::isSuspiciousEmail($email)) {
                AntiSpam::logSpamAttempt($ip_address, 'FAKE_EMAIL', "Email: {$email}");
                $form_errors[] = "Please provide a valid email address.";
            }
            
            // If no validation errors, save to database
            if (empty($form_errors)) {
                try {
                    $db = new Database();
                    $conn = $db->getConnection();
                    
                    if (!$conn) {
                        throw new Exception("Database connection failed");
                    }
                    
                    // Insert inquiry into database
                    $full_name = $first_name . ' ' . $last_name;
                    $stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, service_interest, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                    $result = $stmt->execute([$full_name, $email, $phone, $service_interest, $message]);
                    
                    if ($result) {
                        $form_success = true;
                        
                        // Update rate limiting timestamp
                        $_SESSION[$rate_limit_key] = time();
                        
                        // Log successful submission (for monitoring)
                        AntiSpam::logSpamAttempt($ip_address, 'SUCCESS', "Valid submission from {$email}");
                    
                    // Send email notification
                    try {
                        if (class_exists('EmailService')) {
                            $emailService = new EmailService();
                            $inquiryData = [
                                'name' => $full_name,
                                'email' => $email,
                                'phone' => $phone,
                                'service_interest' => $service_interest,
                                'message' => $message
                            ];
                            $emailService->sendContactInquiry($inquiryData);
                            $emailService->sendContactAutoReply($inquiryData);
                        } else {
                            // Fallback to basic mail
                            $subject = "New Inquiry from " . $full_name;
                            $email_body = "Name: $full_name\nEmail: $email\nPhone: $phone\nService: $service_interest\nMessage: $message";
                            @mail(SITE_EMAIL, $subject, $email_body);
                        }
                    } catch (Exception $e) {
                        // Email failure doesn't affect form success
                        error_log("Email sending failed: " . $e->getMessage());
                    }
                    
                        // Generate new CSRF token for security
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        // Generate new form timestamp
                        $_SESSION['form_timestamp'] = time();
                    
                    } else {
                        $form_errors[] = "Failed to save your inquiry. Please try again.";
                    }
                    
                } catch (Exception $e) {
                    $form_errors[] = "There was an error submitting your inquiry. Please try again.";
                    error_log("Contact form error: " . $e->getMessage());
                }
            }
        }
    }
    }
    
    skip_processing:
}

include 'includes/header.php';
?>



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
                            <p><a href="mailto:contact@alumastergh.com">contact@alumastergh.com</a></p>
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

                    <div class="contact-method">
                        <div class="contact-method-icon website-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                        </div>
                        <div class="contact-method-content">
                            <h3>Website</h3>
                            <p><a href="https://www.alumastergh.com" target="_blank">www.alumastergh.com</a></p>
                        </div>
                    </div>

                    <!-- Social Media Section -->
                    <div class="social-section">
                        <h3>Follow Us</h3>
                        <div class="social-icons-grid">
                            <a href="https://www.facebook.com/alumastergh" target="_blank" class="social-icon facebook" title="Follow us on Facebook">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                            <a href="https://www.instagram.com/alumaster75" target="_blank" class="social-icon instagram" title="Follow us on Instagram">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </a>
                            <a href="https://twitter.com/alumaster75" target="_blank" class="social-icon twitter" title="Follow us on Twitter">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                            </a>
                            <a href="https://www.tiktok.com/@alumaster75" target="_blank" class="social-icon tiktok" title="Follow us on TikTok">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                                </svg>
                            </a>
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
                        <form class="contact-form-clean" method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="submit_inquiry" value="1">
                            <input type="hidden" name="form_timestamp" value="<?php echo time(); ?>">
                            
                            <!-- Honeypot fields - hidden from humans, visible to bots -->
                            <div style="position: absolute; left: -9999px; top: -9999px;" aria-hidden="true">
                                <input type="text" name="website" tabindex="-1" autocomplete="off" />
                                <input type="text" name="company_url" tabindex="-1" autocomplete="off" />
                            </div>
                            
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

    <!-- Map Section -->
    <section class="map-section">
        <div class="container">
            <div class="map-header text-center">
                <h2 class="map-title">Visit Our Office</h2>
                <p class="map-subtitle">Find us at our convenient location in Madina-Accra</p>
            </div>
            
            <div class="map-container">
                <!-- Google Maps Embed for Madina-Accra, Ghana -->
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3970.8267!2d-0.1676!3d5.6037!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfdf9084b2b7a773%3A0x2b2b2b2b2b2b2b2b!2sMadina%2C%20Accra%2C%20Ghana!5e0!3m2!1sen!2sgh!4v1699999999999"
                    width="100%" 
                    height="450" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    title="AluMaster Office Location - 16 Palace Street, Madina-Accra, Ghana">
                </iframe>
                
                <!-- Map Info Overlay -->
                <div class="map-info-overlay">
                    <div class="map-info-content">
                        <div class="map-info-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="map-info-text">
                            <h4>AluMaster Office</h4>
                            <p>16 Palace Street, Madina-Accra</p>
                        </div>
                        <a href="https://maps.google.com/?q=16+Palace+Street,+Madina-Accra,+Ghana" target="_blank" class="btn btn-sm btn-primary">
                            Get Directions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Contact Form JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.querySelector('.contact-form-clean');
    
    if (contactForm) {
        // Track when form was loaded for time-based validation
        const formLoadTime = Date.now();
        
        // Add form submit handler
        contactForm.addEventListener('submit', function(e) {
            // Check minimum time spent on form (3 seconds)
            const timeSpent = (Date.now() - formLoadTime) / 1000;
            if (timeSpent < 3) {
                e.preventDefault();
                alert('Please take a moment to review your message before submitting.');
                return false;
            }
            
            // Simple validation
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            let firstInvalidField = null;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc3545';
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = field;
                } else {
                    field.style.borderColor = '#ced4da';
                }
            });
            
            // Validate email format
            const emailField = this.querySelector('[name="email"]');
            if (emailField && emailField.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value)) {
                    emailField.style.borderColor = '#dc3545';
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = emailField;
                }
            }
            
            // Validate message length
            const messageField = this.querySelector('[name="message"]');
            if (messageField && messageField.value) {
                if (messageField.value.trim().length < 10) {
                    messageField.style.borderColor = '#dc3545';
                    isValid = false;
                    alert('Please provide a more detailed message (at least 10 characters).');
                    if (!firstInvalidField) firstInvalidField = messageField;
                } else if (messageField.value.length > 2000) {
                    messageField.style.borderColor = '#dc3545';
                    isValid = false;
                    alert('Message is too long. Please keep it under 2000 characters.');
                    if (!firstInvalidField) firstInvalidField = messageField;
                }
            }
            
            // Check for spam patterns
            if (messageField && messageField.value) {
                const spamPatterns = [
                    /viagra|cialis|pharmacy|casino|poker/i,
                    /click here|buy now|limited time|act now/i
                ];
                
                for (let pattern of spamPatterns) {
                    if (pattern.test(messageField.value)) {
                        messageField.style.borderColor = '#dc3545';
                        isValid = false;
                        alert('Your message contains content that appears to be spam. Please revise your message.');
                        if (!firstInvalidField) firstInvalidField = messageField;
                        break;
                    }
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                if (firstInvalidField) {
                    firstInvalidField.focus();
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="animation: spin 1s linear infinite;"><circle cx="12" cy="12" r="10" stroke-width="4" stroke="currentColor" stroke-opacity="0.25"></circle><path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" fill="currentColor"></path></svg> Sending...';
                submitBtn.disabled = true;
                
                // Re-enable if submission fails (fallback)
                setTimeout(function() {
                    if (submitBtn.disabled) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                }, 10000);
            }
            
            return true;
        });
        
        // Add input event listeners to clear error styling
        const inputs = contactForm.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.style.borderColor = '#ced4da';
                }
            });
        });
    }
});
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<?php include 'includes/footer.php'; ?>