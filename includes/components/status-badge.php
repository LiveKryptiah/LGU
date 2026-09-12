<?php
/**
 * Government Workflow OS — Status Badge Component
 * Renders pill-shaped status badges compliant with DESIGN.md
 */

if (!function_exists('render_status_badge')) {
    /**
     * Render status badge markup.
     *
     * @param string $status
     * @param bool $returnHtml If true, returns string; else echoes
     * @return string
     */
    function render_status_badge($status, $returnHtml = false) {
        $statusKey = strtolower(trim($status ?? 'pending'));
        $label = 'Pending';
        $class = 'badge-neutral';

        switch ($statusKey) {
            case 'in_progress':
                $label = 'In Progress';
                $class = 'badge-info';
                break;
            case 'completed':
                $label = 'Completed';
                $class = 'badge-success';
                break;
            case 'cancelled':
                $label = 'Cancelled';
                $class = 'badge-danger';
                break;
            case 'pending':
            default:
                $label = 'Pending';
                $class = 'badge-neutral';
                break;
        }

        $html = '<span class="badge ' . $class . '"><span class="badge-dot"></span> ' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
        if ($returnHtml) {
            return $html;
        }
        echo $html;
        return '';
    }
}

// Support direct include with $status variable set
if (isset($status)) {
    render_status_badge($status);
}
