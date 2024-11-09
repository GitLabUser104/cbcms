<?php
// Start session and include database connection
session_start();
include('db_connection.php');

// Initialize error message variable
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input values
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); // Hash the input password using MD5

    // Query to fetch user data based on username
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password
        if ($password === $user['password']) { // Compare MD5 hash
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on user role
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
                exit;
            } elseif ($user['role'] == 'user') {
                header("Location: user_dashboard.php");
                exit;
            } else {
                $error_message = 'Invalid role assigned to user.';
            }
        } else {
            $error_message = 'Incorrect password. Please try again.';
        }
    } else {
        $error_message = 'Username not found. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h3 class="text-center mb-4">Login</h3>

            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
