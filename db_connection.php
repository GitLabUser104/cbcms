<?php
// Database configuration
$host = 'localhost';           // Database host (usually 'localhost')
$db_name = 'compliance_system'; // Name of the database
$username = 'root'; // Database username
$password = ''; // Database password

// Create a new MySQLi connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
