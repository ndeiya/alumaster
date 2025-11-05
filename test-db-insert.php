<?php
/**
 * Test Database Insert
 */

require_once 'includes/config.php';
require_once 'includes/database.php';

echo "Testing database insert...\n";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if ($conn) {
        echo "✅ Database connected\n";
        
        // Test insert
        $stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, service_interest, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $result = $stmt->execute(['Test User', 'test@example.com', '+233-123-456-789', 'Test Service', 'Test message']);
        
        if ($result) {
            echo "✅ Insert successful, ID: " . $conn->lastInsertId() . "\n";
        } else {
            echo "❌ Insert failed\n";
            print_r($stmt->errorInfo());
        }
        
        // Check recent records
        $stmt = $conn->prepare("SELECT COUNT(*) FROM inquiries");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        echo "Total inquiries: $count\n";
        
        // Show last 3 records
        $stmt = $conn->prepare("SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 3");
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nLast 3 inquiries:\n";
        foreach ($records as $record) {
            echo "- ID: {$record['id']}, Name: {$record['name']}, Email: {$record['email']}, Created: {$record['created_at']}\n";
        }
        
    } else {
        echo "❌ Database connection failed\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>