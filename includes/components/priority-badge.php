<?php
/**
 * Government Workflow OS — Priority Badge Component
 * Renders pill-shaped priority badges compliant with DESIGN.md
 */

if (!function_exists('render_priority_badge')) {
    /**
     * Render priority badge markup.
     *
     * @param string $priority
     * @param bool $returnHtml If true, returns string; else echoes
     * @return string
     */
    function render_priority_badge($priority, $returnHtml = false) {
        $key = strtolower(trim($priority ?? 'normal'));
        $label = 'Normal';
        $class = 'badge-neutral';

        switch ($key) {
            case 'urgent':
                $label = 'Urgent';
                $class = 'badge-danger';
                break;
            case 'high':
                $label = 'High';
                $class = 'badge-warning';
                break;
            case 'low':
                $label = 'Low';
                $class = 'badge-neutral';
                break;
            case 'normal':
            default:
                $label = 'Normal';
                $class = 'badge-neutral';
                break;
        }

        $html = '<span class="badge ' . $class . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
        if ($returnHtml) {
            return $html;
        }
        echo $html;
        return '';
    }
}

// Support direct include with $priority variable set
if (isset($priority)) {
    render_priority_badge($priority);
}
