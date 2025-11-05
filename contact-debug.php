<?php
/**
 * Contact Form Debug Version
 * Simplified version to test form submission
 */

// Start session first
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$form_success = false;
$form_errors = [];
$debug_info = [];

// Debug: Check if form was submitted
if ($_POST) {
    $debug_info[] = "✅ Form submitted via POST";
    $debug_info[] = "POST data: " . json_encode($_POST);
} else {
    $debug_info[] = "ℹ️ No POST data received";
}

// Handle form submission
if ($_POST && isset($_POST['submit_inquiry'])) {
    $debug_info[] = "✅ Submit button clicked";
    
    // Generate CSRF token if not exists
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $debug_info[] = "🔑 Generated new CSRF token";
    }
    
    // Skip CSRF validation for debugging
    $debug_info[] = "⚠️ Skipping CSRF validation for debug";
    
    // Validate form data
    $first_name = sanitize_input($_POST['first_name'] ?? '');
    $last_name = sanitize_input($_POST['last_name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $service_interest = sanitize_input($_POST['service_interest'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');
    
    $debug_info[] = "📝 Sanitized data: Name=$first_name $last_name, Email=$email";
    
    // Validation
    if (empty($first_name)) $form_errors[] = "First name is required.";
    if (empty($last_name)) $form_errors[] = "Last name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_errors[] = "Valid email is required.";
    }
    if (empty($phone)) $form_errors[] = "Phone number is required.";
    if (empty($message)) $form_errors[] = "Message is required.";
    
    if (!empty($form_errors)) {
        $debug_info[] = "❌ Validation errors: " . implode(', ', $form_errors);
    } else {
        $debug_info[] = "✅ Validation passed";
    }
    
    // If no errors, save to database
    if (empty($form_errors)) {
        try {
            $debug_info[] = "💾 Attempting database save...";
            
            $db = new Database();
            $conn = $db->getConnection();
            
            if (!$conn) {
                throw new Exception("Database connection failed");
            }
            
            $debug_info[] = "✅ Database connected";
            
            $full_name = $first_name . ' ' . $last_name;
            $stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, service_interest, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $result = $stmt->execute([$full_name, $email, $phone, $service_interest, $message]);
            
            if ($result) {
                $inquiry_id = $conn->lastInsertId();
                $debug_info[] = "✅ Database insert successful (ID: $inquiry_id)";
                $form_success = true;
            } else {
                throw new Exception("Database insert failed");
            }
            
        } catch (Exception $e) {
            $form_errors[] = "Database error: " . $e->getMessage();
            $debug_info[] = "❌ Database error: " . $e->getMessage();
        }
    }
}

// Generate CSRF token for form
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Debug - AluMaster</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .debug { background: #f0f0f0; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .debug h3 { margin-top: 0; }
        .debug ul { margin: 0; padding-left: 20px; }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; 
        }
        .form-row { display: flex; gap: 15px; }
        .form-row .form-group { flex: 1; }
        .btn { background: #007cba; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #005a87; }
        .alert { padding: 15px; margin: 15px 0; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <h1>Contact Form Debug Test</h1>
    
    <div class="debug">
        <h3>🔍 Debug Information</h3>
        <ul>
            <?php foreach ($debug_info as $info): ?>
                <li><?php echo htmlspecialchars($info); ?></li>
            <?php endforeach; ?>
        </ul>
        
        <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
        <p><strong>CSRF Token:</strong> <?php echo substr($_SESSION['csrf_token'] ?? 'Not set', 0, 16); ?>...</p>
        <p><strong>Current Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>

    <?php if ($form_success): ?>
        <div class="alert alert-success">
            <h4>✅ Success!</h4>
            <p>Your message has been submitted successfully and saved to the database.</p>
        </div>
    <?php endif; ?>

    <?php if (!empty($form_errors)): ?>
        <div class="alert alert-error">
            <h4>❌ Errors Found:</h4>
            <ul>
                <?php foreach ($form_errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!$form_success): ?>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" required 
                           value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" required 
                           value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" required 
                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="service_interest">Service Interest</label>
                <select id="service_interest" name="service_interest">
                    <option value="">Select a service</option>
                    <option value="Alucobond Cladding" <?php echo ($_POST['service_interest'] ?? '') === 'Alucobond Cladding' ? 'selected' : ''; ?>>Alucobond Cladding</option>
                    <option value="Curtain Wall" <?php echo ($_POST['service_interest'] ?? '') === 'Curtain Wall' ? 'selected' : ''; ?>>Curtain Wall</option>
                    <option value="Spider Glass" <?php echo ($_POST['service_interest'] ?? '') === 'Spider Glass' ? 'selected' : ''; ?>>Spider Glass</option>
                    <option value="Other" <?php echo ($_POST['service_interest'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="message">Message *</label>
                <textarea id="message" name="message" rows="6" required 
                          placeholder="Tell us about your project..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <button type="submit" name="submit_inquiry" class="btn">Send Message</button>
            </div>
        </form>
    <?php endif; ?>

    <div class="debug">
        <h3>📊 Recent Inquiries</h3>
        <?php
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            if ($conn) {
                $stmt = $conn->prepare("SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 5");
                $stmt->execute();
                $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if ($inquiries) {
                    echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
                    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Created</th></tr>";
                    foreach ($inquiries as $inquiry) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($inquiry['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($inquiry['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($inquiry['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($inquiry['created_at']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No inquiries found.</p>";
                }
            } else {
                echo "<p>Database connection failed.</p>";
            }
        } catch (Exception $e) {
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>
</body>
</html>