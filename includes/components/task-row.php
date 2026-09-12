<?php
/**
 * Government Workflow OS — Task Row Component
 * Renders an accessible, responsive task item row compliant with DESIGN.md
 */

require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/status-badge.php';
require_once __DIR__ . '/priority-badge.php';

if (!function_exists('render_task_row')) {
    /**
     * Render a task item row.
     *
     * @param array $task
     * @param array $options [
     *   'show_priority' => bool,
     *   'show_status'   => bool,
     *   'show_deadline' => bool,
     *   'highlight_urgency' => bool,
     *   'is_last'       => bool
     * ]
     * @param bool $returnHtml
     * @return string
     */
    function render_task_row($task, $options = [], $returnHtml = false) {
        $showPriority = $options['show_priority'] ?? true;
        $showStatus   = $options['show_status'] ?? true;
        $showDeadline = $options['show_deadline'] ?? true;
        $isLast       = !empty($options['is_last']);

        $id       = (int)($task['id'] ?? 0);
        $title    = htmlspecialchars($task['title'] ?? 'Untitled Task', ENT_QUOTES, 'UTF-8');
        $status   = $task['status'] ?? 'pending';
        $priority = $task['priority'] ?? 'normal';
        $dueDate  = $task['due_date'] ?? null;
        $office   = htmlspecialchars($task['office_name'] ?? 'Provincial Office', ENT_QUOTES, 'UTF-8');

        // Deadline analysis
        $deadlineInfo = format_relative_deadline($dueDate, $status);

        // Status indicator dot color
        $dotColor = 'var(--color-ink-muted)';
        if ($status === 'completed') {
            $dotColor = 'var(--color-success)';
        } elseif ($deadlineInfo['is_urgent']) {
            $dotColor = $deadlineInfo['class'] === 'badge-danger' ? 'var(--color-danger)' : 'var(--color-warning)';
        } elseif ($status === 'in_progress') {
            $dotColor = 'var(--color-info)';
        }

        $borderStyle = $isLast ? '' : 'border-bottom: 1px solid var(--color-border-subtle);';

        ob_start();
        ?>
        <div class="task-item-row" style="display: flex; align-items: center; justify-content: space-between; padding: var(--space-4) var(--space-5); gap: var(--space-4); <?= $borderStyle ?> transition: background-color 0.15s ease;">
          <div style="display: flex; align-items: flex-start; gap: var(--space-3); min-width: 0; flex: 1;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background-color: <?= $dotColor ?>; margin-top: 6px; flex-shrink: 0;"></div>
            <div style="min-width: 0; flex: 1;">
              <div style="font-weight: 600; font-size: 0.875rem; color: var(--color-ink-primary); line-height: 1.4; word-break: break-word;">
                <?= $title ?>
              </div>
              <div style="display: flex; align-items: center; flex-wrap: wrap; gap: var(--space-2); font-size: 0.75rem; color: var(--color-ink-muted); margin-top: 4px;">
                <span>#<?= str_pad((string)$id, 4, '0', STR_PAD_LEFT) ?></span>
                <span>&bull;</span>
                <span><?= $office ?></span>
                <?php if (!empty($task['description'])): ?>
                  <span>&bull;</span>
                  <span style="max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= htmlspecialchars($task['description'], ENT_QUOTES, 'UTF-8') ?>
                  </span>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="task-item-meta" style="display: flex; align-items: center; gap: var(--space-2); flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end;">
            <?php if ($showPriority): ?>
              <?= render_priority_badge($priority, true) ?>
            <?php endif; ?>

            <?php if ($showStatus): ?>
              <?= render_status_badge($status, true) ?>
            <?php endif; ?>

            <?php if ($showDeadline && !empty($dueDate)): ?>
              <span class="badge <?= $deadlineInfo['class'] ?>" title="Due: <?= htmlspecialchars($dueDate, ENT_QUOTES, 'UTF-8') ?>">
                <?= $deadlineInfo['label'] ?>
              </span>
            <?php endif; ?>
          </div>
        </div>
        <?php
        $output = ob_get_clean();

        if ($returnHtml) {
            return $output;
        }
        echo $output;
        return '';
    }
}

