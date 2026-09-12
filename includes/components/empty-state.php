<?php
/**
 * Government Workflow OS — Empty State Component
 * Renders consistent, clean empty state when collections have zero items
 */

if (!function_exists('render_empty_state')) {
    /**
     * Render empty state markup.
     *
     * @param array $config [
     *   'icon_svg'     => string|null,
     *   'title'        => string,
     *   'description'  => string,
     *   'action_url'   => string|null,
     *   'action_label' => string|null,
     * ]
     * @param bool $returnHtml
     * @return string
     */
    function render_empty_state($config = [], $returnHtml = false) {
        $title       = htmlspecialchars($config['title'] ?? 'No records found', ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($config['description'] ?? 'There are currently no items to display in this list.', ENT_QUOTES, 'UTF-8');
        $actionUrl   = !empty($config['action_url']) ? htmlspecialchars($config['action_url'], ENT_QUOTES, 'UTF-8') : null;
        $actionLabel = !empty($config['action_label']) ? htmlspecialchars($config['action_label'], ENT_QUOTES, 'UTF-8') : 'Get Started';

        $iconSvg = $config['icon_svg'] ?? '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="9" y1="15" x2="15" y2="15"></line></svg>';

        ob_start();
        ?>
        <div class="empty-state" style="padding: var(--space-8) var(--space-4); margin: 0; border: none;">
          <div class="empty-state-icon" style="width: 52px; height: 52px; margin-bottom: var(--space-3);">
            <?= $iconSvg ?>
          </div>
          <div class="empty-state-title" style="font-size: 0.9375rem; margin-bottom: var(--space-1);"><?= $title ?></div>
          <p class="empty-state-desc" style="font-size: 0.8125rem; max-width: 320px; margin-bottom: <?= $actionUrl ? 'var(--space-4)' : '0' ?>; color: var(--color-ink-muted);">
            <?= $description ?>
          </p>
          <?php if ($actionUrl): ?>
            <a href="<?= $actionUrl ?>" class="btn btn-secondary btn-sm">
              <?= $actionLabel ?>
            </a>
          <?php endif; ?>
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

if (isset($emptyStateConfig) && is_array($emptyStateConfig)) {
    render_empty_state($emptyStateConfig);
}
