<?php
session_start(); // Start the session to access admin information

$servername = "localhost";
$username = "u132092183_distinct";
$password = "Distinct@2024";
$dbname = "u132092183_distinct";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming admin's email is stored in session upon login
$email = $_SESSION['email'] ?? null;

// Ensure the admin is logged in
if (!$email) {
    die("Unauthorized access. Please login.");
}

// Fetch admin's department using the email
$dept_query = "SELECT admin_department FROM admins WHERE email = ?";
$stmt = $conn->prepare($dept_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$dept_result = $stmt->get_result();

if ($dept_result->num_rows > 0) {
    $admin_row = $dept_result->fetch_assoc();
    $admin_department = $admin_row['admin_department'];
} else {
    die("Department not found for this admin.");
}

// Fetch students who qualified for the Dean's List
$sql = "SELECT student_id, student_name, course, major, year_level, semester, average_grade FROM deans_list_averages WHERE deans_list_status = 'Yes'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dean's List Report</title>
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            background-color: #ffffff;
            margin: 0;
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
            text-align: center;
            margin-bottom: 30px;
        }

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

        th {
            background-color: maroon;
            color: white;
        }
    </style>
</head>
<body>

    <a href="dashboard.php">
        <img src="../img/homeicon.png" alt="Home" class="home-icon">
    </a>

    <h1>Dean's List Results - Department: <?php echo htmlspecialchars($admin_department); ?></h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Major</th>
                    <th>Year Level</th>
                    <th>Semester</th>
                    <th>Average Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['student_id']; ?></td>
                        <td><?php echo $row['student_name']; ?></td>
                        <td><?php echo $row['course']; ?></td>
                        <td><?php echo $row['major']; ?></td>
                        <td><?php echo $row['year_level']; ?></td>
                        <td><?php echo $row['semester']; ?></td>
                        <td><?php echo $row['average_grade']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No students from your department qualified for the Dean's List.</p>
    <?php endif; ?>

</body>
</html>

<?php
$conn->close();
?>