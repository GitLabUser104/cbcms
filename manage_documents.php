<?php
// Start session and include database connection
session_start();
include('db_connection.php');

// Handle add, edit, and delete operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add or edit document
    if (isset($_POST['document_name'])) {
        $document_name = mysqli_real_escape_string($conn, $_POST['document_name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $document_id = $_POST['document_id'] ?? null;

        // File upload handling
        $target_file = '';
        if (isset($_FILES['file']) && $_FILES['file']['name']) {
            $file = basename($_FILES['file']['name']);
            $target_dir = "uploads/";
            $target_file = $target_dir . $file;

            // Check if the directory exists, if not create it
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true); // Creates the directory with write permissions
            }

            // Move the uploaded file to the target directory
            if (!move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                die("Error: Unable to upload file. Please check your file permissions.");
            }
        }

        if ($document_id) { // Update existing document
            $query = "UPDATE documents SET document_name = '$document_name', description = '$description' " .
                     ($target_file ? ", file_path = '$target_file' " : "") .
                     "WHERE document_id = $document_id";
        } else { // Insert new document
            if (!$target_file) {
                die("Error: File is required for new document upload.");
            }
            $query = "INSERT INTO documents (document_name, description, file_path, upload_date) VALUES ('$document_name', '$description', '$target_file', NOW())";
        }

        if (!mysqli_query($conn, $query)) {
            die("Database error: " . mysqli_error($conn));
        }
        exit;
    }

    // Delete document
    if (isset($_POST['delete_document_id'])) {
        $document_id = $_POST['delete_document_id'];
        $query = "DELETE FROM documents WHERE document_id = $document_id";
        if (!mysqli_query($conn, $query)) {
            die("Database error: " . mysqli_error($conn));
        }
        exit;
    }
}

// Fetch all documents from the database
function fetchDocuments($conn) {
    $query = "SELECT * FROM documents ORDER BY upload_date DESC";
    $result = mysqli_query($conn, $query);
    $documents = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $documents[] = $row;
    }
    return $documents;
}

$documents = fetchDocuments($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Documents</title>
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
            <li class="nav-item"><a class="nav-link active" href="manage_documents.php">Documents</a></li>
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
    <h2 class="text-center">Manage Documents</h2>

    <!-- Upload Document Button -->
    <button type="button" class="btn btn-primary my-3" data-toggle="modal" data-target="#documentModal" onclick="openAddDocumentModal()">Upload Document</button>

    <!-- Document Table -->
    <div id="documentTable">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Document Name</th>
                    <th>Description</th>
                    <th>Upload Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $document): ?>
                    <tr>
                        <td><?php echo $document['document_id']; ?></td>
                        <td><?php echo $document['document_name']; ?></td>
                        <td><?php echo $document['description']; ?></td>
                        <td><?php echo $document['upload_date']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="openEditDocumentModal(<?php echo $document['document_id']; ?>, '<?php echo $document['document_name']; ?>', '<?php echo $document['description']; ?>')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteDocument(<?php echo $document['document_id']; ?>)">Delete</button>
                            <a href="<?php echo $document['file_path']; ?>" class="btn btn-sm btn-info" download>Download</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Document Modal Structure -->
<div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="documentForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentModalLabel">Upload New Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="document_id" id="document_id">
                    <input type="text" class="form-control mb-3" name="document_name" id="document_name" placeholder="Document Name" required>
                    <textarea class="form-control mb-3" name="description" id="description" placeholder="Description" required></textarea>
                    <input type="file" class="form-control-file mb-3" name="file" id="file" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Document</button>
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
function openAddDocumentModal() {
    $('#documentModalLabel').text('Upload New Document');
    $('#documentForm')[0].reset();
    $('#document_id').val('');
    $('#file').prop('required', true);
    $('#documentModal').modal('show');
}

function openEditDocumentModal(id, name, description) {
    $('#documentModalLabel').text('Edit Document');
    $('#document_id').val(id);
    $('#document_name').val(name);
    $('#description').val(description);
    $('#file').prop('required', false); // Optional file upload for editing
    $('#documentModal').modal('show');
}

$('#documentForm').on('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        url: 'manage_documents.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function() {
            $('#documentModal').modal('hide');
            refreshDocumentTable();
        }
    });
});

function deleteDocument(id) {
    if(confirm("Are you sure you want to delete this document?")) {
        $.ajax({
            url: 'manage_documents.php',
            type: 'POST',
            data: { delete_document_id: id },
            success: function() {
                refreshDocumentTable();
            }
        });
    }
}

function refreshDocumentTable() {
    $.ajax({
        url: 'manage_documents.php',
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
