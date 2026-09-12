<?php
/**
 * Government Workflow OS — Employees Directory (Placeholder)
 * Step 1: Application shell with directory structure and clean empty state
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_auth();

$user = current_user();
$base = get_app_base_url();

$pageTitle = 'Employees';
$pageSubtitle = 'Provincial Government Personnel Directory';
$currentPage = 'employees';

// Attempt to load employees from DB if available
$employees = [];
$db = get_db_connection();
if ($db) {
    try {
        $stmt = $db->query("SELECT e.*, o.name AS office_name FROM employees e LEFT JOIN offices o ON e.office_id = o.id ORDER BY e.id ASC");
        $employees = $stmt->fetchAll();
    } catch (Exception $e) {
        $employees = [];
    }
}

require __DIR__ . '/../includes/header.php';
?>

<!-- Page Action Header -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-6); flex-wrap: wrap; gap: var(--space-4);">
  <div>
    <h2 class="typography-heading">Personnel &amp; Staff Directory</h2>
    <p class="typography-subtitle">Official roster of provincial government administrative officers and employees.</p>
  </div>
  <div style="display: flex; gap: var(--space-3);">
    <button type="button" class="btn btn-secondary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
      </svg>
      <span>Filter by Office</span>
    </button>
    <button type="button" class="btn btn-primary" onclick="alert('Employee registration is not included in Step 1.')">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>Add Employee</span>
    </button>
  </div>
</div>

<?php if (!empty($employees)): ?>
  <!-- Employees Grid / Table Foundation -->
  <div class="card" style="margin-bottom: var(--space-6);">
    <div class="card-header">
      <div class="card-header-title">
        <span>Active Government Personnel (<?= count($employees) ?>)</span>
      </div>
    </div>
    <div class="card-body" style="padding: 0;">
      <div style="display: flex; flex-direction: column;">
        <?php foreach ($employees as $emp): ?>
          <div style="display: flex; align-items: center; justify-content: space-between; padding: var(--space-4) var(--space-5); border-bottom: 1px solid var(--color-border-subtle); gap: var(--space-4);">
            <div style="display: flex; align-items: center; gap: var(--space-3);">
              <div class="user-avatar-initials" style="width: 40px; height: 40px; font-size: 0.875rem;">
                <?= h(strtoupper(substr($emp['first_name'], 0, 1) . substr($emp['last_name'], 0, 1))) ?>
              </div>
              <div>
                <div style="font-weight: 600; font-size: 0.9375rem; color: var(--color-ink-primary);">
                  <?= h($emp['first_name'] . ' ' . $emp['last_name']) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--color-ink-muted);">
                  <?= h($emp['position']) ?> &bull; <?= h($emp['office_name'] ?? 'Provincial Assessor\'s Office') ?>
                </div>
              </div>
            </div>
            <div style="display: flex; align-items: center; gap: var(--space-3);">
              <span class="badge badge-success">
                <span class="badge-dot"></span> Active
              </span>
              <span style="font-size: 0.8125rem; color: var(--color-ink-secondary); font-family: var(--font-mono);">
                <?= h($emp['email']) ?>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php else: ?>
  <!-- Clean Empty State Component -->
  <div class="empty-state">
    <div class="empty-state-icon">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
        <circle cx="9" cy="7" r="4"></circle>
        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
      </svg>
    </div>
    <h3 class="empty-state-title">No Additional Staff Records Loaded</h3>
    <p class="empty-state-desc">
      Staff members across the 6 provincial offices will be indexed here once HRMO synchronization is initialized.
    </p>
    <button type="button" class="btn btn-secondary" onclick="alert('HR management will be expanded in future steps.')">
      <span>View HR Guidelines</span>
    </button>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
