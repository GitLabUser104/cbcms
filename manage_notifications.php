<?php
// Start session and include database connection
session_start();
include('db_connection.php');

// Handle add, edit, and delete operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add or edit notification
    if (isset($_POST['notification_text'])) {
        $notification_text = $_POST['notification_text'];
        $notification_type = $_POST['notification_type'];
        $notification_id = $_POST['notification_id'] ?? null;

        if ($notification_id) { // Update existing notification
            $query = "UPDATE notifications SET message = '$notification_text', notification_type = '$notification_type' WHERE notification_id = $notification_id";
        } else { // Insert new notification
            $query = "INSERT INTO notifications (message, notification_type, created_at) VALUES ('$notification_text', '$notification_type', NOW())";
        }
        mysqli_query($conn, $query);
        exit;
    }

    // Delete notification
    if (isset($_POST['delete_notification_id'])) {
        $notification_id = $_POST['delete_notification_id'];
        $query = "DELETE FROM notifications WHERE notification_id = $notification_id";
        mysqli_query($conn, $query);
        exit;
    }
}

// Fetch all notifications from the database
function fetchNotifications($conn) {
    $query = "SELECT * FROM notifications ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    $notifications = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }
    return $notifications;
}

$notifications = fetchNotifications($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notifications</title>
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
            <li class="nav-item"><a class="nav-link" href="audit_logs.php">Audit Logs</a></li>
            <li class="nav-item"><a class="nav-link active" href="manage_notifications.php">Notifications</a></li>
            <li class="nav-item"><a class="nav-link" href="issue_tracker.php">Issue Tracker</a></li>
            <li class="nav-item"><a class="nav-link" href="system_settings.php">Settings</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center">Manage Notifications</h2>

    <!-- Add Notification Button -->
    <button type="button" class="btn btn-primary my-3" data-toggle="modal" data-target="#notificationModal" onclick="openAddNotificationModal()">Add Notification</button>

    <!-- Notification Table -->
    <div id="notificationTable">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Notification Text</th>
                    <th>Type</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notifications as $notification): ?>
                    <tr>
                        <td><?php echo $notification['notification_id']; ?></td>
                        <td><?php echo $notification['message']; ?></td>
                        <td><?php echo ucfirst($notification['notification_type']); ?></td>
                        <td><?php echo $notification['created_at']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="openEditNotificationModal(<?php echo $notification['notification_id']; ?>, '<?php echo $notification['message']; ?>', '<?php echo $notification['notification_type']; ?>')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteNotification(<?php echo $notification['notification_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Notification Modal Structure -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="notificationForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Add New Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="notification_id" id="notification_id">
                    <textarea class="form-control mb-3" name="notification_text" id="notification_text" placeholder="Notification Text" required></textarea>
                    <select class="form-control mb-3" name="notification_type" id="notification_type" required>
                        <option value="info">Info</option>
                        <option value="warning">Warning</option>
                        <option value="alert">Alert</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Notification</button>
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
function openAddNotificationModal() {
    $('#notificationModalLabel').text('Add New Notification');
    $('#notificationForm')[0].reset();
    $('#notification_id').val('');
    $('#notificationModal').modal('show');
}

function openEditNotificationModal(id, message, type) {
    $('#notificationModalLabel').text('Edit Notification');
    $('#notification_id').val(id);
    $('#notification_text').val(message);
    $('#notification_type').val(type);
    $('#notificationModal').modal('show');
}

$('#notificationForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        url: 'manage_notifications.php',
        type: 'POST',
        data: formData,
        success: function() {
            $('#notificationModal').modal('hide');
            refreshNotificationTable();
        }
    });
});

function deleteNotification(id) {
    if(confirm("Are you sure you want to delete this notification?")) {
        $.ajax({
            url: 'manage_notifications.php',
            type: 'POST',
            data: { delete_notification_id: id },
            success: function() {
                refreshNotificationTable();
            }
        });
    }
}

function refreshNotificationTable() {
    $.ajax({
        url: 'manage_notifications.php',
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
