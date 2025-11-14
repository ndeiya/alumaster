<?php
/**
 * Spam Monitor - Admin Panel
 * View spam attempts, statistics, and manage blocked IPs
 */

require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/anti-spam.php';
require_once 'includes/auth-check.php';

$page_title = 'Spam Monitor';

// Handle actions
$action_message = '';
$action_type = '';

if ($_POST) {
    if (isset($_POST['unblock_ip'])) {
        $ip = $_POST['ip'];
        // Remove from blocked list
        $blocked_file = __DIR__ . '/../logs/blocked_ips.txt';
        if (file_exists($blocked_file)) {
            $blocked_ips = file($blocked_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $new_list = array_filter($blocked_ips, function($line) use ($ip) {
                return strpos($line, $ip) !== 0;
            });
            file_put_contents($blocked_file, implode("\n", $new_list) . "\n");
            $action_message = "IP {$ip} has been unblocked.";
            $action_type = 'success';
        }
    } elseif (isset($_POST['block_ip'])) {
        $ip = $_POST['ip'];
        $reason = $_POST['reason'] ?? 'Manual block';
        AntiSpam::blockIP($ip, $reason);
        $action_message = "IP {$ip} has been blocked.";
        $action_type = 'success';
    } elseif (isset($_POST['clean_logs'])) {
        $days = intval($_POST['days'] ?? 30);
        $removed = AntiSpam::cleanOldLogs($days);
        $action_message = "Cleaned {$removed} old log entries (older than {$days} days).";
        $action_type = 'success';
    }
}

// Get statistics
$stats_7days = AntiSpam::getSpamStats(7);
$stats_30days = AntiSpam::getSpamStats(30);

// Get recent spam attempts
$spam_log_file = __DIR__ . '/../logs/spam.log';
$recent_attempts = [];
if (file_exists($spam_log_file)) {
    $lines = file($spam_log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_reverse($lines); // Most recent first
    $lines = array_slice($lines, 0, 50); // Last 50 entries
    
    foreach ($lines as $line) {
        $entry = json_decode($line, true);
        if ($entry) {
            $recent_attempts[] = $entry;
        }
    }
}

// Get blocked IPs
$blocked_ips = [];
$blocked_file = __DIR__ . '/../logs/blocked_ips.txt';
if (file_exists($blocked_file)) {
    $lines = file($blocked_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode('#', $line, 2);
        $blocked_ips[] = [
            'ip' => trim($parts[0]),
            'reason' => trim($parts[1] ?? 'No reason specified')
        ];
    }
}

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <h1>🛡️ Spam Monitor</h1>
        <p>Monitor spam attempts and manage blocked IPs</p>
    </div>

    <?php if ($action_message): ?>
        <div class="alert alert-<?php echo $action_type; ?>">
            <?php echo htmlspecialchars($action_message); ?>
        </div>
    <?php endif; ?>

    <!-- Statistics Dashboard -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-value"><?php echo $stats_7days['total_attempts']; ?></div>
            <div class="stat-label">Spam Attempts (7 days)</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🌐</div>
            <div class="stat-value"><?php echo $stats_7days['unique_ips']; ?></div>
            <div class="stat-label">Unique IPs (7 days)</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🚫</div>
            <div class="stat-value"><?php echo $stats_7days['blocked_ips']; ?></div>
            <div class="stat-label">Blocked IPs</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">📈</div>
            <div class="stat-value"><?php echo $stats_30days['total_attempts']; ?></div>
            <div class="stat-label">Total Attempts (30 days)</div>
        </div>
    </div>

    <!-- Spam Types Breakdown -->
    <?php if (!empty($stats_7days['by_type'])): ?>
    <div class="card" style="margin-bottom: 30px;">
        <h2>Spam Types (Last 7 Days)</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php
                arsort($stats_7days['by_type']);
                foreach ($stats_7days['by_type'] as $type => $count):
                    $percentage = ($count / $stats_7days['total_attempts']) * 100;
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($type); ?></strong></td>
                    <td><?php echo $count; ?></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="flex: 1; background: #e9ecef; border-radius: 4px; height: 20px; overflow: hidden;">
                                <div style="background: #2c5aa0; height: 100%; width: <?php echo $percentage; ?>%;"></div>
                            </div>
                            <span><?php echo number_format($percentage, 1); ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Blocked IPs -->
    <div class="card" style="margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Blocked IP Addresses</h2>
            <button type="button" class="btn btn-sm btn-primary" onclick="toggleBlockForm()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 5px; vertical-align: middle;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Block New IP
            </button>
        </div>
        
        <!-- Block IP Form (Hidden by default) -->
        <div id="block-ip-form" class="block-ip-form" style="display: none;">
            <div class="block-ip-form-header">
                <div class="block-ip-form-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                    <span>Block IP Address</span>
                </div>
                <button type="button" class="block-ip-form-close" onclick="toggleBlockForm()" title="Close">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form method="POST" action="" class="block-ip-form-content">
                <div class="form-group">
                    <label for="ip" class="form-label-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                        </svg>
                        <span>IP Address</span>
                        <span class="required-asterisk">*</span>
                    </label>
                    <input type="text" id="ip" name="ip" class="form-control" placeholder="e.g., 192.168.1.1 or 203.0.113.50" required pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$" title="Please enter a valid IP address">
                    <small class="form-help">Enter the IPv4 address you want to block</small>
                </div>
                
                <div class="form-group">
                    <label for="reason" class="form-label-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Reason</span>
                    </label>
                    <input type="text" id="reason" name="reason" class="form-control" placeholder="e.g., Excessive spam submissions, Malicious activity" maxlength="200">
                    <small class="form-help">Optional: Add a reason for blocking this IP (helps track why IPs were blocked)</small>
                </div>
                
                <div class="form-alert form-alert-warning">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <strong>Warning:</strong> Blocking an IP will prevent all access from that address. Make sure you have the correct IP before proceeding.
                    </div>
                </div>
                
                <div class="block-ip-form-actions">
                    <button type="button" class="btn btn-secondary" onclick="toggleBlockForm()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 5px; vertical-align: middle;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </button>
                    <button type="submit" name="block_ip" class="btn btn-danger">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 5px; vertical-align: middle;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                        Block IP Address
                    </button>
                </div>
            </form>
        </div>

        <?php if (empty($blocked_ips)): ?>
            <p>No blocked IPs yet. That's good news! 🎉</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>IP Address</th>
                        <th>Reason</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blocked_ips as $blocked): ?>
                    <tr>
                        <td><code><?php echo htmlspecialchars($blocked['ip']); ?></code></td>
                        <td><?php echo htmlspecialchars($blocked['reason']); ?></td>
                        <td>
                            <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Unblock this IP?');">
                                <input type="hidden" name="ip" value="<?php echo htmlspecialchars($blocked['ip']); ?>">
                                <button type="submit" name="unblock_ip" class="btn btn-sm btn-success">Unblock</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Recent Spam Attempts -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Recent Spam Attempts (Last 50)</h2>
            <form method="POST" action="" style="display: inline;">
                <select name="days" class="form-control" style="display: inline-block; width: auto; margin-right: 10px;">
                    <option value="7">7 days</option>
                    <option value="14">14 days</option>
                    <option value="30" selected>30 days</option>
                    <option value="60">60 days</option>
                    <option value="90">90 days</option>
                </select>
                <button type="submit" name="clean_logs" class="btn btn-sm btn-warning" onclick="return confirm('Clean old log entries?');">
                    Clean Old Logs
                </button>
            </form>
        </div>

        <?php if (empty($recent_attempts)): ?>
            <p>No spam attempts logged. Your site is clean! ✨</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Type</th>
                            <th>IP Address</th>
                            <th>Details</th>
                            <th>User Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_attempts as $attempt): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($attempt['timestamp']); ?></td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo $attempt['type'] === 'SUCCESS' ? 'success' : 'warning';
                                ?>">
                                    <?php echo htmlspecialchars($attempt['type']); ?>
                                </span>
                            </td>
                            <td><code><?php echo htmlspecialchars($attempt['ip']); ?></code></td>
                            <td><?php echo htmlspecialchars($attempt['details']); ?></td>
                            <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <small><?php echo htmlspecialchars($attempt['user_agent']); ?></small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.stat-card {
    background: white;
    padding: 24px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}
