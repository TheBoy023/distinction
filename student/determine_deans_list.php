<?php
session_start();

// Database connection details
$host = 'localhost';
$db = 'u132092183_distinct';
$user = 'u132092183_distinct';
$password = 'Distinct@2024';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare a statement to fetch student details
    $stmt = $pdo->prepare("SELECT 
        student_name,
        student_id, 
        department, 
        course, 
        major, 
        year_level, 
        section, 
        program 
    FROM users 
    WHERE email = :email");
    
    // Bind the email parameter
    $email = $_SESSION['email']; // Store in a variable for clarity
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    
    // Execute the query
    $stmt->execute();
    
    // Fetch the student details
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($student) {
        // Assign fetched details to variables
        $studentName = $student['student_name'];
        $studentId = $student['student_id'];
        $course = $student['course'];
        $major = $student['major'];
        $yearlevel = $student['year_level'];
        $section = $student['section'];
        $program = $student['program'];
        $department = $student['department'];
    } else {
        // More detailed error message
        $message = "No student found with the email: " . htmlspecialchars($email);
    }
} catch(PDOException $e) {
    $message = "Error retrieving student details: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Directory to store uploaded files
        $target_dir = "../admin/student/uploads/";

        // Check if uploads directory exists, if not, create it
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Handle file upload
        $file_path = '';
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $target_file = $target_dir . basename($_FILES["file"]["name"]);
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Only allow PDF and image files
            if (in_array($file_type, ["pdf", "jpg", "jpeg", "png"])) {
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                    $file_path = $target_file;
                } else {
                    $message = "Error uploading file.";
                }
            } else {
                $message = "Only PDF and image files are allowed.";
            }
        } else {
            $message = "Please upload a file.";
        }

        // Prepare INSERT statement using PDO
        $stmt = $pdo->prepare("INSERT INTO deans_list_students 
            (student_id, student_name, department, course, major, year_level, section, program, semester, file_path)
            VALUES (:student_id, :student_name, :department, :course, :major, :year_level, :section, :program, :semester, :file_path)");
        
        // Bind parameters
        $stmt->bindParam(':student_id', $studentId, PDO::PARAM_STR);
        $stmt->bindParam(':student_name', $studentName, PDO::PARAM_STR);
        $stmt->bindParam(':department', $department, PDO::PARAM_STR);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':major', $major, PDO::PARAM_STR);
        $stmt->bindParam(':year_level', $yearlevel, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->bindParam(':program', $program, PDO::PARAM_STR);
        $stmt->bindParam(':semester', $_POST['semester'], PDO::PARAM_STR);
        $stmt->bindParam(':file_path', $file_path, PDO::PARAM_STR);

        // Execute the statement
        if ($stmt->execute()) {
            $message = "Applied for Dean's List successfully.";
        } else {
            $message = "Error submitting application.";
        }
    } catch(PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Dean's List</title>
    <link rel="stylesheet" href="../css/determine_deans_list.css">
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">
</head>
<body>
    <a href="dashboard.php">
        <img src="../img/homeicon.png" alt="Home" class="home-icon">
    </a>
    <div class="container">
        <h1>Dean's List Application</h1>
        <form action="" method="post" enctype="multipart/form-data">   
            <label for="student_id">Student Id:</label>
            <input type="number" id="student_id" value="<?php echo htmlspecialchars($studentId); ?>" disabled>  
            
            <label for="student_name">Student Name:</label>
            <input type="text" id="student_name" value="<?php echo htmlspecialchars($studentName); ?>" disabled>

            <label for="department">Department:</label>
            <input type="text" id="department" value="<?php echo htmlspecialchars($department); ?>" disabled>

            <label for="course">Course:</label>
            <input type="text" id="course" value="<?php echo htmlspecialchars($course); ?>" disabled>

            <label for="major">Major:</label>
            <input type="text" id="major" value="<?php echo htmlspecialchars($major); ?>" disabled>

            <label for="year_level">Year Level:</label>
            <input type="text" id="year_level" value="<?php echo htmlspecialchars($yearlevel); ?>" disabled>

            <label for="section">Section:</label>
            <input type="text" id="section" value="<?php echo htmlspecialchars($section); ?>" disabled>

            <label for="program">Program:</label>
            <input type="text" id="program" value="<?php echo htmlspecialchars($program); ?>" disabled>

            <label for="semester">Semester:</label>
            <select id="semester" name="semester" required>
                <option value="First Semester">First Semester</option>
                <option value="Second Semester">Second Semester</option>
            </select>

            <div class="file-upload">
                <label for="file">Upload your grade file (PDF or Image):</label>
                <input type="file" id="file" name="file" class="file-input" accept=".pdf,.jpg,.jpeg,.png" required>
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
        // Modal script remains the same as in the previous version
        var modal = document.getElementById("myModal");
        var span = document.getElementsByClassName("close")[0];
        var modalMessage = document.getElementById("modal-message");

        var message = "<?php echo $message; ?>";

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