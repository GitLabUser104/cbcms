<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compliance Management System</title>
    <!-- Bootstrap CSS and Font Awesome for Icons -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Compliance Management</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="btn btn-primary" href="login.php">Login</a>
            </li>
        </ul>
    </div>
</nav>

<!-- Header Section -->
<header class="bg-primary text-white text-center py-5">
    <div class="container">
        <h1 class="display-4">Welcome to the Compliance Management System</h1>
        <p class="lead">Efficiently manage compliance tasks, documents, users, and policies in one place.</p>
    </div>
</header>

<!-- Features Section -->
<section class="container py-5">
    <div class="row text-center">
        <!-- Card 1: User Management -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">User Management</h5>
                    <p class="card-text">Manage user accounts, roles, and permissions to ensure secure and structured access to system resources.</p>
                </div>
            </div>
        </div>
        <!-- Card 2: Compliance Tasks -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <i class="fas fa-tasks fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Compliance Tasks</h5>
                    <p class="card-text">Define, assign, and monitor compliance tasks with deadlines and priority settings for better tracking.</p>
                </div>
            </div>
        </div>
        <!-- Card 3: Document Management -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <i class="fas fa-file-alt fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Document Management</h5>
                    <p class="card-text">Upload, view, and manage important compliance documents in a secure and accessible environment.</p>
                </div>
            </div>
        </div>
        <!-- Card 4: Policy Management -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <i class="fas fa-book fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Policy Management</h5>
                    <p class="card-text">Create, edit, and publish policies to ensure organization-wide compliance with regulations and standards.</p>
                </div>
            </div>
        </div>
        <!-- Card 5: Audit Logs -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <i class="fas fa-clipboard-list fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Audit Logs</h5>
                    <p class="card-text">Track and monitor all actions performed in the system for accountability and transparency.</p>
                </div>
            </div>
        </div>
        <!-- Card 6: Notifications -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <i class="fas fa-bell fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Notifications</h5>
                    <p class="card-text">Stay informed with real-time notifications on important compliance updates and alerts.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer Section -->
<footer class="bg-dark text-white text-center py-4">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date("Y"); ?> Compliance Management System. All Rights Reserved.</p>
    </div>
</footer>

<!-- Bootstrap JS and jQuery -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
