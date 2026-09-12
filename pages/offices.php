<?php
/**
 * Government Workflow OS — Provincial Offices Directory (Placeholder)
 * Step 1: Application shell with clean empty state
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_auth();

$user = current_user();
$base = get_app_base_url();

$pageTitle = 'Provincial Offices';
$pageSubtitle = 'Provincial Government Department Structure';
$currentPage = 'offices';

require __DIR__ . '/../includes/header.php';
?>

<!-- Page Header -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-6); flex-wrap: wrap; gap: var(--space-4);">
  <div>
    <h2 class="typography-heading">Government Offices &amp; Departments</h2>
    <p class="typography-subtitle">Organization chart and administrative departments under the Provincial Government.</p>
  </div>
</div>

<!-- Empty State Component -->
<div class="empty-state">
  <div class="empty-state-icon">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
      <path d="M3 21h18"></path>
      <path d="M5 21V7l8-4v18"></path>
      <path d="M19 21V11l-6-4"></path>
      <path d="M9 9v.01"></path>
      <path d="M9 12v.01"></path>
      <path d="M9 15v.01"></path>
      <path d="M9 18v.01"></path>
    </svg>
  </div>
  <h3 class="empty-state-title">Provincial Offices Directory</h3>
  <p class="empty-state-desc">
    Detailed departmental structures for the 6 mandated offices (Assessor's, Treasurer's, Engineering, HRMO, GSO, and Planning) will be accessible here in Step 2.
  </p>
  <a href="<?= $base ?>/pages/dashboard.php" class="btn btn-primary">
    <span>Return to Dashboard</span>
  </a>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
