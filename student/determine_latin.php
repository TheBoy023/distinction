<?php
session_start(); // Start the session to access session variables

// Connect to MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "finals";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming the user is logged in and their name and ID are stored in session variables
$studentName = $_SESSION['student_name'] ?? 'Unknown';  // Replace with session key for student name
$studentId = $_SESSION['student_id'] ?? 'Unknown'; // Replace with session key for student ID

// Variables to store form input
$student_name = '';
$year1_sem1 = $year1_sem2 = $year2_sem1 = $year2_sem2 = '';
$year3_sem1 = $year3_sem2 = $year4_sem1 = $year4_sem2 = '';
$average_grade = $honor = '';
$department = $course = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department = $_POST['department'];
    $course = $_POST['course'];  // New course field
    $year1_sem1 = $_POST['year1_sem1'];
    $year1_sem2 = $_POST['year1_sem2'];
    $year2_sem1 = $_POST['year2_sem1'];
    $year2_sem2 = $_POST['year2_sem2'];
    $year3_sem1 = $_POST['year3_sem1'];
    $year3_sem2 = $_POST['year3_sem2'];
    $year4_sem1 = $_POST['year4_sem1'];
    $year4_sem2 = $_POST['year4_sem2'];

    // Calculate average
    $total_grades = $year1_sem1 + $year1_sem2 + $year2_sem1 + $year2_sem2 + $year3_sem1 + $year3_sem2 + $year4_sem1 + $year4_sem2;
    $average_grade = $total_grades / 8;

    // Determine Latin honor
    if ($average_grade <= 1.24) {
        $honor = "Summa Cum Laude";
    } elseif ($average_grade <= 1.49) {
        $honor = "Magna Cum Laude";
    } elseif ($average_grade <= 1.75) {
        $honor = "Cum Laude";
    } else {
        $honor = "No Honor";
    }

    // Save data to database
    $sql = "INSERT INTO latin_grades (student_name, student_id, department, course, year1_sem1, year1_sem2, year2_sem1, year2_sem2, year3_sem1, year3_sem2, year4_sem1, year4_sem2, average_grade, honor)
    VALUES ('$studentName', '$studentId', '$department', '$course', '$year1_sem1', '$year1_sem2', '$year2_sem1', '$year2_sem2', '$year3_sem1', '$year3_sem2', '$year4_sem1', '$year4_sem2', '$average_grade', '$honor')";

    if ($conn->query($sql) === TRUE) {
        echo "<div style='text-align: center; color: green;'>Record saved successfully. Average Grade: $average_grade, Honor: $honor</div>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latin Honors Calculator</title>
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

        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: maroon;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input:focus,
        select:focus {
            border-color: maroon;
            outline: none;
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

        button {
            background-color: maroon;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: red;
        }

        /* Additional styling for table (if needed later) */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>

    <a href="dashboard.php">
        <img src="../img/homeicon.png" alt="Home" class="home-icon">
    </a>

    <h1>Latin Honors Calculator</h1>

    <form method="POST" action="determine_latin.php">
        <label for="student_name">Student Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $studentName; ?>" disabled>

        <label for="student_id">Student ID:</label>
        <input type="text" id="id" name="id" value="<?php echo $studentId; ?>" disabled>

        <label for="department">Department:</label>
        <input type="text" id="department" name="department" required>

        <label for="course">Course:</label>
        <input type="text" id="course" name="course" required>

        <label for="year1_sem1">1st Year, 1st Semester Grade:</label>
        <input type="number" step="0.01" id="year1_sem1" name="year1_sem1" required>

        <label for="year1_sem2">1st Year, 2nd Semester Grade:</label>
        <input type="number" step="0.01" id="year1_sem2" name="year1_sem2" required>

        <label for="year2_sem1">2nd Year, 1st Semester Grade:</label>
        <input type="number" step="0.01" id="year2_sem1" name="year2_sem1" required>

        <label for="year2_sem2">2nd Year, 2nd Semester Grade:</label>
        <input type="number" step="0.01" id="year2_sem2" name="year2_sem2" required>

        <label for="year3_sem1">3rd Year, 1st Semester Grade:</label>
        <input type="number" step="0.01" id="year3_sem1" name="year3_sem1" required>

        <label for="year3_sem2">3rd Year, 2nd Semester Grade:</label>
        <input type="number" step="0.01" id="year3_sem2" name="year3_sem2" required>

        <label for="year4_sem1">4th Year, 1st Semester Grade:</label>
        <input type="number" step="0.01" id="year4_sem1" name="year4_sem1" required>

        <label for="year4_sem2">4th Year, 2nd Semester Grade:</label>
        <input type="number" step="0.01" id="year4_sem2" name="year4_sem2" required>

        <button type="submit">Calculate Honor</button>
    </form>

</body>
</html>