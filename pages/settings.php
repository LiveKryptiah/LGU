<?php
/**
 * Government Workflow OS — Settings & Profile (Placeholder)
 * Step 1: Application shell with profile view and settings tabs
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_auth();

$user = current_user();
$base = get_app_base_url();

$pageTitle = 'Settings';
$pageSubtitle = 'Account preferences and office configuration';
$currentPage = 'settings';

require __DIR__ . '/../includes/header.php';
?>

<!-- Page Header -->
<div style="margin-bottom: var(--space-6);">
  <h2 class="typography-heading">Settings &amp; Preferences</h2>
  <p class="typography-subtitle">Manage administrative account credentials, office assignment, and notification options.</p>
</div>

<!-- Settings Layout Grid -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-6);">
  <!-- Left Column: User Profile Overview Card -->
  <div class="card">
    <div class="card-header">
      <div class="card-header-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-primary">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
          <circle cx="12" cy="7" r="4"></circle>
        </svg>
        <span>Administrative Personnel Profile</span>
      </div>
      <span class="badge badge-success">
        <span class="badge-dot"></span> Active Account
      </span>
    </div>

    <div class="card-body">
      <div style="display: flex; align-items: center; gap: var(--space-4); margin-bottom: var(--space-6); padding-bottom: var(--space-4); border-bottom: 1px solid var(--color-border-subtle);">
        <div class="user-avatar-initials" style="width: 56px; height: 56px; font-size: 1.25rem;">
          <?= h($user['initials']) ?>
        </div>
        <div>
          <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--color-ink-primary);">
            <?= h($user['full_name']) ?>
          </h3>
          <p style="font-size: 0.8125rem; color: var(--color-ink-muted);">
            <?= h($user['position']) ?> &bull; <?= h($user['office_name']) ?>
          </p>
          <div style="margin-top: 6px;">
            <span class="badge badge-neutral" style="font-family: var(--font-mono); font-size: 0.6875rem;">
              <?= h($user['email']) ?>
            </span>
          </div>
        </div>
      </div>

      <!-- Detail Rows -->
      <div style="display: flex; flex-direction: column; gap: var(--space-3); font-size: 0.875rem;">
        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--color-border-subtle);">
          <span class="text-muted">Organization / LGU:</span>
          <strong><?= h($user['organization_name']) ?></strong>
        </div>

        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--color-border-subtle);">
          <span class="text-muted">Assigned Department:</span>
          <strong><?= h($user['office_name']) ?></strong>
        </div>

        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--color-border-subtle);">
          <span class="text-muted">System Role:</span>
          <span class="badge badge-info" style="text-transform: capitalize;"><?= h($user['role']) ?></span>
        </div>

        <div style="display: flex; justify-content: space-between; padding: 8px 0;">
          <span class="text-muted">Authentication Standard:</span>
          <span>Bcrypt Hash (PHP <code>password_hash</code>)</span>
        </div>
      </div>
    </div>

    <div class="card-footer">
      <span class="typography-caption">Profile editing and password change will be unlocked in subsequent steps.</span>
      <button type="button" class="btn btn-secondary btn-sm" onclick="alert('Profile editing is disabled in Step 1.')">
        Edit Profile
      </button>
    </div>
  </div>

  <!-- Right Column: System & Environment Information -->
  <div style="display: flex; flex-direction: column; gap: var(--space-6);">
    <!-- System Environment Card -->
    <div class="card">
      <div class="card-header">
        <div class="card-header-title">
          <span>System Environment</span>
        </div>
      </div>
      <div class="card-body" style="font-size: 0.8125rem; display: flex; flex-direction: column; gap: 10px;">
        <div style="display: flex; justify-content: space-between;">
          <span class="text-muted">Application:</span>
          <strong>Workflow OS</strong>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span class="text-muted">Version:</span>
          <span>v1.0.0 (Step 1)</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span class="text-muted">Database Engine:</span>
          <span>MySQL (utf8mb4)</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span class="text-muted">Server Stack:</span>
          <span>PHP + MySQL + HTML5</span>
        </div>
      </div>
      <div class="card-footer">
        <a href="<?= $base ?>/install.php" class="btn btn-secondary btn-sm" style="width: 100%;">
          Database Setup Utility &rarr;
        </a>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
