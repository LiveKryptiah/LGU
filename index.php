<?php
/**
 * Government Workflow OS — Application Entry Router
 * Directs authenticated users to dashboard, guests to login
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$base = get_app_base_url();

if (is_logged_in()) {
    header("Location: " . $base . "/pages/dashboard.php");
} else {
    header("Location: " . $base . "/login.php");
}
exit;
