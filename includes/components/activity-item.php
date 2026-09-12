<?php
/**
 * Government Workflow OS — Activity Item Component
 * Renders activity timeline entry with initials, description, and relative time
 */

require_once __DIR__ . '/../auth.php';

if (!function_exists('render_activity_item')) {
    /**
     * Render an activity log item.
     *
     * @param array $activity
     * @param bool $isLast
     * @param bool $returnHtml
     * @return string
     */
    function render_activity_item($activity, $isLast = false, $returnHtml = false) {
        $description = htmlspecialchars($activity['description'] ?? '', ENT_QUOTES, 'UTF-8');
        $action      = htmlspecialchars($activity['action'] ?? '', ENT_QUOTES, 'UTF-8');
        $entityType  = htmlspecialchars($activity['entity_type'] ?? 'log', ENT_QUOTES, 'UTF-8');
        $createdAt   = $activity['created_at'] ?? '';
        $timeAgo     = format_relative_time($createdAt);

        // Derive author name and initials
        $firstName = $activity['first_name'] ?? '';
        $lastName  = $activity['last_name'] ?? '';
        $fullName  = trim($firstName . ' ' . $lastName);

        if (empty($fullName)) {
            // Extract from description if format is "First Last ..."
            $parts = explode(' ', $activity['description'] ?? '');
            if (count($parts) >= 2) {
                $fullName = $parts[0] . ' ' . $parts[1];
                $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
            } else {
                $fullName = 'Office Staff';
                $initials = 'OS';
            }
        } else {
            $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
        }

        // Action-specific icon or colors
        $actionBg = 'var(--color-primary-light)';
        $actionColor = 'var(--color-primary)';
        if (strpos($action, 'ASSIGNED') !== false || strpos($action, 'STATUS') !== false) {
            $actionBg = 'var(--color-info-bg)';
            $actionColor = 'var(--color-info)';
        } elseif (strpos($action, 'COMPLETED') !== false) {
            $actionBg = 'var(--color-success-bg)';
            $actionColor = 'var(--color-success)';
        } elseif (strpos($action, 'TRANSMITTAL') !== false) {
            $actionBg = 'var(--color-warning-bg)';
            $actionColor = 'var(--color-warning)';
        }

        $borderStyle = $isLast ? '' : 'border-bottom: 1px solid var(--color-border-subtle);';

        ob_start();
        ?>
        <div class="activity-timeline-item" style="display: flex; align-items: flex-start; gap: var(--space-4); padding: var(--space-4) var(--space-5); <?= $borderStyle ?> transition: background-color 0.15s ease;">
          <div style="width: 36px; height: 36px; min-width: 36px; border-radius: var(--radius-full); background-color: <?= $actionBg ?>; color: <?= $actionColor ?>; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: var(--font-weight-bold); border: 1px solid var(--color-border-subtle);">
            <?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?>
          </div>

          <div style="flex: 1; min-width: 0;">
            <div style="font-size: 0.875rem; color: var(--color-ink-primary); line-height: 1.45; word-break: break-word;">
              <?= $description ?>
            </div>
            <div style="display: flex; align-items: center; gap: var(--space-3); margin-top: 4px; font-size: 0.75rem; color: var(--color-ink-muted);">
              <span style="display: flex; align-items: center; gap: 4px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <?= $timeAgo ?>
              </span>
              <span>&bull;</span>
              <span class="badge badge-neutral" style="font-size: 0.625rem; padding: 1px 6px; text-transform: uppercase;">
                <?= $entityType ?>
              </span>
            </div>
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

if (isset($activityItemData) && is_array($activityItemData)) {
    render_activity_item($activityItemData, !empty($activityIsLast));
}
