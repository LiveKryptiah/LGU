<?php
/**
 * Government Workflow OS — Office Tasks (Placeholder)
 * Step 1: Application shell with clean empty state
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_auth();

$user = current_user();
$base = get_app_base_url();

$pageTitle = 'Office Tasks';
$pageSubtitle = h($user['office_name']) . ' &bull; Departmental Workflows';
$currentPage = 'office-tasks';

require __DIR__ . '/../includes/header.php';
?>

<!-- Page Action Header -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-6); flex-wrap: wrap; gap: var(--space-4);">
  <div>
    <h2 class="typography-heading">Departmental &amp; Inter-Office Tasks</h2>
    <p class="typography-subtitle">Collaborative workflows across <?= h($user['office_name']) ?> and provincial offices.</p>
  </div>
  <div style="display: flex; gap: var(--space-3);">
    <button type="button" class="btn btn-secondary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="6 9 12 15 18 9"></polyline>
      </svg>
      <span>Select Office</span>
    </button>
    <button type="button" class="btn btn-primary" onclick="alert('Department workflow assignment will be enabled in future steps.')">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>Dispatch Office Task</span>
    </button>
  </div>
</div>

<!-- Empty State Component -->
<div class="empty-state">
  <div class="empty-state-icon">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
      <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
      <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
    </svg>
  </div>
  <h3 class="empty-state-title">No Active Inter-Office Workflows</h3>
  <p class="empty-state-desc">
    Office-wide workflows, shared transmittals between Provincial Treasury and Engineering, and collaborative endorsements will appear here.
  </p>
  <button type="button" class="btn btn-secondary" onclick="alert('Workflow coordination will be configured in upcoming steps.')">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="12" cy="12" r="10"></circle>
      <polyline points="12 6 12 12 14 14"></polyline>
    </svg>
    <span>Check Office Queue</span>
  </button>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
