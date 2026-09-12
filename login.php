<?php
/**
 * Government Workflow OS — Sign In Portal
 * Step 1: Authentication against MySQL database
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

// If already authenticated, forward to dashboard
require_guest();

$base = get_app_base_url();
$error = null;
$success = null;
$submittedEmail = '';

// Check if user just logged out
if (isset($_GET['logged_out'])) {
    $success = 'You have successfully signed out of the system.';
}

// Process login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $error = 'Security session expired. Please refresh and try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $submittedEmail = $email;

        if (empty($email) || empty($password)) {
            $error = 'Please enter both your official email address and password.';
        } else {
            $db = get_db_connection();

            if ($db) {
                // Query employee record with office and organization details
                $sql = "SELECT e.*, 
                               o.name AS office_name, 
                               org.name AS organization_name 
                        FROM employees e
                        LEFT JOIN offices o ON e.office_id = o.id
                        LEFT JOIN organizations org ON e.organization_id = org.id
                        WHERE e.email = ? AND e.status = 'active'
                        LIMIT 1";

                try {
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$email]);
                    $employee = $stmt->fetch();

                    if ($employee && password_verify($password, $employee['password'])) {
                        // Successful authentication
                        login_user($employee);
                        header("Location: " . $base . "/pages/dashboard.php");
                        exit;
                    } else {
                        $error = 'Invalid email address or password. Please verify your credentials.';
                    }
                } catch (PDOException $e) {
                    $error = 'Database authentication error: ' . $e->getMessage();
                }
            } else {
                // Database connection is offline or uninitialized
                // If demo credentials match Juan Dela Cruz, permit demo login session
                // while alerting the user to run install.php for full MySQL persistence.
                if (strtolower($email) === 'juan.delacruz@pgov.ph' && $password === 'Password123!') {
                    $demoUser = [
                        'id'                => 1,
                        'first_name'        => 'Juan',
                        'last_name'         => 'Dela Cruz',
                        'position'          => 'Administrative Officer',
                        'email'             => 'juan.delacruz@pgov.ph',
                        'role'              => 'admin',
                        'office_id'         => 1,
                        'office_name'       => 'Provincial Assessor\'s Office',
                        'organization_id'   => 1,
                        'organization_name' => 'Provincial Government'
                    ];
                    login_user($demoUser);
                    header("Location: " . $base . "/pages/dashboard.php");
                    exit;
                } else {
                    $error = 'Could not connect to MySQL database. Please verify MySQL service is running or run <a href="install.php" style="font-weight:600; text-decoration:underline;">install.php</a>.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In &bull; Government Workflow OS</title>
  
  <link rel="stylesheet" href="<?= $base ?>/assets/css/design-system.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/components.css">
  
  <style>
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
      color: var(--color-ink-primary);
    }

    .login-wrapper {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: var(--space-6) var(--space-4);
    }

    .login-container {
      width: 100%;
      max-width: 440px;
    }

    .login-card {
      background-color: var(--color-card);
      border: 1px solid var(--color-border);
      border-radius: var(--radius-lg);
      padding: var(--space-8);
      box-shadow: var(--shadow-md);
    }

    .brand-hero {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      margin-bottom: var(--space-6);
    }

    .brand-hero-emblem {
      width: 56px;
      height: 56px;
      border-radius: var(--radius-md);
      background-color: #0f172a;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: var(--space-3);
      box-shadow: var(--shadow-sm);
    }

    .brand-hero-emblem img {
      width: 44px;
      height: 44px;
    }

    .brand-hero-title {
      font-size: 1.375rem;
      font-weight: var(--font-weight-bold);
      letter-spacing: -0.02em;
      color: var(--color-ink-primary);
    }

    .brand-hero-sub {
      font-size: 0.8125rem;
      color: var(--color-ink-muted);
      margin-top: 2px;
    }

    .demo-callout {
      background-color: var(--color-primary-light);
      border: 1px solid var(--color-primary-border);
      border-radius: var(--radius-sm);
      padding: var(--space-3) var(--space-4);
      margin-bottom: var(--space-5);
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .demo-callout-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .demo-callout-title {
      font-size: 0.75rem;
      font-weight: var(--font-weight-bold);
      color: var(--color-primary);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .demo-creds-row {
      font-size: 0.8125rem;
      color: var(--color-ink-secondary);
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .demo-creds-row code {
      font-family: var(--font-mono);
      font-size: 0.75rem;
      background-color: rgba(255, 255, 255, 0.8);
      padding: 1px 6px;
      border-radius: 4px;
      border: 1px solid var(--color-primary-border);
      color: var(--color-ink-primary);
    }

    .login-footer-meta {
      text-align: center;
      margin-top: var(--space-6);
      font-size: 0.75rem;
      color: var(--color-ink-muted);
    }
  </style>
</head>
<body>

<div class="login-wrapper">
  <div class="login-container">
    <div class="login-card">
      <!-- Official Branding -->
      <div class="brand-hero">
        <div class="brand-hero-emblem">
          <img src="<?= $base ?>/assets/images/logo.svg" alt="Republic Emblem">
        </div>
        <h1 class="brand-hero-title">Government Workflow OS</h1>
        <p class="brand-hero-sub">Workplace Management System &bull; Provincial Government</p>
      </div>

      <!-- Demo Credentials Helper Callout -->
      <div class="demo-callout">
        <div class="demo-callout-header">
          <span class="demo-callout-title">Official Demo Account</span>
          <button 
            type="button" 
            class="btn btn-secondary btn-sm" 
            onclick="autofillDemoCredentials('juan.delacruz@pgov.ph', 'Password123!')"
            style="height: 24px; padding: 0 10px; font-size: 0.6875rem;"
          >
            Autofill Credentials
          </button>
        </div>
        <div class="demo-creds-row">
          <div><strong>User:</strong> Juan Dela Cruz &bull; Administrative Officer</div>
          <div><strong>Email:</strong> <code>juan.delacruz@pgov.ph</code></div>
          <div><strong>Password:</strong> <code>Password123!</code></div>
        </div>
      </div>

      <!-- Alerts -->
      <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <div><?= $error ?></div>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success" role="alert">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
          <div><?= h($success) ?></div>
        </div>
      <?php endif; ?>

      <!-- Login Form -->
      <form action="<?= $base ?>/login.php" method="POST" autocomplete="on">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <!-- Email Field -->
        <div class="form-group">
          <label for="login-email" class="form-label">
            <span>Official Email Address</span>
          </label>
          <div class="input-with-icon">
            <span class="input-icon-left">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
              </svg>
            </span>
            <input 
              type="email" 
              name="email" 
              id="login-email" 
              class="form-control" 
              placeholder="name@pgov.ph" 
              value="<?= h($submittedEmail ?: 'juan.delacruz@pgov.ph') ?>" 
              required 
              autofocus
            >
          </div>
        </div>

        <!-- Password Field with Visibility Toggle -->
        <div class="form-group" style="margin-bottom: var(--space-6);">
          <label for="login-password" class="form-label">
            <span>Password</span>
          </label>
          <div class="input-with-icon">
            <span class="input-icon-left">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </span>
            <input 
              type="password" 
              name="password" 
              id="login-password" 
              class="form-control" 
              placeholder="••••••••" 
              value="Password123!" 
              required
            >
            <button 
              type="button" 
              class="input-action-right" 
              data-toggle-password="login-password" 
              title="Toggle Password Visibility"
              aria-label="Toggle Password Visibility"
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </button>
          </div>
        </div>

        <!-- Submit Button (Pill shaped) -->
        <button type="submit" class="btn btn-primary" style="width: 100%; height: 44px; font-size: 0.9375rem;">
          <span>Sign In to Workplace</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>
      </form>

      <!-- Database Setup Link for Administrators -->
      <div style="margin-top: var(--space-6); padding-top: var(--space-4); border-top: 1px solid var(--color-border-subtle); display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem;">
        <span class="text-muted">First time deploying?</span>
        <a href="<?= $base ?>/install.php" style="font-weight: 600;">
          Run Database Setup &rarr;
        </a>
      </div>
    </div>

    <!-- Official Security Disclaimer -->
    <div class="login-footer-meta">
      <p>Authorized access only &bull; Republic of the Philippines</p>
      <p style="margin-top: 4px; font-size: 0.6875rem; color: var(--color-ink-faint);">
        Provincial Government &bull; Government Workflow OS v1.0.0
      </p>
    </div>
  </div>
</div>

<script src="<?= $base ?>/assets/js/app.js"></script>

</body>
</html>
