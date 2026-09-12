<?php
/**
 * Government Workflow OS — Left Sidebar Navigation
 * Collapsible shell navigation component
 */

$user = current_user();
$base = get_app_base_url();
$activePage = $currentPage ?? 'dashboard';
?>

<!-- Mobile Sidebar Overlay Backdrop -->
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>

<aside class="app-sidebar" id="app-sidebar">
  <!-- Brand Header -->
  <div class="sidebar-header">
    <a href="<?= $base ?>/pages/dashboard.php" class="sidebar-brand" title="Government Workflow OS">
      <div class="brand-emblem">
        <img src="<?= $base ?>/assets/images/logo.svg" alt="Republic Emblem">
      </div>
      <div class="brand-info">
        <span class="brand-title">Workflow OS</span>
        <span class="brand-tagline">Provincial Gov</span>
      </div>
    </a>
    <button type="button" class="sidebar-collapse-btn" id="sidebar-collapse-btn" title="Toggle Sidebar [Ctrl+B]">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
        <line x1="9" y1="3" x2="9" y2="21"></line>
        <path d="m14 9-3 3 3 3"></path>
      </svg>
    </button>
  </div>

  <!-- Navigation Groups -->
  <div class="sidebar-body">
    <!-- Workplace Section -->
    <div class="nav-group">
      <div class="nav-section-title">Workplace</div>
      <ul class="nav-list">
        <!-- Dashboard -->
        <li class="nav-item">
          <a href="<?= $base ?>/pages/dashboard.php" class="nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>" title="Dashboard">
            <span class="nav-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
              </svg>
            </span>
            <span class="nav-label">Dashboard</span>
          </a>
        </li>

        <!-- My Tasks -->
        <li class="nav-item">
          <a href="<?= $base ?>/pages/tasks.php" class="nav-link <?= $activePage === 'tasks' ? 'active' : '' ?>" title="My Tasks">
            <span class="nav-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
                <path d="m9 12 2 2 4-4"></path>
              </svg>
            </span>
            <span class="nav-label">My Tasks</span>
            <span class="nav-pill-badge">12</span>
          </a>
        </li>

        <!-- Office Tasks -->
        <li class="nav-item">
          <a href="<?= $base ?>/pages/office-tasks.php" class="nav-link <?= $activePage === 'office-tasks' ? 'active' : '' ?>" title="Office Tasks">
            <span class="nav-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
              </svg>
            </span>
            <span class="nav-label">Office Tasks</span>
          </a>
        </li>

        <!-- Calendar -->
        <li class="nav-item">
          <a href="<?= $base ?>/pages/calendar.php" class="nav-link <?= $activePage === 'calendar' ? 'active' : '' ?>" title="Calendar">
            <span class="nav-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
            </span>
            <span class="nav-label">Calendar</span>
          </a>
        </li>
      </ul>
    </div>

    <!-- Organization Section -->
    <div class="nav-group">
      <div class="nav-section-title">Organization</div>
      <ul class="nav-list">
        <!-- Employees -->
        <li class="nav-item">
          <a href="<?= $base ?>/pages/employees.php" class="nav-link <?= $activePage === 'employees' ? 'active' : '' ?>" title="Employees">
            <span class="nav-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
            </span>
            <span class="nav-label">Employees</span>
          </a>
        </li>

        <!-- Activity Logs -->
        <li class="nav-item">
          <a href="<?= $base ?>/pages/activity.php" class="nav-link <?= $activePage === 'activity' ? 'active' : '' ?>" title="Activity">
            <span class="nav-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
              </svg>
            </span>
            <span class="nav-label">Activity</span>
          </a>
        </li>

        <!-- Notifications -->
        <li class="nav-item">
          <a href="<?= $base ?>/pages/notifications.php" class="nav-link <?= $activePage === 'notifications' ? 'active' : '' ?>" title="Notifications">
            <span class="nav-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
              </svg>
            </span>
            <span class="nav-label">Notifications</span>
            <span class="nav-pill-badge" style="background-color: var(--color-danger-bg); color: var(--color-danger);">3</span>
          </a>
        </li>

        <!-- Reports -->
        <li class="nav-item">
          <a href="<?= $base ?>/pages/reports.php" class="nav-link <?= $activePage === 'reports' ? 'active' : '' ?>" title="Reports">
            <span class="nav-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"></line>
                <line x1="12" y1="20" x2="12" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="14"></line>
              </svg>
            </span>
            <span class="nav-label">Reports</span>
          </a>
        </li>
      </ul>
    </div>
  </div>

  <!-- Bottom Utility Navigation -->
  <div class="sidebar-footer">
    <ul class="nav-list">
      <!-- Settings -->
      <li class="nav-item">
        <a href="<?= $base ?>/pages/settings.php" class="nav-link <?= $activePage === 'settings' ? 'active' : '' ?>" title="Settings">
          <span class="nav-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="3"></circle>
              <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
          </span>
          <span class="nav-label">Settings</span>
        </a>
      </li>

      <!-- Help -->
      <li class="nav-item">
        <a href="<?= $base ?>/pages/help.php" class="nav-link <?= $activePage === 'help' ? 'active' : '' ?>" title="Help">
          <span class="nav-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
          </span>
          <span class="nav-label">Help</span>
        </a>
      </li>
    </ul>

    <!-- Mini User Profile Card -->
    <a href="<?= $base ?>/pages/settings.php" class="sidebar-user-card" title="Account Settings">
      <div class="user-avatar-initials"><?= h($user['initials']) ?></div>
      <div class="user-meta-compact">
        <span class="user-name-compact"><?= h($user['full_name']) ?></span>
        <span class="user-role-compact"><?= h($user['position']) ?></span>
      </div>
    </a>

    <!-- Logout Item -->
    <a href="<?= $base ?>/logout.php" class="nav-link text-danger" title="Sign out from system" style="color: var(--color-danger);">
      <span class="nav-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
          <polyline points="16 17 21 12 16 7"></polyline>
          <line x1="21" y1="12" x2="9" y2="12"></line>
        </svg>
      </span>
      <span class="nav-label">Logout</span>
    </a>
  </div>
</aside>
