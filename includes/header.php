<?php
/**
 * Government Workflow OS — Top Application Header
 * Header with Page Title, Global Search, Notifications & User Menu
 */

$user = current_user();
$base = get_app_base_url();
$pageTitle = $pageTitle ?? 'Dashboard';
$pageSubtitle = $pageSubtitle ?? 'Provincial Government Workplace';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($pageTitle) ?> &bull; Government Workflow OS</title>
  
  <!-- Design System & Layout Stylesheets -->
  <link rel="stylesheet" href="<?= $base ?>/assets/css/design-system.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/layout.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/components.css">
  
  <link rel="icon" type="image/svg+xml" href="<?= $base ?>/assets/images/logo.svg">
</head>
<body>

<div class="app-shell">
  <!-- Left Collapsible Sidebar -->
  <?php require __DIR__ . '/sidebar.php'; ?>

  <!-- Main Content Wrapper -->
  <div class="app-main">
    <!-- Top Header -->
    <header class="app-header">
      <!-- Left: Mobile Toggle & Page Title -->
      <div class="header-left">
        <button type="button" class="mobile-nav-toggle" id="mobile-nav-toggle" aria-label="Open Navigation">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>

        <div class="header-page-title-group">
          <h1 class="header-page-title"><?= h($pageTitle) ?></h1>
          <span class="header-breadcrumbs"><?= h($pageSubtitle) ?></span>
        </div>
      </div>

      <!-- Right: Search, Notifications, Office Indicator, User Profile -->
      <div class="header-right">
        <!-- Global Search Box -->
        <div class="header-search-box">
          <span class="header-search-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
          </span>
          <input 
            type="text" 
            class="header-search-input" 
            id="header-search-input" 
            placeholder="Search tasks, offices..." 
            autocomplete="off"
          >
          <span class="search-shortcut-badge">Ctrl K</span>
        </div>

        <!-- Office Badge Indicator -->
        <div class="header-office-pill" title="Current Active Office Assignment">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
          </svg>
          <span><?= h($user['office_name']) ?></span>
        </div>

        <!-- Notifications Dropdown Trigger -->
        <div class="dropdown">
          <button 
            type="button" 
            class="header-icon-btn" 
            data-dropdown-trigger="notifications-menu" 
            title="View Notifications"
            aria-label="Notifications"
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span class="unread-notification-dot"></span>
          </button>

          <!-- Notifications Dropdown Menu -->
          <div class="dropdown-menu notifications-dropdown" id="notifications-menu">
            <div class="dropdown-header" style="display: flex; justify-content: space-between; align-items: center;">
              <span>NOTIFICATIONS (3)</span>
              <a href="<?= $base ?>/pages/notifications.php" style="font-size: 0.6875rem; text-transform: none;">View all</a>
            </div>
            <div class="dropdown-divider"></div>
            
            <a href="<?= $base ?>/pages/tasks.php" class="notif-item unread">
              <div class="notif-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
              </div>
              <div>
                <div class="notif-text"><strong>Assessor's Q3 Real Property Tax Appraisal</strong> due today at 5:00 PM.</div>
                <div class="notif-time">20 minutes ago</div>
              </div>
            </a>

            <a href="<?= $base ?>/pages/office-tasks.php" class="notif-item unread">
              <div class="notif-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
              </div>
              <div>
                <div class="notif-text">Provincial Treasurer's Office shared monthly revenue collection summary.</div>
                <div class="notif-time">1 hour ago</div>
              </div>
            </a>

            <a href="<?= $base ?>/pages/calendar.php" class="notif-item unread">
              <div class="notif-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                </svg>
              </div>
              <div>
                <div class="notif-text">Executive Coordination Meeting scheduled tomorrow at 9:00 AM.</div>
                <div class="notif-time">3 hours ago</div>
              </div>
            </a>

            <div class="dropdown-divider"></div>
            <a href="<?= $base ?>/pages/notifications.php" class="dropdown-item" style="justify-content: center; font-weight: 600; color: var(--color-primary);">
              Go to Notification Center &rarr;
            </a>
          </div>
        </div>

        <!-- User Profile Dropdown Trigger -->
        <div class="dropdown">
          <button 
            type="button" 
            class="header-user-btn" 
            data-dropdown-trigger="user-profile-menu" 
            aria-label="User Profile Options"
          >
            <div class="user-avatar-sm"><?= h($user['initials']) ?></div>
            <div class="user-meta-header" style="text-align: left;">
              <div class="user-name-header"><?= h($user['full_name']) ?></div>
              <div class="user-role-header"><?= h($user['position']) ?></div>
            </div>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-ink-muted);">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>

          <!-- User Profile Dropdown Menu -->
          <div class="dropdown-menu" id="user-profile-menu">
            <div class="dropdown-header">
              Signed in as<br>
              <strong style="color: var(--color-ink-primary); font-size: 0.8125rem;"><?= h($user['email']) ?></strong>
            </div>
            <div class="dropdown-divider"></div>
            
            <a href="<?= $base ?>/pages/settings.php" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <span>My Profile &amp; Account</span>
            </a>

            <a href="<?= $base ?>/pages/settings.php#office" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
              </svg>
              <span>Office Configuration</span>
            </a>

            <a href="<?= $base ?>/pages/help.php" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
              </svg>
              <span>User Manual &amp; Help Desk</span>
            </a>

            <div class="dropdown-divider"></div>

            <a href="<?= $base ?>/logout.php" class="dropdown-item danger-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
              </svg>
              <span>Sign Out</span>
            </a>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Dynamic Page Content Container -->
    <main class="app-content">
