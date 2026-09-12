<?php
/**
 * Government Workflow OS — Database Setup & Installation Wizard
 * One-click initialization of MySQL schema and seed data
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$base = get_app_base_url();
$message = null;
$messageType = null;
$installLogs = [];
$isInstalled = false;

// Check existing status
$connectionOk = false;
$existingTables = [];
try {
    $testDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $testPdo = new PDO($testDsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT]);
    if ($testPdo) {
        $connectionOk = true;
        $tablesStmt = $testPdo->query("SHOW TABLES");
        if ($tablesStmt) {
            $existingTables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);
            if (count($existingTables) >= 5) {
                $isInstalled = true;
            }
        }
    }
} catch (Exception $e) {
    $connectionOk = false;
}

// Handle Setup Execution
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_setup'])) {
    try {
        // 1. Connect to MySQL server directly
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        $installLogs[] = "✓ Successfully connected to MySQL server at " . DB_HOST . ":" . DB_PORT;

        // 2. Create Database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $installLogs[] = "✓ Verified / Created database `" . DB_NAME . "`";

        // 3. Switch to Database
        $pdo->exec("USE `" . DB_NAME . "`");

        // 4. Execute Schema
        $schemaPath = __DIR__ . '/database/schema.sql';
        if (!file_exists($schemaPath)) {
            throw new Exception("Schema file not found at database/schema.sql");
        }
        $schemaSql = file_get_contents($schemaPath);
        $pdo->exec($schemaSql);
        $installLogs[] = "✓ Relational tables established (`organizations`, `offices`, `employees`, `tasks`, `activity_logs`)";

        // 5. Execute Seed Data
        $seedPath = __DIR__ . '/database/seed.sql';
        if (!file_exists($seedPath)) {
            throw new Exception("Seed file not found at database/seed.sql");
        }
        $seedSql = file_get_contents($seedPath);
        $pdo->exec($seedSql);
        $installLogs[] = "✓ Seeded provincial offices, personnel, 12 demo tasks, and activity logs";

        // 6. Guarantee fresh native PHP password hash for Juan Dela Cruz
        $nativeHash = password_hash('Password123!', PASSWORD_BCRYPT, ['cost' => 10]);
        $updateStmt = $pdo->prepare("UPDATE `employees` SET `password` = ? WHERE `email` = ?");
        $updateStmt->execute([$nativeHash, 'juan.delacruz@pgov.ph']);
        $installLogs[] = "✓ Demo user initialized: Juan Dela Cruz (juan.delacruz@pgov.ph / Password123!)";

        $message = "Database successfully initialized! The application foundation is ready.";
        $messageType = "success";
        $isInstalled = true;

        // Re-read tables
        $tablesStmt = $pdo->query("SHOW TABLES");
        if ($tablesStmt) {
            $existingTables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);
        }
    } catch (Exception $e) {
        $message = "Installation failed: " . $e->getMessage();
        $messageType = "danger";
        $installLogs[] = "✗ Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Database Setup &bull; Government Workflow OS</title>
  
  <link rel="stylesheet" href="<?= $base ?>/assets/css/design-system.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/components.css">

  <style>
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
      padding: var(--space-6) var(--space-4);
      color: var(--color-ink-primary);
    }

    .setup-card {
      width: 100%;
      max-width: 600px;
      background-color: var(--color-card);
      border: 1px solid var(--color-border);
      border-radius: var(--radius-lg);
      padding: var(--space-8);
      box-shadow: var(--shadow-md);
    }

    .terminal-output {
      background-color: #0f172a;
      color: #f1f5f9;
      font-family: var(--font-mono);
      font-size: 0.8125rem;
      border-radius: var(--radius-sm);
      padding: var(--space-4);
      margin: var(--space-4) 0;
      line-height: 1.6;
      max-height: 220px;
      overflow-y: auto;
    }

    .config-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: var(--space-2);
      font-size: 0.8125rem;
      background-color: var(--color-canvas-subtle);
      border: 1px solid var(--color-border);
      border-radius: var(--radius-sm);
      padding: var(--space-3) var(--space-4);
      margin: var(--space-4) 0;
    }
  </style>
</head>
<body>

<div class="setup-card">
  <!-- Top Header -->
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-4);">
    <div style="display: flex; align-items: center; gap: var(--space-3);">
      <div style="width: 40px; height: 40px; background-color: #0f172a; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;">
        <img src="<?= $base ?>/assets/images/logo.svg" alt="Logo" width="32" height="32">
      </div>
      <div>
        <h2 style="font-size: 1.125rem; font-weight: 700; line-height: 1.2;">Government Workflow OS</h2>
        <span style="font-size: 0.75rem; color: var(--color-ink-muted);">System Setup Wizard</span>
      </div>
    </div>

    <?php if ($isInstalled): ?>
      <span class="badge badge-success">
        <span class="badge-dot"></span> Ready &amp; Initialized
      </span>
    <?php else: ?>
      <span class="badge badge-warning">
        <span class="badge-dot"></span> Setup Required
      </span>
    <?php endif; ?>
  </div>

  <p class="typography-body" style="color: var(--color-ink-muted); margin-bottom: var(--space-2);">
    This wizard initializes the <strong><?= h(DB_NAME) ?></strong> MySQL database, creates the foundational organizational tables, and provisions the demo administrator account.
  </p>

  <!-- Database Parameters -->
  <div class="config-grid">
    <div><strong>Host:</strong> <?= h(DB_HOST) ?>:<?= h(DB_PORT) ?></div>
    <div><strong>Database:</strong> <code><?= h(DB_NAME) ?></code></div>
    <div><strong>User:</strong> <?= h(DB_USER) ?></div>
    <div><strong>Password:</strong> <?= empty(DB_PASS) ? '<em>(none)</em>' : '••••••••' ?></div>
  </div>

  <?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?>">
      <div><?= h($message) ?></div>
    </div>
  <?php endif; ?>

  <?php if (!empty($installLogs)): ?>
    <div class="terminal-output">
      <?php foreach ($installLogs as $log): ?>
        <div><?= h($log) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($isInstalled && empty($installLogs)): ?>
    <div style="background-color: var(--color-canvas-subtle); border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: var(--space-3) var(--space-4); margin-bottom: var(--space-4);">
      <div style="font-size: 0.75rem; font-weight: 600; color: var(--color-ink-muted); margin-bottom: 6px;">EXISTING DATABASE TABLES:</div>
      <div style="display: flex; gap: var(--space-2); flex-wrap: wrap;">
        <?php foreach ($existingTables as $tbl): ?>
          <span class="badge badge-neutral" style="font-family: var(--font-mono); font-size: 0.6875rem;"><?= h($tbl) ?></span>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- Actions -->
  <form method="POST" style="display: flex; flex-direction: column; gap: var(--space-3); margin-top: var(--space-4);">
    <input type="hidden" name="run_setup" value="1">
    
    <button type="submit" class="btn btn-primary" style="height: 44px; font-size: 0.875rem;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="23 4 23 10 17 10"></polyline>
        <polyline points="1 20 1 14 7 14"></polyline>
        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
      </svg>
      <span><?= $isInstalled ? 'Re-Run &amp; Synchronize Database' : 'Initialize Database &amp; Seed Data' ?></span>
    </button>

    <a href="<?= $base ?>/login.php" class="btn btn-secondary" style="height: 44px; font-size: 0.875rem;">
      <span>Go to Sign In Portal</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="5" y1="12" x2="19" y2="12"></line>
        <polyline points="12 5 19 12 12 19"></polyline>
      </svg>
    </a>
  </form>
</div>

</body>
</html>
