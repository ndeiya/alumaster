<?php
/**
 * AluMaster Aluminum System - Configuration File
 * Contains database credentials, site settings, and security constants
 */

// Configuration file for AluMaster Aluminum System

// Environment Configuration
define('ENVIRONMENT', 'development'); // Change to 'production' for live site
define('DEBUG_MODE', ENVIRONMENT === 'development');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'alumaster');
define('DB_USER', 'root'); // Change for production
define('DB_PASS', ''); // Change for production
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_NAME', 'AluMaster Aluminum System');
define('SITE_TAGLINE', 'Where Quality Meets Affordability');
define('SITE_URL', 'http://localhost:8000'); // Change for production
define('SITE_EMAIL', 'contact@alumastergh.com');

// Contact Information
define('CONTACT_PHONE_PRIMARY', '+233-541-737-575');
define('CONTACT_PHONE_SECONDARY', '+233-502-777-703');
define('CONTACT_EMAIL', 'contact@alumastergh.com');
define('CONTACT_ADDRESS', '16 Palace Street, Madina-Accra, Ghana');

// Social Media
define('SOCIAL_FACEBOOK', 'alumastergh');
define('SOCIAL_INSTAGRAM', 'alumaster75');
define('SOCIAL_TWITTER', 'alumaster75');
define('SOCIAL_TIKTOK', 'alumaster75');

// Security Configuration
define('SESSION_TIMEOUT', 7200); // 2 hours in seconds
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes in seconds

// File Upload Configuration
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('UPLOAD_PATH', 'uploads/');

// Email Configuration (for contact forms)
define('SMTP_HOST', ''); // Configure for production
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_ENCRYPTION', 'tls');

// SEO Configuration
define('DEFAULT_META_DESCRIPTION', 'Professional aluminum and glass solutions in Ghana. Alucobond cladding, curtain walls, spider glass, and more. Quality meets affordability.');
define('DEFAULT_META_KEYWORDS', 'aluminum, glass, cladding, curtain wall, spider glass, Ghana, construction, architecture');

// Timezone
date_default_timezone_set('Africa/Accra');

// Error Reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Session Configuration (only set ini settings, don't start session here)
if (session_status() === PHP_SESSION_NONE) {
    // Only set session ini settings if no session is active
    // Session will be started in individual files as needed
    if (!headers_sent()) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
        ini_set('session.use_strict_mode', 1);
    }
}

// Security Headers
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    if (isset($_SERVER['HTTPS'])) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
?>