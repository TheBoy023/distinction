<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "finals");

$notification = ""; // Initialize notification variable

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $student_id = $_POST['student_id'];  // Student ID entered by the user
    $year_level = $_POST['year_level'];
    $semester = $_POST['semester'];
    $codes = $_POST['code'];
    $subjects = $_POST['subject'];
    $descriptions = $_POST['description'];
    $units = $_POST['units'];
    $grades = $_POST['grade'];
    $average_grade = isset($_POST['average_grade']) ? floatval($_POST['average_grade']) : 0; // Retrieve the average grade
    
    // Dean's List criteria
    $deans_list_threshold = 1.75; // Example threshold for Dean's List eligibility
    $deans_list_status = ($average_grade <= $deans_list_threshold) ? "Yes" : "No"; // Determine eligibility

    // Loop through the inputs and insert each course record
    for ($i = 0; $i < count($codes); $i++) {
        $code = $conn->real_escape_string($codes[$i]);
        $subject = $conn->real_escape_string($subjects[$i]);
        $description = $conn->real_escape_string($descriptions[$i]);
        $unit = floatval($units[$i]);
        $grade = floatval($grades[$i]);

        // Insert each course record into the courses table
        $sql = "INSERT INTO calculate_average (student_id, year_level, semester, course_code, subject, description, units, grade) 
                VALUES ('$student_id', '$year_level', '$semester', '$code', '$subject', '$description', '$unit', '$grade')";

        if (!$conn->query($sql)) {
            $notification = "Error: " . $sql . "<br>" . $conn->error; // Capture error
        }
    }

    // Store the average grade and Dean's List status in a separate table
    $sql_avg = "INSERT INTO deans_list_averages (student_id, year_level, semester, average_grade, deans_list_status) 
                VALUES ('$student_id', '$year_level', '$semester', '$average_grade', '$deans_list_status')";

    if (!$conn->query($sql_avg)) {
        $notification .= "Error storing average grade: " . $conn->error; // Capture error for average grade
    } else {
        $notification .= "Records and average grade inserted successfully."; // Success message
        // Notify if student is on Dean's List
        if ($deans_list_status == "Yes") {
            $notification .= " This student is eligible for the Dean's List.";
        } else {
            $notification .= " This student is not eligible for the Dean's List." . $conn->error;
        }
    }

    // Echo the notification message as a JavaScript function call
    echo "<script>window.onload = function() { showNotification('{$notification}'); };</script>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculate Dean's List Average</title>
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">
    <style>
        /* Your existing styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
            text-align: center;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: maroon;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus, input[type="number"]:focus {
            border-color: maroon;
        }
        .footer {
            text-align: right;
            font-weight: bold;
            font-size: 1.1em;
            color: maroon;
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
            padding: 10px 15px;
            margin: 10px 0;
            border: none;
            border-radius: 4px;
            background-color: maroon;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: darkred; /* Change to a darker shade on hover */
        }
    </style>
</head>
<body>

    <a href="dashboard.php">
        <img src="../img/homeicon.png" alt="Home" class="home-icon">
    </a>

    <div class="container">
        <h1>Calculate Average</h1>

        <form action="calculate_deans_list.php" method="POST" id="gradeForm">
            <label for="studentId">Student ID:</label>
            <input type="number" name="student_id" id="studentId" required>

            <label for="year_level">Year Level:</label>
            <select id="year_level" name="year_level" required>
                <option value="First Year">First Year</option>
                <option value="Second Year">Second Year</option>
                <option value="Third Year">Third Year</option>
                <option value="Fourth Year">Fourth Year</option>
            </select>

            <label for="semester">Semester:</label>
            <select id="semester" name="semester" required>
                <option value="First Semester">First Semester</option>
                <option value="Second Semester">Second Semester</option>
            </select>

            <table id="gradeTable">
                <tr>
                    <th>Course Code</th>
                    <th>Subject</th>
                    <th>Description</th>
                    <th>Units</th>
                    <th>Grade</th>
                </tr>
                <?php for ($i = 0; $i < 10; $i++): ?>
                <tr>
                    <td><input type="text" name="code[]"></td>
                    <td><input type="text" name="subject[]"></td>
                    <td><input type="text" name="description[]"></td>
                    <td><input type="number" name="units[]" step="0.01" min="0"></td>
                    <td><input type="number" name="grade[]" step="0.01" min="0" max="100"></td>
                </tr>
                <?php endfor; ?>
                <tr>
                    <td colspan="3"></td>
                    <td class="footer">Average:</td>
                    <td class="footer" id="averageGrade">0.00</td>
                </tr>
            </table>

            <!-- Hidden input to store the calculated average grade -->
            <input type="hidden" id="averageGradeInput" name="average_grade">
            
            <button type="button" onclick="calculateAverage()">Calculate Average</button>
            <button type="submit">Save Grades</button>
        </form>
    </div>

    <!-- Modal for Notification -->
    <div id="notificationModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeNotification()">&times;</span>
            <p id="modalMessage"></p>
        </div>
    </div>

    <script>
        function showNotification(message) {
            document.getElementById('modalMessage').textContent = message; // Set message
            document.getElementById('notificationModal').style.display = "block"; // Show modal
        }

        function closeNotification() {
            document.getElementById('notificationModal').style.display = "none"; // Hide modal
        }

        window.onclick = function(event) {
            const modal = document.getElementById('notificationModal');
            if (event.target === modal) {
                modal.style.display = "none"; // Hide modal on click outside
            }
        };

        function calculateAverage() {
            let units = document.querySelectorAll('input[name="units[]"]');
            let grades = document.querySelectorAll('input[name="grade[]"]');
            let totalUnits = 0, weightedSum = 0;

            for (let i = 0; i < units.length; i++) {
                let unit = parseFloat(units[i].value);
                let grade = parseFloat(grades[i].value);

                if (!isNaN(unit) && !isNaN(grade)) {
                    totalUnits += unit;
                    weightedSum += unit * grade;
                }
            }

            let average = weightedSum / totalUnits || 0;
            document.getElementById("averageGrade").textContent = average.toFixed(2);

            // Store the calculated average in the hidden input field
            document.getElementById("averageGradeInput").value = average.toFixed(2);  // Send this value when form is submitted
        }
    </script>

</body>
</html>