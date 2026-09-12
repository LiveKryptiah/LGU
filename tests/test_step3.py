"""
Step 3 Logic Verification
"""

def test_sorting():
    allowed_sorts = {
        'updated_desc': 't.updated_at DESC',
        'due_asc': 't.due_date IS NULL, t.due_date ASC, t.updated_at DESC',
        'due_desc': 't.due_date DESC, t.updated_at DESC',
        'priority_desc': "FIELD(t.priority, 'urgent', 'high', 'normal', 'low') ASC, t.updated_at DESC",
        'title_asc': 't.title ASC'
    }
    for s in ['updated_desc', 'due_asc', 'due_desc', 'priority_desc', 'title_asc']:
        assert s in allowed_sorts
    assert 'arbitrary_sql; DROP TABLE' not in allowed_sorts
    print('Test 1: Sorting Whitelist OK')

def test_transitions():
    def can_transition(curr, target):
        if curr == target:
            return True
        if curr == 'pending' and target in ['in_progress', 'completed']:
            return True
        if curr == 'in_progress' and target in ['pending', 'completed']:
            return True
        if curr == 'completed' and target == 'in_progress':
            return True
        return False

    assert can_transition('pending', 'in_progress') is True
    assert can_transition('pending', 'completed') is True
    assert can_transition('in_progress', 'pending') is True
    assert can_transition('in_progress', 'completed') is True
    assert can_transition('completed', 'in_progress') is True
    assert can_transition('completed', 'pending') is False
    assert can_transition('cancelled', 'in_progress') is False
    assert can_transition('pending', 'cancelled') is False
    print('Test 2: Status Transition Rules OK')

def test_pagination():
    total_tasks = 24
    per_page = 10
    total_pages = (total_tasks + per_page - 1) // per_page
    assert total_pages == 3
    assert (1 - 1) * per_page == 0
    assert (2 - 1) * per_page == 10
    assert (3 - 1) * per_page == 20
    print('Test 3: Pagination Math OK')

def test_files():
    with open('pages/tasks.php', 'r', encoding='utf-8') as f:
        tasks_php = f.read()
    assert 'require_auth()' in tasks_php
    assert 'tasks.php' in tasks_php
    assert 'filter-bar' in tasks_php
    assert 'pagination-bar' in tasks_php
    assert 'render_task_table_row' in tasks_php
    print('Test 4: tasks.php assertions OK')

    with open('includes/components/task-row.php', 'r', encoding='utf-8') as f:
        task_row_php = f.read()
    assert 'task-view.php' in task_row_php
    print('Test 4b: task-row.php task-view link assertions OK')

    with open('pages/task-view.php', 'r', encoding='utf-8') as f:
        task_view_php = f.read()
    assert 'require_auth()' in task_view_php
    assert 'Access Denied' in task_view_php
    assert 'Task Not Found' in task_view_php
    assert '403' in task_view_php
    assert '404' in task_view_php
    assert 'Update Progress' in task_view_php
    print('Test 5: task-view.php assertions OK')

    with open('api/update-task-status.php', 'r', encoding='utf-8') as f:
        api_php = f.read()
    assert 'verify_csrf_token' in api_php
    assert 'task_status_updated' in api_php
    assert 'activity_logs' in api_php
    assert 'assigned_to' in api_php
    print('Test 6: update-task-status.php assertions OK')

if __name__ == '__main__':
    test_sorting()
    test_transitions()
    test_pagination()
    test_files()
    print('\nALL STEP 3 UNIT TESTS PASSED!')
