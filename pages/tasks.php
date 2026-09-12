<?php
/**
 * Government Workflow OS — My Tasks
 * Step 3: Dynamic task list, search, multi-criteria filtering, sorting, pagination & status updates
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/components/status-badge.php';
require_once __DIR__ . '/../includes/components/priority-badge.php';
require_once __DIR__ . '/../includes/components/task-row.php';
require_once __DIR__ . '/../includes/components/empty-state.php';

// Authentication guard
require_auth();

$user = current_user();
$base = get_app_base_url();
$orgId = (int)($user['organization_id'] ?? 1);
$userId = (int)($user['id'] ?? 1);

// Metadata for layout
$pageTitle = 'My Tasks';
$pageSubtitle = 'Tasks assigned to you and their current progress.';
$currentPage = 'tasks';

// Ensure tables exist
ensure_dashboard_tables();

// 1. Sanitize & Parse Query Parameters
$search   = trim($_GET['search'] ?? '');
$status   = strtolower(trim($_GET['status'] ?? ''));
$priority = strtolower(trim($_GET['priority'] ?? ''));
$dueDate  = strtolower(trim($_GET['due_date'] ?? ''));
$sort     = strtolower(trim($_GET['sort'] ?? 'updated_desc'));
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 10;

// Whitelist validation for sort parameter
$allowedSorts = [
    'updated_desc'  => 't.updated_at DESC',
    'due_asc'       => 't.due_date IS NULL, t.due_date ASC, t.updated_at DESC',
    'due_desc'      => 't.due_date DESC, t.updated_at DESC',
    'priority_desc' => "FIELD(t.priority, 'urgent', 'high', 'normal', 'low') ASC, t.updated_at DESC",
    'title_asc'     => 't.title ASC'
];
$orderSql = $allowedSorts[$sort] ?? $allowedSorts['updated_desc'];

// 2. Build Safe Parameterized SQL Query
$whereConditions = ["t.organization_id = ?", "t.assigned_to = ?"];
$queryParams = [$orgId, $userId];

// Search filter: title, description, or office name
if ($search !== '') {
    $whereConditions[] = "(t.title LIKE ? OR t.description LIKE ? OR o.name LIKE ?)";
    $likeTerm = '%' . $search . '%';
    $queryParams[] = $likeTerm;
    $queryParams[] = $likeTerm;
    $queryParams[] = $likeTerm;
}

// Status filter
$allowedStatuses = ['pending', 'in_progress', 'completed', 'cancelled'];
if (in_array($status, $allowedStatuses, true)) {
    $whereConditions[] = "t.status = ?";
    $queryParams[] = $status;
}

// Priority filter
$allowedPriorities = ['low', 'normal', 'high', 'urgent'];
if (in_array($priority, $allowedPriorities, true)) {
    $whereConditions[] = "t.priority = ?";
    $queryParams[] = $priority;
}

// Due date filter
if ($dueDate === 'today') {
    $whereConditions[] = "t.due_date = CURDATE()";
} elseif ($dueDate === 'week') {
    $whereConditions[] = "t.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
} elseif ($dueDate === 'overdue') {
    $whereConditions[] = "t.due_date < CURDATE() AND t.status NOT IN ('completed', 'cancelled')";
} elseif ($dueDate === 'no_due_date') {
    $whereConditions[] = "t.due_date IS NULL";
}

$whereSql = implode(" AND ", $whereConditions);

// 3. Execute Queries (Count & Paginated Data)
$totalTasks = 0;
$tasks = [];
$db = get_db_connection();

if ($db) {
    try {
        // Count Query
        $countSql = "SELECT COUNT(*) FROM `tasks` t LEFT JOIN `offices` o ON t.office_id = o.id WHERE {$whereSql}";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($queryParams);
        $totalTasks = (int)$countStmt->fetchColumn();

        // Calculate pagination offsets
        $totalPages = max(1, (int)ceil($totalTasks / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        // Fetch Data Query
        $dataSql = "SELECT t.*, o.name AS office_name 
                    FROM `tasks` t 
                    LEFT JOIN `offices` o ON t.office_id = o.id 
                    WHERE {$whereSql} 
                    ORDER BY {$orderSql} 
                    LIMIT {$perPage} OFFSET {$offset}";
        $dataStmt = $db->prepare($dataSql);
        $dataStmt->execute($queryParams);
        $tasks = $dataStmt->fetchAll();
    } catch (Exception $e) {
        error_log("Tasks page query error: " . $e->getMessage());
        $tasks = [];
        $totalTasks = 0;
    }
} else {
    // Fallback if DB offline
    $totalTasks = 0;
    $totalPages = 1;
}

// Helpers for pagination links preserving active query parameters
function build_pagination_link($targetPage) {
    $params = $_GET;
    $params['page'] = $targetPage;
    return '?' . http_build_query($params);
}

$isFilterActive = ($search !== '' || $status !== '' || $priority !== '' || $dueDate !== '' || $sort !== 'updated_desc');
$csrfToken = csrf_token();

// Compute range display
$startItem = $totalTasks === 0 ? 0 : (($page - 1) * $perPage) + 1;
$endItem   = min($totalTasks, $page * $perPage);

require __DIR__ . '/../includes/header.php';
?>

<!-- Page Action Header -->
<div style="margin-bottom: var(--space-6); display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: var(--space-4);">
  <div>
    <div style="display: flex; align-items: center; gap: var(--space-3); margin-bottom: var(--space-1);">
      <h2 class="typography-display" style="color: var(--color-ink-primary);">My Tasks</h2>
      <span class="badge badge-neutral" style="font-size: 0.75rem;">
        <?= $totalTasks ?> <?= $totalTasks === 1 ? 'Task' : 'Tasks' ?>
      </span>
    </div>
    <p class="typography-subtitle" style="font-size: 0.9375rem;">
      Tasks assigned to you and their current progress.
    </p>
  </div>

  <div style="display: flex; align-items: center; gap: var(--space-3); flex-wrap: wrap;">
    <a href="<?= $base ?>/pages/dashboard.php" class="btn btn-secondary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="7" height="7"></rect>
        <rect x="14" y="3" width="7" height="7"></rect>
        <rect x="14" y="14" width="7" height="7"></rect>
        <rect x="3" y="14" width="7" height="7"></rect>
      </svg>
      <span>Dashboard</span>
    </a>
    <a href="<?= $base ?>/pages/calendar.php" class="btn btn-secondary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
        <line x1="16" y1="2" x2="16" y2="6"></line>
        <line x1="8" y1="2" x2="8" y2="6"></line>
      </svg>
      <span>Calendar</span>
    </a>
  </div>
</div>

<!-- Flash Message Feedback Banner -->
<?php if (isset($_GET['updated'])): ?>
  <div class="alert alert-success" id="flash-message">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
      <polyline points="22 4 12 14.01 9 11.01"></polyline>
    </svg>
    <span>Task status has been updated successfully.</span>
  </div>
<?php endif; ?>

<!-- Multi-Criteria Filter Bar (Preserves State in URL via GET) -->
<form method="GET" action="tasks.php" class="filter-bar">
  <!-- Search Input -->
  <div class="filter-search">
    <span class="filter-icon">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
    </span>
    <input 
      type="text" 
      name="search" 
      class="form-control" 
      placeholder="Search tasks..." 
      value="<?= h($search) ?>"
      autocomplete="off"
    >
  </div>

  <!-- Status Filter Dropdown -->
  <select name="status" class="filter-select" onchange="this.form.submit()">
    <option value="">All Status</option>
    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
    <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
    <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
    <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
  </select>

  <!-- Priority Filter Dropdown -->
  <select name="priority" class="filter-select" onchange="this.form.submit()">
    <option value="">All Priority</option>
    <option value="low" <?= $priority === 'low' ? 'selected' : '' ?>>Low</option>
    <option value="normal" <?= $priority === 'normal' ? 'selected' : '' ?>>Normal</option>
    <option value="high" <?= $priority === 'high' ? 'selected' : '' ?>>High</option>
    <option value="urgent" <?= $priority === 'urgent' ? 'selected' : '' ?>>Urgent</option>
  </select>

  <!-- Due Date Filter Dropdown -->
  <select name="due_date" class="filter-select" onchange="this.form.submit()">
    <option value="">All Dates</option>
    <option value="today" <?= $dueDate === 'today' ? 'selected' : '' ?>>Due Today</option>
    <option value="week" <?= $dueDate === 'week' ? 'selected' : '' ?>>Due This Week</option>
    <option value="overdue" <?= $dueDate === 'overdue' ? 'selected' : '' ?>>Overdue</option>
    <option value="no_due_date" <?= $dueDate === 'no_due_date' ? 'selected' : '' ?>>No Due Date</option>
  </select>

  <!-- Sorting Dropdown -->
  <select name="sort" class="filter-select" onchange="this.form.submit()">
    <option value="updated_desc" <?= $sort === 'updated_desc' ? 'selected' : '' ?>>Recently Updated</option>
    <option value="due_asc" <?= $sort === 'due_asc' ? 'selected' : '' ?>>Due Date: Earliest First</option>
    <option value="due_desc" <?= $sort === 'due_desc' ? 'selected' : '' ?>>Due Date: Latest First</option>
    <option value="priority_desc" <?= $sort === 'priority_desc' ? 'selected' : '' ?>>Priority: Highest First</option>
    <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>Title: A–Z</option>
  </select>

  <!-- Filter Action Buttons -->
  <div class="filter-actions">
    <button type="submit" class="btn btn-secondary btn-sm" title="Apply filters">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
      </svg>
      <span>Filter</span>
    </button>
    <?php if ($isFilterActive): ?>
      <a href="tasks.php" class="btn btn-ghost btn-sm" title="Clear all filters">
        <span>Reset</span>
      </a>
    <?php endif; ?>
  </div>
</form>

<!-- Task Table Section -->
<div class="task-table-wrapper">
  <?php if (!empty($tasks)): ?>
    <table class="task-table">
      <thead>
        <tr>
          <th>Task</th>
          <th>Office</th>
          <th>Status</th>
          <th>Priority</th>
          <th>Due Date</th>
          <th>Updated</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tasks as $task): ?>
          <?= render_task_table_row($task, [
              'csrf_token' => $csrfToken,
              'base_url'   => $base
          ], true); ?>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Pagination Controls (Only displayed if totalPages > 1) -->
    <?php if ($totalPages > 1): ?>
      <div class="pagination-bar">
        <div class="pagination-count">
          Showing <?= $startItem ?>–<?= $endItem ?> of <?= $totalTasks ?> tasks
        </div>

        <nav class="pagination-nav" aria-label="Task pagination">
          <!-- Previous Button -->
          <a 
            href="<?= $page > 1 ? build_pagination_link($page - 1) : '#' ?>" 
            class="page-btn <?= $page <= 1 ? 'disabled' : '' ?>"
            aria-label="Previous Page"
          >
            &larr; Previous
          </a>

          <!-- Page Numbers -->
          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a 
              href="<?= build_pagination_link($p) ?>" 
              class="page-btn <?= $p === $page ? 'active' : '' ?>"
            >
              <?= $p ?>
            </a>
          <?php endfor; ?>

          <!-- Next Button -->
          <a 
            href="<?= $page < $totalPages ? build_pagination_link($page + 1) : '#' ?>" 
            class="page-btn <?= $page >= $totalPages ? 'disabled' : '' ?>"
            aria-label="Next Page"
          >
            Next &rarr;
          </a>
        </nav>
      </div>
    <?php else: ?>
      <!-- Single Page Result Count Display -->
      <div class="pagination-bar">
        <div class="pagination-count">
          Showing <?= $totalTasks ?> of <?= $totalTasks ?> tasks
        </div>
      </div>
    <?php endif; ?>

  <?php else: ?>
    <!-- Graceful Empty States -->
    <div style="padding: var(--space-8) var(--space-4);">
      <?php if ($isFilterActive): ?>
        <?php render_empty_state([
            'title'        => 'No tasks match your search.',
            'description'  => 'Try adjusting your filters, clearing the search term, or browsing all tasks.',
            'action_url'   => 'tasks.php',
            'action_label' => 'Reset All Filters'
        ]); ?>
      <?php else: ?>
        <?php render_empty_state([
            'title'        => 'No tasks found.',
            'description'  => 'You currently have no tasks assigned to your account in this organization.',
            'action_url'   => $base . '/pages/dashboard.php',
            'action_label' => 'Return to Dashboard'
        ]); ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Inline JavaScript for Seamless Status Transitions with AJAX -->
<script>
function handleStatusSubmit(event, form) {
  // If fetch API is supported, attempt seamless background POST
  if (window.fetch) {
    event.preventDefault();
    const select = form.querySelector('select[name="status"]');
    const newStatus = select.value;
    if (!newStatus) return false;

    const formData = new FormData(form);

    // Provide visual indication
    select.disabled = true;

    fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        // Refresh page to show updated badges, sorting, and activity
        window.location.reload();
      } else {
        alert(data.message || 'Unable to update status.');
        select.disabled = false;
        select.selectedIndex = 0;
      }
    })
    .catch(err => {
      console.error('Status update error:', err);
      // Fallback to normal form submission
      form.submit();
    });

    return false;
  }
  return true;
}
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
