<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Check authentication - redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'includes/auth-check.php';

$page_title = 'Dashboard';

// Get dashboard statistics
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Total services
    $stmt = $conn->prepare("SELECT COUNT(*) FROM services WHERE status = 'published'");
    $stmt->execute();
    $total_services = $stmt->fetchColumn();
    
    // Unread inquiries
    $stmt = $conn->prepare("SELECT COUNT(*) FROM inquiries WHERE status = 'unread'");
    $stmt->execute();
    $unread_inquiries = $stmt->fetchColumn();
    
    // Total inquiries this month
    $stmt = $conn->prepare("SELECT COUNT(*) FROM inquiries WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
    $stmt->execute();
    $monthly_inquiries = $stmt->fetchColumn();
    
    // Total admin users
    $stmt = $conn->prepare("SELECT COUNT(*) FROM admins WHERE status = 'active'");
    $stmt->execute();
    $total_admins = $stmt->fetchColumn();
    
    // Recent inquiries
    $stmt = $conn->prepare("SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $recent_inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent activity logs
    $stmt = $conn->prepare("SELECT al.*, a.full_name as admin_name FROM activity_logs al 
                           LEFT JOIN admins a ON al.admin_id = a.id 
                           ORDER BY al.created_at DESC LIMIT 10");
    $stmt->execute();
    $recent_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $total_services = 0;
    $unread_inquiries = 0;
    $monthly_inquiries = 0;
    $total_admins = 0;
    $recent_inquiries = [];
    $recent_activities = [];
}

include 'includes/header.php';
?><!-
- Dashboard Stats -->
<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon stat-icon-primary">
            <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $total_services; ?></div>
            <div class="stat-label">Total Services</div>
        </div>
        <div class="stat-action">
            <a href="services/list.php" class="stat-link">View All</a>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-warning">
            <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $unread_inquiries; ?></div>
            <div class="stat-label">Unread Inquiries</div>
        </div>
        <div class="stat-action">
            <a href="inquiries/list.php" class="stat-link">View All</a>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-success">
            <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $monthly_inquiries; ?></div>
            <div class="stat-label">This Month</div>
        </div>
        <div class="stat-action">
            <a href="inquiries/list.php" class="stat-link">View Report</a>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-info">
            <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $total_admins; ?></div>
            <div class="stat-label">Admin Users</div>
        </div>
        <div class="stat-action">
            <?php if (check_admin_permission('admin')): ?>
            <a href="settings/users.php" class="stat-link">Manage</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="dashboard-section">
    <h2 class="section-title">Quick Actions</h2>
    <div class="quick-actions">
        <a href="services/add.php" class="quick-action-card">
            <div class="quick-action-icon">
                <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <div class="quick-action-content">
                <h3 class="quick-action-title">Add New Service</h3>
                <p class="quick-action-description">Create a new service offering</p>
            </div>
        </a>

        <a href="media/library.php" class="quick-action-card">
            <div class="quick-action-icon">
                <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="quick-action-content">
                <h3 class="quick-action-title">Upload Media</h3>
                <p class="quick-action-description">Add images to media library</p>
            </div>
        </a>

        <a href="settings/general.php" class="quick-action-card">
            <div class="quick-action-icon">
                <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div class="quick-action-content">
                <h3 class="quick-action-title">Site Settings</h3>
                <p class="quick-action-description">Update contact info and settings</p>
            </div>
        </a>

        <a href="../index.php" target="_blank" class="quick-action-card">
            <div class="quick-action-icon">
                <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
            </div>
            <div class="quick-action-content">
                <h3 class="quick-action-title">View Website</h3>
                <p class="quick-action-description">See your live website</p>
            </div>
        </a>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Recent Inquiries -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3 class="card-title">Recent Inquiries</h3>
            <a href="inquiries/list.php" class="card-action">View All</a>
        </div>
        <div class="card-content">
            <?php if (!empty($recent_inquiries)): ?>
                <div class="inquiry-list">
                    <?php foreach ($recent_inquiries as $inquiry): ?>
                    <div class="inquiry-item <?php echo $inquiry['status'] === 'unread' ? 'unread' : ''; ?>">
                        <div class="inquiry-info">
                            <div class="inquiry-name"><?php echo htmlspecialchars($inquiry['name']); ?></div>
                            <div class="inquiry-service"><?php echo htmlspecialchars($inquiry['service_interest'] ?: 'General Inquiry'); ?></div>
                            <div class="inquiry-date"><?php echo date('M j, Y g:i A', strtotime($inquiry['created_at'])); ?></div>
                        </div>
                        <div class="inquiry-actions">
                            <a href="inquiries/view.php?id=<?php echo $inquiry['id']; ?>" class="btn btn-sm btn-outline">View</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <p class="empty-message">No inquiries yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3 class="card-title">Recent Activity</h3>
        </div>
        <div class="card-content">
            <?php if (!empty($recent_activities)): ?>
                <div class="activity-list">
                    <?php foreach ($recent_activities as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <?php
                            $icon = '';
                            switch ($activity['action']) {
                                case 'login':
                                    $icon = '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>';
                                    break;
                                case 'create':
                                    $icon = '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>';
                                    break;
                                case 'update':
                                    $icon = '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>';
                                    break;
                                case 'delete':
                                    $icon = '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>';
                                    break;
                                default:
                                    $icon = '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                            }
                            echo $icon;
                            ?>
                        </div>
                        <div class="activity-content">
                            <div class="activity-text">
                                <strong><?php echo htmlspecialchars($activity['admin_name'] ?: 'System'); ?></strong>
                                <?php echo htmlspecialchars($activity['details']); ?>
                            </div>
                            <div class="activity-time"><?php echo time_ago($activity['created_at']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="empty-message">No recent activity</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>