<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "finals";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch recent updates
$recent_updates_sql = "SELECT message, timestamp FROM recent_updates ORDER BY timestamp DESC LIMIT 5";
$result = $conn->query($recent_updates_sql);

$updates = [];
if ($result->num_rows > 0) {
    // Gather updates
    while($row = $result->fetch_assoc()) {
        $updates[] = [
            'message' => $row['message'],
            'timestamp' => date("F j, Y, g:i a", strtotime($row['timestamp']))
        ];
    }
}

// Return updates as JSON
header('Content-Type: application/json');
echo json_encode($updates);

$conn->close();
?>