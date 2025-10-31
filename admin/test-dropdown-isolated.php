<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';
require_once 'includes/auth-check.php';

$page_title = 'Isolated Dropdown Test';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Isolated Dropdown Test</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background-color: #2d3748;
            color: white;
        }
        
        .test-dropdown {
            position: relative;
            margin-bottom: 20px;
        }
        
        .test-dropdown-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            background-color: #4a5568;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            cursor: pointer;
            user-select: none;
        }
        
        .test-dropdown-toggle:hover {
            background-color: #5a6578;
        }
        
        .test-dropdown-arrow {
            width: 16px;
            height: 16px;
            transition: transform 0.2s ease;
        }
        
        .test-dropdown.open .test-dropdown-arrow {
            transform: rotate(180deg);
        }
        
        .test-dropdown-menu {
            list-style: none;
            margin: 0;
            padding: 0;
            background-color: #374151;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        
        .test-dropdown.open .test-dropdown-menu {
            max-height: 200px;
        }
        
        .test-dropdown-link {
            display: block;
            padding: 10px 20px;
            color: #d1d5db;
            text-decoration: none;
            border-bottom: 1px solid #4b5563;
        }
        
        .test-dropdown-link:hover {
            background-color: #4b5563;
            color: white;
        }
        
        .debug-info {
            margin-top: 20px;
            padding: 15px;
            background: #1f2937;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
            white-space: pre-wrap;
        }
        
        .btn {
            padding: 10px 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h1>Isolated Dropdown Test</h1>
    <p>This test uses completely isolated CSS and JavaScript to test dropdown functionality.</p>
    
    <!-- Test Dropdown 1 -->
    <div class="test-dropdown" id="dropdown1">
        <a href="#" class="test-dropdown-toggle">
            <span>Test Services Dropdown</span>
            <svg class="test-dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </a>
        <ul class="test-dropdown-menu">
            <li><a href="#" class="test-dropdown-link">All Services</a></li>
            <li><a href="#" class="test-dropdown-link">Add Service</a></li>
            <li><a href="#" class="test-dropdown-link">Categories</a></li>
        </ul>
    </div>
    
    <!-- Test Dropdown 2 -->
    <div class="test-dropdown" id="dropdown2">
        <a href="#" class="test-dropdown-toggle">
            <span>Test Settings Dropdown</span>
            <svg class="test-dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </a>
        <ul class="test-dropdown-menu">
            <li><a href="#" class="test-dropdown-link">General Settings</a></li>
            <li><a href="#" class="test-dropdown-link">SEO Settings</a></li>
            <li><a href="#" class="test-dropdown-link">Admin Users</a></li>
        </ul>
    </div>
    
    <button class="btn" onclick="toggleDropdown('dropdown1')">Toggle Dropdown 1</button>
    <button class="btn" onclick="toggleDropdown('dropdown2')">Toggle Dropdown 2</button>
    <button class="btn" onclick="closeAllDropdowns()">Close All</button>
    
    <div id="debug-info" class="debug-info"></div>

    <script>
        let debugLog = [];
        
        function log(message) {
            debugLog.push(`${new Date().toLocaleTimeString()}: ${message}`);
            updateDebugInfo();
        }
        
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            dropdown.classList.toggle('open');
            log(`Manually toggled ${dropdownId}: ${dropdown.classList.contains('open') ? 'OPEN' : 'CLOSED'}`);
        }
        
        function closeAllDropdowns() {
            document.querySelectorAll('.test-dropdown.open').forEach(dropdown => {
                dropdown.classList.remove('open');
            });
            log('Closed all dropdowns manually');
        }
        
        function updateDebugInfo() {
            const debugDiv = document.getElementById('debug-info');
            const dropdowns = document.querySelectorAll('.test-dropdown');
            const toggles = document.querySelectorAll('.test-dropdown-toggle');
            
            let info = `Test Results:\n`;
            info += `Dropdowns found: ${dropdowns.length}\n`;
            info += `Toggles found: ${toggles.length}\n\n`;
            
            dropdowns.forEach((dropdown, index) => {
                info += `Dropdown ${index + 1}: ${dropdown.classList.contains('open') ? 'OPEN' : 'CLOSED'}\n`;
            });
            
            info += '\nRecent Activity:\n';
            info += debugLog.slice(-8).join('\n');
            
            debugDiv.textContent = info;
        }
        
        // Initialize dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            log('DOM loaded - initializing isolated dropdowns');
            
            const dropdownToggles = document.querySelectorAll('.test-dropdown-toggle');
            
            dropdownToggles.forEach((toggle, index) => {
                log(`Setting up toggle ${index + 1}`);
                
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    log(`Toggle ${index + 1} clicked`);
                    
                    const dropdown = this.parentElement;
                    const wasOpen = dropdown.classList.contains('open');
                    
                    // Close all dropdowns first
                    document.querySelectorAll('.test-dropdown.open').forEach(openDropdown => {
                        openDropdown.classList.remove('open');
                    });
                    
                    // Toggle current dropdown
                    if (!wasOpen) {
                        dropdown.classList.add('open');
                        log(`Opened dropdown ${index + 1}`);
                    } else {
                        log(`Closed dropdown ${index + 1}`);
                    }
                });
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.test-dropdown')) {
                    const openDropdowns = document.querySelectorAll('.test-dropdown.open');
                    if (openDropdowns.length > 0) {
                        openDropdowns.forEach(dropdown => dropdown.classList.remove('open'));
                        log('Closed dropdowns by clicking outside');
                    }
                }
            });
            
            log('Dropdown initialization complete');
            updateDebugInfo();
        });
        
        // Update debug info every 3 seconds
        setInterval(updateDebugInfo, 3000);
    </script>
</body>
</html>