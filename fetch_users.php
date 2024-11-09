<?php
include('db_connection.php');
$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);

echo '<table class="table table-bordered">';
echo '<thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead><tbody>';
while ($row = mysqli_fetch_assoc($result)) {
    echo '<tr>';
    echo '<td>' . $row['user_id'] . '</td>';
    echo '<td>' . $row['username'] . '</td>';
    echo '<td>' . $row['email'] . '</td>';
    echo '<td>' . ucfirst($row['role']) . '</td>';
    echo '<td>';
    echo '<button class="btn btn-sm btn-warning edit-user" data-id="' . $row['user_id'] . '">Edit</button> ';
    echo '<button class="btn btn-sm btn-danger delete-user" data-id="' . $row['user_id'] . '">Delete</button>';
    echo '</td>';
    echo '</tr>';
}
echo '</tbody></table>';
?>
