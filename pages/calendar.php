<?php
/**
 * Government Workflow OS — Calendar (Placeholder)
 * Step 1: Application shell with clean empty state
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_auth();

$user = current_user();
$base = get_app_base_url();

$pageTitle = 'Calendar';
$pageSubtitle = 'Office schedules, hearings, meetings &amp; statutory deadlines';
$currentPage = 'calendar';

require __DIR__ . '/../includes/header.php';
?>

<!-- Page Action Header -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-6); flex-wrap: wrap; gap: var(--space-4);">
  <div>
    <h2 class="typography-heading">Government Workplace Calendar</h2>
    <p class="typography-subtitle">Executive sessions, tax appraisal deadlines, and inter-office coordination.</p>
  </div>
  <div style="display: flex; gap: var(--space-3);">
    <button type="button" class="btn btn-secondary">Today</button>
    <button type="button" class="btn btn-primary" onclick="alert('Scheduling events will be introduced in future steps.')">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>Schedule Event</span>
    </button>
  </div>
</div>

<!-- Empty State Component -->
<div class="empty-state">
  <div class="empty-state-icon">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
      <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
      <line x1="16" y1="2" x2="16" y2="6"></line>
      <line x1="8" y1="2" x2="8" y2="6"></line>
      <line x1="3" y1="10" x2="21" y2="10"></line>
    </svg>
  </div>
  <h3 class="empty-state-title">No Scheduled Events Today</h3>
  <p class="empty-state-desc">
    Your office schedule is currently clear. Upcoming hearings, tax declaration assessment dates, and provincial committee meetings will be visualized here.
  </p>
  <button type="button" class="btn btn-secondary" onclick="alert('Calendar integration will be active in future steps.')">
    <span>View Month View</span>
  </button>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
