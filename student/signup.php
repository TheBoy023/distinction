<?php
include '../assets/config.php'; // Ensure this points to the correct config.php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $studentId = mysqli_real_escape_string($conn, $_POST['student_id']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $department = mysqli_real_escape_string($conn, $_POST['department']);

    // Password validation (add more criteria if needed)
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into the database
        $sql = "INSERT INTO users (name, student_id, email, password, department)
                VALUES ('$name', '$studentId', '$email', '$hashedPassword', '$department')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Account created successfully!'); window.location.href='login.php';</script>";
        } else {
            // Log the error and display a generic message
            error_log("Error creating account: " . mysqli_error($conn));
            echo "<script>alert('An error occurred. Please try again later.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup | Deanslist</title>
    <link rel="stylesheet" href="../css/signup_student.css">
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">
</head>
<body>
    <!-- Home Icon -->
    <a href="../home.php">
        <img src="../img/homeicon.png" alt="Home" class="home-icon">
    </a>
    
    <div class="signup-container">
        <h2>Create an Account</h2>
        <form action="signup.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required><br>
            <input type="text" name="student_id" placeholder="Student ID" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
            <select name="department" required>
                <option value="">Select Department</option>
                <option value="College of Education, Arts and Sciences">CEAS</option>
                <option value="College of Management and Entrepreneurship">CME</option>
                <option value="College of Enginnering">COE</option>
                <option value="College of Technology">COT</option>
            </select><br><br>
            <button type="submit">Sign Up</button>
        </form>
    </div>
</body>
</html>
