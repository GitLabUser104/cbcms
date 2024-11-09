<?php
// Database configuration
$host = 'AZURE_MYSQL_HOST';           // Database host (usually 'localhost')
$db_name = 'AZURE_MYSQL_DBNAME'; // Name of the database
$username = 'AZURE_MYSQL_USERNAME'; // Database username
$password = 'AZURE_MYSQL_PASSWORD'; // Database password

// Create a new MySQLi connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
