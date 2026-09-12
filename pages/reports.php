<?php
/**
 * Government Workflow OS — Reports & Analytics (Placeholder)
 * Step 1: Application shell with clean empty state
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_auth();

$user = current_user();
$base = get_app_base_url();

$pageTitle = 'Reports';
$pageSubtitle = 'Provincial administrative analytics &amp; statutory compliance';
$currentPage = 'reports';

require __DIR__ . '/../includes/header.php';
?>

<!-- Page Action Header -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-6); flex-wrap: wrap; gap: var(--space-4);">
  <div>
    <h2 class="typography-heading">Administrative Reports</h2>
    <p class="typography-subtitle">Generate performance summaries, assessment statistics, and office transmittal records.</p>
  </div>
  <div style="display: flex; gap: var(--space-3);">
    <button type="button" class="btn btn-secondary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
        <line x1="16" y1="2" x2="16" y2="6"></line>
        <line x1="8" y1="2" x2="8" y2="6"></line>
      </svg>
      <span>Select Date Range</span>
    </button>
    <button type="button" class="btn btn-primary" onclick="alert('Report generation will be built in future steps.')">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
        <polyline points="7 10 12 15 17 10"></polyline>
        <line x1="12" y1="15" x2="12" y2="3"></line>
      </svg>
      <span>Generate Report</span>
    </button>
  </div>
</div>

<!-- Empty State Component -->
<div class="empty-state">
  <div class="empty-state-icon">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
      <line x1="18" y1="20" x2="18" y2="10"></line>
      <line x1="12" y1="20" x2="12" y2="4"></line>
      <line x1="6" y1="20" x2="6" y2="14"></line>
    </svg>
  </div>
  <h3 class="empty-state-title">No Generated Reports Yet</h3>
  <p class="empty-state-desc">
    Monthly real property tax assessments, departmental task completion rates, and inter-office transmittals can be compiled and exported in subsequent releases.
  </p>
  <button type="button" class="btn btn-secondary" onclick="alert('Standard compliance templates will be available in later steps.')">
    <span>View Standard COA / DILG Report Templates</span>
  </button>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
