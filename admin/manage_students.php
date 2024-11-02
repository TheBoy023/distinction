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

// Assuming admin's department is stored in session upon login
$admin_department = $_SESSION['admin_department'] ?? null; // Get admin's department from session

// Ensure the admin is logged in and their department is set
if (!$admin_department) {
    die("Unauthorized access. Please login.");
}

// Handle search
$searchQuery = "";
if (isset($_POST['search'])) {
    $searchQuery = $conn->real_escape_string($_POST['search']);
}

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $conn->query("DELETE FROM students WHERE student_id='$deleteId'");
    header("Location: admin.php"); // Redirect after deletion
    exit();
}

// Handle edit/update
if (isset($_POST['update'])) {
    $student_id = $_POST['student_id'];
    $student_name = $conn->real_escape_string($_POST['student_name']);
    $department = $conn->real_escape_string($_POST['department']);
    $grade_level = $conn->real_escape_string($_POST['grade_level']);
    $section = $conn->real_escape_string($_POST['section']);
    $program = $conn->real_escape_string($_POST['program']);
    $major = $conn->real_escape_string($_POST['major']); // New major field
    $course = $conn->real_escape_string($_POST['course']); // New course field

    // Update query
    $sql = "UPDATE students SET student_name='$student_name', department='$department', grade_level='$grade_level', section='$section', program='$program', major='$major', course='$course' WHERE student_id='$student_id'";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to normal view after update
        header("Location: admin.php");
        exit();
    } else {
        echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
    }
}

// Fetch records
$sql = "SELECT student_id, student_name, department, grade_level, section, program, major, course, average FROM students 
        WHERE student_name LIKE '%$searchQuery%' OR student_id LIKE '%$searchQuery%' 
        ORDER BY grade_level ASC, section ASC";
$result = $conn->query($sql);

// Check if the query was successful
if ($result === false) {
    echo "Error: " . $conn->error;
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Student Records</title>
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
            margin-bottom: 20px;
        }

        input, select {
            padding: 10px;
            margin: 5px 0;
            width: calc(100% - 22px);
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: maroon;
            color: white;
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
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: red;
        }

        .action-icon {
            display: inline-block;
            width: 24px; /* Set a width for the icons */
            height: 24px; /* Set a height for the icons */
            overflow: hidden;
            transition: transform 0.3s; /* Smooth zoom transition */
        }

        .icon {
            width: 100%;
            height: 100%;
        }

        .action-icon:hover {
            transform: scale(1.2); /* Zoom in effect */
        }
    </style>
</head>
<body>
    
    <a href="dashboard.php">
        <img src="../img/homeicon.png" alt="Home" class="home-icon">
    </a>

    <h1>Admin - Student Records</h1>

    <form method="POST">
        <input type="text" name="search" placeholder="Search by Name or Student ID" value="<?php echo htmlspecialchars($searchQuery); ?>">
        <button type="submit">Search</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Student Name</th>
                <th>Department</th>
                <th>Grade Level</th>
                <th>Section</th>
                <th>Program</th>
                <th>Major</th> <!-- New Major Column -->
                <th>Course</th> <!-- New Course Column -->
                <th>Average Grade</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['student_id']; ?></td>
                        <td><?php echo $row['student_name']; ?></td>
                        <td><?php echo $row['department']; ?></td>
                        <td><?php echo $row['grade_level']; ?></td>
                        <td><?php echo $row['section']; ?></td>
                        <td><?php echo $row['program']; ?></td>
                        <td><?php echo $row['major']; ?></td> <!-- Display Major -->
                        <td><?php echo $row['course']; ?></td> <!-- Display Course -->
                        <td><?php echo number_format($row['average'], 2); ?></td>
                        <td>
                            <a href="?edit=<?php echo $row['student_id']; ?>" class="action-icon">
                                <img src="../img/edit (1).png" alt="Edit" class="icon" />
                            </a> 
                            <a href="?delete=<?php echo $row['student_id']; ?>" class="action-icon" onclick="return confirm('Are you sure you want to delete this record?');">
                                <img src="../img/dlt.png" alt="Delete" class="icon" />
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (isset($_GET['edit'])): ?>
        <?php
        $editId = $_GET['edit'];
        $editResult = $conn->query("SELECT * FROM students WHERE student_id='$editId' AND department='$admin_department'");
        $editRow = $editResult->fetch_assoc();
        
        if ($editRow): ?>
            <h2>Edit Record</h2>
            <form method="POST">
                <input type="hidden" name="student_id" value="<?php echo $editRow['student_id']; ?>">
                <input type="text" name="student_name" placeholder="Student Name" value="<?php echo $editRow['student_name']; ?>" required>
                <input type="text" name="department" placeholder="Department" value="<?php echo $editRow['department']; ?>" required>
                <input type="text" name="grade_level" placeholder="Grade Level" value="<?php echo $editRow['grade_level']; ?>" required>
                <input type="text" name="section" placeholder="Section" value="<?php echo $editRow['section']; ?>" required>
                <input type="text" name="program" placeholder="Program" value="<?php echo $editRow['program']; ?>" required>
                <input type="text" name="major" placeholder="Major" value="<?php echo $editRow['major']; ?>" required> <!-- Major Field -->
                <input type="text" name="course" placeholder="Course" value="<?php echo $editRow['course']; ?>" required> <!-- Course Field -->
                <button type="submit" name="update">Update</button>
            </form>
        <?php else: ?>
            <p>You are not authorized to edit this record.</p>
        <?php endif; ?>
    <?php endif; ?>

</body>
</html>

<?php
$conn->close();
?>