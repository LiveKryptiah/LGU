<?php
/**
 * Government Workflow OS — Task Detail View
 * Step 3: Detailed task information and authorization verification
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/components/status-badge.php';
require_once __DIR__ . '/../includes/components/priority-badge.php';
require_once __DIR__ . '/../includes/components/empty-state.php';

// Authentication guard
require_auth();

$user = current_user();
$base = get_app_base_url();
$orgId = (int)($user['organization_id'] ?? 1);
$userId = (int)($user['id'] ?? 1);

$taskId = (int)($_GET['id'] ?? 0);
$task = null;
$accessDenied = false;
$notFound = false;

// Auto-migration check
ensure_dashboard_tables();

if ($taskId <= 0) {
    $notFound = true;
    http_response_code(404);
} else {
    $db = get_db_connection();
    if ($db) {
        try {
            // First check if the task exists anywhere in the system
            $stmt = $db->prepare("SELECT t.*, o.name AS office_name, o.description AS office_desc 
                                  FROM `tasks` t 
                                  LEFT JOIN `offices` o ON t.office_id = o.id 
                                  WHERE t.id = ? 
                                  LIMIT 1");
            $stmt->execute([$taskId]);
            $rawTask = $stmt->fetch();

            if (!$rawTask) {
                $notFound = true;
                http_response_code(404);
            } elseif ((int)$rawTask['organization_id'] !== $orgId || (int)$rawTask['assigned_to'] !== $userId) {
                // Strict scoping: task does not belong to the authenticated employee
                $accessDenied = true;
                http_response_code(403);
            } else {
                $task = $rawTask;
            }
        } catch (Exception $e) {
            error_log("Task view query error: " . $e->getMessage());
            $notFound = true;
        }
    } else {
        // Fallback demo task if database is offline
        if ($taskId === 1) {
            $task = [
                'id'          => 1,
                'title'       => 'Review incoming assessment documents',
                'description' => "Conduct technical verification of incoming land transfer tax assessments and title deeds from district offices.\n\nEnsure compliance with the Provincial Revenue Code and real property classification schedules before endorsing to Treasury.",
                'status'      => 'in_progress',
                'priority'    => 'urgent',
                'due_date'    => date('Y-m-d', strtotime('-2 days')),
                'created_at'  => date('Y-m-d H:i:s', strtotime('-4 days')),
                'updated_at'  => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'office_name' => "Provincial Assessor's Office"
            ];
        } else {
            $notFound = true;
            http_response_code(404);
        }
    }
}

// Page Metadata
$pageTitle = $task ? h($task['title']) : ($accessDenied ? 'Access Denied' : 'Task Not Found');
$pageSubtitle = $task ? "Workflow Task #{$taskId} &bull; " . h($task['office_name']) : 'My Tasks';
$currentPage = 'tasks';

require __DIR__ . '/../includes/header.php';
?>

<!-- Back Navigation Breadcrumb -->
<div style="margin-bottom: var(--space-5);">
  <a href="<?= $base ?>/pages/tasks.php" class="btn btn-ghost btn-sm" style="padding-left: 0;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <line x1="19" y1="12" x2="5" y2="12"></line>
      <polyline points="12 19 5 12 12 5"></polyline>
    </svg>
    <span>&larr; Back to My Tasks</span>
  </a>
</div>

<?php if ($accessDenied): ?>
  <!-- 403 Access Denied View -->
  <div class="card" style="max-width: 600px; margin: var(--space-8) auto; text-align: center; padding: var(--space-8);">
    <div style="width: 64px; height: 64px; border-radius: 50%; background-color: var(--color-danger-bg); color: var(--color-danger); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4);">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
      </svg>
    </div>
    <h3 class="typography-heading" style="color: var(--color-danger); margin-bottom: var(--space-2);">Access Denied</h3>
    <p class="typography-subtitle" style="margin-bottom: var(--space-6); color: var(--color-ink-muted);">
      You do not have permission to view this task. Tasks are restricted strictly to the assigned employee within the designated local government office.
    </p>
    <a href="<?= $base ?>/pages/tasks.php" class="btn btn-secondary">
      Return to My Tasks
    </a>
  </div>

<?php elseif ($notFound): ?>
  <!-- 404 Not Found View -->
  <div class="card" style="max-width: 600px; margin: var(--space-8) auto; text-align: center; padding: var(--space-8);">
    <div style="width: 64px; height: 64px; border-radius: 50%; background-color: var(--color-canvas-muted); color: var(--color-ink-muted); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4);">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="8" x2="12" y2="12"></line>
        <line x1="12" y1="16" x2="12.01" y2="16"></line>
      </svg>
    </div>
    <h3 class="typography-heading" style="margin-bottom: var(--space-2);">Task Not Found</h3>
    <p class="typography-subtitle" style="margin-bottom: var(--space-6); color: var(--color-ink-muted);">
      The requested task record does not exist or has been deleted from the office workflow index.
    </p>
    <a href="<?= $base ?>/pages/tasks.php" class="btn btn-secondary">
      Return to My Tasks
    </a>
  </div>

<?php else: ?>
  <?php
  // Format dates & deadline
  $deadlineInfo = format_relative_deadline($task['due_date'], $task['status']);
  $formattedDue = !empty($task['due_date']) ? date('F j, Y', strtotime($task['due_date'])) : 'No deadline assigned';
  $formattedCreated = !empty($task['created_at']) ? date('M j, Y &bull; g:i A', strtotime($task['created_at'])) : '—';
  $formattedUpdated = !empty($task['updated_at']) ? date('M j, Y &bull; g:i A', strtotime($task['updated_at'])) : '—';
  $timeAgoUpdated = format_relative_time($task['updated_at']);

  // Allowed transitions
  $currStatus = $task['status'];
  $transitionOptions = [];
  if ($currStatus === 'pending') {
      $transitionOptions = ['in_progress' => 'In Progress', 'completed' => 'Completed'];
  } elseif ($currStatus === 'in_progress') {
      $transitionOptions = ['pending' => 'Pending', 'completed' => 'Completed'];
  } elseif ($currStatus === 'completed') {
      $transitionOptions = ['in_progress' => 'In Progress'];
  }
  ?>

  <!-- Task Detail Surface Grid -->
  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-6); margin-bottom: var(--space-8);" class="dashboard-grid">
    <!-- Left Main Column: Task Details -->
    <div style="display: flex; flex-direction: column; gap: var(--space-6);">
      <!-- Task Header Card -->
      <div class="card">
        <div class="card-header">
          <div class="card-header-title">
            <span style="color: var(--color-ink-faint); font-family: var(--font-mono);">#<?= str_pad((string)$task['id'], 4, '0', STR_PAD_LEFT) ?></span>
            <span>&bull;</span>
            <span><?= h($task['office_name']) ?></span>
          </div>
          <div class="card-header-actions" style="gap: var(--space-2);">
            <?= render_priority_badge($task['priority'], true) ?>
            <?= render_status_badge($task['status'], true) ?>
          </div>
        </div>

        <div class="card-body">
          <h1 class="typography-heading" style="font-size: 1.375rem; color: var(--color-ink-primary); margin-bottom: var(--space-4); line-height: 1.4;">
            <?= h($task['title']) ?>
          </h1>

          <div style="border-top: 1px solid var(--color-border-subtle); padding-top: var(--space-4); margin-top: var(--space-4);">
            <h4 style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-ink-muted); margin-bottom: var(--space-2); font-weight: 600;">
              Task Description &amp; Scope
            </h4>
            <?php if (!empty($task['description'])): ?>
              <div style="font-size: 0.9375rem; color: var(--color-ink-secondary); line-height: 1.65; white-space: pre-line;">
                <?= h($task['description']) ?>
              </div>
            <?php else: ?>
              <p class="text-muted" style="font-style: italic; font-size: 0.875rem;">
                No description was provided for this task assignment.
              </p>
            <?php endif; ?>
          </div>
        </div>

        <div class="card-footer">
          <span class="typography-caption" style="color: var(--color-ink-muted);">
            Last updated <?= $timeAgoUpdated ?> (<?= $formattedUpdated ?>)
          </span>
          <a href="<?= $base ?>/pages/tasks.php" class="btn btn-secondary btn-sm">
            &larr; Back to Task List
          </a>
        </div>
      </div>
    </div>

    <!-- Right Sidebar Column: Metadata & Status Controls -->
    <div style="display: flex; flex-direction: column; gap: var(--space-6);">
      <!-- Status Transition Card -->
      <div class="card">
        <div class="card-header">
          <div class="card-header-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
              <polyline points="9 11 12 14 22 4"></polyline>
              <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
            </svg>
            <span>Update Progress</span>
          </div>
        </div>
        <div class="card-body">
          <div style="margin-bottom: var(--space-3);">
            <span style="font-size: 0.75rem; color: var(--color-ink-muted);">Current State:</span>
            <div style="margin-top: 4px;">
              <?= render_status_badge($task['status'], true) ?>
            </div>
          </div>

          <?php if (!empty($transitionOptions)): ?>
            <form method="POST" action="<?= $base ?>/api/update-task-status.php" style="display: flex; flex-direction: column; gap: var(--space-3); margin-top: var(--space-4);">
              <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
              <input type="hidden" name="task_id" value="<?= $task['id'] ?>">

              <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="target-status">Select Next Status</label>
                <select name="status" id="target-status" class="form-select" style="height: 38px;">
                  <?php foreach ($transitionOptions as $optVal => $optLabel): ?>
                    <option value="<?= $optVal ?>">Transition to <?= $optLabel ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center; height: 36px;">
                Confirm Status Update
              </button>
            </form>
          <?php else: ?>
            <p style="font-size: 0.8125rem; color: var(--color-ink-muted); margin-top: var(--space-2);">
              This task is in a final state and cannot be transitioned further.
            </p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Task Details Metadata Card -->
      <div class="card">
        <div class="card-header">
          <div class="card-header-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
            </svg>
            <span>Assignment Info</span>
          </div>
        </div>
        <div class="card-body" style="display: flex; flex-direction: column; gap: var(--space-3); font-size: 0.8125rem;">
          <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--color-border-subtle); padding-bottom: 8px;">
            <span class="text-muted">Assigned To:</span>
            <strong><?= h($user['full_name']) ?></strong>
          </div>
          <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--color-border-subtle); padding-bottom: 8px;">
            <span class="text-muted">Office:</span>
            <span><?= h($task['office_name']) ?></span>
          </div>
          <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--color-border-subtle); padding-bottom: 8px;">
            <span class="text-muted">Priority:</span>
            <?= render_priority_badge($task['priority'], true) ?>
          </div>
          <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--color-border-subtle); padding-bottom: 8px;">
            <span class="text-muted">Deadline:</span>
            <div style="text-align: right;">
              <div><?= $formattedDue ?></div>
              <?php if (!empty($task['due_date'])): ?>
                <span class="badge <?= $deadlineInfo['class'] ?>" style="font-size: 0.625rem; margin-top: 2px;">
                  <?= $deadlineInfo['label'] ?>
                </span>
              <?php endif; ?>
            </div>
          </div>
          <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--color-border-subtle); padding-bottom: 8px;">
            <span class="text-muted">Created:</span>
            <span><?= $formattedCreated ?></span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span class="text-muted">Updated:</span>
            <span><?= $formattedUpdated ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
