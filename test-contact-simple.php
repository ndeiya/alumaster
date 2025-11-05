<?php
/**
 * Simple Contact Form Test
 * Minimal test to isolate the contact form issue
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Contact Form Debug Test</h1>";

// Test 1: Check if form was submitted
if ($_POST) {
    echo "<h2>✅ Form was submitted</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
} else {
    echo "<h2>ℹ️ No form submission detected</h2>";
}

// Test 2: Load required files
echo "<h2>Loading Required Files</h2>";
try {
    require_once 'includes/config.php';
    echo "✅ Config loaded<br>";
    
    require_once 'includes/database.php';
    echo "✅ Database class loaded<br>";
    
    require_once 'includes/functions.php';
    echo "✅ Functions loaded<br>";
    
} catch (Exception $e) {
    echo "❌ Error loading files: " . $e->getMessage() . "<br>";
}

// Test 3: Database connection
echo "<h2>Testing Database</h2>";
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if ($conn) {
        echo "✅ Database connected<br>";
        
        // Check if inquiries table exists
        $stmt = $conn->prepare("SHOW TABLES LIKE 'inquiries'");
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo "✅ Inquiries table exists<br>";
            
            // Test insert
            if ($_POST && isset($_POST['test_insert'])) {
                $stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, message, created_at) VALUES (?, ?, ?, ?, NOW())");
                $result = $stmt->execute(['Test User', 'test@example.com', '+233-123-456-789', 'Test message from debug script']);
                
                if ($result) {
                    echo "✅ Test record inserted successfully (ID: " . $conn->lastInsertId() . ")<br>";
                } else {
                    echo "❌ Failed to insert test record<br>";
                }
            }
        } else {
            echo "❌ Inquiries table does not exist<br>";
            echo "Run this SQL to create it:<br>";
            echo "<code>CREATE TABLE inquiries (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100), email VARCHAR(100), phone VARCHAR(20), message TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);</code><br>";
        }
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 4: Session
echo "<h2>Testing Session</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "✅ Session started<br>";
echo "Session ID: " . session_id() . "<br>";

// Test form processing
if ($_POST && isset($_POST['submit_test'])) {
    echo "<h2>Processing Test Form</h2>";
    
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');
    
    echo "Sanitized data:<br>";
    echo "Name: " . htmlspecialchars($name) . "<br>";
    echo "Email: " . htmlspecialchars($email) . "<br>";
    echo "Message: " . htmlspecialchars($message) . "<br>";
    
    if (!empty($name) && !empty($email) && !empty($message)) {
        try {
            $stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            $result = $stmt->execute([$name, $email, '', $message]);
            
            if ($result) {
                echo "<div style='color: green; font-weight: bold;'>✅ SUCCESS: Form data saved to database!</div>";
            } else {
                echo "<div style='color: red; font-weight: bold;'>❌ FAILED: Could not save to database</div>";
            }
        } catch (Exception $e) {
            echo "<div style='color: red; font-weight: bold;'>❌ ERROR: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div style='color: orange; font-weight: bold;'>⚠️ WARNING: Missing required fields</div>";
    }
}
?>

<h2>Test Forms</h2>

<h3>Database Insert Test</h3>
<form method="POST">
    <button type="submit" name="test_insert" style="padding: 10px; background: blue; color: white; border: none;">Insert Test Record</button>
</form>

<h3>Contact Form Test</h3>
<form method="POST" style="max-width: 400px;">
    <div style="margin: 10px 0;">
        <label>Name:</label><br>
        <input type="text" name="name" required style="width: 100%; padding: 5px;">
    </div>
    
    <div style="margin: 10px 0;">
        <label>Email:</label><br>
        <input type="email" name="email" required style="width: 100%; padding: 5px;">
    </div>
    
    <div style="margin: 10px 0;">
        <label>Message:</label><br>
        <textarea name="message" required style="width: 100%; padding: 5px; height: 100px;"></textarea>
    </div>
    
    <button type="submit" name="submit_test" style="padding: 10px; background: green; color: white; border: none;">Submit Test Form</button>
</form>

<h3>Check Recent Inquiries</h3>
<?php
if (isset($conn)) {
    try {
        $stmt = $conn->prepare("SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 5");
        $stmt->execute();
        $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($inquiries) {
            echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Message</th><th>Created</th></tr>";
            foreach ($inquiries as $inquiry) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($inquiry['id']) . "</td>";
                echo "<td>" . htmlspecialchars($inquiry['name']) . "</td>";
                echo "<td>" . htmlspecialchars($inquiry['email']) . "</td>";
                echo "<td>" . htmlspecialchars(substr($inquiry['message'], 0, 50)) . "...</td>";
                echo "<td>" . htmlspecialchars($inquiry['created_at']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No inquiries found in database.</p>";
        }
    } catch (Exception $e) {
        echo "<p>Error loading inquiries: " . $e->getMessage() . "</p>";
    }
}
?>