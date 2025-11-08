<?php
/**
 * AluMaster Aluminum System - Production Configuration File
 * IMPORTANT: Rename this to config.php when deploying to production
 * Contains database credentials, site settings, and security constants
 */

// Environment Configuration
define('ENVIRONMENT', 'production'); // PRODUCTION MODE
define('DEBUG_MODE', false); // NEVER enable debug in production

// Database Configuration - UPDATE THESE FOR PRODUCTION
define('DB_HOST', 'localhost'); // Update if different
define('DB_NAME', 'alumaster'); // Update to production database name
define('DB_USER', 'alumaster_user'); // UPDATE: Use dedicated database user
define('DB_PASS', 'CHANGE_THIS_PASSWORD'); // UPDATE: Use strong password
define('DB_CHARSET', 'utf8mb4');

// Site Configuration - UPDATE THESE FOR PRODUCTION
define('SITE_NAME', 'AluMaster Aluminum System');
define('SITE_TAGLINE', 'Where Quality Meets Affordability');
define('SITE_URL', 'https://www.alumastergh.com'); // UPDATE: Production URL with HTTPS
define('SITE_EMAIL', 'contact@alumastergh.com'); // Production email

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

// Email Configuration (loaded from .env file)
// See .env.example for configuration details
define('SMTP_HOST', ''); // Configured via .env
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_ENCRYPTION', 'tls');

// SEO Configuration
define('DEFAULT_META_DESCRIPTION', 'Professional aluminum and glass solutions in Ghana. Alucobond cladding, curtain walls, spider glass, and more. Quality meets affordability.');
define('DEFAULT_META_KEYWORDS', 'aluminum, glass, cladding, curtain wall, spider glass, Ghana, construction, architecture');

// Timezone
date_default_timezone_set('Africa/Accra');

// Error Reporting - PRODUCTION SETTINGS
error_reporting(E_ALL);
ini_set('display_errors', 0); // NEVER display errors in production
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log'); // Log to file

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    if (!headers_sent()) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1); // HTTPS only
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
    }
}

// Security Headers
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
}

// Content Security Policy (optional but recommended)
// Uncomment and adjust as needed
// header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; frame-src https://www.google.com;");
?>
