<?php
/**
 * Government Workflow OS — Notifications Center (Placeholder)
 * Step 1: Application shell with clean empty state
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_auth();

$user = current_user();
$base = get_app_base_url();

$pageTitle = 'Notifications';
$pageSubtitle = 'Alerts, transmittal notices &amp; system advisories';
$currentPage = 'notifications';

require __DIR__ . '/../includes/header.php';
?>

<!-- Page Action Header -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-6); flex-wrap: wrap; gap: var(--space-4);">
  <div>
    <h2 class="typography-heading">Notification Center</h2>
    <p class="typography-subtitle">Statutory deadline reminders, inter-office communications, and system updates.</p>
  </div>
  <div style="display: flex; gap: var(--space-3);">
    <button type="button" class="btn btn-secondary btn-sm" onclick="alert('All notifications marked as read.')">
      <span>Mark all as read</span>
    </button>
  </div>
</div>

<!-- Empty State Component -->
<div class="empty-state">
  <div class="empty-state-icon">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
      <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
      <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
    </svg>
  </div>
  <h3 class="empty-state-title">You're All Caught Up</h3>
  <p class="empty-state-desc">
    There are no unread workplace notifications. New task requests, workflow approvals, and department transmittals will trigger instant alerts here.
  </p>
  <a href="<?= $base ?>/pages/dashboard.php" class="btn btn-secondary">
    <span>Return to Dashboard</span>
  </a>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
