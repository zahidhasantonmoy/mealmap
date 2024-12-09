<?php
// db.php - Database Connection
$host = "sql302.infinityfree.com"; // Replace with your host
$username = "if0_37587887";        // Replace with your username
$password = "cTOvQVmKy5FMKU";      // Replace with your password
$database = "if0_37587887_mealmap"; // Replace with your database name

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