.stat-icon {
    font-size: 32px;
    margin-bottom: 10px;
}
.stat-value {
    font-size: 36px;
    font-weight: bold;
    color: #2c5aa0;
    margin-bottom: 5px;
}
.stat-label {
    color: #666;
    font-size: 14px;
}
.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}
.badge-success {
    background: #d4edda;
    color: #155724;
}
.badge-warning {
    background: #fff3cd;
    color: #856404;
}
.badge-danger {
    background: #f8d7da;
    color: #721c24;
}

/* Block IP Form Styling */
.block-ip-form {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 2px solid #dee2e6;
    border-radius: 12px;
    margin-bottom: 24px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.block-ip-form-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.block-ip-form-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 600;
}

.block-ip-form-title svg {
    flex-shrink: 0;
}

.block-ip-form-close {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 0;
}

.block-ip-form-close:hover {
    background: rgba(255,255,255,0.2);
    transform: scale(1.05);
}

.block-ip-form-content {
    padding: 24px;
}

.form-label-icon {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-label-icon svg {
    color: #6c757d;
    flex-shrink: 0;
}

.required-asterisk {
    color: #dc3545;
    margin-left: 2px;
}

.form-help {
    display: block;
    color: #6c757d;
    font-size: 12px;
    margin-top: 6px;
    font-style: italic;
}

.form-alert {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 13px;
    line-height: 1.5;
}

.form-alert svg {
    flex-shrink: 0;
    margin-top: 2px;
}

.form-alert-warning {
    background: #fff3cd;
    border: 1px solid #ffc107;
    color: #856404;
}

.form-alert strong {
    font-weight: 600;
    display: block;
    margin-bottom: 2px;
}

.block-ip-form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.block-ip-form-actions .btn {
    display: inline-flex;
    align-items: center;
    padding: 10px 20px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.block-ip-form-actions .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.block-ip-form .form-control {
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 10px 14px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.block-ip-form .form-control:focus {
    border-color: #2c5aa0;
    box-shadow: 0 0 0 3px rgba(44, 90, 160, 0.1);
    outline: none;
}

.block-ip-form .form-control:invalid:not(:placeholder-shown) {
    border-color: #dc3545;
}

.block-ip-form .form-control:invalid:not(:placeholder-shown):focus {
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}

.block-ip-form .form-group {
    margin-bottom: 20px;
}
</style>

<script>
function toggleBlockForm() {
    const form = document.getElementById('block-ip-form');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        // Focus on IP input when form opens
        setTimeout(() => {
            document.getElementById('ip').focus();
        }, 100);
    } else {
        form.style.display = 'none';
    }
}

// Close form when clicking outside
document.addEventListener('click', function(event) {
    const form = document.getElementById('block-ip-form');
    const btn = event.target.closest('button');
    
    if (form && form.style.display === 'block' && !form.contains(event.target) && (!btn || !btn.textContent.includes('Block New IP'))) {
        // Don't close if clicking inside form or the open button
        if (!event.target.closest('.block-ip-form')) {
            form.style.display = 'none';
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>
