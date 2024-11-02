<?php
session_start(); // Start the session to access admin info

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

// Assuming admin's department is stored in session upon login
$admin_department = $_SESSION['admin_department'] ?? null; // Get admin's department from session

// Ensure the admin is logged in and their department is set
if (!$admin_department) {
    die("Unauthorized access. Please login.");
}

// Update logic for editing honors
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $student_id = $_POST['student_id'];
    $honor = $_POST['honor'];

    $update_sql = "UPDATE latin_grades SET honor='$honor' WHERE student_id='$student_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        echo "<div style='text-align: center; color: green;'>Honor updated successfully for Student ID: $student_id</div>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Fetch all students with their Latin honors
$sql = "SELECT student_id, student_name, department, course, average_grade, honor FROM latin_grades";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View/Edit Latin Honors</title>
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

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: maroon;
            color: white;
            font-weight: bold;
        }

        td {
            background-color: #f9f9f9;
        }

        select, button {
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        select {
            width: 100%;
        }

        select:focus {
            border-color: maroon;
            outline: none;
        }

        button {
            background-color: maroon;
            color: white;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s;
            margin-top: 10px;
            padding: 10px 20px;
        }

        button:hover {
            background-color: red;
        }

        /* Table hover effect */
        tr:hover {
            background-color: #f1f1f1;
        }

        /* Styling for forms within the table */
        form {
            display: inline-block;
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
    </style>
</head>
<body>

    <a href="dashboard.php">
        <img src="../img/homeicon.png" alt="Home" class="home-icon">
    </a>

    <h1>View/Edit Latin Honors - Department: <?= htmlspecialchars($admin_department) ?></h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Department</th>
                    <th>Course</th>
                    <th>Average Grade</th>
                    <th>Latin Honor</th>
                    <th>Edit Honor</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['student_id'] ?></td>
                    <td><?= $row['student_name'] ?></td>
                    <td><?= $row['department'] ?></td>
                    <td><?= $row['course'] ?></td>
                    <td><?= $row['average_grade'] ?></td>
                    <td><?= $row['honor'] ?></td>
                    <td>
                        <form method="POST" action="view_edit_latin_honors.php">
                            <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                            <select name="honor">
                                <option value="Summa Cum Laude" <?= ($row['honor'] == 'Summa Cum Laude') ? 'selected' : '' ?>>Summa Cum Laude</option>
                                <option value="Magna Cum Laude" <?= ($row['honor'] == 'Magna Cum Laude') ? 'selected' : '' ?>>Magna Cum Laude</option>
                                <option value="Cum Laude" <?= ($row['honor'] == 'Cum Laude') ? 'selected' : '' ?>>Cum Laude</option>
                                <option value="No Honor" <?= ($row['honor'] == 'No Honor') ? 'selected' : '' ?>>No Honor</option>
                            </select>
                            <button type="submit" name="update">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No latin students found for your department.</p>
    <?php endif; ?>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>