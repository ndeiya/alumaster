// Simplified Admin JavaScript - Only Dropdown Functionality

console.log('Simple Admin JS: Loading...');

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Simple Admin JS: DOM loaded');
    
    // Wait a bit for elements to be ready
    setTimeout(function() {
        initializeDropdowns();
        console.log('Simple Admin JS: Dropdowns initialized');
    }, 200);
});

function initializeDropdowns() {
    const dropdownToggles = document.querySelectorAll('.nav-dropdown-toggle');
    console.log('Simple Admin JS: Found', dropdownToggles.length, 'dropdown toggles');
    
    if (dropdownToggles.length === 0) {
        console.log('Simple Admin JS: No dropdown toggles found');
        return;
    }
    
    dropdownToggles.forEach((toggle, index) => {
        console.log('Simple Admin JS: Setting up dropdown', index + 1);
        
        // Remove any existing listeners
        toggle.removeEventListener('click', handleDropdownClick);
        
        // Add new listener
        toggle.addEventListener('click', handleDropdownClick);
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.nav-dropdown')) {
            closeAllDropdowns();
        }
    });
    
    console.log('Simple Admin JS: All dropdowns set up');
}

function handleDropdownClick(e) {
    e.preventDefault();
    e.stopPropagation();
    
    console.log('Simple Admin JS: Dropdown clicked');
    
    const dropdown = this.parentElement;
    const wasOpen = dropdown.classList.contains('open');
    
    console.log('Simple Admin JS: Dropdown was open:', wasOpen);
    
    // Close all dropdowns first
    closeAllDropdowns();
    
    // Open this dropdown if it wasn't open
    if (!wasOpen) {
        dropdown.classList.add('open');
        console.log('Simple Admin JS: Opened dropdown');
    } else {
        console.log('Simple Admin JS: Kept dropdown closed');
    }
}

function closeAllDropdowns() {
    const openDropdowns = document.querySelectorAll('.nav-dropdown.open');
    if (openDropdowns.length > 0) {
        openDropdowns.forEach(dropdown => {
            dropdown.classList.remove('open');
        });
        console.log('Simple Admin JS: Closed', openDropdowns.length, 'dropdowns');
    }
}

// Test function for manual testing
window.testDropdown = function() {
    console.log('Manual test: Toggling first dropdown');
    const firstDropdown = document.querySelector('.nav-dropdown');
    if (firstDropdown) {
        firstDropdown.classList.toggle('open');
        console.log('Manual test: Dropdown is now', firstDropdown.classList.contains('open') ? 'OPEN' : 'CLOSED');
    } else {
        console.log('Manual test: No dropdown found');
    }
};

console.log('Simple Admin JS: Script loaded completely');