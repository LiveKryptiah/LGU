<?php
/**
 * Government Workflow OS — Activity Logs (Placeholder)
 * Step 1: Application shell with clean empty state
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_auth();

$user = current_user();
$base = get_app_base_url();

$pageTitle = 'Activity';
$pageSubtitle = 'Audit trail and administrative event log';
$currentPage = 'activity';

require __DIR__ . '/../includes/header.php';
?>

<!-- Page Header -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-6); flex-wrap: wrap; gap: var(--space-4);">
  <div>
    <h2 class="typography-heading">System &amp; Office Activity</h2>
    <p class="typography-subtitle">Immutable audit trail of transmittals, task completions, and authentication events.</p>
  </div>
  <div style="display: flex; gap: var(--space-3);">
    <button type="button" class="btn btn-secondary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
      </svg>
      <span>Filter Timeline</span>
    </button>
  </div>
</div>

<!-- Empty State Component -->
<div class="empty-state">
  <div class="empty-state-icon">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
    </svg>
  </div>
  <h3 class="empty-state-title">No Recent Audit Trail Events</h3>
  <p class="empty-state-desc">
    As administrative actions, tax assessment endorsements, and document approvals occur, complete chronological records with timestamps and actor details will be recorded here.
  </p>
  <button type="button" class="btn btn-secondary" onclick="alert('Audit trail logs will be recorded in future workflow steps.')">
    <span>Refresh Event Stream</span>
  </button>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
