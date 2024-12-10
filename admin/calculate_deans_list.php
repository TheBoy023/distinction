<?php
// Database connection
$conn = new mysqli("localhost", "u132092183_distinct", "Distinct@2024", "u132092183_distinct");

$notification = ""; // Initialize notification variable
$notification_type = ""; // Initialize notification type
$student_courses = []; // Array to store student's courses
$selected_year = ""; // Variable to store selected year
$selected_semester = ""; // Variable to store selected semester

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if student ID is provided via GET parameter
if (isset($_GET['student_id']) && isset($_GET['year']) && isset($_GET['semester'])) {
    $student_id = $conn->real_escape_string($_GET['student_id']);
    $selected_year = $conn->real_escape_string($_GET['year']);
    $selected_semester = $conn->real_escape_string($_GET['semester']);

    // Fetch student's course details with specific year and semester
    $student_sql = "SELECT DISTINCT student_name, course, major, year_level, semester 
                    FROM calculate_average 
                    WHERE student_id = '$student_id' 
                    AND year_level = '$selected_year' 
                    AND semester = '$selected_semester'";
    $student_result = $conn->query($student_sql);

    // Fetch student's courses with specific year and semester
    $courses_sql = "SELECT course_code, subject, description, units, grade 
                    FROM calculate_average 
                    WHERE student_id = '$student_id' 
                    AND year_level = '$selected_year' 
                    AND semester = '$selected_semester'";
    $courses_result = $conn->query($courses_sql);

    // Check if courses exist
    if ($courses_result->num_rows > 0) {
        // Fetch student details
        if ($student_details = $student_result->fetch_assoc()) {
            $student_name = $student_details['student_name'];
            $course = $student_details['course'];
            $major = $student_details['major'];
            $year_level = $student_details['year_level'];
            $semester = $student_details['semester'];

            // Fetch all courses
            while ($row = $courses_result->fetch_assoc()) {
                $student_courses[] = $row;
            }
        }
    } else {
        $notification = "No grades found for this student in the selected year and semester.";
        $notification_type = "error";
    }
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $student_id = $_POST['student_id'];
    $student_name = $_POST['student_name'];
    $course = $_POST['course'];
    $major = $_POST['major'];
    $year_level = $_POST['year_level'];
    $semester = $_POST['semester'];
    
    // Calculate average grade
    $total_units = 0;
    $weighted_sum = 0;
    
    foreach ($student_courses as $course_data) {
        $units = floatval($course_data['units']);
        $grade = floatval($course_data['grade']);
        
        $total_units += $units;
        $weighted_sum += $units * $grade;
    }
    
    $average_grade = ($total_units > 0) ? ($weighted_sum / $total_units) : 0;
    
    // Dean's List criteria
    $deans_list_threshold = 1.75; // Example threshold for Dean's List eligibility
    $deans_list_status = ($average_grade <= $deans_list_threshold) ? "Yes" : "No";

    // Check if record already exists to prevent duplication
    $check_stmt = $conn->prepare("SELECT * FROM deans_list_averages 
        WHERE student_id = ? AND year_level = ? AND semester = ?");
    $check_stmt->bind_param("sss", $student_id, $year_level, $semester);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $notification = "Record for this student, year, and semester already exists.";
        $notification_type = "warning";
    } else {
        // Prepare and execute the SQL to store average grade and Dean's List status
        $stmt = $conn->prepare("INSERT INTO deans_list_averages (student_id, student_name, course, major, year_level, semester, average_grade, deans_list_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssssds", 
            $student_id, 
            $student_name, 
            $course, 
            $major, 
            $year_level, 
            $semester, 
            $average_grade, 
            $deans_list_status
        );

        if ($stmt->execute()) {
            $notification = "Records and average grade inserted successfully. ";
            $notification .= ($deans_list_status == "Yes") 
                ? "This student is eligible for the Dean's List." 
                : "This student is not eligible for the Dean's List.";
            $notification_type = "success";
        } else {
            $notification = "Error: " . $stmt->error;
            $notification_type = "error";
        }

        $stmt->close();
    }
    $check_stmt->close();

    // Pass notification to JavaScript to trigger modal
    echo "<script>window.onload = function() { 
            showNotification('" . htmlspecialchars($notification) . "', '" . $notification_type . "'); 
        };
    </script>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculate Dean's List Average</title>
    <link rel="stylesheet" href="../css/calculate_deans_list.css">
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <h1>Calculate Average</h1>

        <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
            <form action="uploaded_application_deans_list.php" method="get" style="display: inline;">
                <button type="submit">Back</button>
            </form>
        </div>

        <div class="search-bar">
            <!-- Student Search Form -->
            <div class="student-search">
                <form action="" method="GET">
                    <input type="text" name="student_id" placeholder="Enter Student ID" required>
                    <select name="year" required>
                        <option value="">Select Year</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                    <select name="semester" required>
                        <option value="">Select Semester</option>
                        <option value="First Semester">1st Semester</option>
                        <option value="Second Semester">2nd Semester</option>
                    </select>
                    <button type="submit">Search Student</button>
                </form>
            </div>
        </div>

        <?php if (!empty($student_courses)): ?>
        <form action="" method="POST" id="gradeForm">
            <!-- Hidden inputs to pass student details -->
            <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
            <input type="hidden" name="student_name" value="<?php echo $student_name; ?>">
            <input type="hidden" name="course" value="<?php echo $course; ?>">
            <input type="hidden" name="major" value="<?php echo $major; ?>">
            <input type="hidden" name="year_level" value="<?php echo $year_level; ?>">
            <input type="hidden" name="semester" value="<?php echo $semester; ?>">

            <label>Student Details</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="number" value="<?php echo $student_id; ?>" placeholder="Student ID">
                <input type="text" value="<?php echo $student_name; ?>" placeholder="Student Name">
                <input type="text" value="<?php echo $course; ?>" placeholder="Course">
                <input type="text" value="<?php echo $major; ?>" placeholder="Major">
                <input type="text" value="<?php echo $year_level; ?>" placeholder="Year Level">
                <input type="text" value="<?php echo $semester; ?>" placeholder="Semester">
            </div>

            <table id="gradeTable">
                <tr>
                    <th>Course Code</th>
                    <th>Subject</th>
                    <th>Description</th>
                    <th>Units</th>
                    <th>Grade</th>
                </tr>
                <?php foreach ($student_courses as $course_data): ?>
                <tr>
                    <td><input type="text" value="<?php echo htmlspecialchars($course_data['course_code']); ?>"></td>
                    <td><input type="text" value="<?php echo htmlspecialchars($course_data['subject']); ?>"></td>
                    <td><input type="text" value="<?php echo htmlspecialchars($course_data['description']); ?>"></td>
                    <td><input type="number" value="<?php echo htmlspecialchars($course_data['units']); ?>" step="0.01" min="0"></td>
                    <td><input type="number" value="<?php echo htmlspecialchars($course_data['grade']); ?>" step="0.01" min="0" max="5"></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3"></td>
                    <td class="footer">Weighted Average:</td>
                    <td class="footer" id="averageGrade">0.00</td>
                </tr>
            </table>
            
            <button type="submit">Save Grade</button>
        </form>
        <?php endif; ?>
    </div>

    <!-- Modal for Notification -->
    <div id="notificationModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeNotification()">&times;</span>
            <p id="modalMessage"></p>
            <button onclick="closeNotification()">OK</button>
        </div>
    </div>

    <script>
        function calculateWeightedAverage() {
            const rows = document.querySelectorAll('#gradeTable tr');
            let totalUnits = 0;
            let weightedSum = 0;

            // Start from index 1 to skip the header row
            for (let i = 1; i < rows.length - 1; i++) {
                const unitInput = rows[i].querySelectorAll('input')[3];
                const gradeInput = rows[i].querySelectorAll('input')[4];

                const units = parseFloat(unitInput.value);
                const grade = parseFloat(gradeInput.value);

                if (!isNaN(units) && !isNaN(grade)) {
                    totalUnits += units;
                    weightedSum += units * grade;
                }
            }

            const average = totalUnits > 0 ? weightedSum / totalUnits : 0;
            document.getElementById("averageGrade").textContent = average.toFixed(2);
        }

        function showNotification(message, type) {
            const modal = document.getElementById('notificationModal');
            const modalContent = modal.querySelector('.modal-content');
            const modalMessage = document.getElementById('modalMessage');
            
            // Remove previous type classes
            modalContent.classList.remove('success', 'error', 'warning');
            
            // Add current type class
            if (type) {
                modalContent.classList.add(type);
            }
            
            modalMessage.textContent = message;
            modal.style.display = "block";
        }

        function closeNotification() {
            const modal = document.getElementById('notificationModal');
            modal.style.display = "none";
        }

        window.onload = function() {
            calculateWeightedAverage();
        };

        window.onclick = function(event) {
            const modal = document.getElementById('notificationModal');
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
    </script>
</body>
</html>