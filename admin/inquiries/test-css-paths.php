<?php
// Test CSS Path Resolution from Subdirectory
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'CSS Path Test - Subdirectory';

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

echo "<h1>CSS Path Debug - Subdirectory</h1>";
echo "<p><strong>Script Path:</strong> " . $script_path . "</p>";
echo "<p><strong>After Admin:</strong> " . $after_admin . "</p>";
echo "<p><strong>Depth:</strong> " . $depth . "</p>";
echo "<p><strong>Base Path:</strong> '" . $base_path . "'</p>";
echo "<p><strong>CSS Path:</strong> " . $base_path . "assets/css/admin.css</p>";
echo "<p><strong>Style Path:</strong> " . $base_path . "../assets/css/style.css</p>";

include '../includes/header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">CSS Test Page - Subdirectory</h2>
    </div>
    <div class="card-content">
        <p>If you can see this styled properly from the inquiries subdirectory, the CSS paths are working!</p>
        <div class="alert alert-info">
            <div class="alert-content">
                <p>This is an info alert to test styling from subdirectory.</p>
            </div>
        </div>
        <button class="btn btn-success">Test Button</button>
        <a href="../index.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>