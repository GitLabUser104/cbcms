<?php
// Start session and include database connection
session_start();
include('db_connection.php');

// Sample query to get counts of tasks, users, and documents for the dashboard
$task_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM compliance_tasks"))['count'];
$issue_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM issue_tracker"))['count'];
$document_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM documents"))['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: 0.3s;
        }
        .card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .nav-link.active {
            font-weight: bold;
            color: #007bff !important;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="user_dashboard.php">User Dashboard</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
			<li class="nav-item"><a class="nav-link" href="user_manage_users.php">Users</a></li>
            <li class="nav-item"><a class="nav-link" href="user_compliance_tasks.php">Compliance Tasks</a></li>
            <li class="nav-item"><a class="nav-link" href="user_manage_documents.php">Documents</a></li>
            <li class="nav-item"><a class="nav-link" href="user_manage_policies.php">Policies</a></li>
            <li class="nav-item"><a class="nav-link" href="user_issue_tracker.php">Issues</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<!-- Main Dashboard Content -->
<div class="container mt-4">
    <h2 class="text-center mb-4">Welcome to the User Dashboard</h2>

    <div class="row">
        <!-- Issue Card -->
        <div class="col-md-4">
            <div class="card text-center bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Issues in tracker</h5>
                    <p class="card-text display-4"><?php echo $issue_count; ?></p>
                    <a href="user_issue_tracker.php" class="btn btn-outline-light">View Issues</a>
                </div>
            </div>
        </div>
        <!-- Compliance Tasks Card -->
        <div class="col-md-4">
            <div class="card text-center bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Compliance Tasks</h5>
                    <p class="card-text display-4"><?php echo $task_count; ?></p>
                    <a href="user_compliance_tasks.php" class="btn btn-outline-light">View Tasks</a>
                </div>
            </div>
        </div>
        <!-- Documents Card -->
        <div class="col-md-4">
            <div class="card text-center bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Documents</h5>
                    <p class="card-text display-4"><?php echo $document_count; ?></p>
                    <a href="user_manage_documents.php" class="btn btn-outline-light">View Documents</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Notifications and Recent Activity -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">Recent Notifications</div>
                <div class="card-body">
                    <?php
                    // Sample query for recent notifications
                    $notifications = mysqli_query($conn, "SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5");
                    while ($notification = mysqli_fetch_assoc($notifications)) {
                        echo "<p>{$notification['message']} - <small class='text-muted'>{$notification['created_at']}</small></p>";
                    }
                    ?>
                    <a href="user_manage_notifications.php" class="btn btn-dark btn-sm">View All Notifications</a>
                </div>
            </div>
        </div>

        <!-- Upcoming Tasks -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">Upcoming Compliance Deadlines</div>
                <div class="card-body">
                    <?php
                    // Sample query for tasks with approaching deadlines
                    $upcoming_tasks = mysqli_query($conn, "SELECT * FROM compliance_tasks WHERE due_date > NOW() ORDER BY due_date ASC LIMIT 5");
                    while ($task = mysqli_fetch_assoc($upcoming_tasks)) {
                        echo "<p>{$task['task_name']} - <small class='text-muted'>Due by: {$task['due_date']}</small></p>";
                    }
                    ?>
                    <a href="user_compliance_tasks.php" class="btn btn-dark btn-sm">View All Tasks</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap and jQuery JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
