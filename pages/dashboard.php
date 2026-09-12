<?php
/**
 * Government Workflow OS — Authenticated Dashboard
 * Step 1: Visual foundation and summary cards
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Authentication guard
require_auth();

$user = current_user();
$base = get_app_base_url();

// Set page metadata for header/sidebar
$pageTitle = 'Dashboard';
$pageSubtitle = 'Provincial Assessor\'s Office &bull; Overview';
$currentPage = 'dashboard';

// Compute time-appropriate greeting
$hour = (int)date('G');
if ($hour < 12) {
    $greetingTime = 'Good morning';
} elseif ($hour < 18) {
    $greetingTime = 'Good afternoon';
} else {
    $greetingTime = 'Good evening';
}

require __DIR__ . '/../includes/header.php';
?>

<!-- Welcome Greeting Hero -->
<div style="margin-bottom: var(--space-6); display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: var(--space-4);">
  <div>
    <div style="display: flex; align-items: center; gap: var(--space-3); margin-bottom: var(--space-1);">
      <h2 class="typography-display" style="color: var(--color-ink-primary);">
        <?= $greetingTime ?>, <?= h($user['first_name']) ?>
      </h2>
      <span class="badge badge-info">
        <span class="badge-dot"></span> Active Session
      </span>
    </div>
    <p class="typography-subtitle" style="font-size: 0.9375rem;">
      Here's what's happening across your office today.
    </p>
  </div>

  <!-- Primary Workplace Quick Actions -->
  <div style="display: flex; align-items: center; gap: var(--space-3);">
    <a href="<?= $base ?>/pages/tasks.php" class="btn btn-secondary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
        <line x1="16" y1="2" x2="16" y2="6"></line>
        <line x1="8" y1="2" x2="8" y2="6"></line>
      </svg>
      <span>View Schedule</span>
    </a>
    <a href="<?= $base ?>/pages/tasks.php" class="btn btn-primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>Create Task</span>
    </a>
  </div>
</div>

<!-- Four Foundational Summary Cards -->
<div class="stat-card-grid">
  <!-- Card 1: My Tasks -->
  <div class="stat-card">
    <div class="stat-card-top">
      <span class="stat-card-label">My Tasks</span>
      <div class="stat-card-icon-container stat-card-icon-primary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
          <path d="m9 12 2 2 4-4"></path>
        </svg>
      </div>
    </div>
    <div class="stat-card-value">12</div>
    <div class="stat-card-trend text-primary">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"></circle>
        <polyline points="12 6 12 12 14 14"></polyline>
      </svg>
      <span>Active assignments</span>
    </div>
  </div>

  <!-- Card 2: Due Today -->
  <div class="stat-card">
    <div class="stat-card-top">
      <span class="stat-card-label">Due Today</span>
      <div class="stat-card-icon-container stat-card-icon-warning">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
      </div>
    </div>
    <div class="stat-card-value">4</div>
    <div class="stat-card-trend text-warning">
      <span class="badge badge-warning" style="font-size: 0.6875rem;">
        <span class="badge-dot"></span> Requires attention
      </span>
    </div>
  </div>

  <!-- Card 3: In Progress -->
  <div class="stat-card">
    <div class="stat-card-top">
      <span class="stat-card-label">In Progress</span>
      <div class="stat-card-icon-container stat-card-icon-info">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="20" x2="18" y2="10"></line>
          <line x1="12" y1="20" x2="12" y2="4"></line>
          <line x1="6" y1="20" x2="6" y2="14"></line>
        </svg>
      </div>
    </div>
    <div class="stat-card-value">7</div>
    <div class="stat-card-trend" style="color: var(--color-info);">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
        <polyline points="17 6 23 6 23 12"></polyline>
      </svg>
      <span>Under review / processing</span>
    </div>
  </div>

  <!-- Card 4: Overdue -->
  <div class="stat-card">
    <div class="stat-card-top">
      <span class="stat-card-label">Overdue</span>
      <div class="stat-card-icon-container stat-card-icon-danger">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
      </div>
    </div>
    <div class="stat-card-value" style="color: var(--color-danger);">2</div>
    <div class="stat-card-trend text-danger">
      <span class="badge badge-danger" style="font-size: 0.6875rem;">
        <span class="badge-dot"></span> Action needed
      </span>
    </div>
  </div>
</div>

<!-- Workplace Content Grid (Two-Column Layout) -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-6); margin-bottom: var(--space-6);">
  <!-- Left Column: Priority Workplace Tasks Overview -->
  <div class="card">
    <div class="card-header">
      <div class="card-header-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
          <polyline points="14 2 14 8 20 8"></polyline>
          <line x1="16" y1="13" x2="8" y2="13"></line>
          <line x1="16" y1="17" x2="8" y2="17"></line>
          <polyline points="10 9 9 9 8 9"></polyline>
        </svg>
        <span>Assessor's Office &bull; Active Workflows</span>
      </div>
      <div class="card-header-actions">
        <a href="<?= $base ?>/pages/tasks.php" class="btn btn-ghost btn-sm">View All &rarr;</a>
      </div>
    </div>
    
    <div class="card-body" style="padding: 0;">
      <!-- Structured Table / List Items -->
      <div style="display: flex; flex-direction: column;">
        <!-- Item 1 -->
        <div style="display: flex; align-items: center; justify-content: space-between; padding: var(--space-4) var(--space-5); border-bottom: 1px solid var(--color-border-subtle); gap: var(--space-4);">
          <div style="display: flex; align-items: flex-start; gap: var(--space-3);">
            <div style="width: 8px; height: 8px; border-radius: 50%; background-color: var(--color-danger); margin-top: 6px; flex-shrink: 0;"></div>
            <div>
              <div style="font-weight: 600; font-size: 0.875rem; color: var(--color-ink-primary);">
                Real Property Unit Appraisal — Barangay San Isidro Commercial Zone
              </div>
              <div style="font-size: 0.75rem; color: var(--color-ink-muted); margin-top: 2px;">
                Workflow ID: WF-2026-0842 &bull; Assigned to: Juan Dela Cruz
              </div>
            </div>
          </div>
          <div style="display: flex; align-items: center; gap: var(--space-3); flex-shrink: 0;">
            <span class="badge badge-danger">Overdue (2d)</span>
            <span style="font-size: 0.75rem; color: var(--color-ink-muted);">Sep 04</span>
          </div>
        </div>

        <!-- Item 2 -->
        <div style="display: flex; align-items: center; justify-content: space-between; padding: var(--space-4) var(--space-5); border-bottom: 1px solid var(--color-border-subtle); gap: var(--space-4);">
          <div style="display: flex; align-items: flex-start; gap: var(--space-3);">
            <div style="width: 8px; height: 8px; border-radius: 50%; background-color: var(--color-warning); margin-top: 6px; flex-shrink: 0;"></div>
            <div>
              <div style="font-weight: 600; font-size: 0.875rem; color: var(--color-ink-primary);">
                Consolidated Tax Declaration Verification for Provincial Treasury
              </div>
              <div style="font-size: 0.75rem; color: var(--color-ink-muted); margin-top: 2px;">
                Workflow ID: WF-2026-0855 &bull; In coordination with: Provincial Treasurer's Office
              </div>
            </div>
          </div>
          <div style="display: flex; align-items: center; gap: var(--space-3); flex-shrink: 0;">
            <span class="badge badge-warning">Due Today</span>
            <span style="font-size: 0.75rem; color: var(--color-ink-muted);">5:00 PM</span>
          </div>
        </div>

        <!-- Item 3 -->
        <div style="display: flex; align-items: center; justify-content: space-between; padding: var(--space-4) var(--space-5); border-bottom: 1px solid var(--color-border-subtle); gap: var(--space-4);">
          <div style="display: flex; align-items: flex-start; gap: var(--space-3);">
            <div style="width: 8px; height: 8px; border-radius: 50%; background-color: var(--color-info); margin-top: 6px; flex-shrink: 0;"></div>
            <div>
              <div style="font-weight: 600; font-size: 0.875rem; color: var(--color-ink-primary);">
                Q3 Provincial Assessment Roll Validation &amp; Boundary Synchronization
              </div>
              <div style="font-size: 0.75rem; color: var(--color-ink-muted); margin-top: 2px;">
                Workflow ID: WF-2026-0861 &bull; Technical Review Phase
              </div>
            </div>
          </div>
          <div style="display: flex; align-items: center; gap: var(--space-3); flex-shrink: 0;">
            <span class="badge badge-info">In Progress</span>
            <span style="font-size: 0.75rem; color: var(--color-ink-muted);">Sep 09</span>
          </div>
        </div>

        <!-- Item 4 -->
        <div style="display: flex; align-items: center; justify-content: space-between; padding: var(--space-4) var(--space-5); gap: var(--space-4);">
          <div style="display: flex; align-items: flex-start; gap: var(--space-3);">
            <div style="width: 8px; height: 8px; border-radius: 50%; background-color: var(--color-success); margin-top: 6px; flex-shrink: 0;"></div>
            <div>
              <div style="font-weight: 600; font-size: 0.875rem; color: var(--color-ink-primary);">
                Annual Property Tax Exemption List Endorsement to Governor's Office
              </div>
              <div style="font-size: 0.75rem; color: var(--color-ink-muted); margin-top: 2px;">
                Workflow ID: WF-2026-0839 &bull; Ready for Executive Signature
              </div>
            </div>
          </div>
          <div style="display: flex; align-items: center; gap: var(--space-3); flex-shrink: 0;">
            <span class="badge badge-success">Completed</span>
            <span style="font-size: 0.75rem; color: var(--color-ink-muted);">Sep 05</span>
          </div>
        </div>
      </div>
    </div>

    <div class="card-footer">
      <span class="typography-caption">Showing 4 of 12 workplace assignments</span>
      <a href="<?= $base ?>/pages/tasks.php" class="btn btn-secondary btn-sm">Manage All Tasks</a>
    </div>
  </div>

  <!-- Right Column: Office & Schedule Overview -->
  <div style="display: flex; flex-direction: column; gap: var(--space-6);">
    <!-- Office Info Surface -->
    <div class="card">
      <div class="card-header">
        <div class="card-header-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
          </svg>
          <span>Assigned Office</span>
        </div>
        <span class="badge badge-neutral">LGU Provincial</span>
      </div>
      <div class="card-body">
        <div style="font-weight: 700; font-size: 1rem; color: var(--color-ink-primary); margin-bottom: 4px;">
          <?= h($user['office_name']) ?>
        </div>
        <div style="font-size: 0.8125rem; color: var(--color-ink-muted); line-height: 1.5; margin-bottom: var(--space-4);">
          Responsible for establishing a systematic method of real property assessment and appraisal for local taxation.
        </div>
        
        <div style="border-top: 1px solid var(--color-border-subtle); padding-top: var(--space-3); display: flex; flex-direction: column; gap: 8px; font-size: 0.8125rem;">
          <div style="display: flex; justify-content: space-between;">
            <span class="text-muted">Officer-in-Charge:</span>
            <strong><?= h($user['full_name']) ?></strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span class="text-muted">Designation:</span>
            <span><?= h($user['position']) ?></span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span class="text-muted">Department Staff:</span>
            <span>8 Active Officers</span>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <a href="<?= $base ?>/pages/employees.php" class="btn btn-secondary btn-sm" style="width: 100%;">
          View Office Staff Directory &rarr;
        </a>
      </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="card">
      <div class="card-header">
        <div class="card-header-title">
          <span>Quick Actions</span>
        </div>
      </div>
      <div class="card-body" style="display: flex; flex-direction: column; gap: var(--space-2);">
        <a href="<?= $base ?>/pages/tasks.php" class="btn btn-secondary btn-sm" style="justify-content: flex-start; height: 36px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          <span>Log New Property Record</span>
        </a>
        <a href="<?= $base ?>/pages/office-tasks.php" class="btn btn-secondary btn-sm" style="justify-content: flex-start; height: 36px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9 11 12 14 22 4"></polyline>
            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
          </svg>
          <span>Endorse Document to Treasury</span>
        </a>
        <a href="<?= $base ?>/pages/reports.php" class="btn btn-secondary btn-sm" style="justify-content: flex-start; height: 36px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
          </svg>
          <span>Export Monthly Assessment Summary</span>
        </a>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
