<?php
// Database connection settings
$servername = "localhost";
$username = "u132092183_distinct"; // Replace with your actual database username
$password = "Distinct@2024"; // Replace with your actual database password
$dbname = "u132092183_distinct";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>