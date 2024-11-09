<?php
// Start session and include database connection
session_start();
include('db_connection.php');

// Fetch user emails for dropdown
function fetchUserEmails($conn) {
    $query = "SELECT email FROM users";
    $result = mysqli_query($conn, $query);
    $emails = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $emails[] = $row['email'];
    }
    return $emails;
}


// Handle add, edit, delete, and export operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the form submission is for adding or editing an issue
    if (isset($_POST['issue_description'])) {
        $issue_description = trim($_POST['issue_description']);
        $status = trim($_POST['status']);
        $assigned_to = trim($_POST['assigned_to']);
        $issue_id = isset($_POST['issue_id']) ? intval($_POST['issue_id']) : null;

        if ($issue_id) { // Update existing issue
            $stmt = $conn->prepare("UPDATE issue_tracker SET issue_description = ?, status = ?, assigned_to = ? WHERE issue_id = ?");
            $stmt->bind_param("sssi", $issue_description, $status, $assigned_to, $issue_id);
        } else { // Insert new issue
            $stmt = $conn->prepare("INSERT INTO issue_tracker (issue_description, status, assigned_to, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $issue_description, $status, $assigned_to);
        }

        // Execute the query and handle errors
        if ($stmt->execute()) {
            echo "Record added/updated successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        exit;
    }

    // Delete issue
    if (isset($_POST['delete_issue_id'])) {
        $issue_id = intval($_POST['delete_issue_id']);
        $stmt = $conn->prepare("DELETE FROM issue_tracker WHERE issue_id = ?");
        $stmt->bind_param("i", $issue_id);

        if ($stmt->execute()) {
            echo "Record deleted successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        exit;
    }

    // Export issues to CSV
    if (isset($_POST['export_issues'])) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=issue_report.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'Description', 'Status', 'Assigned To', 'Created At'));

        $query = "SELECT * FROM issue_tracker ORDER BY created_at DESC";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }
}

// Fetch all issues from the database
function fetchIssues($conn) {
    $query = "SELECT * FROM issue_tracker ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error fetching issues: " . mysqli_error($conn));
    }

    $issues = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $issues[] = $row;
    }
    return $issues;
}

$issues = fetchIssues($conn);
$userEmails = fetchUserEmails($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Tracker</title>
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
            <li class="nav-item"><a class="nav-link" href="manage_notifications.php">Notifications</a></li>
            <li class="nav-item"><a class="nav-link active" href="issue_tracker.php">Issue Tracker</a></li>
            <li class="nav-item"><a class="nav-link" href="system_settings.php">Settings</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center">Issue Tracker</h2>

    <!-- Export Report Button -->
    <form method="POST" action="issue_tracker.php">
        <button type="submit" name="export_issues" class="btn btn-success mb-3">Export Report </button>
    </form>

    <!-- Add Issue Button -->
    <button type="button" class="btn btn-primary my-3" data-toggle="modal" data-target="#issueModal" onclick="openAddIssueModal()">Add Issue</button>

    <!-- Issue Table -->
    <div id="issueTable">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($issues as $issue): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($issue['issue_id']); ?></td>
                        <td><?php echo htmlspecialchars($issue['issue_description']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($issue['status'])); ?></td>
                        <td><?php echo htmlspecialchars($issue['assigned_to']); ?></td>
                        <td><?php echo htmlspecialchars($issue['created_at']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="openEditIssueModal(<?php echo $issue['issue_id']; ?>, '<?php echo htmlspecialchars($issue['issue_description']); ?>', '<?php echo $issue['status']; ?>', '<?php echo $issue['assigned_to']; ?>')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteIssue(<?php echo $issue['issue_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Issue Modal Structure -->
<div class="modal fade" id="issueModal" tabindex="-1" aria-labelledby="issueModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="issueForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="issueModalLabel">Add New Issue</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="issue_id" id="issue_id">
                    <textarea class="form-control mb-3" name="issue_description" id="issue_description" placeholder="Issue Description" required></textarea>
                    <select class="form-control mb-3" name="status" id="status" required>
                        <option value="open">Open</option>
                        <option value="in-progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                    </select>
                     <select class="form-control mb-3" name="assigned_to" id="assigned_to" required>
                        <option value="">Select Assigned Email</option>
                        <?php foreach ($userEmails as $email): ?>
                            <option value="<?php echo $email; ?>"><?php echo $email; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Issue</button>
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
function openAddIssueModal() {
    $('#issueModalLabel').text('Add New Issue');
    $('#issueForm')[0].reset();
    $('#issue_id').val('');
    $('#issueModal').modal('show');
}

function openEditIssueModal(id, description, status, assigned_to) {
    $('#issueModalLabel').text('Edit Issue');
    $('#issue_id').val(id);
    $('#issue_description').val(description);
    $('#status').val(status);
    $('#assigned_to').val(assigned_to);
    $('#issueModal').modal('show');
}

$('#issueForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        url: 'issue_tracker.php',
        type: 'POST',
        data: formData,
        success: function() {
            $('#issueModal').modal('hide');
            refreshIssueTable();
        }
    });
});

function deleteIssue(id) {
    if (confirm("Are you sure you want to delete this issue?")) {
        $.ajax({
            url: 'issue_tracker.php',
            type: 'POST',
            data: { delete_issue_id: id },
            success: function() {
                refreshIssueTable();
            }
        });
    }
}

function refreshIssueTable() {
    $.ajax({
        url: 'issue_tracker.php',
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
