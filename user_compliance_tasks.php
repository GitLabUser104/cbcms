<?php
// Start session and include database connection
session_start();
include('db_connection.php');

// Handle add, edit, and delete operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add or edit task
    if (isset($_POST['task_name'])) {
        $task_name = $_POST['task_name'];
        $description = $_POST['description'];
        $assigned_to = $_POST['assigned_to'];
        $due_date = $_POST['due_date'];
        $status = $_POST['status'];
        $priority = $_POST['priority'];
        $task_id = $_POST['task_id'] ?? null;

        if ($task_id) { // Update existing task
            $query = "UPDATE compliance_tasks SET task_name = '$task_name', description = '$description', assigned_to = '$assigned_to', due_date = '$due_date', status = '$status', priority = '$priority' WHERE task_id = $task_id";
        } else { // Insert new task
            $query = "INSERT INTO compliance_tasks (task_name, description, assigned_to, due_date, status, priority, created_at) VALUES ('$task_name', '$description', '$assigned_to', '$due_date', '$status', '$priority', NOW())";
        }
        mysqli_query($conn, $query);
        exit;
    }

    // Delete task
    if (isset($_POST['delete_task_id'])) {
        $task_id = $_POST['delete_task_id'];
        $query = "DELETE FROM compliance_tasks WHERE task_id = $task_id";
        mysqli_query($conn, $query);
        exit;
    }
	
	// Export issues to CSV
    if (isset($_POST['export_tasks'])) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=compliance_tasks_report.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'Task Name', 'Description', 'Assigned To', 'Due Date','Status','Priority','Created on'));

        $query = "SELECT * FROM compliance_tasks ORDER BY created_at DESC";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
	}
}

// Fetch all tasks from the database
function fetchTasks($conn) {
    $query = "SELECT * FROM compliance_tasks ORDER BY due_date ASC";
    $result = mysqli_query($conn, $query);
    $tasks = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $tasks[] = $row;
    }
    return $tasks;
}

$tasks = fetchTasks($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compliance Tasks</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="user_dashboard.php">User Dashboard</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="user_dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="user_manage_users.php">Users</a></li>
            <li class="nav-item"><a class="nav-link active" href="user_compliance_tasks.php">Compliance Tasks</a></li>
            <li class="nav-item"><a class="nav-link" href="user_manage_documents.php">Documents</a></li>
            <li class="nav-item"><a class="nav-link" href="user_manage_policies.php">Policies</a></li>
            <li class="nav-item"><a class="nav-link" href="user_manage_notifications.php">Notifications</a></li>
            <li class="nav-item"><a class="nav-link" href="user_issue_tracker.php">Issue Tracker</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center">Compliance Task Management</h2>

    <!-- Task Table -->
    <div id="taskTable">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Task Name</th>
                    <th>Description</th>
                    <th>Assigned To</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?php echo $task['task_id']; ?></td>
                        <td><?php echo $task['task_name']; ?></td>
                        <td><?php echo $task['description']; ?></td>
                        <td><?php echo $task['assigned_to']; ?></td>
                        <td><?php echo $task['due_date']; ?></td>
                        <td><?php echo ucfirst($task['status']); ?></td>
                        <td><?php echo ucfirst($task['priority']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="openEditTaskModal(<?php echo $task['task_id']; ?>, '<?php echo $task['task_name']; ?>', '<?php echo $task['description']; ?>', '<?php echo $task['assigned_to']; ?>', '<?php echo $task['due_date']; ?>', '<?php echo $task['status']; ?>', '<?php echo $task['priority']; ?>')">Edit</button>
                        </td>
                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
     <!-- Export Report Button -->
    <form method="POST" action="compliance_tasks.php">
        <button type="submit" name="export_tasks" class="btn btn-success mb-3">Export Report </button>
    </form>
    </div>
</div>
<script>
function printPage() {
    window.print();
}
</script>
<!-- Task Modal Structure -->
<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="taskForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalLabel">Add New Task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="task_id" id="task_id">
                    <input type="text" class="form-control mb-3" name="task_name" id="task_name" placeholder="Task Name" required>
                    <textarea class="form-control mb-3" name="description" id="description" placeholder="Description" required></textarea>
                    <input type="text" class="form-control mb-3" name="assigned_to" id="assigned_to" placeholder="Assigned To" required>
                    <input type="date" class="form-control mb-3" name="due_date" id="due_date" required>
                    <select class="form-control mb-3" name="status" id="status" required>
                        <option value="pending">Pending</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                    <select class="form-control mb-3" name="priority" id="priority" required>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Task</button>
                </div>
            </form>
          
        </div>
    </div>
</div>

<!-- jQuery and Bootstrap JS for Modals -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- AJAX Code for Add/Edit/Delete -->
<script>
function openAddTaskModal() {
    $('#taskModalLabel').text('Add New Task');
    $('#taskForm')[0].reset();
    $('#task_id').val('');
    $('#taskModal').modal('show');
}

function openEditTaskModal(id, name, description, assigned_to, due_date, status, priority) {
    $('#taskModalLabel').text('Edit Task');
    $('#task_id').val(id);
    $('#task_name').val(name);
    $('#description').val(description);
    $('#assigned_to').val(assigned_to);
    $('#due_date').val(due_date);
    $('#status').val(status);
    $('#priority').val(priority);
    $('#taskModal').modal('show');
}

$('#taskForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        url: 'compliance_tasks.php',
        type: 'POST',
        data: formData,
        success: function() {
            $('#taskModal').modal('hide');
            refreshTaskTable();
        }
    });
});

function deleteTask(id) {
    if(confirm("Are you sure you want to delete this task?")) {
        $.ajax({
            url: 'compliance_tasks.php',
            type: 'POST',
            data: { delete_task_id: id },
            success: function() {
                refreshTaskTable();
            }
        });
    }
}

function refreshTaskTable() {
    $.ajax({
        url: 'compliance_tasks.php',
        type: 'GET',
        success: function(data) {
            var newDoc = document.open("text/html", "replace");
            newDoc.write(data);
            newDoc.close();
        }
    });
}
</script>

</body>
</html>
