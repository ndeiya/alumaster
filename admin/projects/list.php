<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/functions.php';
require_once '../includes/auth-check.php';

$page_title = 'Manage Projects';

// Initialize database
$db = new Database();
$pdo = $db->getConnection();

// Handle delete action
if (isset($_POST['delete_project'])) {
    $project_id = (int)$_POST['project_id'];
    
    // Delete project images first
    $stmt = $pdo->prepare("DELETE FROM project_images WHERE project_id = ?");
    $stmt->execute([$project_id]);
    
    // Delete project
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    if ($stmt->execute([$project_id])) {
        $success_message = "Project deleted successfully!";
    } else {
        $error_message = "Failed to delete project.";
    }
}

// Handle status toggle
if (isset($_POST['toggle_status'])) {
    $project_id = (int)$_POST['project_id'];
    $stmt = $pdo->prepare("UPDATE projects SET status = IF(status = 'active', 'inactive', 'active') WHERE id = ?");
    $stmt->execute([$project_id]);
}

// Handle featured toggle
if (isset($_POST['toggle_featured'])) {
    $project_id = (int)$_POST['project_id'];
    $stmt = $pdo->prepare("UPDATE projects SET is_featured = IF(is_featured = 1, 0, 1) WHERE id = ?");
    $stmt->execute([$project_id]);
}

// Fetch all projects
$stmt = $pdo->query("SELECT * FROM projects ORDER BY display_order ASC, created_at DESC");
$projects = $stmt->fetchAll();

include '../includes/header.php';
?>

<?php if (isset($success_message)): ?>
<div class="alert alert-success">
    <div class="alert-icon">
        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>
    <div class="alert-content"><?php echo $success_message; ?></div>
</div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
<div class="alert alert-error">
    <div class="alert-icon">
        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
    <div class="alert-content"><?php echo $error_message; ?></div>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Manage Projects</h1>
        <p class="page-description">View and manage all project showcases</p>
    </div>
    <div class="page-header-actions">
        <a href="add.php" class="btn btn-primary">
            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Project
        </a>
    </div>
</div>

