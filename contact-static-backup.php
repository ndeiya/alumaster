<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$page_title = "Contact Us - AluMaster Aluminum System";
$page_description = "Get in touch with AluMaster for your aluminum and glass solutions. Located in Madina-Accra, Ghana.";

// Handle form submission
$form_success = false;
$form_errors = [];

if ($_POST && isset($_POST['submit_inquiry'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $form_errors[] = "Security token mismatch. Please try again.";
    } else {
        // Validate form data
        $name = sanitize_input($_POST['name'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $phone = sanitize_input($_POST['phone'] ?? '');
        $service_interest = sanitize_input($_POST['service_interest'] ?? '');
        $message = sanitize_input($_POST['message'] ?? '');
        
        // Validation
        if (empty($name)) $form_errors[] = "Name is required.";
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
                
                $stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, service_interest, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$name, $email, $phone, $service_interest, $message]);
                
                $form_success = true;
                
                // Send email notification (optional)
                $subject = "New Inquiry from " . $name;
                $email_body = "Name: $name\nEmail: $email\nPhone: $phone\nService Interest: $service_interest\nMessage: $message";
                mail('alumaster75@gmail.com', $subject, $email_body);
                
            } catch (Exception $e) {
                $form_errors[] = "There was an error submitting your inquiry. Please try again.";
            }
        }
    }
}

// Generate new CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include 'includes/header.php';
?>

<main>
    <!-- Contact Hero -->
    <section class="hero-section contact-hero">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content">
                <nav class="breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-current">Contact</span>
                </nav>
                <h1 class="hero-title">Get In Touch</h1>
                <p class="hero-subtitle">Ready to start your project? Contact us for a free consultation and quote.</p>
            </div>
        </div>
    </section>

    <!-- Contact Methods -->
    <section class="section">
        <div class="container">
            <div class="contact-methods-grid">
                <div class="contact-method-card">
                    <div class="contact-method-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <div class="contact-method-content">
                        <h3 class="contact-method-label">Call Us</h3>
                        <div class="contact-method-value">
                            <a href="tel:+233541737575" class="contact-link">+233-541-737-575</a>
                            <a href="tel:+233502777703" class="contact-link">+233-502-777-703</a>
                        </div>
                    </div>
                </div>

                <div class="contact-method-card">
                    <div class="contact-method-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="contact-method-content">
                        <h3 class="contact-method-label">Email Us</h3>
                        <div class="contact-method-value">
                            <a href="mailto:alumaster75@gmail.com" class="contact-link">alumaster75@gmail.com</a>
                        </div>
                    </div>
                </div>

                <div class="contact-method-card">
                    <div class="contact-method-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="contact-method-content">
                        <h3 class="contact-method-label">Visit Us</h3>
                        <div class="contact-method-value">
                            <address class="contact-address">
                                16 Palace Street<br>
                                Madina-Accra, Ghana
                            </address>
                        </div>
                    </div>
                </div>

                <div class="contact-method-card">
                    <div class="contact-method-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="contact-method-content">
                        <h3 class="contact-method-label">Business Hours</h3>
                        <div class="contact-method-value">
                            <div>Mon - Fri: 8:00 AM - 6:00 PM</div>
                            <div>Sat: 9:00 AM - 4:00 PM</div>
                            <div>Sun: Closed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form -->
    <section class="section bg-gray-50">
        <div class="container">
            <div class="contact-form-wrapper">
                <div class="contact-form-header">
                    <h2 class="section-title">Send Us a Message</h2>
                    <p class="section-description">Fill out the form below and we'll get back to you within 24 hours.</p>
                </div>

                <?php if ($form_success): ?>
                    <div class="alert alert-success">
                        <div class="alert-icon">
                            <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <label for="name" class="form-label">Full Name <span class="required">*</span></label>
                                <input type="text" id="name" name="name" class="form-input" required 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address <span class="required">*</span></label>
                                <input type="email" id="email" name="email" class="form-input" required 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number <span class="required">*</span></label>
                                <input type="tel" id="phone" name="phone" class="form-input" required 
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="service_interest" class="form-label">Service Interest</label>
                                <select id="service_interest" name="service_interest" class="form-select">
                                    <option value="">Select a service</option>
                                    <option value="Alucobond Cladding" <?php echo ($_POST['service_interest'] ?? '') === 'Alucobond Cladding' ? 'selected' : ''; ?>>Alucobond Cladding</option>
                                    <option value="Curtain Wall" <?php echo ($_POST['service_interest'] ?? '') === 'Curtain Wall' ? 'selected' : ''; ?>>Curtain Wall</option>
                                    <option value="Spider Glass" <?php echo ($_POST['service_interest'] ?? '') === 'Spider Glass' ? 'selected' : ''; ?>>Spider Glass</option>
                                    <option value="Sliding Windows And Doors" <?php echo ($_POST['service_interest'] ?? '') === 'Sliding Windows And Doors' ? 'selected' : ''; ?>>Sliding Windows And Doors</option>
                                    <option value="Frameless Door" <?php echo ($_POST['service_interest'] ?? '') === 'Frameless Door' ? 'selected' : ''; ?>>Frameless Door</option>
                                    <option value="PVC Windows" <?php echo ($_POST['service_interest'] ?? '') === 'PVC Windows' ? 'selected' : ''; ?>>PVC Windows</option>
                                    <option value="Sun-breakers" <?php echo ($_POST['service_interest'] ?? '') === 'Sun-breakers' ? 'selected' : ''; ?>>Sun-breakers</option>
                                    <option value="Stainless Steel Balustrades" <?php echo ($_POST['service_interest'] ?? '') === 'Stainless Steel Balustrades' ? 'selected' : ''; ?>>Stainless Steel Balustrades</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="message" class="form-label">Message <span class="required">*</span></label>
                            <textarea id="message" name="message" class="form-textarea" rows="6" required 
                                      placeholder="Tell us about your project..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="submit_inquiry" class="btn btn-primary btn-lg">
                                Send Message
                                <svg class="icon-sm ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3970.8267739384!2d-0.1677!3d5.6037!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNcKwMzYnMTMuMyJOIDDCsDEwJzAzLjciVw!5e0!3m2!1sen!2sgh!4v1635789012345!5m2!1sen!2sgh"
                width="100%" 
                height="400" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade"
                title="AluMaster Location - 16 Palace Street, Madina-Accra">
            </iframe>
        </div>
    </section>
</main>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/233541737575?text=Hello%20AluMaster,%20I'm%20interested%20in%20your%20services" 
   class="whatsapp-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <svg class="icon-lg" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
    </svg>
</a>

<?php include 'includes/footer.php'; ?>