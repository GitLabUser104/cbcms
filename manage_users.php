<?php
// Start session and include database connection
session_start();
include('db_connection.php');

// Function to log actions in the audit log
function logAction($userId, $action, $conn) {
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, action, ip_address, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $userId, $action, $ipAddress);
    $stmt->execute();
    $stmt->close();
}

// Handle add, edit, and delete operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add or edit user
    if (isset($_POST['username'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $user_id = $_POST['user_id'] ?? null;

        // If adding a new user, hash the password
        if (!$user_id && isset($_POST['password'])) {
            $password = md5($_POST['password']); // Use MD5 for compatibility, but consider using a stronger hash in production
            $query = "INSERT INTO users (username, password, email, role, created_at) VALUES ('$username', '$password', '$email', '$role', NOW())";
            mysqli_query($conn, $query);
            
            // Log action in the audit log
            $newUserId = mysqli_insert_id($conn); // Get the ID of the newly inserted user
            logAction($newUserId, "User added: $username", $conn);
        } else { // Update existing user
            $query = "UPDATE users SET username = '$username', email = '$email', role = '$role' WHERE user_id = $user_id";
            mysqli_query($conn, $query);
            
            // Log action in the audit log
            logAction($user_id, "User updated: $username", $conn);
        }
        exit;
    }

    // Delete user
    if (isset($_POST['delete_user_id'])) {
        $user_id = $_POST['delete_user_id'];
        $query = "DELETE FROM users WHERE user_id = $user_id";
        mysqli_query($conn, $query);

        // Log action in the audit log
        logAction($user_id, "User deleted: ID $user_id", $conn);
        exit;
    }
}

// Fetch all users from the database
function fetchUsers($conn) {
    $query = "SELECT * FROM users ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    return $users;
}

$users = fetchUsers($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
            <li class="nav-item"><a class="nav-link active" href="manage_users.php">Users</a></li>
            <li class="nav-item"><a class="nav-link" href="compliance_tasks.php">Compliance Tasks</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_documents.php">Documents</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_policies.php">Policies</a></li>
            <li class="nav-item"><a class="nav-link" href="audit_logs.php">Audit Logs</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_notifications.php">Notifications</a></li>
            <li class="nav-item"><a class="nav-link" href="issue_tracker.php">Issue Tracker</a></li>
            <li class="nav-item"><a class="nav-link" href="system_settings.php">Settings</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center">Manage Users</h2>

    <!-- Add User Button -->
    <button type="button" class="btn btn-primary my-3" data-toggle="modal" data-target="#userModal" onclick="openAddUserModal()">Add User</button>

    <!-- User Table -->
    <div id="userTable">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['user_id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo ucfirst($user['role']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="openEditUserModal(<?php echo $user['user_id']; ?>, '<?php echo $user['username']; ?>', '<?php echo $user['email']; ?>', '<?php echo $user['role']; ?>')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['user_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- User Modal Structure -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="userForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Add New User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="user_id">
                    <input type="text" class="form-control mb-3" name="username" id="username" placeholder="Username" required>
                    <input type="email" class="form-control mb-3" name="email" id="email" placeholder="Email" required>
                    <input type="password" class="form-control mb-3" name="password" id="password" placeholder="Password" required>
                    <select class="form-control mb-3" name="role" id="role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save User</button>
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
function openAddUserModal() {
    $('#userModalLabel').text('Add New User');
    $('#userForm')[0].reset();
    $('#user_id').val('');
    $('#password').prop('required', true);
    $('#userModal').modal('show');
}

function openEditUserModal(id, username, email, role) {
    $('#userModalLabel').text('Edit User');
    $('#user_id').val(id);
    $('#username').val(username);
    $('#email').val(email);
    $('#role').val(role);
    $('#password').prop('required', false); // Password optional for editing
    $('#userModal').modal('show');
}

$('#userForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        url: 'manage_users.php',
        type: 'POST',
        data: formData,
        success: function() {
            $('#userModal').modal('hide');
            refreshUserTable();
        }
    });
});

function deleteUser(id) {
    if(confirm("Are you sure you want to delete this user?")) {
        $.ajax({
            url: 'manage_users.php',
            type: 'POST',
            data: { delete_user_id: id },
            success: function() {
                refreshUserTable();
            }
        });
    }
}

function refreshUserTable() {
    $.ajax({
        url: 'manage_users.php',
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
