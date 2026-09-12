<?php
/**
 * Government Workflow OS — Help & Documentation (Placeholder)
 * Step 1: Application shell with clean empty state and user guidance
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_auth();

$user = current_user();
$base = get_app_base_url();

$pageTitle = 'Help & Support';
$pageSubtitle = 'Workplace user manual, statutory references &amp; IT help desk';
$currentPage = 'help';

require __DIR__ . '/../includes/header.php';
?>

<!-- Page Header -->
<div style="margin-bottom: var(--space-6);">
  <h2 class="typography-heading">Help Desk &amp; Documentation</h2>
  <p class="typography-subtitle">Guidelines for provincial government office workflows and system navigation.</p>
</div>

<!-- Empty State Component -->
<div class="empty-state">
  <div class="empty-state-icon">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="10"></circle>
      <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
      <line x1="12" y1="17" x2="12.01" y2="17"></line>
    </svg>
  </div>
  <h3 class="empty-state-title">Government Workflow OS Knowledge Base</h3>
  <p class="empty-state-desc">
    Official user manuals, standard operating procedures (SOPs) for the 6 provincial departments, and IT support ticket submission will be available here in upcoming modules.
  </p>
  <a href="<?= $base ?>/pages/dashboard.php" class="btn btn-primary">
    <span>Return to Dashboard</span>
  </a>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
