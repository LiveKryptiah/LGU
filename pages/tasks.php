<?php
/**
 * Government Workflow OS — My Tasks (Placeholder)
 * Step 1: Application shell with clean empty state
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_auth();

$user = current_user();
$base = get_app_base_url();

$pageTitle = 'My Tasks';
$pageSubtitle = 'Individual assignments and pending action items';
$currentPage = 'tasks';

require __DIR__ . '/../includes/header.php';
?>

<!-- Page Action Header -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-6); flex-wrap: wrap; gap: var(--space-4);">
  <div>
    <h2 class="typography-heading">My Assigned Tasks</h2>
    <p class="typography-subtitle">Manage personal administrative tasks, appraisals, and document reviews.</p>
  </div>
  <div style="display: flex; gap: var(--space-3);">
    <button type="button" class="btn btn-secondary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
      </svg>
      <span>Filter</span>
    </button>
    <button type="button" class="btn btn-primary" onclick="alert('Task creation will be enabled in Step 2.')">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>New Task</span>
    </button>
  </div>
</div>

<!-- Clean Empty State Component -->
<div class="empty-state">
  <div class="empty-state-icon">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
      <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
      <path d="m9 12 2 2 4-4"></path>
    </svg>
  </div>
  <h3 class="empty-state-title">No Pending Task Assignments</h3>
  <p class="empty-state-desc">
    You have caught up with all individual assignments. Tasks assigned directly to your desk or endorsed from other departments will appear here.
  </p>
  <button type="button" class="btn btn-primary" onclick="alert('Task management functionality will be introduced in the upcoming step.')">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <line x1="12" y1="5" x2="12" y2="19"></line>
      <line x1="5" y1="12" x2="19" y2="12"></line>
    </svg>
    <span>Create Personal Task</span>
  </button>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
