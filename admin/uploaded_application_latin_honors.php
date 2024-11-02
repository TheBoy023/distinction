<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "finals");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$search_query = "";

// Fetch all student records based on search
$sql = "SELECT * FROM latin_honor_students";
$result = $conn->query($sql);

// Handle search request
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
    $sql = "SELECT * FROM latin_honor_students WHERE student_id LIKE '%$search_query%' OR department LIKE '%$search_query%'";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Application</title>
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            display: flex;
        }
        .container {
            flex-grow: 1;
            max-width: 1000px; /* Adjust the width as needed */
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #fff;
        }
        h1 {
            text-align: center;
            color: maroon;
        }
        .header-buttons {
            text-align: center;
            margin-bottom: 20px;
        }
        /* Common button styles */
        button {
            padding: 10px 20px;
            background-color: maroon;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 10px; /* Add space between buttons */
        }
        button:hover {
            background-color: #800000;
        }
        .search-bar {
            margin: 20px 0;
        }
        input[type="text"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 200px;
            margin-left: 10px; /* Space between input and button */
        }
        .semester-select {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }
        .semester-options {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            z-index: 1;
            padding: 5px;
            margin-top: 5px;
        }
        .semester-select:hover .semester-options {
            display: block;
        }
        .semester-option {
            padding: 5px 10px;
            color: maroon;
            text-decoration: none;
            display: block;
        }
        .semester-option:hover {
            background-color: #f0f0f0;
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
        a {
            color: maroon;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function selectSemester(semester) {
            document.getElementById('semesterInput').value = semester;
            document.getElementById('semesterForm').submit();
        }
    </script>
</head>
<body>

    <div class="container">
        <h1>Uploaded Application for Latin Honor Students</h1>

        <!-- Header Buttons and Search Bar -->
        <div class="search-bar">
            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                <!-- Home Button -->
                <form action="dashboard.php" method="get" style="display: inline;">
                    <button type="submit">Home</button>
                </form>

                <!-- Calculate Average Button -->
                <form action="calculate_latin_honors.php" method="get" style="display: inline;">
                    <button type="submit">Calculate Latin Average</button>
                </form>

                <!-- Search Form -->
                <form action="" method="post" style="display: flex; align-items: center;">
                    <input type="text" name="search" placeholder="Search by Student ID or Department" value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>

        <table>
            <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Department</th>
                <th>Course</th>
                <th>Major</th>
                <th>Year Level</th>
                <th>Section</th>
                <th>Program</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['student_id'] . "</td>";
                    echo "<td>" . $row['student_name'] . "</td>";
                    echo "<td>" . $row['department'] . "</td>";
                    echo "<td>" . $row['course'] . "</td>";
                    echo "<td>" . $row['major'] . "</td>";
                    echo "<td>" . $row['year_level'] . "</td>";
                    echo "<td>" . $row['section'] . "</td>";
                    echo "<td>" . $row['program'] . "</td>";
                }
            } else {
                echo "<tr><td colspan='9'>No records found</td></tr>";
            }
            $conn->close();
            ?>
        </table>
    </div>

</body>
</html>