<?php
/**
 * Anti-Spam Utilities
 * Provides additional spam protection functions for forms
 */

class AntiSpam {
    
    private static $spam_log_file = __DIR__ . '/../logs/spam.log';
    private static $blocked_ips_file = __DIR__ . '/../logs/blocked_ips.txt';
    
    /**
     * Check if IP is blocked
     */
    public static function isIPBlocked($ip) {
        if (!file_exists(self::$blocked_ips_file)) {
            return false;
        }
        
        $blocked_ips = file(self::$blocked_ips_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return in_array($ip, $blocked_ips);
    }
    
    /**
     * Block an IP address
     */
    public static function blockIP($ip, $reason = '') {
        self::ensureLogDirectory();
        
        // Add to blocked list
        $entry = $ip . ($reason ? " # {$reason}" : '') . "\n";
        file_put_contents(self::$blocked_ips_file, $entry, FILE_APPEND | LOCK_EX);
        
        // Log the blocking
        self::logSpamAttempt($ip, 'IP_BLOCKED', $reason);
    }
    
    /**
     * Log spam attempt
     */
    public static function logSpamAttempt($ip, $type, $details = '') {
        self::ensureLogDirectory();
        
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $ip,
            'type' => $type,
            'details' => $details,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ];
        
        $log_line = json_encode($log_entry) . "\n";
        file_put_contents(self::$spam_log_file, $log_line, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Get submission count from IP in time window
     */
    public static function getSubmissionCount($ip, $minutes = 60) {
        if (!file_exists(self::$spam_log_file)) {
            return 0;
        }
        
        $cutoff_time = time() - ($minutes * 60);
        $count = 0;
        
        $handle = fopen(self::$spam_log_file, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $entry = json_decode($line, true);
                if ($entry && $entry['ip'] === $ip) {
                    $entry_time = strtotime($entry['timestamp']);
                    if ($entry_time >= $cutoff_time) {
                        $count++;
                    }
                }
            }
            fclose($handle);
        }
        
        return $count;
    }
    
    /**
     * Check if message contains spam patterns
     */
    public static function containsSpamPatterns($text) {
        $spam_patterns = [
            '/\\b(viagra|cialis|levitra|pharmacy|pills|medication)\\b/i',
            '/\\b(casino|poker|gambling|lottery|jackpot|slots)\\b/i',
            '/\\b(forex|bitcoin|cryptocurrency|trading|investment|profit)\\b/i',
            '/\\b(click here|click now|buy now|order now|limited time|act now)\\b/i',
            '/\\b(free money|make money|earn money|work from home)\\b/i',
            '/\\b(weight loss|diet pills|muscle gain|testosterone)\\b/i',
            '/\\b(loan|credit|mortgage|refinance|bankruptcy)\\b/i',
            '/\\b(seo service|backlinks|rank higher|google ranking)\\b/i',
            '/(http:\\/\\/|https:\\/\\/|www\\.)(.*?)(http:\\/\\/|https:\\/\\/|www\\.)/', // Multiple URLs
            '/[A-Z]{15,}/', // Excessive caps (15+ consecutive)
            '/\\b([a-z0-9]{40,})\\b/i', // Very long random strings
            '/([!@#$%^&*]){5,}/', // Excessive special characters
        ];
        
        foreach ($spam_patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if email is likely fake/disposable
     */
    public static function isSuspiciousEmail($email) {
        // Common fake/test domains
        $fake_domains = [
            'test.com', 'example.com', 'fake.com', 'spam.com',
            'temp.com', 'tempmail.com', 'throwaway.com',
            'mailinator.com', 'guerrillamail.com', '10minutemail.com',
            'sharklasers.com', 'yopmail.com'
        ];
        
        $email_lower = strtolower($email);
        
        foreach ($fake_domains as $domain) {
            if (stripos($email_lower, '@' . $domain) !== false) {
                return true;
            }
        }
        
        // Check for obviously fake patterns
        if (preg_match('/^(test|fake|spam|noreply|abuse)@/i', $email_lower)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Count URLs in text
     */
    public static function countURLs($text) {
        return preg_match_all('/(http:\\/\\/|https:\\/\\/|www\\.)/i', $text);
    }
    
    /**
     * Ensure logs directory exists
     */
    private static function ensureLogDirectory() {
        $log_dir = dirname(self::$spam_log_file);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
    }
    
    /**
     * Get spam statistics
     */
    public static function getSpamStats($days = 7) {
        if (!file_exists(self::$spam_log_file)) {
            return [
                'total_attempts' => 0,
                'unique_ips' => 0,
                'blocked_ips' => 0,
                'by_type' => []
            ];
        }
        
        $cutoff_time = time() - ($days * 24 * 60 * 60);
        $stats = [
            'total_attempts' => 0,
            'unique_ips' => [],
            'by_type' => []
        ];
        
        $handle = fopen(self::$spam_log_file, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $entry = json_decode($line, true);
                if ($entry) {
                    $entry_time = strtotime($entry['timestamp']);
                    if ($entry_time >= $cutoff_time) {
                        $stats['total_attempts']++;
                        $stats['unique_ips'][$entry['ip']] = true;
                        
                        $type = $entry['type'] ?? 'unknown';
                        if (!isset($stats['by_type'][$type])) {
                            $stats['by_type'][$type] = 0;
                        }
                        $stats['by_type'][$type]++;
                    }
                }
            }
            fclose($handle);
        }
        
        $stats['unique_ips'] = count($stats['unique_ips']);
        
        // Count blocked IPs
        if (file_exists(self::$blocked_ips_file)) {
            $blocked_ips = file(self::$blocked_ips_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $stats['blocked_ips'] = count($blocked_ips);
        } else {
            $stats['blocked_ips'] = 0;
        }
        
        return $stats;
    }
    
    /**
     * Clean old log entries
     */
    public static function cleanOldLogs($days = 30) {
        if (!file_exists(self::$spam_log_file)) {
            return 0;
        }
        
        $cutoff_time = time() - ($days * 24 * 60 * 60);
        $temp_file = self::$spam_log_file . '.tmp';
        $removed_count = 0;
        
        $read_handle = fopen(self::$spam_log_file, 'r');
        $write_handle = fopen($temp_file, 'w');
        
        if ($read_handle && $write_handle) {
            while (($line = fgets($read_handle)) !== false) {
                $entry = json_decode($line, true);
                if ($entry) {
                    $entry_time = strtotime($entry['timestamp']);
                    if ($entry_time >= $cutoff_time) {
                        fwrite($write_handle, $line);
                    } else {
                        $removed_count++;
                    }
                }
            }
            
            fclose($read_handle);
            fclose($write_handle);
            
            // Replace old file with cleaned version
            rename($temp_file, self::$spam_log_file);
        }
        
        return $removed_count;
    }
}
