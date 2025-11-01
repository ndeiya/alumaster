<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'Inquiries';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => '../index.php'],
    ['title' => 'Inquiries']
];

// Handle success message from URL
if (isset($_GET['success'])) {
    $success_message = sanitize_input($_GET['success']);
}

// Handle mark as read/unread
if ($_GET && isset($_GET['action']) && isset($_GET['id'])) {
    $inquiry_id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        if ($action === 'mark_read') {
            $stmt = $conn->prepare("UPDATE inquiries SET status = 'read' WHERE id = ?");
            $stmt->execute([$inquiry_id]);
            log_admin_activity('update', "Marked inquiry as read", $inquiry_id);
            $success_message = "Inquiry marked as read.";
        } elseif ($action === 'mark_unread') {
            $stmt = $conn->prepare("UPDATE inquiries SET status = 'unread' WHERE id = ?");
            $stmt->execute([$inquiry_id]);
            log_admin_activity('update', "Marked inquiry as unread", $inquiry_id);
            $success_message = "Inquiry marked as unread.";
        } elseif ($action === 'delete' && check_admin_permission('admin')) {
            $stmt = $conn->prepare("DELETE FROM inquiries WHERE id = ?");
            $stmt->execute([$inquiry_id]);
            log_admin_activity('delete', "Deleted inquiry", $inquiry_id);
            $success_message = "Inquiry deleted successfully.";
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Get inquiries with pagination
$page = (int)($_GET['page'] ?? 1);
$per_page = 20;
$offset = ($page - 1) * $per_page;

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Build query conditions
    $conditions = ["1=1"];
    $params = [];
    
    if (!empty($search)) {
        $conditions[] = "(name LIKE ? OR email LIKE ? OR message LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if (!empty($status_filter)) {
        $conditions[] = "status = ?";
        $params[] = $status_filter;
    }
    
    $where_clause = implode(' AND ', $conditions);
    
    // Get total count
    $count_sql = "SELECT COUNT(*) FROM inquiries WHERE $where_clause";
    $stmt = $conn->prepare($count_sql);
    $stmt->execute($params);
    $total_inquiries = $stmt->fetchColumn();
    
    // Get inquiries
    $sql = "SELECT * FROM inquiries WHERE $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total_pages = ceil($total_inquiries / $per_page);
    
} catch (Exception $e) {
    $inquiries = [];
    $total_inquiries = 0;
    $total_pages = 0;
    $error_message = "Error loading inquiries: " . $e->getMessage();
}

include '../includes/header.php';
?>

<?php if (isset($success_message)): ?>
<div class="alert alert-success">
    <div class="alert-icon">
        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>
    <div class="alert-content"><?php echo htmlspecialchars($success_message); ?></div>
</div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
<div class="alert alert-error">
    <div class="alert-icon">
        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
    <div class="alert-content"><?php echo htmlspecialchars($error_message); ?></div>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Customer Inquiries</h1>
        <p class="page-description">Manage customer inquiries and contact requests</p>
    </div>
</div>

<!-- Filters -->
<div class="filters-card">
    <form method="GET" class="filters-form">
        <div class="filters-row">
            <div class="filter-group">
                <label for="search" class="filter-label">Search</label>
                <input type="text" id="search" name="search" class="form-input" 
                       placeholder="Search inquiries..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            
            <div class="filter-group">
                <label for="status" class="filter-label">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="unread" <?php echo $status_filter === 'unread' ? 'selected' : ''; ?>>Unread</option>
                    <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Read</option>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="list.php" class="btn btn-outline">Clear</a>
            </div>
        </div>
    </form>
</div>

<!-- Inquiries Table -->
<div class="table-card">
    <div class="table-header">
        <div class="table-title">
            Inquiries (<?php echo $total_inquiries; ?>)
        </div>
    </div>
    
    <?php if (!empty($inquiries)): ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Service Interest</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="table-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inquiries as $inquiry): ?>
                <tr class="<?php echo $inquiry['status'] === 'unread' ? 'unread-row' : ''; ?>">
                    <td>
                        <div class="customer-info">
                            <div class="customer-name">
                                <?php echo htmlspecialchars($inquiry['name']); ?>
                                <?php if ($inquiry['status'] === 'unread'): ?>
                                    <span class="unread-badge">New</span>
                                <?php endif; ?>
                            </div>
                            <div class="customer-email"><?php echo htmlspecialchars($inquiry['email']); ?></div>
                            <?php if (!empty($inquiry['phone'])): ?>
                            <div class="customer-phone"><?php echo htmlspecialchars($inquiry['phone']); ?></div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div class="service-interest">
                            <?php if (!empty($inquiry['service_interest'])): ?>
                                <span class="service-badge"><?php echo htmlspecialchars($inquiry['service_interest']); ?></span>
                            <?php else: ?>
                                <span class="service-badge service-general">General Inquiry</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <span class="status-badge status-<?php echo $inquiry['status']; ?>">
                            <?php echo ucfirst($inquiry['status']); ?>
                        </span>
                    </td>
                    <td>
                        <div class="date-info">
                            <div class="date-primary"><?php echo date('M j, Y', strtotime($inquiry['created_at'])); ?></div>
                            <div class="date-secondary"><?php echo date('g:i A', strtotime($inquiry['created_at'])); ?></div>
                        </div>
                    </td>
                    <td class="table-actions">
                        <div class="action-buttons">
                            <button class="btn-action" onclick="viewInquiry(<?php echo $inquiry['id']; ?>)" title="View Details">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                            
                            <?php if ($inquiry['status'] === 'unread'): ?>
                            <a href="?action=mark_read&id=<?php echo $inquiry['id']; ?>" class="btn-action" title="Mark as Read">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </a>
                            <?php else: ?>
                            <a href="?action=mark_unread&id=<?php echo $inquiry['id']; ?>" class="btn-action" title="Mark as Unread">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </a>
                            <?php endif; ?>
                            
                            <?php if (check_admin_permission('admin')): ?>
                            <a href="?action=delete&id=<?php echo $inquiry['id']; ?>" class="btn-action btn-action-danger" 
                               title="Delete" onclick="return confirm('Are you sure you want to delete this inquiry?')">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="table-pagination">
        <div class="pagination-info">
            Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $per_page, $total_inquiries); ?> 
            of <?php echo $total_inquiries; ?> inquiries
        </div>
        <div class="pagination-controls">
            <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&<?php echo http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)); ?>" 
               class="pagination-btn">Previous</a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
            <a href="?page=<?php echo $i; ?>&<?php echo http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)); ?>" 
               class="pagination-btn <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&<?php echo http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY)); ?>" 
               class="pagination-btn">Next</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="empty-state">
        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
        </svg>
        <h3 class="empty-title">No inquiries found</h3>
        <p class="empty-message">
            <?php if (!empty($search) || !empty($status_filter)): ?>
                No inquiries match your current filters. Try adjusting your search criteria.
            <?php else: ?>
                No customer inquiries have been received yet.
            <?php endif; ?>
        </p>
        <div class="empty-actions">
            <?php if (!empty($search) || !empty($status_filter)): ?>
                <a href="list.php" class="btn btn-secondary">Clear Filters</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Inquiry Details Modal -->
