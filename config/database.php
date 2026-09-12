<?php
/**
 * Government Workflow OS
 * Central Database Configuration & PDO Query Helpers
 */

defined('DB_HOST')    or define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
defined('DB_PORT')    or define('DB_PORT', getenv('DB_PORT') ?: '3306');
defined('DB_NAME')    or define('DB_NAME', getenv('DB_NAME') ?: 'government_workflow_os');
defined('DB_USER')    or define('DB_USER', getenv('DB_USER') ?: 'root');
defined('DB_PASS')    or define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
defined('DB_CHARSET') or define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a shared PDO instance.
 *
 * @return PDO|null
 */
function get_db_connection() {
    static $pdo = null;
    static $connectionAttempted = false;

    if ($pdo !== null) {
        return $pdo;
    }

    if ($connectionAttempted) {
        return null;
    }

    $connectionAttempted = true;
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Unknown database (code 1049) - redirect to install.php if accessed via browser
        if ($e->getCode() == 1049) {
            $isBrowser = php_sapi_name() !== 'cli' && empty($_SERVER['HTTP_X_REQUESTED_WITH']);
            $currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
            if ($isBrowser && $currentScript !== 'install.php') {
                $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
                $installUrl = ($base === '' || $base === '/' || $base === '\\') ? '/install.php' : $base . '/install.php';
                // Adjust if currently inside /pages
                if (basename(dirname($_SERVER['SCRIPT_NAME'] ?? '')) === 'pages') {
                    $installUrl = '../install.php';
                }
                header("Location: " . $installUrl);
                exit;
            }
        }

        // Check if API request
        $isApi = (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
              || (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false);

        if ($isApi) {
            http_response_code(503);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success'    => false,
                'error_code' => 'DB_CONNECTION_ERROR',
                'message'    => 'Database connection failed: ' . $e->getMessage()
            ]);
            exit;
        }

        return null;
    }
}

/**
 * Check if the database connection is currently alive and tables exist.
 *
 * @return bool
 */
