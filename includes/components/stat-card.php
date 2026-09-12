<?php
/**
 * Government Workflow OS — Stat Card Component
 * Renders executive KPI summary card compliant with DESIGN.md
 */

if (!function_exists('render_stat_card')) {
    /**
     * Render a stat summary card.
     *
     * @param array $config [
     *   'label'    => string,
     *   'value'    => int|string,
     *   'subtitle' => string,
     *   'accent'   => 'primary'|'warning'|'info'|'danger',
     *   'icon_svg' => string,
     *   'badge'    => string|null,
     * ]
     * @param bool $returnHtml
     * @return string
     */
    function render_stat_card($config, $returnHtml = false) {
        $label    = htmlspecialchars($config['label'] ?? '', ENT_QUOTES, 'UTF-8');
        $value    = htmlspecialchars((string)($config['value'] ?? '0'), ENT_QUOTES, 'UTF-8');
        $subtitle = htmlspecialchars($config['subtitle'] ?? '', ENT_QUOTES, 'UTF-8');
        $accent   = in_array($config['accent'] ?? '', ['primary', 'warning', 'info', 'danger']) ? $config['accent'] : 'primary';
        $iconSvg  = $config['icon_svg'] ?? '';
        $badge    = $config['badge'] ?? null;

        $valueStyle = ($accent === 'danger' && (int)$value > 0) ? ' style="color: var(--color-danger);"' : '';

        ob_start();
        ?>
        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-card-label"><?= $label ?></span>
            <div class="stat-card-icon-container stat-card-icon-<?= $accent ?>">
              <?= $iconSvg ?>
            </div>
          </div>
          <div class="stat-card-value"<?= $valueStyle ?>><?= $value ?></div>
          <div class="stat-card-trend text-<?= $accent ?>" style="display: flex; align-items: center; gap: 6px;">
            <?php if (!empty($badge)): ?>
              <span class="badge badge-<?= $accent ?>" style="font-size: 0.6875rem;">
                <span class="badge-dot"></span> <?= htmlspecialchars($badge, ENT_QUOTES, 'UTF-8') ?>
              </span>
            <?php else: ?>
              <span><?= $subtitle ?></span>
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

// Support direct include with $statCardConfig set
if (isset($statCardConfig) && is_array($statCardConfig)) {
    render_stat_card($statCardConfig);
}
