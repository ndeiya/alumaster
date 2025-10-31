<?php
// Password Hash Generator
// Run this script to generate a secure password hash

$password = 'admin123'; // Change this to your desired password
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n";
echo "\n";
echo "SQL to insert admin user:\n";
echo "INSERT INTO admins (username, email, password, role, first_name, last_name, is_active) VALUES ('admin', 'admin@alumastergh.com', '$hash', 'super_admin', 'System', 'Administrator', 1);\n";
?>