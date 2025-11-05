<?php
/**
 * Test Frontend Display
 * Check if success/error messages display correctly
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simulate different states
$test_mode = $_GET['mode'] ?? 'success';

$form_success = false;
$form_errors = [];

switch ($test_mode) {
    case 'success':
        $form_success = true;
        break;
    case 'error':
        $form_errors = ['This is a test error message', 'Another test error'];
        break;
    case 'validation':
        $form_errors = ['First name is required', 'Email is invalid'];
        break;
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frontend Display Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .alert { padding: 15px; margin: 15px 0; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-icon { display: inline-block; margin-right: 10px; }
        .test-links { margin: 20px 0; }
        .test-links a { margin-right: 15px; padding: 8px 12px; background: #007cba; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Frontend Display Test</h1>
    
    <div class="test-links">
        <a href="?mode=success">Test Success</a>
        <a href="?mode=error">Test Error</a>
        <a href="?mode=validation">Test Validation</a>
        <a href="contact.php">Back to Contact</a>
    </div>
    
    <p><strong>Current Mode:</strong> <?php echo htmlspecialchars($test_mode); ?></p>
    <p><strong>Form Success:</strong> <?php echo $form_success ? 'TRUE' : 'FALSE'; ?></p>
    <p><strong>Form Errors:</strong> <?php echo empty($form_errors) ? 'None' : count($form_errors) . ' errors'; ?></p>

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

    <h3>Debug Information</h3>
    <pre>
$form_success = <?php var_export($form_success); ?>

$form_errors = <?php var_export($form_errors); ?>

$_SESSION['csrf_token'] = <?php echo substr($_SESSION['csrf_token'] ?? 'Not set', 0, 16); ?>...
    </pre>
</body>
</html>