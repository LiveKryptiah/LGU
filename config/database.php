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
