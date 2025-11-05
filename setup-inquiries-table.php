<?php
/**
 * Setup Inquiries Table
 * Creates the inquiries table if it doesn't exist
 */

require_once 'includes/config.php';
require_once 'includes/database.php';

echo "Setting up inquiries table...\n";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if (!$conn) {
        die("❌ Database connection failed\n");
    }
    
    echo "✅ Database connected\n";
    
    // Check if inquiries table exists
    $stmt = $conn->prepare("SHOW TABLES LIKE 'inquiries'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Inquiries table already exists\n";
        
        // Show current structure
        $stmt = $conn->prepare("DESCRIBE inquiries");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Current table structure:\n";
        foreach ($columns as $column) {
            echo "  - {$column['Field']} ({$column['Type']})\n";
        }
        
        // Count existing records
        $stmt = $conn->prepare("SELECT COUNT(*) FROM inquiries");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        echo "Current records: $count\n";
        
    } else {
        echo "Creating inquiries table...\n";
        
        $sql = "CREATE TABLE inquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            service_interest VARCHAR(100),
            message TEXT,
            status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
            ip_address VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_status (status),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $conn->exec($sql);
        echo "✅ Inquiries table created successfully\n";
        
        // Insert a test record
        $stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, service_interest, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            'Test User',
            'test@example.com',
            '+233-123-456-789',
            'Alucobond Cladding',
            'This is a test inquiry to verify the table is working correctly.'
        ]);
        
        echo "✅ Test record inserted (ID: " . $conn->lastInsertId() . ")\n";
    }
    
    echo "\n✅ Setup complete!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>