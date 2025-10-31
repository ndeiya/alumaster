                </div>
            </main>
        </div>
    </div>

    <!-- Toast Notifications Container -->
    <div id="toast-container" class="toast-container"></div>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal" class="modal">
        <div class="modal-overlay"></div>
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Action</h3>
                <button class="modal-close" aria-label="Close modal">
                    <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="confirmation-icon">
                    <svg class="icon-2xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="confirmation-message">Are you sure you want to perform this action?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="confirmation-cancel">Cancel</button>
                <button class="btn btn-danger" id="confirmation-confirm">Confirm</button>
            </div>
        </div>
    </div>

    <?php
    // Use the same base path calculation as in header
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
    <script src="<?php echo $base_path; ?>assets/js/admin-simple.js"></script>
    <script>
        // Initialize admin interface
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle functionality
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
            const sidebar = document.getElementById('adminSidebar');
            
            function toggleSidebar() {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            }
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }
            
            if (mobileSidebarToggle) {
                mobileSidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('mobile-open');
                });
            }
            
            // Restore sidebar state
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                sidebar.classList.add('collapsed');
            }
            
            // Dropdown functionality is handled by admin.js
            
            // User dropdown functionality
            const userToggle = document.querySelector('.sidebar-user-toggle');
            const userMenu = document.querySelector('.sidebar-user-menu');
            
            if (userToggle && userMenu) {
                userToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('show');
                });
                
                document.addEventListener('click', function() {
                    userMenu.classList.remove('show');
                });
            }
            
            // Close sidebar on mobile when clicking outside
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !mobileSidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('mobile-open');
                }
            });
        });
        
        // Toast notification system
        function showToast(message, type = 'success', duration = 3000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            const icon = type === 'success' ? 
                '<svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                '<svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
            
            toast.innerHTML = `
                <div class="toast-icon">${icon}</div>
                <div class="toast-message">${message}</div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            
            container.appendChild(toast);
            
            // Auto remove after duration
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, duration);
        }
        
        // Confirmation modal system
        function showConfirmation(message, onConfirm, onCancel = null) {
            const modal = document.getElementById('confirmation-modal');
            const messageElement = modal.querySelector('.confirmation-message');
            const confirmButton = document.getElementById('confirmation-confirm');
            const cancelButton = document.getElementById('confirmation-cancel');
            const closeButton = modal.querySelector('.modal-close');
            const overlay = modal.querySelector('.modal-overlay');
            
            messageElement.textContent = message;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            function closeModal() {
                modal.classList.remove('active');
                document.body.style.overflow = '';
                if (onCancel) onCancel();
            }
            
            function handleConfirm() {
                closeModal();
                if (onConfirm) onConfirm();
            }
            
            // Remove existing event listeners
            const newConfirmButton = confirmButton.cloneNode(true);
            const newCancelButton = cancelButton.cloneNode(true);
            const newCloseButton = closeButton.cloneNode(true);
            const newOverlay = overlay.cloneNode(true);
            
            confirmButton.parentNode.replaceChild(newConfirmButton, confirmButton);
            cancelButton.parentNode.replaceChild(newCancelButton, cancelButton);
            closeButton.parentNode.replaceChild(newCloseButton, closeButton);
            overlay.parentNode.replaceChild(newOverlay, overlay);
            
            // Add new event listeners
            newConfirmButton.addEventListener('click', handleConfirm);
            newCancelButton.addEventListener('click', closeModal);
            newCloseButton.addEventListener('click', closeModal);
            newOverlay.addEventListener('click', closeModal);
            
            // Keyboard support
            function handleKeydown(e) {
                if (e.key === 'Escape') {
                    closeModal();
                    document.removeEventListener('keydown', handleKeydown);
                }
            }
            document.addEventListener('keydown', handleKeydown);
        }
        
        // Auto-save functionality for forms
        function enableAutoSave(formSelector, saveUrl, interval = 30000) {
            const form = document.querySelector(formSelector);
            if (!form) return;
            
            let autoSaveTimer;
            let hasChanges = false;
            
            // Track changes
            form.addEventListener('input', function() {
                hasChanges = true;
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(autoSave, interval);
            });
            
            function autoSave() {
                if (!hasChanges) return;
                
                const formData = new FormData(form);
                formData.append('auto_save', '1');
                
                fetch(saveUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        hasChanges = false;
                        showToast('Draft saved automatically', 'success', 2000);
                    }
                })
                .catch(error => {
                    console.error('Auto-save failed:', error);
                });
            }
        }
        
        // Table row selection
        function initializeTableSelection() {
            const selectAllCheckbox = document.querySelector('.select-all-checkbox');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const bulkActions = document.querySelector('.bulk-actions');
            
            if (!selectAllCheckbox || !rowCheckboxes.length) return;
            
            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActions();
            });
            
            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                    selectAllCheckbox.checked = checkedCount === rowCheckboxes.length;
                    selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < rowCheckboxes.length;
                    updateBulkActions();
                });
            });
            
            function updateBulkActions() {
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                if (bulkActions) {
                    bulkActions.style.display = checkedCount > 0 ? 'block' : 'none';
                }
            }
        }
        
        // Initialize table selection on page load
        document.addEventListener('DOMContentLoaded', initializeTableSelection);
    </script>
</body>
</html>