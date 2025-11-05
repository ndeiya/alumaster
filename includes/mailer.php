<?php
/**
 * AluMaster Aluminum System - Email Service
 * Professional email handling using PHPMailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;
    private $config;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->config = $this->loadEmailConfig();
        $this->setupMailer();
    }
    
    /**
     * Load email configuration from config file or environment
     */
    private function loadEmailConfig() {
        return [
            'smtp_host' => $_ENV['SMTP_HOST'] ?? 'mail.' . str_replace(['http://', 'https://', 'www.'], '', SITE_URL),
            'smtp_port' => $_ENV['SMTP_PORT'] ?? 587,
            'smtp_username' => $_ENV['SMTP_USERNAME'] ?? SITE_EMAIL,
            'smtp_password' => $_ENV['SMTP_PASSWORD'] ?? '',
            'smtp_encryption' => $_ENV['SMTP_ENCRYPTION'] ?? 'tls',
            'from_email' => $_ENV['FROM_EMAIL'] ?? SITE_EMAIL,
            'from_name' => $_ENV['FROM_NAME'] ?? SITE_NAME,
            'reply_to' => $_ENV['REPLY_TO'] ?? SITE_EMAIL,
            'admin_email' => $_ENV['ADMIN_EMAIL'] ?? SITE_EMAIL
        ];
    }
    
    /**
     * Setup PHPMailer configuration
     */
    private function setupMailer() {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['smtp_host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['smtp_username'];
            $this->mailer->Password = $this->config['smtp_password'];
            $this->mailer->SMTPSecure = $this->config['smtp_encryption'];
            $this->mailer->Port = $this->config['smtp_port'];
            
            // Default sender
            $this->mailer->setFrom($this->config['from_email'], $this->config['from_name']);
            $this->mailer->addReplyTo($this->config['reply_to'], $this->config['from_name']);
            
            // Content settings
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8';
            
        } catch (Exception $e) {
            error_log("Mailer setup error: " . $e->getMessage());
        }
    }
    
    /**
     * Send contact form inquiry notification
     */
    public function sendContactInquiry($inquiryData) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($this->config['admin_email']);
            
            $this->mailer->Subject = 'New Contact Inquiry - ' . $inquiryData['name'];
            
            $htmlBody = $this->getContactInquiryTemplate($inquiryData);
            $textBody = $this->getContactInquiryTextTemplate($inquiryData);
            
            $this->mailer->Body = $htmlBody;
            $this->mailer->AltBody = $textBody;
            
            $result = $this->mailer->send();
            
            if ($result) {
                $this->logEmail('contact_inquiry', $inquiryData['email'], 'sent');
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->logEmail('contact_inquiry', $inquiryData['email'] ?? 'unknown', 'failed', $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send auto-reply to customer
     */
    public function sendContactAutoReply($inquiryData) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($inquiryData['email'], $inquiryData['name']);
            
            $this->mailer->Subject = 'Thank you for contacting AluMaster - We\'ll be in touch soon!';
            
            $htmlBody = $this->getAutoReplyTemplate($inquiryData);
            $textBody = $this->getAutoReplyTextTemplate($inquiryData);
            
            $this->mailer->Body = $htmlBody;
            $this->mailer->AltBody = $textBody;
            
            $result = $this->mailer->send();
            
            if ($result) {
                $this->logEmail('auto_reply', $inquiryData['email'], 'sent');
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->logEmail('auto_reply', $inquiryData['email'], 'failed', $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send custom email
     */
    public function sendCustomEmail($to, $subject, $htmlBody, $textBody = '', $attachments = []) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            // Add recipients
            if (is_array($to)) {
                foreach ($to as $email => $name) {
                    if (is_numeric($email)) {
                        $this->mailer->addAddress($name);
                    } else {
                        $this->mailer->addAddress($email, $name);
                    }
                }
            } else {
                $this->mailer->addAddress($to);
            }
            
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $htmlBody;
            $this->mailer->AltBody = $textBody ?: strip_tags($htmlBody);
            
            // Add attachments
            foreach ($attachments as $attachment) {
                if (is_array($attachment)) {
                    $this->mailer->addAttachment($attachment['path'], $attachment['name'] ?? '');
                } else {
                    $this->mailer->addAttachment($attachment);
                }
            }
            
            $result = $this->mailer->send();
            
            if ($result) {
                $recipient = is_array($to) ? implode(', ', array_keys($to)) : $to;
                $this->logEmail('custom', $recipient, 'sent');
            }
            
            return $result;
            
        } catch (Exception $e) {
            $recipient = is_array($to) ? implode(', ', array_keys($to)) : $to;
            $this->logEmail('custom', $recipient, 'failed', $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get contact inquiry HTML template
     */
    private function getContactInquiryTemplate($data) {
        $template = file_get_contents(__DIR__ . '/../templates/email-contact-inquiry.html');
        
        $replacements = [
            '{{SITE_NAME}}' => SITE_NAME,
            '{{CUSTOMER_NAME}}' => htmlspecialchars($data['name']),
            '{{CUSTOMER_EMAIL}}' => htmlspecialchars($data['email']),
            '{{CUSTOMER_PHONE}}' => htmlspecialchars($data['phone']),
            '{{SERVICE_INTEREST}}' => htmlspecialchars($data['service_interest'] ?: 'Not specified'),
            '{{MESSAGE}}' => nl2br(htmlspecialchars($data['message'])),
            '{{INQUIRY_DATE}}' => date('F j, Y \a\t g:i A'),
            '{{SITE_URL}}' => SITE_URL
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
    
    /**
     * Get contact inquiry text template
     */
    private function getContactInquiryTextTemplate($data) {
        return "New Contact Inquiry - " . SITE_NAME . "\n\n" .
               "Customer Details:\n" .
               "Name: " . $data['name'] . "\n" .
               "Email: " . $data['email'] . "\n" .
               "Phone: " . $data['phone'] . "\n" .
               "Service Interest: " . ($data['service_interest'] ?: 'Not specified') . "\n\n" .
               "Message:\n" . $data['message'] . "\n\n" .
               "Inquiry Date: " . date('F j, Y \a\t g:i A') . "\n" .
               "Website: " . SITE_URL;
    }
    
    /**
     * Get auto-reply HTML template
     */
    private function getAutoReplyTemplate($data) {
        $template = file_get_contents(__DIR__ . '/../templates/email-auto-reply.html');
        
        $replacements = [
            '{{SITE_NAME}}' => SITE_NAME,
            '{{CUSTOMER_NAME}}' => htmlspecialchars($data['name']),
            '{{SITE_URL}}' => SITE_URL,
            '{{CONTACT_PHONE}}' => CONTACT_PHONE_PRIMARY,
            '{{CONTACT_EMAIL}}' => CONTACT_EMAIL,
            '{{CONTACT_ADDRESS}}' => CONTACT_ADDRESS
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
    
    /**
     * Get auto-reply text template
     */
    private function getAutoReplyTextTemplate($data) {
        return "Dear " . $data['name'] . ",\n\n" .
               "Thank you for contacting " . SITE_NAME . "!\n\n" .
               "We have received your inquiry and will get back to you within 24 hours. " .
               "Our team is excited to help you with your aluminum and glass solution needs.\n\n" .
               "If you have any urgent questions, please don't hesitate to call us at " . CONTACT_PHONE_PRIMARY . ".\n\n" .
               "Best regards,\n" .
               "The AluMaster Team\n\n" .
               "Contact Information:\n" .
               "Phone: " . CONTACT_PHONE_PRIMARY . "\n" .
               "Email: " . CONTACT_EMAIL . "\n" .
               "Address: " . CONTACT_ADDRESS . "\n" .
               "Website: " . SITE_URL;
    }
    
    /**
     * Log email activity
     */
    private function logEmail($type, $recipient, $status, $error = '') {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'recipient' => $recipient,
            'status' => $status,
            'error' => $error
        ];
        
        $logFile = __DIR__ . '/../logs/email.log';
        $logLine = json_encode($logEntry) . "\n";
        
        // Ensure logs directory exists
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Test email configuration
     */
    public function testConnection() {
        try {
            $this->mailer->smtpConnect();
            $this->mailer->smtpClose();
            return true;
        } catch (Exception $e) {
            error_log("SMTP connection test failed: " . $e->getMessage());
            return false;
        }
    }
}