<?php
/**
 * Security Audit Script
 * Run this to check for common security issues before deployment
 * 
 * Usage: php security-audit.php
 */

echo "=== AluMaster Security Audit ===\n\n";

$issues = [];
$warnings = [];
$passed = [];

// Check 1: Environment setting
echo "Checking environment configuration...\n";
require_once 'includes/config.php';

if (ENVIRONMENT === 'production') {
    $passed[] = "Environment set to production";
} else {
    $issues[] = "CRITICAL: Environment is set to '" . ENVIRONMENT . "' instead of 'production'";
}

if (!DEBUG_MODE) {
    $passed[] = "Debug mode is disabled";
} else {
    $issues[] = "CRITICAL: Debug mode is enabled in production";
}

// Check 2: Database credentials
echo "Checking database configuration...\n";

if (DB_USER === 'root') {
    $issues[] = "CRITICAL: Using 'root' database user in production";
} else {
    $passed[] = "Using dedicated database user";
}

if (empty(DB_PASS)) {
    $issues[] = "CRITICAL: Database password is empty";
} else {
    $passed[] = "Database password is set";
}

// Check 3: HTTPS configuration
echo "Checking HTTPS configuration...\n";

if (strpos(SITE_URL, 'https://') === 0) {
    $passed[] = "Site URL uses HTTPS";
} else {
    $issues[] = "CRITICAL: Site URL does not use HTTPS";
}

// Check 4: File permissions
echo "Checking file permissions...\n";

if (file_exists('.env')) {
    $perms = substr(sprintf('%o', fileperms('.env')), -4);
    if ($perms === '0600') {
        $passed[] = ".env file has secure permissions (600)";
    } else {
        $warnings[] = ".env file permissions are $perms (should be 600)";
    }
} else {
    $warnings[] = ".env file not found";
}

// Check 5: Test files
echo "Checking for test files...\n";

$test_files = glob('test-*.php');
$debug_files = glob('debug-*.php');
$all_test_files = array_merge($test_files, $debug_files);

if (empty($all_test_files)) {
    $passed[] = "No test/debug files found in root";
} else {
    $issues[] = "CRITICAL: Found " . count($all_test_files) . " test/debug files that should be removed";
    foreach ($all_test_files as $file) {
        echo "  - $file\n";
    }
}

// Check 6: .htaccess protection
echo "Checking .htaccess protection...\n";

if (file_exists('.htaccess')) {
    $htaccess = file_get_contents('.htaccess');
    
    if (strpos($htaccess, 'RewriteCond %{HTTPS} off') !== false) {
        if (strpos($htaccess, '# RewriteCond %{HTTPS} off') !== false) {
            $warnings[] = "HTTPS redirect is commented out in .htaccess";
        } else {
            $passed[] = "HTTPS redirect is enabled";
        }
    } else {
        $warnings[] = "HTTPS redirect not found in .htaccess";
    }
    
    if (strpos($htaccess, '<Files ".env">') !== false) {
        $passed[] = ".env file is protected in .htaccess";
    } else {
        $warnings[] = ".env file protection not found in .htaccess";
    }
} else {
    $issues[] = "CRITICAL: .htaccess file not found";
}

// Check 7: Sensitive directories
echo "Checking sensitive directory protection...\n";

$sensitive_dirs = ['database', 'logs', 'vendor'];
foreach ($sensitive_dirs as $dir) {
    if (is_dir($dir)) {
        if (file_exists("$dir/.htaccess")) {
            $passed[] = "$dir/ directory has .htaccess protection";
        } else {
            $warnings[] = "$dir/ directory should have .htaccess protection";
        }
    }
}

// Check 8: Error reporting
echo "Checking error reporting settings...\n";

if (ini_get('display_errors') == 0) {
    $passed[] = "Error display is disabled";
} else {
    $issues[] = "CRITICAL: Error display is enabled (should be disabled in production)";
}

if (ini_get('log_errors') == 1) {
    $passed[] = "Error logging is enabled";
} else {
    $warnings[] = "Error logging is disabled (should be enabled)";
}

// Check 9: Session security
echo "Checking session security...\n";

if (ini_get('session.cookie_httponly') == 1) {
    $passed[] = "Session cookies are HTTP-only";
} else {
    $warnings[] = "Session cookies should be HTTP-only";
}

if (ini_get('session.cookie_secure') == 1) {
    $passed[] = "Session cookies are secure (HTTPS only)";
} else {
    $warnings[] = "Session cookies should be secure for HTTPS";
}

// Check 10: Composer dependencies
echo "Checking Composer dependencies...\n";

if (file_exists('vendor/autoload.php')) {
    $passed[] = "Composer dependencies are installed";
} else {
    $warnings[] = "Composer dependencies not installed (run: composer install)";
}

// Check 11: Required directories
echo "Checking required directories...\n";

$required_dirs = ['uploads', 'logs'];
foreach ($required_dirs as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            $passed[] = "$dir/ directory exists and is writable";
        } else {
            $warnings[] = "$dir/ directory exists but is not writable";
        }
    } else {
        $warnings[] = "$dir/ directory does not exist";
    }
}

// Check 12: Admin security
echo "Checking admin security...\n";

if (file_exists('admin/.htaccess')) {
    $passed[] = "Admin directory has .htaccess protection";
} else {
    $warnings[] = "Admin directory should have .htaccess protection";
}

if (file_exists('admin/includes/auth-check.php')) {
    $passed[] = "Admin authentication check exists";
} else {
    $issues[] = "CRITICAL: Admin authentication check not found";
}

// Print results
echo "\n=== AUDIT RESULTS ===\n\n";

if (!empty($issues)) {
    echo "🔴 CRITICAL ISSUES (" . count($issues) . "):\n";
    foreach ($issues as $issue) {
        echo "  ✗ $issue\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "🟡 WARNINGS (" . count($warnings) . "):\n";
    foreach ($warnings as $warning) {
        echo "  ⚠ $warning\n";
    }
    echo "\n";
}

if (!empty($passed)) {
    echo "🟢 PASSED CHECKS (" . count($passed) . "):\n";
    foreach ($passed as $pass) {
        echo "  ✓ $pass\n";
    }
    echo "\n";
}

// Summary
echo "=== SUMMARY ===\n";
echo "Critical Issues: " . count($issues) . "\n";
echo "Warnings: " . count($warnings) . "\n";
echo "Passed: " . count($passed) . "\n\n";

if (empty($issues)) {
    if (empty($warnings)) {
        echo "✅ All security checks passed! Site is ready for deployment.\n";
        exit(0);
    } else {
        echo "⚠️  No critical issues, but please review warnings before deployment.\n";
        exit(0);
    }
} else {
    echo "❌ Critical issues found! Fix these before deploying to production.\n";
    echo "See PRE_DEPLOYMENT_CHECKLIST.md for detailed instructions.\n";
    exit(1);
}
?>
