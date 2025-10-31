<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Simple Dropdown Test';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropdown Test</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-body">
    <div style="padding: 20px; background: #1a202c; min-height: 100vh;">
        <h1 style="color: white; margin-bottom: 30px;">Dropdown Test</h1>
        
        <!-- Test Dropdown 1 -->
        <div class="nav-item nav-dropdown" style="margin-bottom: 20px;">
            <a href="#" class="nav-link nav-dropdown-toggle" style="display: flex; align-items: center; padding: 12px 20px; color: #a0aec0; text-decoration: none; background: #2d3748; border-radius: 8px;">
                <span class="nav-text">Test Services</span>
                <svg class="nav-dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </a>
            <ul class="nav-dropdown-menu">
                <li><a href="#" class="nav-dropdown-link">All Services</a></li>
                <li><a href="#" class="nav-dropdown-link">Add Service</a></li>
                <li><a href="#" class="nav-dropdown-link">Categories</a></li>
            </ul>
        </div>
        
        <!-- Test Dropdown 2 -->
        <div class="nav-item nav-dropdown" style="margin-bottom: 20px;">
            <a href="#" class="nav-link nav-dropdown-toggle" style="display: flex; align-items: center; padding: 12px 20px; color: #a0aec0; text-decoration: none; background: #2d3748; border-radius: 8px;">
                <span class="nav-text">Test Settings</span>
                <svg class="nav-dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </a>
            <ul class="nav-dropdown-menu">
                <li><a href="#" class="nav-dropdown-link">General Settings</a></li>
                <li><a href="#" class="nav-dropdown-link">SEO Settings</a></li>
                <li><a href="#" class="nav-dropdown-link">Admin Users</a></li>
            </ul>
        </div>
        
        <button onclick="manualTest()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer;">Manual Toggle Test</button>
        
        <div id="debug-info" style="margin-top: 20px; padding: 15px; background: #374151; color: white; border-radius: 8px; font-family: monospace; font-size: 12px;"></div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="assets/js/admin.js"></script>
    
    <script>
    let debugLog = [];
    
    function log(message) {
        debugLog.push(`${new Date().toLocaleTimeString()}: ${message}`);
        updateDebugInfo();
    }
    
    function manualTest() {
        log('Manual test button clicked');
        const dropdown = document.querySelector('.nav-dropdown');
        dropdown.classList.toggle('open');
        log(`Dropdown manually toggled. Now: ${dropdown.classList.contains('open') ? 'OPEN' : 'CLOSED'}`);
        updateDebugInfo();
    }
    
    function updateDebugInfo() {
        const debugDiv = document.getElementById('debug-info');
        const dropdowns = document.querySelectorAll('.nav-dropdown');
        const toggles = document.querySelectorAll('.nav-dropdown-toggle');
        
        let info = `Dropdowns found: ${dropdowns.length}\n`;
        info += `Toggles found: ${toggles.length}\n`;
        info += `Admin.js loaded: ${typeof initializeAdminInterface !== 'undefined'}\n`;
        info += `AdminUtils available: ${typeof window.adminUtils !== 'undefined'}\n\n`;
        
        dropdowns.forEach((dropdown, index) => {
            info += `Dropdown ${index + 1}: ${dropdown.classList.contains('open') ? 'OPEN' : 'CLOSED'}\n`;
        });
        
        info += '\nRecent Log:\n';
        info += debugLog.slice(-5).join('\n');
        
        debugDiv.textContent = info;
    }
    
    // Test if admin.js functions are available
    document.addEventListener('DOMContentLoaded', function() {
        log('DOM Content Loaded');
        
        setTimeout(() => {
            log('Checking admin.js initialization...');
            
            // Test if dropdowns have event listeners
            const toggles = document.querySelectorAll('.nav-dropdown-toggle');
            toggles.forEach((toggle, index) => {
                log(`Adding test listener to toggle ${index + 1}`);
                toggle.addEventListener('click', function(e) {
                    log(`Toggle ${index + 1} clicked directly!`);
                });
            });
            
            updateDebugInfo();
        }, 1000);
    });
    
    // Update debug info every 2 seconds
    setInterval(updateDebugInfo, 2000);
    
    // Initial update
    setTimeout(updateDebugInfo, 500);
    </script>
</body>
</html>