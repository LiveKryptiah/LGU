<?php
/**
 * Government Workflow OS — Sign Out Controller
 * Destroys user session and redirects to sign in portal
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

logout_user();

$base = get_app_base_url();
header("Location: " . $base . "/login.php?logged_out=1");
exit;
