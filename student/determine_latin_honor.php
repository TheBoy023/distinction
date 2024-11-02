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

// Retrieve session variables
$studentName = htmlspecialchars($_SESSION['student_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8');
$studentId = htmlspecialchars($_SESSION['student_id'] ?? 'Unknown', ENT_QUOTES, 'UTF-8');
$department = htmlspecialchars($_SESSION['department'] ?? 'Unknown', ENT_QUOTES, 'UTF-8');

$message = ""; // Variable to hold the message for the notification

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $course = htmlspecialchars(trim($_POST['course']), ENT_QUOTES, 'UTF-8');
    $major = htmlspecialchars(trim($_POST['major']), ENT_QUOTES, 'UTF-8');
    $year_level = filter_var($_POST['year_level'], FILTER_VALIDATE_INT);
    $section = htmlspecialchars(trim($_POST['section']), ENT_QUOTES, 'UTF-8');
    $program = htmlspecialchars(trim($_POST['program']), ENT_QUOTES, 'UTF-8');

    if ($year_level === false) {
        $message = "Invalid year level.";
    } else {
        // Use prepared statements to securely insert data
        $stmt = $conn->prepare("INSERT INTO latin_honor_students (student_id, student_name, department, course, major, year_level, section, program)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssis", $studentId, $studentName, $department, $course, $major, $year_level, $section, $program);

        if ($stmt->execute() !== TRUE) {
            $message = "Error: " . $stmt->error;
        } else {
            $message = "Applied for Latin Honor successfully.";
        }
        $stmt->close();
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