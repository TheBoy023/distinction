<?php
// Start a secure session with appropriate settings
session_start([
    'cookie_lifetime' => 0,
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'use_strict_mode' => true
]);

include '../assets/config.php'; // Securely include your database configuration

// Only process the form if it's a POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize inputs
    $studentName = htmlspecialchars(trim($_POST['student_name']), ENT_QUOTES, 'UTF-8');
    $studentId = htmlspecialchars(trim($_POST['student_id']), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $course = htmlspecialchars(trim($_POST['course']), ENT_QUOTES, 'UTF-8');
    $major = htmlspecialchars(trim($_POST['major']), ENT_QUOTES, 'UTF-8');
    $yearlevel = htmlspecialchars(trim($_POST['year_level']), ENT_QUOTES, 'UTF-8');
    $section = htmlspecialchars(trim($_POST['section']), ENT_QUOTES, 'UTF-8');
    $program = htmlspecialchars(trim($_POST['program']), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $department = htmlspecialchars(trim($_POST['department']), ENT_QUOTES, 'UTF-8');

    // Initialize an array to store validation errors
    $errors = [];

    // Server-side validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    } 

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match!';
    } 

    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must be at least 8 characters and contain letters and numbers.';
    }

    // Check for duplicate student ID
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE student_id = ?");
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $errors[] = 'Student ID is already registered';
    }
    $stmt->close();

    // Check for duplicate student name
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE student_name = ?");
    $stmt->bind_param("s", $studentName);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $errors[] = 'Student Name is already registered';
    }
    $stmt->close();

    // Check for duplicate email
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $errors[] = 'Email is already registered';
    }
    $stmt->close();

    // If no errors, proceed with account creation
    if (empty($errors)) {
        // Hash the password securely
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO users (student_name, student_id, email, course, major, year_level, section, program, password, department) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $studentName, $studentId, $email, $course, $major, $yearlevel, $section, $program, $hashedPassword, $department);

        // Execute the query and handle errors
        if ($stmt->execute()) {
            echo "<script>alert('Account created successfully!'); window.location.href='login.php';</script>";
            exit();
        } else {
            error_log("Error creating account: " . $stmt->error); // Log specific error for debugging
            $errors[] = 'An error occurred. Please try again later.';
        }

        $stmt->close(); // Close the prepared statement
    }

    // If there are errors, store them in the session to display on the form
    if (!empty($errors)) {
        $_SESSION['signup_errors'] = $errors;
    }
}

$conn->close(); // Close the database connection securely
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup | Deanslist</title>
    <link rel="stylesheet" href="../css/signup_student.css">
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">

    <!-- Security headers -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self'; style-src 'self'; script-src 'self';">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
</head>
<body>
    <!-- Home Icon -->
    <a href="../home.php">
        <img src="../img/homeicon.png" alt="Home" class="home-icon">
    </a>
    
    <div class="signup-container">
        <h2>Create an Account</h2>
        
        <?php
        // Display any signup errors
        if (isset($_SESSION['signup_errors'])) {
            echo '<div class="error-messages">';
            foreach ($_SESSION['signup_errors'] as $error) {
                echo '<p class="error">' . htmlspecialchars($error) . '</p>';
            }
            echo '</div>';
            // Clear the errors after displaying
            unset($_SESSION['signup_errors']);
        }
        ?>
        
        <form action="signup.php" method="POST" autocomplete="off">
            <input type="text" name="student_name" maxlength="50" placeholder="Full Name" required 
                value="<?php echo isset($_POST['student_name']) ? htmlspecialchars($_POST['student_name']) : ''; ?>"><br>
            <input type="text" name="student_id" maxlength="7" placeholder="Student ID" required 
                value="<?php echo isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : ''; ?>"><br>
            <input type="email" name="email" maxlength="50" placeholder="Email" required 
                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"><br>
            <input type="text" name="course" maxlength="50" placeholder="Course" required 
                value="<?php echo isset($_POST['course']) ? htmlspecialchars($_POST['course']) : ''; ?>"><br>
            <input type="text" name="major" maxlength="15" placeholder="Major" required 
                value="<?php echo isset($_POST['major']) ? htmlspecialchars($_POST['major']) : ''; ?>"><br>
            <input type="text" name="year_level" maxlength="10" placeholder="Year Level (ex: 1st Year, 2nd Year, 3rd Year, 4th Year)" required 
                value="<?php echo isset($_POST['year_level']) ? htmlspecialchars($_POST['year_level']) : ''; ?>"><br>
            <input type="text" name="section" maxlength="1" placeholder="Section" required 
                value="<?php echo isset($_POST['section']) ? htmlspecialchars($_POST['section']) : ''; ?>"><br>
            <input type="text" name="program" maxlength="6" placeholder="Program" required 
                value="<?php echo isset($_POST['program']) ? htmlspecialchars($_POST['program']) : ''; ?>"><br>    
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
            <select name="department" required>
                <option value="">Select Department</option>
                <option value="College of Education, Arts and Sciences" 
                    <?php echo (isset($_POST['department']) && $_POST['department'] === 'College of Education, Arts and Sciences') ? 'selected' : ''; ?>>
                    CEAS
                </option>
                <option value="College of Management and Entrepreneurship" 
                    <?php echo (isset($_POST['department']) && $_POST['department'] === 'College of Management and Entrepreneurship') ? 'selected' : ''; ?>>
                    CME
                </option>
                <option value="College of Engineering" 
                    <?php echo (isset($_POST['department']) && $_POST['department'] === 'College of Engineering') ? 'selected' : ''; ?>>
                    COE
                </option>
                <option value="College of Technology" 
                    <?php echo (isset($_POST['department']) && $_POST['department'] === 'College of Technology') ? 'selected' : ''; ?>>
                    COT
                </option>
            </select><br><br>
            <button type="submit">Sign Up</button>
        </form>
    </div>
</body>
</html>