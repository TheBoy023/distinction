<?php
session_start(); // Start the session to access session variables

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

// Assuming the user is logged in and their name and ID are stored in session variables
$studentName = $_SESSION['student_name'] ?? 'Unknown';  // Replace with session key for student name
$studentId = $_SESSION['student_id'] ?? 'Unknown'; // Replace with session key for student ID
$department = $_SESSION['department'] ?? 'Unknown'; // Replace with session key for department

$message = ""; // Variable to hold the message for the notification

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course = $_POST['course'];
    $major = $_POST['major'];
    $year_level = $_POST['year_level'];
    $section = $_POST['section'];
    $program = $_POST['program'];
    $semester = $_POST['semester'];
    $file_path = "";

    // Directory to store uploaded files
    $target_dir = "../admin/student/uploads/";

    // Check if uploads directory exists, if not, create it
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Only allow PDF and image files
        if (in_array($file_type, ["pdf", "jpg", "jpeg", "png"])) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $file_path = $target_file;
                $message = "Applied for Dean's List successfully."; // Success message
            } else {
                $message = "Error uploading file."; // Error message
            }
        } else {
            $message = "Only PDF and image files are allowed."; // Error message for invalid file type
        }
    }

    // Insert student data into the database
    $sql = "INSERT INTO deans_list_students (student_id, student_name, department, course, major, year_level, section, program, semester, file_path)
            VALUES ('$studentId', '$studentName', '$department', '$course', '$major', $year_level, '$section', '$program', '$semester', '$file_path')";

    if ($conn->query($sql) !== TRUE) {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Dean's List</title>
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: maroon;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        input:focus, select:focus {
            border-color: maroon;
            outline: none;
        }

        .home-icon {
            position: absolute;
            top: 5px;
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
            width: 100%;
            background-color: maroon;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #800000;
        }
        .file-upload {
            margin-top: 10px;
        }
        /* Modal styling */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4); /* Black with opacity */
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 300px;
            text-align: center;
            border-radius: 10px;
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
    </style>
</head>
<body>

    <a href="dashboard.php">
        <img src="../img/homeicon.png" alt="Home" class="home-icon">
    </a>
    <div class="container">
        <h1>Application Form</h1>
        <form action="determine_deans_list.php" method="post" enctype="multipart/form-data">
            <label for="student_id">Student ID:</label>
            <input type="text" id="student_id" name="student_id" value="<?php echo $studentId; ?>" disabled>
            
            <label for="student_name">Student Name:</label>
            <input type="text" id="student_name" name="student_name" value="<?php echo $studentName; ?>" disabled>

            <label for="department">Department:</label>
            <input type="text" id="department" name="department" value="<?php echo $department; ?>" disabled>

            <label for="course">Course:</label>
            <input type="text" id="course" name="course" maxlength="50" required>

            <label for="major">Major:</label>
            <input type="text" id="major" name="major" maxlength="15" required>

            <label for="year_level">Year Level:</label>
            <input type="number" id="year_level" name="year_level" min="1" max="4" required>

            <label for="section">Section:</label>
            <input type="text" id="section" name="section" maxlength="1" required>

            <label for="program">Program:</label>
            <input type="text" id="program" name="program" maxlength="6" required>

            <label for="semester">Semester:</label>
            <select id="semester" name="semester" required>
                <option value="First Semester">First Semester</option>
                <option value="Second Semester">Second Semester</option>
            </select>

            <div class="file-upload">
                <label for="file">Upload your grade file (PDF or Image):</label>
                <input type="file" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <button type="submit">Submit</button>
        </form>
    </div>

    <!-- The Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="modal-message"></p>
        </div>
    </div>

    <script>
        // Modal script
        var modal = document.getElementById("myModal");
        var span = document.getElementsByClassName("close")[0];
        var modalMessage = document.getElementById("modal-message");

        // PHP message passed from server
        var message = "<?php echo $message; ?>";

        if (message) {
            modalMessage.textContent = message;
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>