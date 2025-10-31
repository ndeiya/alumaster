<?php
/**
 * AluMaster Aluminum System - Helper Functions
 * Contains reusable functions for security, validation, and utilities
 */

// Helper functions for AluMaster Aluminum System

/**
 * Security Functions
 */

// Sanitize input data
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Validate email address
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate phone number (basic validation)
function validate_phone($phone) {
    $phone = preg_replace('/[^0-9+\-\(\)\s]/', '', $phone);
    return strlen($phone) >= 10;
}

// Generate CSRF token
function generate_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

// Validate CSRF token
function validate_csrf_token($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Utility Functions
 */

// Get current URL
function get_current_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    return $protocol . '://' . $host . $uri;
}

// Format phone number for display
function format_phone($phone) {
    // Remove all non-numeric characters except +
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    // Format Ghana phone numbers
    if (strpos($phone, '+233') === 0) {
        $number = substr($phone, 4);
        if (strlen($number) === 9) {
            return '+233-' . substr($number, 0, 3) . '-' . substr($number, 3, 3) . '-' . substr($number, 6);
        }
    }
    
    return $phone;
}

// Time ago function
function time_ago($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    
    return floor($time/31536000) . ' years ago';
}

// Truncate text
function truncate_text($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

// Generate slug from text
function generate_slug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// Check if request is AJAX
function is_ajax_request() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Redirect function
function redirect($url, $permanent = false) {
    $status_code = $permanent ? 301 : 302;
    header("Location: $url", true, $status_code);
    exit;
}

// Flash message functions
function set_flash_message($message, $type = 'info') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['flash_messages'][] = [
        'message' => $message,
        'type' => $type
    ];
}

function get_flash_messages() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

// Simple logging function
function log_error($message, $context = []) {
    $log_message = date('Y-m-d H:i:s') . ' - ' . $message;
    if (!empty($context)) {
        $log_message .= ' - Context: ' . json_encode($context);
    }
    error_log($log_message);
}

// Get site setting (placeholder function)
function get_setting($key, $default = '') {
    // This would normally fetch from database
    // For now, return defaults
    $settings = [
        'site_title' => 'AluMaster Aluminum System',
        'site_tagline' => 'Where Quality Meets Affordability',
        'site_description' => 'Professional aluminum and glass solutions in Ghana',
        'contact_phone' => '+233-541-737-575',
        'contact_email' => 'alumaster75@gmail.com',
        'contact_address' => '16 Palace Street, Madina-Accra, Ghana'
    ];
    
    return $settings[$key] ?? $default;
}
?>