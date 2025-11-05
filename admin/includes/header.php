<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Dashboard'; ?> - AluMaster Admin</title>
    <?php
    // Calculate relative path based on current file location
    $script_path = $_SERVER['SCRIPT_NAME'];
    $admin_pos = strpos($script_path, '/admin/');
    if ($admin_pos !== false) {
        $after_admin = substr($script_path, $admin_pos + 7); // +7 for '/admin/'
        $depth = substr_count($after_admin, '/');
        $base_path = str_repeat('../', $depth);
    } else {
        $base_path = '';
    }
    ?>
    <link rel="stylesheet" href="<?php echo $base_path; ?>../assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/admin.css">
    <link rel="icon" type="image/x-icon" href="<?php echo $base_path; ?>../assets/images/favicon.ico">
</head>
<body class="admin-body">
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="<?php echo $base_path; ?>../assets/images/Alumaster-logo.png" alt="AluMaster" class="sidebar-logo-image">
                    <span class="sidebar-logo-text">AluMaster</span>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                    <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo $base_path; ?>index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v3H8V5z"></path>
                            </svg>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item nav-dropdown">
                        <a href="#" class="nav-link nav-dropdown-toggle <?php echo strpos($_SERVER['PHP_SELF'], '/services/') !== false ? 'active' : ''; ?>">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="nav-text">Services</span>
                            <svg class="nav-dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </a>
                        <ul class="nav-dropdown-menu">
                            <li><a href="<?php echo $base_path; ?>services/list.php" class="nav-dropdown-link">All Services</a></li>
                            <li><a href="<?php echo $base_path; ?>services/add.php" class="nav-dropdown-link">Add Service</a></li>
                            <li><a href="<?php echo $base_path; ?>services/categories.php" class="nav-dropdown-link">Categories</a></li>
                        </ul>
                    </li>



                    <li class="nav-item">
                        <a href="<?php echo $base_path; ?>media/library.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/media/') !== false ? 'active' : ''; ?>">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="nav-text">Media Library</span>
                        </a>
                    </li>

                    <li class="nav-item nav-dropdown">
                        <a href="#" class="nav-link nav-dropdown-toggle <?php echo strpos($_SERVER['PHP_SELF'], '/pages/') !== false ? 'active' : ''; ?>">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="nav-text">Pages</span>
                            <svg class="nav-dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </a>
                        <ul class="nav-dropdown-menu">
                            <li><a href="<?php echo $base_path; ?>pages/homepage.php" class="nav-dropdown-link">Homepage</a></li>
                            <li><a href="<?php echo $base_path; ?>pages/about.php" class="nav-dropdown-link">About Page</a></li>
                            <li><a href="<?php echo $base_path; ?>pages/contact.php" class="nav-dropdown-link">Contact Page</a></li>
                            <li><a href="<?php echo $base_path; ?>pages/list.php" class="nav-dropdown-link">All Pages</a></li>
                            <li><a href="<?php echo $base_path; ?>pages/add.php" class="nav-dropdown-link">Add Page</a></li>
                        </ul>
                    </li>

                    <li class="nav-item nav-dropdown">
                        <a href="#" class="nav-link nav-dropdown-toggle <?php echo strpos($_SERVER['PHP_SELF'], '/navigation/') !== false ? 'active' : ''; ?>">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                            </svg>
                            <span class="nav-text">Navigation</span>
                            <svg class="nav-dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </a>
                        <ul class="nav-dropdown-menu">
                            <li><a href="<?php echo $base_path; ?>navigation/list.php" class="nav-dropdown-link">All Menus</a></li>
                            <li><a href="<?php echo $base_path; ?>navigation/add.php" class="nav-dropdown-link">Add Menu</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo $base_path; ?>inquiries/list.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/inquiries/') !== false ? 'active' : ''; ?>">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="nav-text">Inquiries</span>
                            <?php
                            // Get unread inquiries count
                            try {
                                $db = new Database();
                                $conn = $db->getConnection();
                                $stmt = $conn->prepare("SELECT COUNT(*) FROM inquiries WHERE status = 'unread'");
                                $stmt->execute();
                                $unread_count = $stmt->fetchColumn();
                                if ($unread_count > 0) {
                                    echo '<span class="nav-badge">' . $unread_count . '</span>';
                                }
                            } catch (Exception $e) {
                                // Ignore error
                            }
                            ?>
                        </a>
                    </li>

                    <li class="nav-item nav-dropdown">
                        <a href="#" class="nav-link nav-dropdown-toggle <?php echo strpos($_SERVER['PHP_SELF'], '/settings/') !== false ? 'active' : ''; ?>">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="nav-text">Settings</span>
                            <svg class="nav-dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </a>
                        <ul class="nav-dropdown-menu">
                            <li><a href="<?php echo $base_path; ?>settings/general.php" class="nav-dropdown-link">General Settings</a></li>
                            <li><a href="<?php echo $base_path; ?>settings/email.php" class="nav-dropdown-link">Email Settings</a></li>
                            <li><a href="<?php echo $base_path; ?>settings/seo.php" class="nav-dropdown-link">SEO Settings</a></li>
                            <?php if (check_admin_permission('admin')): ?>
                            <li><a href="<?php echo $base_path; ?>settings/users.php" class="nav-dropdown-link">Admin Users</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="sidebar-user-avatar">
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="sidebar-user-info">
                        <div class="sidebar-user-name"><?php echo htmlspecialchars($current_admin['name']); ?></div>
                        <div class="sidebar-user-role"><?php echo ucfirst($current_admin['role']); ?></div>
                    </div>
                    <div class="sidebar-user-dropdown">
                        <button class="sidebar-user-toggle" aria-label="User menu">
                            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="sidebar-user-menu">
                            <a href="<?php echo $base_path; ?>settings/profile.php" class="sidebar-user-menu-item">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                My Profile
                            </a>
                            <a href="<?php echo $base_path; ?>../index.php" target="_blank" class="sidebar-user-menu-item">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                View Website
                            </a>
                            <a href="<?php echo $base_path; ?>logout.php" class="sidebar-user-menu-item">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="admin-main">
            <!-- Top Bar -->
            <header class="admin-topbar">
                <div class="topbar-left">
                    <button class="mobile-sidebar-toggle" id="mobileSidebarToggle" aria-label="Toggle sidebar">
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="topbar-breadcrumb">
                        <h1 class="topbar-title"><?php echo $page_title ?? 'Dashboard'; ?></h1>
                        <?php if (isset($breadcrumb)): ?>
                        <nav class="breadcrumb">
                            <?php foreach ($breadcrumb as $item): ?>
                                <?php if (isset($item['url'])): ?>
                                    <a href="<?php echo $item['url']; ?>" class="breadcrumb-link"><?php echo $item['title']; ?></a>
                                <?php else: ?>
                                    <span class="breadcrumb-current"><?php echo $item['title']; ?></span>
                                <?php endif; ?>
                                <?php if ($item !== end($breadcrumb)): ?>
                                    <span class="breadcrumb-separator">/</span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </nav>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="topbar-right">
                    <div class="topbar-actions">
                        <a href="<?php echo $base_path; ?>../index.php" target="_blank" class="topbar-action" title="View Website">
                            <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="admin-content">
                <div class="content-wrapper">