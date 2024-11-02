<?php
session_start(); // Start the session to access session variables

// Database connection
$conn = new mysqli("localhost", "u132092183_distinct", "Distinct@2024", "u132092183_distinct");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$studentName = $_SESSION['student_name'] ?? 'Unknown';  // Replace with session key for student name
$studentId = $_SESSION['student_id'] ?? 'Unknown'; // Replace with session key for student ID
$status_message = ""; // Initialize status message

// Query to retrieve the student's average grade and Dean's List status
if ($studentId !== 'Unknown') {
    $sql = "SELECT average_grade, deans_list_status FROM deans_list_averages WHERE student_id = '$studentId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the student's details
        $row = $result->fetch_assoc();
        $average_grade = $row['average_grade'];
        $deans_list_status = $row['deans_list_status'];

        // Construct status message
        $status_message = "Student ID: $studentId<br>";
        $status_message .= "Average Grade: " . number_format($average_grade, 2) . "<br>";
        $status_message .= "Dean's List Status: " . ($deans_list_status == "Yes" ? "You're eligible for Dean's List,<br>$studentName<br><br>NOTICE:<br>Congratulations in advance, Technologist!<br>Please wait for the announcement of the awarding ceremony to receive your certificate during the event. Thank you!" : "You're not eligible for Dean's List,<br>$studentName<br><br>Try again next time and study hard, Technologist!");
    } else {
        $status_message = "No grade uploaded, apply now for Dean's List Technologist.";
    }
} else {
    $status_message = "Invalid or missing Student ID.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dean's List Status</title>
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            background-color: #ffffff;
            margin: 0;
        }

        h1 {
            color: maroon;
            text-align: center;
            margin-bottom: 30px;
        }

        /* Center the table and make it smaller */
        .form-container {
            max-width: 500px; /* Adjust the width to make it smaller but visible */
            margin: 0 auto; /* Center the container */
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: maroon;
            color: white;
        }

        .home-icon {
            position: absolute;
            top: 5px;
            left: 20px;
            width: 40px;
            height: 40px;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }
        .home-icon:hover {
            transform: scale(1.1);
        }

        /* Center and style the status message */
        .status-message {
            margin-top: 20px;
            font-size: 1.2em;
            color: maroon;
            text-align: center;
        }
    </style>
</head>
<body>

    <a href="dashboard.php">
        <img src="../img/homeicon.png" alt="Home" class="home-icon">
    </a>

    <h1>Check Dean's List Status</h1>

    <div class="form-container">
        <table>
            <tr>
                <th>Student ID:</th>
                <td><?php echo $studentId; ?></td>
            </tr>
        </table>

        <?php if (!empty($status_message)): ?>
            <div class="status-message">
                <p><?php echo $status_message; ?></p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>