<div id="inquiryModal" class="modal">
    <div class="modal-overlay"></div>
    <div class="modal-container modal-lg">
        <div class="modal-header">
            <h3 class="modal-title">Inquiry Details</h3>
            <button class="modal-close" onclick="closeInquiryModal()">
                <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="modal-body" id="inquiryModalContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
function viewInquiry(inquiryId) {
    // Get inquiry data
    const inquiries = <?php echo json_encode($inquiries); ?>;
    const inquiry = inquiries.find(i => i.id == inquiryId);
    
    if (!inquiry) return;
    
    const content = `
        <div class="inquiry-details">
            <div class="inquiry-header">
                <div class="customer-info-detailed">
                    <h4>${inquiry.name}</h4>
                    <p><strong>Email:</strong> ${inquiry.email}</p>
                    ${inquiry.phone ? `<p><strong>Phone:</strong> ${inquiry.phone}</p>` : ''}
                    ${inquiry.company ? `<p><strong>Company:</strong> ${inquiry.company}</p>` : ''}
                    ${inquiry.service_interest ? `<p><strong>Service Interest:</strong> ${inquiry.service_interest}</p>` : ''}
                </div>
                <div class="inquiry-meta">
                    <span class="status-badge status-${inquiry.status}">${inquiry.status.charAt(0).toUpperCase() + inquiry.status.slice(1)}</span>
                    <p class="inquiry-date">${new Date(inquiry.created_at).toLocaleString()}</p>
                </div>
            </div>
            <div class="inquiry-message">
                <h5>Message:</h5>
                <div class="message-content">${inquiry.message.replace(/\n/g, '<br>')}</div>
            </div>
            <div class="inquiry-actions">
                <a href="mailto:${inquiry.email}" class="btn btn-primary">Reply via Email</a>
                ${inquiry.phone ? `<a href="tel:${inquiry.phone}" class="btn btn-secondary">Call Customer</a>` : ''}
                ${inquiry.status === 'unread' ? 
                    `<a href="?action=mark_read&id=${inquiry.id}" class="btn btn-outline">Mark as Read</a>` :
                    `<a href="?action=mark_unread&id=${inquiry.id}" class="btn btn-outline">Mark as Unread</a>`
                }
            </div>
        </div>
    `;
    
    document.getElementById('inquiryModalContent').innerHTML = content;
    document.getElementById('inquiryModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeInquiryModal() {
    document.getElementById('inquiryModal').classList.remove('active');
    document.body.style.overflow = '';
}

// Close modal when clicking overlay
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        closeInquiryModal();
    }
});
</script>

<!-- 
To enable inquiries, add this table to your database:

CREATE TABLE IF NOT EXISTS `inquiries` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `company` varchar(100) DEFAULT NULL,
    `service_interest` varchar(100) DEFAULT NULL,
    `message` text,
    `status` enum('unread','read','replied') DEFAULT 'unread',
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_status` (`status`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-->

<?php include '../includes/footer.php'; ?>