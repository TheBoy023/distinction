<?php
session_start(); // Start the session to access session variables
session_regenerate_id(true); // Regenerate session ID to prevent session fixation

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
    <title>Apply for Latin Honor</title>
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
        <form action="determine_latin_honor.php" method="post">
            <label for="student_id">Student ID:</label>
            <input type="text" id="student_id" name="student_id" value="<?php echo $studentId; ?>" disabled>

            <label for="student_name">Student Name:</label>
            <input type="text" id="student_name" name="student_name" value="<?php echo $studentName; ?>" disabled>

            <label for="department">Department:</label>
            <input type="text" id="department" name="department" value="<?php echo $department; ?>" disabled>

            <label for="course">Course:</label>
            <input type="text" id="course" name="course" required maxlength="50">

            <label for="major">Major:</label>
            <input type="text" id="major" name="major" required maxlength="15">

            <label for="year_level">Year Level:</label>
            <input type="number" id="year_level" name="year_level" required min="1" max="4">

            <label for="section">Section:</label>
            <input type="text" id="section" name="section" required maxlength="1">

            <label for="program">Program:</label>
            <input type="text" id="program" name="program" required maxlength="6">

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
        var modal = document.getElementById("myModal");
        var span = document.getElementsByClassName("close")[0];
        var modalMessage = document.getElementById("modal-message");

        var message = "<?php echo addslashes($message); ?>";

        if (message) {
            modalMessage.textContent = message;
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>