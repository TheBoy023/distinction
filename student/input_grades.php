<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "u132092183_distinct", "Distinct@2024", "u132092183_distinct");

$notification = ""; // Initialize notification variable
$student_data = null; // Variable to store student data

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve student data from users table using email from session
$email = $conn->real_escape_string($_SESSION['email']);
$sql = "SELECT student_id, student_name, course, major, year_level FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $student_data = $result->fetch_assoc();
} else {
    $notification = "Student not found.";
}

$submission_success = false; // Flag for successful submission
// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $student_id = $student_data['student_id'];
    $student_name = $student_data['student_name'];
    $course = $student_data['course'];
    $major = $student_data['major'];
    $year_level = $student_data['year_level'];
    $semester = $_POST['semester'];
    $codes = $_POST['code'];
    $subjects = $_POST['subject'];
    $descriptions = $_POST['description'];
    $units = $_POST['units'];
    $grades = $_POST['grade'];

    // Flag to track if any valid rows were inserted
    $valid_rows_inserted = false;

    // Loop through the inputs and insert each course record
    for ($i = 0; $i < count($codes); $i++) {
        // Skip empty rows
        if (empty($codes[$i]) && empty($subjects[$i]) && empty($descriptions[$i]) && empty($units[$i]) && empty($grades[$i])) {
            continue;
        }

        // Validate inputs before insertion
        $code = $conn->real_escape_string(trim($codes[$i]));
        $subject = $conn->real_escape_string(trim($subjects[$i]));
        $description = $conn->real_escape_string(trim($descriptions[$i]));
        
        // Ensure units and grades are numeric and within valid ranges
        $unit = filter_var($units[$i], FILTER_VALIDATE_FLOAT);
        $grade = filter_var($grades[$i], FILTER_VALIDATE_FLOAT);

        // Additional validation
        if ($unit === false || $unit < 0 || $grade === false || $grade < 0 || $grade > 100) {
            $notification = "Invalid input for units or grades. Please check your entries.";
            $valid_rows_inserted = false;
            break;
        }

        // Prepare and execute the SQL statement using prepared statements
        $stmt = $conn->prepare("INSERT INTO calculate_average (student_id, student_name, course, major, year_level, semester, course_code, subject, description, units, grade) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt === false) {
            $notification = "Prepare failed: " . $conn->error;
            $valid_rows_inserted = false;
            break;
        }

        $stmt->bind_param("ssssissssdd", 
            $student_id, 
            $student_name, 
            $course, 
            $major, 
            $year_level, 
            $semester, 
            $code, 
            $subject, 
            $description, 
            $unit, 
            $grade
        );

        if ($stmt->execute()) {
            $valid_rows_inserted = true;
            $submission_success = true;
            $notification = "Grades successfully submitted!";
        } else {
            $notification = "Error inserting record: " . $stmt->error;
            $valid_rows_inserted = false;
            $stmt->close();
            break;
        }

        $stmt->close();
    }

    // Echo the notification message as a JavaScript function call
    echo "<script>window.onload = function() { 
        showNotification('" . addslashes($notification) . "', " . ($submission_success ? 'true' : 'false') . "); 
    };</script>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Grades</title>
    <link rel="stylesheet" href="../css/input_grades.css">
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <h1>Input Grades</h1>

        <div class="search-bar">
            <div style="display: flex; align-items: left; justify-content: left; gap: 10px;">
                <!-- Home Button -->
                <form action="dashboard.php" method="get" style="display: inline;">
                    <button type="submit">Back</button>
                </form>
            </div>
        </div>

        <form action="input_grades.php" method="POST" id="gradeForm">
            <label for="studentId">Student ID:</label>
            <input type="number" name="student_id" id="studentId" 
                   value="<?php echo $student_data ? $student_data['student_id'] : ''; ?>" 
                   disabled>

            <label for="studentName">Student Name:</label>
            <input type="text" name="student_name" id="studentName" 
                   value="<?php echo $student_data ? $student_data['student_name'] : ''; ?>" 
                   disabled>

            <label for="course">Course:</label>
            <input type="text" name="course" id="course" 
                   value="<?php echo $student_data ? $student_data['course'] : ''; ?>" 
                   disabled>

            <label for="major">Major:</label>
            <input type="text" name="major" id="major" 
                   value="<?php echo $student_data ? $student_data['major'] : ''; ?>" 
                   disabled>

            <label for="yearlevel">Year Level:</label>
            <input type="text" name="year_level" id="yearlevel" 
                   value="<?php echo $student_data ? $student_data['year_level'] : ''; ?>" 
                   disabled>

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
                    <td><input type="text" name="code[]" maxlength="50"></td>
                    <td><input type="text" name="subject[]" maxlength="50"></td>
                    <td><input type="text" name="description[]" maxlength="50"></td>
                    <td><input type="number" name="units[]" step="0.01" min="0"></td>
                    <td><input type="number" name="grade[]" step="0.01" min="0" max="100"></td>
                </tr>
                <?php endfor; ?>
            </table>
 
            <button type="submit">Send Grades</button>
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
        function showNotification(message, isSuccess) {
            const modal = document.getElementById('notificationModal');
            const modalMessage = document.getElementById('modalMessage');
            
            // Set message
            modalMessage.textContent = message;
            
            // Add success or error styling
            modal.classList.remove('success-modal', 'error-modal');
            modal.classList.add(isSuccess ? 'success-modal' : 'error-modal');
            
            // Show modal
            modal.style.display = "block";
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
    </script>
</body>
</html>