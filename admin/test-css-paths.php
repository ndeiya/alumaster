<?php
// Test CSS Path Resolution
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'CSS Path Test';

// Test the path calculation
$script_path = $_SERVER['SCRIPT_NAME'];
$admin_pos = strpos($script_path, '/admin/');
if ($admin_pos !== false) {
    $after_admin = substr($script_path, $admin_pos + 7);
    $depth = substr_count($after_admin, '/');
    $base_path = str_repeat('../', $depth);
} else {
    $base_path = '';
}

echo "<h1>CSS Path Debug</h1>";
echo "<p><strong>Script Path:</strong> " . $script_path . "</p>";
echo "<p><strong>After Admin:</strong> " . $after_admin . "</p>";
echo "<p><strong>Depth:</strong> " . $depth . "</p>";
echo "<p><strong>Base Path:</strong> '" . $base_path . "'</p>";
echo "<p><strong>CSS Path:</strong> " . $base_path . "assets/css/admin.css</p>";
echo "<p><strong>Style Path:</strong> " . $base_path . "../assets/css/style.css</p>";

include 'includes/header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">CSS Test Page</h2>
    </div>
    <div class="card-content">
        <p>If you can see this styled properly, the CSS paths are working!</p>
        <div class="alert alert-success">
            <div class="alert-content">
                <p>This is a success alert to test styling.</p>
            </div>
        </div>
        <button class="btn btn-primary">Test Button</button>
    </div>
</div>

<?php include 'includes/footer.php'; ?>