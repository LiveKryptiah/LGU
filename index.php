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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="refresh" content="0; url=index.html">
  <script>window.location.replace("index.html");</script>
  <title>Government Workflow OS</title>
</head>
<body>
  <p>Loading Government Workflow OS... If you are not redirected automatically, <a href="index.html">click here to open the application</a>.</p>
</body>
</html>
