<?php
session_start(); 

// Secure configuration - move to a separate config file
require_once '../assets/database.php';

// Validate user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page or show an error
    header('Location: login.php');
    exit();
}

try {
    // Use prepared statements for ALL database queries
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

    // Retrieve student's information using a prepared statement
    $stmt_user = $pdo->prepare("SELECT student_name, student_id FROM users WHERE email = :email");
    $stmt_user->bindValue(':email', $_SESSION['email'], PDO::PARAM_STR);
    $stmt_user->execute();
    $user_info = $stmt_user->fetch(PDO::FETCH_ASSOC);

    // Set default values if user info is not found
    $studentName = $user_info['student_name'] ?? 'Unknown';
    $studentId = $user_info['student_id'] ?? 'Unknown';

    // Initialize status message variable
    $status_message = '';

    // Check if student ID is valid before querying
    if ($studentId !== 'Unknown') {
        // Use prepared statement for grades query
        $stmt_grades = $pdo->prepare("SELECT average_grade, deans_list_status FROM deans_list_averages WHERE student_id = :student_id");
        $stmt_grades->bindValue(':student_id', $studentId, PDO::PARAM_STR);
        $stmt_grades->execute();
        $grade_info = $stmt_grades->fetch(PDO::FETCH_ASSOC);

        if ($grade_info) {
            $average_grade = $grade_info['average_grade'];
            $deans_list_status = $grade_info['deans_list_status'];

            // Construct status message with proper escaping
            $status_message = sprintf(
                "Student ID: %s<br>" .
                "Average Grade: %.2f<br>" .
                "Dean's List Status: %s",
                htmlspecialchars($studentId, ENT_QUOTES, 'UTF-8'),
                $average_grade,
                $deans_list_status === "Yes" 
                    ? "You're eligible for Dean's List,<br>" . htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') . "<br><br>NOTICE:<br>Congratulations in advance, Technologist!<br>Please wait for the announcement of the awarding ceremony to receive your certificate during the event. Thank you!" 
                    : "You're not eligible for Dean's List,<br>" . htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') . "<br><br>Try again next time and study hard, Technologist!"
            );
        } else {
            $status_message = "No grade uploaded, apply now for Dean's List Technologist.";
        }
    } else {
        $status_message = "Invalid or missing Student ID.";
    }

} catch (PDOException $e) {
    // Log the error instead of displaying it
    error_log('Database error: ' . $e->getMessage());
    $status_message = "An error occurred while retrieving your information.";
}
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

        .form-container {
            max-width: 500px;
            margin: 0 auto;
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
                <td><?php echo htmlspecialchars($studentId, ENT_QUOTES, 'UTF-8'); ?></td>
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