<?php
/**
 * Government Workflow OS
 * Authentication Guard, Session Management & Security Helpers
 */

require_once __DIR__ . '/../config/database.php';

// Secure session initialization
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

/**
 * Returns the application root web path (e.g. "" or "/LGU-OS")
 *
 * @return string
 */
function get_app_base_url() {
    static $baseUrl = null;
    if ($baseUrl !== null) {
        return $baseUrl;
    }

    $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    // If inside /pages or /api or /includes, get parent directory
    $lastDir = basename($scriptDir);
    if (in_array($lastDir, ['pages', 'api', 'includes', 'config', 'database'])) {
        $scriptDir = dirname($scriptDir);
    }

    $clean = str_replace('\\', '/', $scriptDir);
    $baseUrl = ($clean === '/' || $clean === '.') ? '' : rtrim($clean, '/');
    return $baseUrl;
}

/**
 * Check if the current user session is authenticated.
 *
 * @return bool
 */
function is_logged_in() {
    return !empty($_SESSION['auth_user']['id']);
}

/**
 * Retrieve the current authenticated user's details.
 *
 * @return array
 */
function current_user() {
    if (!is_logged_in()) {
        return [
            'id'                => 0,
            'first_name'        => 'Guest',
            'last_name'         => 'User',
            'full_name'         => 'Guest User',
            'initials'          => 'GU',
            'position'          => 'Visitor',
            'email'             => '',
            'role'              => 'guest',
            'office_id'         => 0,
            'office_name'       => 'Public Portal',
            'organization_id'   => 0,
            'organization_name' => 'Provincial Government'
        ];
    }

    $user = $_SESSION['auth_user'];
    $first = $user['first_name'] ?? 'Juan';
    $last = $user['last_name'] ?? 'Dela Cruz';
    $initials = strtoupper(substr($first, 0, 1) . substr($last, 0, 1));

    return [
        'id'                => (int)$user['id'],
        'first_name'        => $first,
        'last_name'         => $last,
        'full_name'         => trim($first . ' ' . $last),
        'initials'          => $initials,
        'position'          => $user['position'] ?? 'Administrative Officer',
        'email'             => $user['email'] ?? 'juan.delacruz@pgov.ph',
        'role'              => $user['role'] ?? 'admin',
        'office_id'         => (int)($user['office_id'] ?? 1),
        'office_name'       => $user['office_name'] ?? 'Provincial Assessor\'s Office',
        'organization_id'   => (int)($user['organization_id'] ?? 1),
        'organization_name' => $user['organization_name'] ?? 'Provincial Government'
    ];
}

/**
 * Route guard: require user to be authenticated.
 * Redirects to login.php if session does not exist.
 *
 * @param string|null $redirectUrl
 */
function require_auth($redirectUrl = null) {
    if (!is_logged_in()) {
        $base = get_app_base_url();
        $target = $redirectUrl ?: ($base . '/login.php');

        $isApi = (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
              || (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false);

        if ($isApi) {
            json_response(false, null, 'Authentication required. Please sign in.', 401);
        }

        header("Location: " . $target);
        exit;
    }
}

/**
 * Route guard: require user to be a guest (not authenticated).
 * Redirects to dashboard if already logged in.
 *
 * @param string|null $redirectUrl
 */
function require_guest($redirectUrl = null) {
    if (is_logged_in()) {
        $base = get_app_base_url();
        $target = $redirectUrl ?: ($base . '/pages/dashboard.php');
        header("Location: " . $target);
        exit;
    }
}

/**
 * Provision user into session on successful authentication.
 *
 * @param array $employee
 */
function login_user($employee) {
    session_regenerate_id(true);

    $_SESSION['auth_user'] = [
        'id'                => (int)$employee['id'],
        'first_name'        => $employee['first_name'],
        'last_name'         => $employee['last_name'],
        'position'          => $employee['position'],
        'email'             => $employee['email'],
        'role'              => $employee['role'],
        'office_id'         => (int)$employee['office_id'],
        'office_name'       => $employee['office_name'] ?? 'Provincial Assessor\'s Office',
        'organization_id'   => (int)$employee['organization_id'],
        'organization_name' => $employee['organization_name'] ?? 'Provincial Government',
        'logged_in_at'      => date('Y-m-d H:i:s')
    ];
}

/**
 * Terminate current user session and wipe session cookie.
 */
function logout_user() {
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}

/**
 * Generate or fetch existing CSRF token.
 *
 * @return string
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify submitted CSRF token.
 *
 * @param string|null $token
 * @return bool
 */
function verify_csrf_token($token) {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * HTML Escaper shortcut for XSS safety.
 *
 * @param mixed $string
 * @return string
 */
function h($string) {
    return htmlspecialchars((string)($string ?? ''), ENT_QUOTES, 'UTF-8');
}
