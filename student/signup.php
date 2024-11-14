<?php
// Start a secure session with appropriate settings
session_start([
    'cookie_lifetime' => 0,
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'use_strict_mode' => true
]);

include '../assets/config.php'; // Securely include your database configuration

// Only process the form if it’s a POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize inputs
    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $studentId = htmlspecialchars(trim($_POST['student_id']), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $department = htmlspecialchars(trim($_POST['department']), ENT_QUOTES, 'UTF-8');

    // Server-side validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format');</script>";
    } elseif ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match!');</script>";
    } elseif (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        echo "<script>alert('Password must be at least 8 characters and contain letters and numbers.');</script>";
    } else {
        // Hash the password securely
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO users (name, student_id, email, password, department) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $studentId, $email, $hashedPassword, $department);

        // Execute the query and handle errors
        if ($stmt->execute()) {
            echo "<script>alert('Account created successfully!'); window.location.href='login.php';</script>";
        } else {
            error_log("Error creating account: " . $stmt->error); // Log specific error for debugging
            echo "<script>alert('An error occurred. Please try again later.');</script>";
        }

        $stmt->close(); // Close the prepared statement
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
    <a href="../index.php">
        <img src="../img/homeicon.png" alt="Home" class="home-icon">
    </a>
    
    <div class="signup-container">
        <h2>Create an Account</h2>
        <form action="signup.php" method="POST" autocomplete="off">
            <input type="text" name="name" placeholder="Full Name" required><br>
            <input type="text" name="student_id" placeholder="Student ID" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
            <select name="department" required>
                <option value="">Select Department</option>
                <option value="College of Education, Arts and Sciences">CEAS</option>
                <option value="College of Management and Entrepreneurship">CME</option>
                <option value="College of Engineering">COE</option>
                <option value="College of Technology">COT</option>
            </select><br><br>
            <button type="submit">Sign Up</button>
        </form>
    </div>
</body>
</html>