// Admin Panel JavaScript - Simplified Working Version

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin JS: DOM loaded, initializing...');
    
    // Initialize dropdowns
    initializeDropdowns();
    
    // Initialize sidebar
    initializeSidebar();
    
    console.log('Admin JS: Initialization complete');
});

// Dropdown functionality - simplified and working
function initializeDropdowns() {
    const dropdownToggles = document.querySelectorAll('.nav-dropdown-toggle');
    console.log('Admin JS: Found dropdown toggles:', dropdownToggles.length);
    
    dropdownToggles.forEach((toggle, index) => {
        console.log(`Admin JS: Setting up dropdown ${index + 1}`);
        
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Admin JS: Dropdown clicked');
            
            const dropdown = this.parentElement;
            const wasOpen = dropdown.classList.contains('open');
            
            // Close all dropdowns first
            document.querySelectorAll('.nav-dropdown.open').forEach(openDropdown => {
                openDropdown.classList.remove('open');
            });
            
            // Toggle current dropdown
            if (!wasOpen) {
                dropdown.classList.add('open');
                console.log('Admin JS: Dropdown opened');
            } else {
                console.log('Admin JS: Dropdown closed');
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.nav-dropdown')) {
            const openDropdowns = document.querySelectorAll('.nav-dropdown.open');
            if (openDropdowns.length > 0) {
                openDropdowns.forEach(dropdown => dropdown.classList.remove('open'));
                console.log('Admin JS: Closed dropdowns by clicking outside');
            }
        }
    });
}

// Basic sidebar functionality
function initializeSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    
    // Desktop sidebar toggle
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    }
    
    // Mobile sidebar toggle
    if (mobileSidebarToggle && sidebar) {
        mobileSidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('mobile-open');
        });
    }
    
    // Restore sidebar state
    if (sidebar && localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
    }
}

// Table selection functionality
function initializeTableSelection() {
    const selectAllCheckbox = document.querySelector('.select-all-checkbox');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkActions = document.querySelector('.bulk-actions');
    
    if (!selectAllCheckbox || !rowCheckboxes.length) return;
    
    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });
    
    // Individual row selection
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
        
        // Update bulk action form with selected items
        const bulkForm = document.querySelector('.bulk-actions-form');
        if (bulkForm) {
            // Remove existing hidden inputs
            const existingInputs = bulkForm.querySelectorAll('input[name="selected_items[]"]');
            existingInputs.forEach(input => input.remove());
            
            // Add selected items as hidden inputs
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            checkedBoxes.forEach(checkbox => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'selected_items[]';
                hiddenInput.value = checkbox.value;
                bulkForm.appendChild(hiddenInput);
            });
        }
    }
}

// Form enhancements
function initializeFormEnhancements() {
    // Auto-resize textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        autoResizeTextarea(textarea);
        textarea.addEventListener('input', function() {
            autoResizeTextarea(this);
        });
    });
    
    // Form validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
    
    // Character counters
    const inputsWithCounter = document.querySelectorAll('[data-max-length]');
    inputsWithCounter.forEach(input => {
        addCharacterCounter(input);
    });
}

function autoResizeTextarea(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            showFieldError(field, 'This field is required');
            isValid = false;
        } else {
            clearFieldError(field);
        }
    });
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('error');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.classList.remove('error');
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

function addCharacterCounter(input) {
    const maxLength = parseInt(input.dataset.maxLength);
    const counter = document.createElement('div');
    counter.className = 'character-counter';
    input.parentNode.appendChild(counter);
    
    function updateCounter() {
        const remaining = maxLength - input.value.length;
        counter.textContent = `${remaining} characters remaining`;
        counter.className = 'character-counter ' + (remaining < 10 ? 'warning' : '');
    }
    
    input.addEventListener('input', updateCounter);
    updateCounter();
}

// Tooltip functionality
function initializeTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
    const element = e.target;
    const text = element.dataset.tooltip;
    
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = text;
    document.body.appendChild(tooltip);
    
    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
    
    element._tooltip = tooltip;
}

function hideTooltip(e) {
    const element = e.target;
    if (element._tooltip) {
        element._tooltip.remove();
        delete element._tooltip;
    }
}

// Utility functions
function showToast(message, type = 'success', duration = 3000) {
    const container = document.getElementById('toast-container') || createToastContainer();
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
    
    // Auto remove
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, duration);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container';
    document.body.appendChild(container);
    return container;
}

function showConfirmation(message, onConfirm, onCancel = null) {
    const modal = document.getElementById('confirmation-modal');
    if (!modal) {
        console.error('Confirmation modal not found');
        return;
    }
    
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
    
    // Remove existing listeners and add new ones
    const newConfirmButton = confirmButton.cloneNode(true);
    const newCancelButton = cancelButton.cloneNode(true);
    const newCloseButton = closeButton.cloneNode(true);
    const newOverlay = overlay.cloneNode(true);
    
    confirmButton.parentNode.replaceChild(newConfirmButton, confirmButton);
    cancelButton.parentNode.replaceChild(newCancelButton, cancelButton);
    closeButton.parentNode.replaceChild(newCloseButton, closeButton);
    overlay.parentNode.replaceChild(newOverlay, overlay);
    
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

// Auto-save functionality
function enableAutoSave(formSelector, saveUrl, interval = 30000) {
    const form = document.querySelector(formSelector);
    if (!form) return;
    
    let autoSaveTimer;
    let hasChanges = false;
    
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

// Search functionality
function initializeSearch() {
    const searchInputs = document.querySelectorAll('[data-search]');
    
    searchInputs.forEach(input => {
        let searchTimeout;
        
        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(this);
            }, 300);
        });
    });
}

function performSearch(input) {
    const searchTerm = input.value.toLowerCase();
    const targetSelector = input.dataset.search;
    const targets = document.querySelectorAll(targetSelector);
    
    targets.forEach(target => {
        const text = target.textContent.toLowerCase();
        const shouldShow = text.includes(searchTerm);
        target.style.display = shouldShow ? '' : 'none';
    });
}

// Export functions for global use
window.adminUtils = {
    showToast,
    showConfirmation,
    enableAutoSave,
    initializeSearch
};