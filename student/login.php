<?php
include '../assets/config.php'; // Ensure this points to the correct config.php
session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $studentName = $_POST['name'];
    $studentId = $_POST['student_id']; // No need for mysqli_real_escape_string here
    $department = $_POST['department'];
    $password = $_POST['password'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE student_id = ?");
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['student_id'] = $user['student_id'];  // Store student ID
            $_SESSION['student_name'] = $user['name'];  // Store student name
            $_SESSION['department'] = $user['department'];
            
            // Redirect to student data management page
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid password.');</script>";
        }
    } else {
        echo "<script>alert('No account found with this Student ID.');</script>";
    }
    
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deanslist Login</title>
    <link rel="stylesheet" href="../css/login_student.css">
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">
</head>
<body>
    <!-- Home icon -->
    <a href="../home.php">
        <img src="../img/homeicon.png" alt="Home" class="home-icon">
    </a>

    <div class="container">
        <!-- Left section with a single quote -->
        <div class="left-section">
            <div class="quote">
                "The honor of a student lies in their ability to strive for academic excellence and distinction." 
                <br><span>- Deanslist Quote</span>
            </div>
        </div>

        <!-- Right section with login form -->
        <div class="right-section">
            <h2>Login to Dean's List</h2>
            <form action="login.php" method="POST">
                <input type="text" name="name" id="full-name" placeholder="Full Name" required><br>    
                <input type="text" name="student_id" id="student-id" placeholder="Student ID" required><br>
                <input type="text" name="department" id="department" placeholder="Department" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <button type="submit">Login</button>
            </form>
            <a href="signup.php" class="signup-link">Don't have an account? Sign up</a>
        </div>
    </div>
</body>
</html>
