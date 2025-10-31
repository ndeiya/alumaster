<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Dropdown Debug';

include 'includes/header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">Dropdown Debug Test</h2>
    </div>
    <div class="card-content">
        <p>This page is for testing dropdown functionality.</p>
        <p>Check the browser console for any JavaScript errors.</p>
        
        <h3>Test Dropdown</h3>
        <div class="nav-item nav-dropdown">
            <a href="#" class="nav-link nav-dropdown-toggle">
                <span>Test Dropdown</span>
                <svg class="nav-dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </a>
            <ul class="nav-dropdown-menu">
                <li><a href="#" class="nav-dropdown-link">Test Item 1</a></li>
                <li><a href="#" class="nav-dropdown-link">Test Item 2</a></li>
                <li><a href="#" class="nav-dropdown-link">Test Item 3</a></li>
            </ul>
        </div>
        
        <button onclick="testDropdown()" class="btn btn-primary">Test Dropdown Manually</button>
    </div>
</div>

<script>
function testDropdown() {
    console.log('Testing dropdown functionality...');
    
    const dropdownToggles = document.querySelectorAll('.nav-dropdown-toggle');
    console.log('Found dropdown toggles:', dropdownToggles.length);
    
    dropdownToggles.forEach((toggle, index) => {
        console.log(`Toggle ${index}:`, toggle);
        console.log('Parent element:', toggle.parentElement);
        console.log('Has open class:', toggle.parentElement.classList.contains('open'));
    });
    
    // Test manual toggle
    const testDropdown = document.querySelector('.nav-dropdown');
    if (testDropdown) {
        testDropdown.classList.toggle('open');
        console.log('Manually toggled dropdown. Open:', testDropdown.classList.contains('open'));
    }
}

// Check if admin.js is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    console.log('adminUtils available:', typeof window.adminUtils !== 'undefined');
    
    setTimeout(() => {
        console.log('Checking dropdown setup after 1 second...');
        const dropdownToggles = document.querySelectorAll('.nav-dropdown-toggle');
        console.log('Dropdown toggles found:', dropdownToggles.length);
        
        dropdownToggles.forEach((toggle, index) => {
            console.log(`Toggle ${index} click listeners:`, getEventListeners ? getEventListeners(toggle) : 'DevTools required');
        });
    }, 1000);
});
</script>

<?php include 'includes/footer.php'; ?>