function is_db_connected() {
    try {
        $db = get_db_connection();
        return $db !== null;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Execute parameterized query.
 *
 * @param string $sql
 * @param array $params
 * @return PDOStatement|null
 */
function db_query($sql, $params = []) {
    $db = get_db_connection();
    if (!$db) {
        return null;
    }
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Fetch all matching records.
 *
 * @param string $sql
 * @param array $params
 * @return array
 */
function db_fetch_all($sql, $params = []) {
    $stmt = db_query($sql, $params);
    return $stmt ? $stmt->fetchAll() : [];
}

/**
 * Fetch a single record.
 *
 * @param string $sql
 * @param array $params
 * @return array|false
 */
function db_fetch_one($sql, $params = []) {
    $stmt = db_query($sql, $params);
    return $stmt ? $stmt->fetch() : false;
}

/**
 * Insert record into table and return lastInsertId.
 *
 * @param string $table
 * @param array $data Associative array of column => value
 * @return int Last insert ID
 */
function db_insert($table, $data) {
    $db = get_db_connection();
    if (!$db) {
        return 0;
    }
    $columns = array_keys($data);
    $fields = implode('`, `', $columns);
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));

    $sql = "INSERT INTO `{$table}` (`{$fields}`) VALUES ({$placeholders})";
    $stmt = $db->prepare($sql);
    $stmt->execute(array_values($data));
    return (int)$db->lastInsertId();
}

/**
 * Update records in table.
 *
 * @param string $table
 * @param array $data Associative array of column => value
 * @param string $whereClause
 * @param array $whereParams
 * @return int Rows affected
 */
function db_update($table, $data, $whereClause, $whereParams = []) {
    $db = get_db_connection();
    if (!$db) {
        return 0;
    }
    $setParts = [];
    $values = [];

    foreach ($data as $column => $value) {
        $setParts[] = "`{$column}` = ?";
        $values[] = $value;
    }

    $setString = implode(', ', $setParts);
    $sql = "UPDATE `{$table}` SET {$setString} WHERE {$whereClause}";
    $stmt = $db->prepare($sql);
    $stmt->execute(array_merge($values, $whereParams));
    return $stmt->rowCount();
}

/**
 * Count records.
 *
 * @param string $table
 * @param string $whereClause
 * @param array $whereParams
 * @return int
 */
function db_count($table, $whereClause = '', $whereParams = []) {
    $sql = "SELECT COUNT(*) AS total FROM `{$table}`";
    if (!empty($whereClause)) {
        $sql .= " WHERE {$whereClause}";
    }
    $row = db_fetch_one($sql, $whereParams);
    return (int)($row['total'] ?? 0);
}

/**
 * Send standard JSON response and exit.
 *
 * @param bool $success
 * @param mixed $data
 * @param string $message
 * @param int $statusCode
 */
function json_response($success, $data = null, $message = '', $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success'   => (bool)$success,
        'data'      => $data,
        'message'   => $message,
        'timestamp' => date('c')
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Ensures required dashboard tables (tasks, activity_logs) exist and are seeded.
 *
 * @return bool
 */
function ensure_dashboard_tables() {
    static $checked = false;
    if ($checked) {
        return true;
    }
    $checked = true;

    $db = get_db_connection();
    if (!$db) {
        return false;
    }

    try {
        // 1. Create tasks table if not exists
        $db->exec("CREATE TABLE IF NOT EXISTS `tasks` (
          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
          `organization_id` INT UNSIGNED NOT NULL,
          `office_id` INT UNSIGNED NULL,
          `assigned_to` INT UNSIGNED NULL,
          `created_by` INT UNSIGNED NULL,
          `title` VARCHAR(255) NOT NULL,
          `description` TEXT NULL,
          `status` ENUM('pending', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
          `priority` ENUM('low', 'normal', 'high', 'urgent') NOT NULL DEFAULT 'normal',
          `due_date` DATE NULL,
          `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          INDEX `idx_tasks_assigned_to` (`assigned_to`),
          INDEX `idx_tasks_status` (`status`),
          INDEX `idx_tasks_due_date` (`due_date`),
          INDEX `idx_tasks_org` (`organization_id`),
          INDEX `idx_tasks_office` (`office_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 2. Create activity_logs table if not exists
        $db->exec("CREATE TABLE IF NOT EXISTS `activity_logs` (
          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
          `organization_id` INT UNSIGNED NOT NULL,
          `employee_id` INT UNSIGNED NULL,
          `action` VARCHAR(100) NOT NULL,
          `description` VARCHAR(255) NOT NULL,
          `entity_type` VARCHAR(50) NULL,
          `entity_id` INT UNSIGNED NULL,
          `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          INDEX `idx_activity_employee_id` (`employee_id`),
          INDEX `idx_activity_created_at` (`created_at`),
          INDEX `idx_activity_org` (`organization_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 3. Check if tasks count is 0, if so, seed demo tasks
        $taskCountStmt = $db->query("SELECT COUNT(*) AS total FROM `tasks`");
        $taskCount = (int)($taskCountStmt ? $taskCountStmt->fetchColumn() : 0);

        if ($taskCount === 0) {
            $db->exec("INSERT INTO `tasks` (`id`, `organization_id`, `office_id`, `assigned_to`, `created_by`, `title`, `description`, `status`, `priority`, `due_date`, `created_at`, `updated_at`)
            VALUES
            (1, 1, 1, 1, 1, 'Review incoming assessment documents', 'Conduct technical verification of incoming land transfer tax assessments and title deeds from district offices.', 'in_progress', 'urgent', DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 2 HOUR)),
            (2, 1, 1, 1, 1, 'Coordinate field inspection schedule', 'Synchronize on-site commercial appraisal schedule with Provincial Engineering surveyors.', 'in_progress', 'high', DATE_SUB(CURDATE(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 HOUR)),
            (3, 1, 1, 1, 1, 'Prepare monthly office accomplishment report', 'Consolidate real property appraisal statistics for Q3 submission to the Governor\'s Office.', 'in_progress', 'urgent', CURDATE(), DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 1 HOUR)),
            (4, 1, 1, 1, 2, 'Verify Tax Declaration supporting documents', 'Review submitted subdivision survey plans and certified true copies of cadastral maps.', 'in_progress', 'high', CURDATE(), DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 30 MINUTE)),
            (5, 1, 1, 1, 4, 'Review pending employee requests', 'Process official overtime authorization and staff travel orders for field appraisers.', 'pending', 'normal', CURDATE(), DATE_SUB(NOW(), INTERVAL 5 HOUR), DATE_SUB(NOW(), INTERVAL 5 HOUR)),
            (6, 1, 1, 1, 1, 'Encode received documents', 'Log newly endorsed assessment appeals into the central municipal record management index.', 'pending', 'normal', CURDATE(), DATE_SUB(NOW(), INTERVAL 4 HOUR), DATE_SUB(NOW(), INTERVAL 4 HOUR)),
            (7, 1, 1, 1, 1, 'Update property assessment records', 'Update zonal valuation roll and tax classifications for Poblacion commercial district.', 'in_progress', 'normal', DATE_ADD(CURDATE(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 45 MINUTE)),
            (8, 1, 1, 1, 3, 'Coordinate boundary synchronization with Engineering Office', 'Resolve parcel discrepancy along provincial road right-of-way boundaries.', 'in_progress', 'high', DATE_ADD(CURDATE(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 6 HOUR)),
            (9, 1, 1, 1, 1, 'Prepare transmittal letter for approved documents', 'Draft formal endorsement transmittal for approved tax declarations to the Provincial Treasurer\'s Office.', 'in_progress', 'normal', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 50 MINUTE)),
            (10, 1, 1, 1, 1, 'Submit quarterly real property assessment statistical report', 'Official compilation submitted to Bureau of Local Government Finance regional directorate.', 'completed', 'high', DATE_SUB(CURDATE(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY)),
            (11, 1, 1, 1, 2, 'Consolidate monthly collection reconciliation report', 'Cross-referenced assessment registry with Treasury collections for previous month.', 'completed', 'normal', DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),
            (12, 1, 1, 1, 1, 'Archive processed assessment appeals for Q2', 'Indexed and transferred physical docket files to the General Services records archive.', 'completed', 'low', DATE_SUB(CURDATE(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY))
            ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);");
        }

        // 4. Check if activity_logs count is 0, if so, seed demo activity logs
        $activityCountStmt = $db->query("SELECT COUNT(*) AS total FROM `activity_logs`");
        $activityCount = (int)($activityCountStmt ? $activityCountStmt->fetchColumn() : 0);

        if ($activityCount === 0) {
            $db->exec("INSERT INTO `activity_logs` (`id`, `organization_id`, `employee_id`, `action`, `description`, `entity_type`, `entity_id`, `created_at`)
            VALUES
            (1, 1, 1, 'TASK_CREATED', 'Juan Dela Cruz created a task: Review incoming assessment documents', 'task', 1, DATE_SUB(NOW(), INTERVAL 10 MINUTE)),
            (2, 1, 2, 'STATUS_UPDATED', 'Maria Santos updated a task status to in-progress', 'task', 4, DATE_SUB(NOW(), INTERVAL 45 MINUTE)),
            (3, 1, 3, 'REVIEW_COMPLETED', 'Pedro Reyes completed a document review for infrastructure appraisal', 'review', 8, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
            (4, 1, 4, 'TASK_ASSIGNED', 'Ana Cruz assigned a task to Juan Dela Cruz: Review pending employee requests', 'task', 5, DATE_SUB(NOW(), INTERVAL 5 HOUR)),
            (5, 1, 1, 'TRANSMITTAL_SENT', 'Juan Dela Cruz endorsed transmittal documents to Provincial Treasury', 'transmittal', 9, DATE_SUB(NOW(), INTERVAL 1 DAY))
            ON DUPLICATE KEY UPDATE `action` = VALUES(`action`);");
        }

        return true;
    } catch (Exception $e) {
        error_log('ensure_dashboard_tables warning: ' . $e->getMessage());
        return false;
    }
}

