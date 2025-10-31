<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'JavaScript Loading Test';

// Calculate base path
$script_path = $_SERVER['SCRIPT_NAME'];
$admin_pos = strpos($script_path, '/admin/');
if ($admin_pos !== false) {
    $after_admin = substr($script_path, $admin_pos + 7);
    $depth = substr_count($after_admin, '/');
    $base_path = str_repeat('../', $depth);
} else {
    $base_path = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JS Loading Test</title>
    <style>
        body { padding: 20px; font-family: Arial, sans-serif; background: #2d3748; color: white; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #065f46; }
        .error { background: #7f1d1d; }
        .info { background: #1e3a8a; }
    </style>
</head>
<body>
    <h1>JavaScript Loading Test</h1>
    
    <div id="test-results"></div>
    
    <p><strong>Calculated base path:</strong> "<?php echo $base_path; ?>"</p>
    <p><strong>Main.js path:</strong> <?php echo $base_path; ?>../assets/js/main.js</p>
    <p><strong>Admin.js path:</strong> <?php echo $base_path; ?>assets/js/admin.js</p>
    
    <script>
        function addResult(message, type = 'info') {
            const div = document.createElement('div');
            div.className = `status ${type}`;
            div.textContent = message;
            document.getElementById('test-results').appendChild(div);
        }
        
        addResult('Basic JavaScript is working', 'success');
        
        // Test if files exist by trying to load them
        function testScriptLoad(src, name) {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = src;
                script.onload = () => {
                    addResult(`${name} loaded successfully`, 'success');
                    resolve();
                };
                script.onerror = () => {
                    addResult(`${name} failed to load from: ${src}`, 'error');
                    reject();
                };
                document.head.appendChild(script);
            });
        }
        
        // Test main.js
        testScriptLoad('<?php echo $base_path; ?>../assets/js/main.js', 'main.js')
            .then(() => {
                // Test admin.js
                return testScriptLoad('<?php echo $base_path; ?>assets/js/admin.js', 'admin.js');
            })
            .then(() => {
                addResult('All scripts loaded successfully', 'success');
                
                // Test if admin functions are available
                setTimeout(() => {
                    if (typeof initializeAdminInterface !== 'undefined') {
                        addResult('initializeAdminInterface function is available', 'success');
                    } else {
                        addResult('initializeAdminInterface function is NOT available', 'error');
                    }
                    
                    if (typeof window.adminUtils !== 'undefined') {
                        addResult('window.adminUtils is available', 'success');
                    } else {
                        addResult('window.adminUtils is NOT available', 'error');
                    }
                }, 1000);
            })
            .catch(() => {
                addResult('Script loading failed', 'error');
            });
    </script>
</body>
</html>