if (!function_exists('render_task_table_row')) {
    /**
     * Render a task item as a table row with clickable navigation and quick status transition.
     *
     * @param array $task
     * @param array $options ['csrf_token' => string, 'base_url' => string]
     * @param bool $returnHtml
     * @return string
     */
    function render_task_table_row($task, $options = [], $returnHtml = false) {
        $id          = (int)($task['id'] ?? 0);
        $title       = htmlspecialchars($task['title'] ?? 'Untitled Task', ENT_QUOTES, 'UTF-8');
        $desc        = htmlspecialchars($task['description'] ?? '', ENT_QUOTES, 'UTF-8');
        $office      = htmlspecialchars($task['office_name'] ?? 'Provincial Office', ENT_QUOTES, 'UTF-8');
        $status      = $task['status'] ?? 'pending';
        $priority    = $task['priority'] ?? 'normal';
        $dueDate     = $task['due_date'] ?? null;
        $updatedAt   = $task['updated_at'] ?? $task['created_at'] ?? '';
        $csrfToken   = $options['csrf_token'] ?? csrf_token();
        $base        = $options['base_url'] ?? get_app_base_url();
        $viewUrl     = "{$base}/pages/task-view.php?id={$id}";

        // Deadline analysis
        $deadlineInfo = format_relative_deadline($dueDate, $status);
        $timeAgo = format_relative_time($updatedAt);
        $formattedDue = !empty($dueDate) ? date('M j, Y', strtotime($dueDate)) : 'No deadline';

        // Allowed transitions:
        // pending -> in_progress, completed
        // in_progress -> pending, completed
        // completed -> in_progress
        $transitionOptions = [];
        if ($status === 'pending') {
            $transitionOptions = ['in_progress' => 'In Progress', 'completed' => 'Completed'];
        } elseif ($status === 'in_progress') {
            $transitionOptions = ['pending' => 'Pending', 'completed' => 'Completed'];
        } elseif ($status === 'completed') {
            $transitionOptions = ['in_progress' => 'In Progress'];
        }

        ob_start();
        ?>
        <tr class="task-table-row" data-task-id="<?= $id ?>" onclick="if (!event.target.closest('.no-row-click')) window.location.href='<?= $viewUrl ?>';">
          <!-- Column 1: Task Title & Description -->
          <td class="task-col-main">
            <div class="task-title-cell">
              <a href="<?= $viewUrl ?>" class="task-title-text" onclick="event.stopPropagation();">
                <?= $title ?>
              </a>
              <?php if (!empty($desc)): ?>
                <span class="task-desc-preview"><?= $desc ?></span>
              <?php endif; ?>
              <div class="task-mobile-meta">
                <span>#<?= str_pad((string)$id, 4, '0', STR_PAD_LEFT) ?></span>
                <span>&bull;</span>
                <span><?= $office ?></span>
              </div>
            </div>
          </td>

          <!-- Column 2: Office (desktop) -->
          <td class="task-col-office" style="white-space: nowrap;">
            <span style="font-size: 0.8125rem; color: var(--color-ink-secondary);"><?= $office ?></span>
          </td>

          <!-- Column 3: Status & Transition -->
          <td class="task-col-status" style="white-space: nowrap;">
            <div style="display: flex; align-items: center; gap: var(--space-2); flex-wrap: wrap;">
              <?= render_status_badge($status, true) ?>
              
              <?php if (!empty($transitionOptions)): ?>
                <form class="status-transition-form no-row-click" method="POST" action="<?= $base ?>/api/update-task-status.php" onsubmit="return handleStatusSubmit(event, this);" onclick="event.stopPropagation();">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="task_id" value="<?= $id ?>">
                  <select name="status" class="status-quick-select" onchange="this.form.requestSubmit ? this.form.requestSubmit() : this.form.submit();" title="Quick change status">
                    <option value="" disabled selected>Move to &hellip;</option>
                    <?php foreach ($transitionOptions as $val => $lbl): ?>
                      <option value="<?= $val ?>"><?= $lbl ?></option>
                    <?php endforeach; ?>
                  </select>
                </form>
              <?php endif; ?>
            </div>
          </td>

          <!-- Column 4: Priority -->
          <td class="task-col-priority" style="white-space: nowrap;">
            <?= render_priority_badge($priority, true) ?>
          </td>

          <!-- Column 5: Due Date -->
          <td class="task-col-due" style="white-space: nowrap;">
            <div style="display: flex; flex-direction: column; gap: 2px;">
              <span style="font-size: 0.8125rem; font-weight: var(--font-weight-medium); color: var(--color-ink-primary);">
                <?= $formattedDue ?>
              </span>
              <?php if (!empty($dueDate)): ?>
                <span class="badge <?= $deadlineInfo['class'] ?>" style="font-size: 0.625rem; padding: 1px 6px; align-self: flex-start;">
                  <?= $deadlineInfo['label'] ?>
                </span>
              <?php endif; ?>
            </div>
          </td>

          <!-- Column 6: Updated -->
          <td class="task-col-updated" style="white-space: nowrap;">
            <span style="font-size: 0.75rem; color: var(--color-ink-muted);" title="<?= htmlspecialchars($updatedAt, ENT_QUOTES, 'UTF-8') ?>">
              <?= $timeAgo ?>
            </span>
          </td>
        </tr>
        <?php
        $output = ob_get_clean();

        if ($returnHtml) {
            return $output;
        }
        echo $output;
        return '';
    }
}

if (isset($taskRowData) && is_array($taskRowData)) {
    render_task_row($taskRowData, $taskRowOptions ?? []);
}

