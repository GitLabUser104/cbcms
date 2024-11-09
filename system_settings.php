<?php
// Start session and include database connection
session_start();
include('db_connection.php');

// Handle add, edit, and delete operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add or edit setting
    if (isset($_POST['setting_name'])) {
        $setting_name = $_POST['setting_name'];
        $setting_value = $_POST['setting_value'];
        $description = $_POST['description'];
        $setting_id = $_POST['setting_id'] ?? null;

        if ($setting_id) { // Update existing setting
            $query = "UPDATE system_settings SET setting_name = '$setting_name', setting_value = '$setting_value', description = '$description' WHERE setting_id = $setting_id";
        } else { // Insert new setting
            $query = "INSERT INTO system_settings (setting_name, setting_value, description, updated_at) VALUES ('$setting_name', '$setting_value', '$description', NOW())";
        }
        mysqli_query($conn, $query);
        exit;
    }

    // Delete setting
    if (isset($_POST['delete_setting_id'])) {
        $setting_id = $_POST['delete_setting_id'];
        $query = "DELETE FROM system_settings WHERE setting_id = $setting_id";
        mysqli_query($conn, $query);
        exit;
    }
}

// Fetch all system settings from the database
function fetchSettings($conn) {
    $query = "SELECT * FROM system_settings ORDER BY updated_at DESC";
    $result = mysqli_query($conn, $query);
    $settings = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $settings[] = $row;
    }
    return $settings;
}

// Fetch the specific setting, like 'max_logon_attempts'
function getSetting($conn, $setting_name) {
    $query = "SELECT setting_value FROM system_settings WHERE setting_name = '$setting_name' LIMIT 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row ? $row['setting_value'] : null;
}

$settings = fetchSettings($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings</title>
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
            <li class="nav-item"><a class="nav-link" href="issue_tracker.php">Issue Tracker</a></li>
            <li class="nav-item"><a class="nav-link active" href="system_settings.php">Settings</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center">System Settings</h2>

    <!-- Add Setting Button -->
    <button type="button" class="btn btn-primary my-3" data-toggle="modal" data-target="#settingModal" onclick="openAddSettingModal()">Add Setting</button>

    <!-- Settings Table -->
    <div id="settingsTable">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Setting Name</th>
                    <th>Value</th>
                    <th>Description</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($settings as $setting): ?>
                    <tr>
                        <td><?php echo $setting['setting_id']; ?></td>
                        <td><?php echo $setting['setting_name']; ?></td>
                        <td><?php echo $setting['setting_value']; ?></td>
                        <td><?php echo $setting['description']; ?></td>
                        <td><?php echo $setting['updated_at']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="openEditSettingModal(<?php echo $setting['setting_id']; ?>, '<?php echo $setting['setting_name']; ?>', '<?php echo $setting['setting_value']; ?>', '<?php echo $setting['description']; ?>')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteSetting(<?php echo $setting['setting_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Setting Modal Structure -->
<div class="modal fade" id="settingModal" tabindex="-1" aria-labelledby="settingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="settingForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="settingModalLabel">Add New Setting</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="setting_id" id="setting_id">
                    <input type="text" class="form-control mb-3" name="setting_name" id="setting_name" placeholder="Setting Name" required>
                    <input type="text" class="form-control mb-3" name="setting_value" id="setting_value" placeholder="Setting Value" required>
                    <textarea class="form-control mb-3" name="description" id="description" placeholder="Description" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Setting</button>
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
function openAddSettingModal() {
    $('#settingModalLabel').text('Add New Setting');
    $('#settingForm')[0].reset();
    $('#setting_id').val('');
    $('#settingModal').modal('show');
}

function openEditSettingModal(id, name, value, description) {
    $('#settingModalLabel').text('Edit Setting');
    $('#setting_id').val(id);
    $('#setting_name').val(name);
    $('#setting_value').val(value);
    $('#description').val(description);
    $('#settingModal').modal('show');
}

$('#settingForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        url: 'system_settings.php',
        type: 'POST',
        data: formData,
        success: function() {
            $('#settingModal').modal('hide');
            refreshSettingsTable();
        }
    });
});

function deleteSetting(id) {
    if(confirm("Are you sure you want to delete this setting?")) {
        $.ajax({
            url: 'system_settings.php',
            type: 'POST',
            data: { delete_setting_id: id },
            success: function() {
                refreshSettingsTable();
            }
        });
    }
}

function refreshSettingsTable() {
    $.ajax({
        url: 'system_settings.php',
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
