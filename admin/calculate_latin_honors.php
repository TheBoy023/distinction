<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the student_id from the form
    $student_id = $_POST['student_id'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "finals");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Initialize variables
    $total_grades = 0;
    $total_semesters = 0;
    $average_grade = 0.0;
    $honor_message = "";

    // Query to retrieve the student's grades across all semesters for this specific student
    $sql = "SELECT student_id, semester, average_grade FROM deans_list_averages WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $student_id); // Bind the student_id to the query
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Loop through the results to calculate the overall average
        while ($row = $result->fetch_assoc()) {
            $total_grades += $row['average_grade'];
            $total_semesters++;
        }

        // Calculate the overall average
        if ($total_semesters > 0) {
            $average_grade = $total_grades / $total_semesters;
        }

        // Determine Latin Honor based on the overall average
        if ($average_grade <= 1.20) {
            $honor_message = "Summa Cum Laude";
        } elseif ($average_grade >= 1.21 && $average_grade <= 1.50) {
            $honor_message = "Magna Cum Laude";
        } elseif ($average_grade >= 1.51 && $average_grade <= 1.75) {
            $honor_message = "Cum Laude";
        } else {
            $honor_message = "No Latin Honor";
        }
    } else {
        $honor_message = "No records found for Student ID: $student_id";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latin Honor Calculation</title>
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            text-align: center;
            padding: 50px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .home-icon {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 40px;
            height: 40px;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }
        .home-icon:hover {
            transform: scale(1.1);
        }
        h1 {
            color: maroon;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: maroon;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .honor-message {
            margin-top: 20px;
            font-size: 1.5em;
            color: maroon;
        }
    </style>
</head>
<body>

    <a href="dashboard.php">
        <img src="../img/homeicon.png" alt="Home" class="home-icon">
    </a>

    <div class="container">
        <h1>Latin Honor Calculation</h1>

        <!-- Form to input student ID -->
        <form method="POST" action="">
            <label for="student_id">Enter Student ID:</label>
            <input type="text" id="student_id" name="student_id" required>
            <button type="submit">Calculate</button>
        </form>

        <?php if (isset($honor_message)): ?>
        <table>
            <tr>
                <th>Student ID</th>
                <td><?php echo htmlspecialchars($student_id); ?></td>
            </tr>
            <tr>
                <th>Overall Average</th>
                <td><?php echo number_format($average_grade, 2); ?></td>
            </tr>
            <tr>
                <th>Latin Honor</th>
                <td><?php echo $honor_message; ?></td>
            </tr>
        </table>

        <div class="honor-message">
            <?php echo $honor_message; ?>
        </div>
        <?php endif; ?>
    </div>

</body>
</html>