<!-- Projects Table (Desktop) -->
<div class="data-table-card desktop-only">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Thumbnail</th>
                    <th>Project Name</th>
                    <th>Location</th>
                    <th>Scope</th>
                    <th style="width: 100px; text-align: center;">Images</th>
                    <th style="width: 100px; text-align: center;">Featured</th>
                    <th style="width: 120px; text-align: center;">Status</th>
                    <th style="width: 140px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($projects)): ?>
                    <tr>
                        <td colspan="8" class="empty-state">
                            <div class="empty-state-content">
                                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <p class="empty-state-title">No projects found</p>
                                <p class="empty-state-description">Get started by creating your first project</p>
                                <a href="add.php" class="btn btn-primary">Add Your First Project</a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                        <?php
                        // Count images
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM project_images WHERE project_id = ?");
                        $stmt->execute([$project['id']]);
                        $image_count = $stmt->fetchColumn();
                        ?>
                        <tr>
                            <td>
                                <?php if ($project['thumbnail']): ?>
                                    <img src="../../<?php echo htmlspecialchars($project['thumbnail']); ?>" 
                                         alt="<?php echo htmlspecialchars($project['name']); ?>" 
                                         class="table-thumbnail">
                                <?php else: ?>
                                    <div class="table-thumbnail-placeholder">
                                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong class="table-title"><?php echo htmlspecialchars($project['name']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($project['location']); ?></td>
                            <td class="table-text-truncate"><?php echo htmlspecialchars($project['scope']); ?></td>
                            <td style="text-align: center;">
                                <span class="badge badge-info"><?php echo $image_count; ?> images</span>
                            </td>
                            <td style="text-align: center;">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                    <button type="submit" name="toggle_featured" 
                                            class="btn-icon <?php echo $project['is_featured'] ? 'btn-icon-warning' : 'btn-icon-secondary'; ?>"
                                            title="<?php echo $project['is_featured'] ? 'Remove from featured' : 'Mark as featured'; ?>">
                                        <svg class="icon-md" fill="<?php echo $project['is_featured'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                            <td style="text-align: center;">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                    <button type="submit" name="toggle_status" 
                                            class="badge badge-interactive <?php echo $project['status'] === 'active' ? 'badge-success' : 'badge-secondary'; ?>">
                                        <?php echo ucfirst($project['status']); ?>
                                    </button>
                                </form>
                            </td>
                            <td style="text-align: center;">
                                <div class="table-actions">
                                    <a href="edit.php?id=<?php echo $project['id']; ?>" 
                                       class="btn-icon btn-icon-primary" 
                                       title="Edit project">
                                        <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button type="button" 
                                            class="btn-icon btn-icon-danger" 
                                            onclick="confirmDelete(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars(addslashes($project['name'])); ?>')"
                                            title="Delete project">
                                        <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Projects Cards (Mobile) -->
<div class="mobile-only">
    <?php if (empty($projects)): ?>
        <div class="empty-state-card">
            <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <p class="empty-state-title">No projects found</p>
            <p class="empty-state-description">Get started by creating your first project</p>
            <a href="add.php" class="btn btn-primary">Add Your First Project</a>
        </div>
    <?php else: ?>
        <?php foreach ($projects as $project): ?>
            <?php
            // Count images
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM project_images WHERE project_id = ?");
            $stmt->execute([$project['id']]);
            $image_count = $stmt->fetchColumn();
            ?>
            <div class="project-card-mobile">
                <div class="project-card-header">
                    <?php if ($project['thumbnail']): ?>
                        <img src="../../<?php echo htmlspecialchars($project['thumbnail']); ?>" 
                             alt="<?php echo htmlspecialchars($project['name']); ?>" 
                             class="project-card-thumbnail">
                    <?php else: ?>
                        <div class="project-card-thumbnail-placeholder">
                            <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    <?php endif; ?>
                    <div class="project-card-info">
                        <h3 class="project-card-title"><?php echo htmlspecialchars($project['name']); ?></h3>
                        <p class="project-card-location"><?php echo htmlspecialchars($project['location']); ?></p>
                    </div>
                </div>
                
                <div class="project-card-body">
                    <div class="project-card-field">
                        <span class="field-label">Scope:</span>
                        <span class="field-value"><?php echo htmlspecialchars($project['scope']); ?></span>
                    </div>
                    
                    <div class="project-card-meta">
                        <span class="badge badge-info"><?php echo $image_count; ?> images</span>
                        
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                            <button type="submit" name="toggle_status" 
                                    class="badge badge-interactive <?php echo $project['status'] === 'active' ? 'badge-success' : 'badge-secondary'; ?>">
                                <?php echo ucfirst($project['status']); ?>
                            </button>
                        </form>
                        
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                            <button type="submit" name="toggle_featured" 
                                    class="btn-icon-mobile <?php echo $project['is_featured'] ? 'btn-icon-warning' : 'btn-icon-secondary'; ?>"
                                    title="<?php echo $project['is_featured'] ? 'Featured' : 'Not featured'; ?>">
                                <svg class="icon-sm" fill="<?php echo $project['is_featured'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="project-card-actions">
                    <a href="edit.php?id=<?php echo $project['id']; ?>" class="btn btn-secondary btn-sm">
                        <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <button type="button" 
                            class="btn btn-danger btn-sm" 
                            onclick="confirmDelete(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars(addslashes($project['name'])); ?>')">
                        <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeDeleteModal()"></div>
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">Confirm Delete</h3>
            <button class="modal-close" onclick="closeDeleteModal()" aria-label="Close modal">
                <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="confirmation-icon">
                <svg class="icon-2xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <p class="confirmation-message">Are you sure you want to delete the project "<strong id="projectName"></strong>"?</p>
            <p class="confirmation-warning">This will also delete all associated images. This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <form method="POST" id="deleteForm">
                <input type="hidden" name="project_id" id="deleteProjectId">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button type="submit" name="delete_project" class="btn btn-danger">Delete Project</button>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete(projectId, projectName) {
    document.getElementById('deleteProjectId').value = projectId;
    document.getElementById('projectName').textContent = projectName;
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'flex';
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('active');
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }, 200);
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>

<?php include '../includes/footer.php'; ?>
