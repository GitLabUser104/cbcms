<?php
// Start session and include database connection
session_start();
include('db_connection.php');

// Fetch audit logs with optional filters
function fetchAuditLogs($conn, $action_filter = null, $date_filter = null) {
    $query = "SELECT * FROM audit_logs WHERE 1=1";
    
    if ($action_filter) {
        $query .= " AND action = '$action_filter'";
    }
    if ($date_filter) {
        $query .= " AND DATE(action_time) = '$date_filter'";
    }
    
    $query .= " ORDER BY action_time DESC";
    $result = mysqli_query($conn, $query);
    $logs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $logs[] = $row;
    }
    return $logs;
}

$action_filter = $_POST['action_filter'] ?? null;
$date_filter = $_POST['date_filter'] ?? null;
$audit_logs = fetchAuditLogs($conn, $action_filter, $date_filter);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="admin_dashboard.php">Admin Dashboard</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_users.php">Users</a></li>
            <li class="nav-item"><a class="nav-link" href="compliance_tasks.php">Compliance Tasks</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_documents.php">Documents</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_policies.php">Policies</a></li>
            <li class="nav-item"><a class="nav-link active" href="audit_logs.php">Audit Logs</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_notifications.php">Notifications</a></li>
            <li class="nav-item"><a class="nav-link" href="issue_tracker.php">Issue Tracker</a></li>
            <li class="nav-item"><a class="nav-link" href="system_settings.php">Settings</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center">Audit Logs</h2>

    <!-- Filter Form -->
    <form id="filterForm" class="form-inline my-3">
        <div class="form-group mr-3">
            <label for="action_filter" class="mr-2">Action</label>
            <select name="action_filter" id="action_filter" class="form-control">
                <option value="">All</option>
                <option value="login">Login</option>
                <option value="logout">Logout</option>
                <option value="add">Add</option>
                <option value="edit">Edit</option>
                <option value="delete">Delete</option>
            </select>
        </div>
        <div class="form-group mr-3">
            <label for="date_filter" class="mr-2">Date</label>
            <input type="date" name="date_filter" id="date_filter" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <!-- Audit Log Table -->
    <div id="logTable">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Action Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($audit_logs as $log): ?>
                    <tr>
                        <td><?php echo $log['log_id']; ?></td>
                        <td><?php echo $log['user_id']; ?></td>
                        <td><?php echo ucfirst($log['action']); ?></td>
                        <td><?php echo $log['details']; ?></td>
                        <td><?php echo $log['action_time']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- jQuery and Bootstrap JS for Modals -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- AJAX Code for Filter -->
<script>
$('#filterForm').on('submit', function(e) {
    e.preventDefault();
    var actionFilter = $('#action_filter').val();
    var dateFilter = $('#date_filter').val();
    
    $.ajax({
        url: 'audit_logs.php',
        type: 'POST',
        data: {
            action_filter: actionFilter,
            date_filter: dateFilter
        },
        success: function(data) {
            var newDoc = document.open("text/html", "replace");
            newDoc.write(data);
            newDoc.close();
        }
    });
});
</script>

</body>
</html>
