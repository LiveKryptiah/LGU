<?php
/**
 * Government Workflow OS — Task Status Update API Endpoint
 * Step 3: Secure, scoped task status transitions and audit logging
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Detect if caller expects JSON response
$isJsonRequest = (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
              || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
              || (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false);

// 1. Enforce POST Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($isJsonRequest) {
        json_response(false, null, 'Method not allowed. Use POST.', 405);
    } else {
        http_response_code(405);
        header('Allow: POST');
        echo 'Method Not Allowed. Use POST.';
        exit;
    }
}

// 2. Authentication Guard
if (!is_logged_in()) {
    if ($isJsonRequest) {
        json_response(false, null, 'Authentication required. Please sign in.', 401);
    } else {
        $base = get_app_base_url();
        header("Location: {$base}/login.php");
        exit;
    }
}

$user = current_user();
$userId = (int)$user['id'];
$orgId = (int)$user['organization_id'];
$userFullName = $user['full_name'] ?: ($user['first_name'] . ' ' . $user['last_name']);

// Handle JSON input payload if content-type is application/json
$input = $_POST;
if (empty($input) && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $rawInput = file_get_contents('php://input');
    $decoded = json_decode($rawInput, true);
    if (is_array($decoded)) {
        $input = $decoded;
    }
}

// 3. CSRF Verification
$csrfToken = $input['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!verify_csrf_token($csrfToken)) {
    if ($isJsonRequest) {
        json_response(false, null, 'Invalid or expired CSRF token. Please refresh the page.', 403);
    } else {
        http_response_code(403);
        echo 'Security check failed: Invalid CSRF token. Please return to the previous page and retry.';
        exit;
    }
}

// 4. Input Validation
$taskId = (int)($input['task_id'] ?? 0);
$newStatus = strtolower(trim($input['status'] ?? ''));

$allowedStatuses = ['pending', 'in_progress', 'completed'];
if ($taskId <= 0 || !in_array($newStatus, $allowedStatuses, true)) {
    if ($isJsonRequest) {
        json_response(false, null, 'Invalid task ID or unsupported target status.', 422);
    } else {
        http_response_code(422);
        echo 'Invalid task parameter or status.';
        exit;
    }
}

// 5. Database Verification & Ownership Check
$db = get_db_connection();
if (!$db) {
    if ($isJsonRequest) {
        json_response(false, null, 'Database connection is currently unavailable.', 503);
    } else {
        http_response_code(503);
        echo 'Database service unavailable.';
        exit;
    }
}

try {
    // Verify task exists, belongs to organization, and is assigned to the current employee
    $checkStmt = $db->prepare("SELECT id, title, status, organization_id, assigned_to FROM `tasks` WHERE `id` = ? AND `organization_id` = ? AND `assigned_to` = ? LIMIT 1");
    $checkStmt->execute([$taskId, $orgId, $userId]);
    $task = $checkStmt->fetch();

    if (!$task) {
        if ($isJsonRequest) {
            json_response(false, null, 'Task not found or you do not have permission to update it.', 403);
        } else {
            http_response_code(403);
            echo 'Access denied: You can only update tasks assigned directly to you.';
            exit;
        }
    }

    $currentStatus = strtolower($task['status']);

    // Cancelled tasks cannot be transitioned
    if ($currentStatus === 'cancelled') {
        if ($isJsonRequest) {
            json_response(false, null, 'Cancelled tasks cannot be transitioned.', 422);
        } else {
            http_response_code(422);
            echo 'Cancelled tasks cannot be transitioned.';
            exit;
        }
    }

    // 6. Validate Allowed Transitions
    // Allowed:
    // pending -> in_progress, completed
    // in_progress -> pending, completed
    // completed -> in_progress
    $validTransition = false;
    if ($currentStatus === $newStatus) {
        $validTransition = true; // No-op is valid
    } elseif ($currentStatus === 'pending' && in_array($newStatus, ['in_progress', 'completed'], true)) {
        $validTransition = true;
    } elseif ($currentStatus === 'in_progress' && in_array($newStatus, ['pending', 'completed'], true)) {
        $validTransition = true;
    } elseif ($currentStatus === 'completed' && $newStatus === 'in_progress') {
        $validTransition = true;
    }

    if (!$validTransition) {
        $msg = "Invalid status transition from '" . ucwords(str_replace('_', ' ', $currentStatus)) . "' to '" . ucwords(str_replace('_', ' ', $newStatus)) . "'.";
        if ($isJsonRequest) {
            json_response(false, null, $msg, 422);
        } else {
            http_response_code(422);
            echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
            exit;
        }
    }

    // 7. Perform Status Update
    if ($currentStatus !== $newStatus) {
        $updateStmt = $db->prepare("UPDATE `tasks` SET `status` = ?, `updated_at` = NOW() WHERE `id` = ? AND `organization_id` = ? AND `assigned_to` = ?");
        $updateStmt->execute([$newStatus, $taskId, $orgId, $userId]);

        // Human-readable status label
        $statusLabels = [
            'pending'     => 'Pending',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            'cancelled'   => 'Cancelled'
        ];
        $label = $statusLabels[$newStatus] ?? ucwords(str_replace('_', ' ', $newStatus));

        // 8. Insert Activity Audit Log
        $logDescription = "{$userFullName} changed task status to {$label}";
        $logStmt = $db->prepare("INSERT INTO `activity_logs` (`organization_id`, `employee_id`, `action`, `description`, `entity_type`, `entity_id`, `created_at`) VALUES (?, ?, 'task_status_updated', ?, 'task', ?, NOW())");
        $logStmt->execute([$orgId, $userId, $logDescription, $taskId]);
    }

    // 9. Respond
    $statusLabels = [
        'pending'     => 'Pending',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
        'cancelled'   => 'Cancelled'
    ];

    if ($isJsonRequest) {
        json_response(true, [
            'task_id'      => $taskId,
            'status'       => $newStatus,
            'status_label' => $statusLabels[$newStatus] ?? $newStatus,
            'updated_at'   => date('c')
        ], 'Task status updated successfully.');
    } else {
        $base = get_app_base_url();
        $referer = $_SERVER['HTTP_REFERER'] ?? "{$base}/pages/tasks.php";
        // Append query flag to referer
        $separator = (strpos($referer, '?') !== false) ? '&' : '?';
        header("Location: {$referer}{$separator}updated=1&task_id={$taskId}");
        exit;
    }

} catch (Exception $e) {
    error_log("Error in update-task-status.php: " . $e->getMessage());
    if ($isJsonRequest) {
        json_response(false, null, 'An unexpected server error occurred while updating task status.', 500);
    } else {
        http_response_code(500);
        echo 'An internal error occurred. Please try again later.';
        exit;
    }
}
