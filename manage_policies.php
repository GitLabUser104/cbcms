<?php
// Start session and include database connection
session_start();
include('db_connection.php');

// Handle add, edit, and delete operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add or edit policy
    if (isset($_POST['policy_name'])) {
        $policy_name = $_POST['policy_name'];
        $description = $_POST['description'];
        $policy_id = $_POST['policy_id'] ?? null;

        if ($policy_id) { // Update existing policy
            $query = "UPDATE policies SET policy_name = '$policy_name', description = '$description' WHERE policy_id = $policy_id";
        } else { // Insert new policy
            $query = "INSERT INTO policies (policy_name, description, created_at) VALUES ('$policy_name', '$description', NOW())";
        }
        mysqli_query($conn, $query);
        exit;
    }

    // Delete policy
    if (isset($_POST['delete_policy_id'])) {
        $policy_id = $_POST['delete_policy_id'];
        $query = "DELETE FROM policies WHERE policy_id = $policy_id";
        mysqli_query($conn, $query);
        exit;
    }

    // Export policies to CSV
    if (isset($_POST['export_policies'])) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=policies.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'Policy Name', 'Description', 'Created At'));

        $query = "SELECT * FROM policies ORDER BY created_at DESC";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }
}

// Fetch all policies from the database
function fetchPolicies($conn) {
    $query = "SELECT * FROM policies ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    $policies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $policies[] = $row;
    }
    return $policies;
}

$policies = fetchPolicies($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Policies</title>
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
            <li class="nav-item"><a class="nav-link active" href="manage_policies.php">Policies</a></li>
            <li class="nav-item"><a class="nav-link" href="audit_logs.php">Audit Logs</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_notifications.php">Notifications</a></li>
            <li class="nav-item"><a class="nav-link" href="issue_tracker.php">Issue Tracker</a></li>
            <li class="nav-item"><a class="nav-link" href="system_settings.php">Settings</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center">Manage Policies</h2>

    <!-- Export Button -->
    <form method="POST" action="manage_policies.php">
        <button type="submit" name="export_policies" class="btn btn-success mb-3">Export to CSV</button>
    </form>

    <!-- Add Policy Button -->
    <button type="button" class="btn btn-primary my-3" data-toggle="modal" data-target="#policyModal" onclick="openAddPolicyModal()">Add Policy</button>

    <!-- Policy Table -->
    <div id="policyTable">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Policy Name</th>
                    <th>Description</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($policies as $policy): ?>
                    <tr>
                        <td><?php echo $policy['policy_id']; ?></td>
                        <td><?php echo $policy['policy_name']; ?></td>
                        <td><?php echo $policy['description']; ?></td>
                        <td><?php echo $policy['created_at']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="openEditPolicyModal(<?php echo $policy['policy_id']; ?>, '<?php echo $policy['policy_name']; ?>', '<?php echo $policy['description']; ?>')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deletePolicy(<?php echo $policy['policy_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Policy Modal Structure -->
<div class="modal fade" id="policyModal" tabindex="-1" aria-labelledby="policyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="policyForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="policyModalLabel">Add New Policy</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="policy_id" id="policy_id">
                    <input type="text" class="form-control mb-3" name="policy_name" id="policy_name" placeholder="Policy Name" required>
                    <textarea class="form-control mb-3" name="description" id="description" placeholder="Description" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Policy</button>
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
function openAddPolicyModal() {
    $('#policyModalLabel').text('Add New Policy');
    $('#policyForm')[0].reset();
    $('#policy_id').val('');
    $('#policyModal').modal('show');
}

function openEditPolicyModal(id, name, description) {
    $('#policyModalLabel').text('Edit Policy');
    $('#policy_id').val(id);
    $('#policy_name').val(name);
    $('#description').val(description);
    $('#policyModal').modal('show');
}

$('#policyForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        url: 'manage_policies.php',
        type: 'POST',
        data: formData,
        success: function() {
            $('#policyModal').modal('hide');
            refreshPolicyTable();
        }
    });
});

function deletePolicy(id) {
    if(confirm("Are you sure you want to delete this policy?")) {
        $.ajax({
            url: 'manage_policies.php',
            type: 'POST',
            data: { delete_policy_id: id },
            success: function() {
                refreshPolicyTable();
            }
        });
    }
}

function refreshPolicyTable() {
    $.ajax({
        url: 'manage_policies.php',
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
