<?php
include '../assets/config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Password validation
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert admin into the database
        $sql = "INSERT INTO admins (username, email, password)
                VALUES ('$username', '$email', '$hashedPassword')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Admin account created successfully!'); window.location.href='login_admin.php';</script>";
        } else {
            echo "<script>alert('Error creating admin account. Please try again.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up</title>
    <link rel="stylesheet" href="../css/signup_admin.css">
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">
</head>
<body>
    <!-- Home Icon -->
    <a href="../home.php">
        <img src="../img/homeicon.png" alt="Home" class="home-icon">
    </a>

    <div class="container">
        <!-- Left Section with the Quote -->
        <div class="left-section">
            <div class="quote">
                "The journey of leadership begins with a strong foundation." <br>
                <span>- Admin's Code</span>
            </div>
        </div>

        <!-- Right Section with the Admin Signup Form -->
        <div class="right-section">
            <h2>Create Admin Account</h2>
            <form action="signup.php" method="POST">
                <input type="text" name="username" placeholder="Username" required><br>
                <input type="email" name="email" placeholder="Email" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
                <button type="submit">Sign Up</button>
            </form>
            <a href="login_admin.php" class="login-link">Already have an account? Log in</a>
        </div>
    </div>
</body>
</html>
