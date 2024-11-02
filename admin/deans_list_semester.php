<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "finals");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$search_query = "";
$semester = "";

// Check if semester is set and search query exists
if (isset($_POST['semester'])) {
    $semester = $_POST['semester'];
}

// Fetch all student records based on semester
$sql = "SELECT * FROM deans_list_students";
if (!empty($semester)) {
    $sql .= " WHERE semester = '$semester'";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dean's List Semester View</title>
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
</head>
<body>

    <div class="container">
        <h1>Uploaded Application for Dean's List Students - <?php echo htmlspecialchars($semester); ?></h1>

        <!-- Header Buttons and Search Bar -->
        <div class="search-bar">
            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                <!-- Home Button -->
                <form action="uploaded_application_deans_list.php" method="get" style="display: inline;">
                    <button type="submit">Back</button>
                </form>

                <!-- Calculate Average Button -->
                <form action="calculate_deans_list.php" method="get" style="display: inline;">
                    <button type="submit">Calculate Average</button>
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
                <th>Department</th>
                <th>Course</th>
                <th>Major</th>
                <th>Year Level</th>
                <th>Section</th>
                <th>Program</th>
                <th>Semester</th>
                <th>Uploaded File</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['student_id'] . "</td>";
                    echo "<td>" . $row['department'] . "</td>";
                    echo "<td>" . $row['course'] . "</td>";
                    echo "<td>" . $row['major'] . "</td>";
                    echo "<td>" . $row['year_level'] . "</td>";
                    echo "<td>" . $row['section'] . "</td>";
                    echo "<td>" . $row['program'] . "</td>";
                    echo "<td>" . $row['semester'] . "</td>";

                    if (!empty($row['file_path'])) {
                        echo "<td><a href='" . $row['file_path'] . "' target='_blank'>View File</a></td>";
                    } else {
                        echo "<td>No file uploaded</td>";
                    }

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No records found for " . htmlspecialchars($semester) . "</td></tr>";
            }
            $conn->close();
            ?>
        </table>
    </div>

</body>
</html>