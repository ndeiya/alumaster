<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "Setting up pages and navigation tables...\n";
    
    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/add_pages_navigation.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^(USE|--)/i', $statement)) {
            $conn->exec($statement);
            echo "Executed: " . substr($statement, 0, 50) . "...\n";
        }
    }
    
    echo "\nPages and navigation setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>