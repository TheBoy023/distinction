<?php
// Start a secure session with cookies only (disable access via JavaScript)
session_start([
    'cookie_lifetime' => 0,
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']), // Only use secure cookies over HTTPS
    'use_strict_mode' => true
]);

include '../assets/config.php'; // Securely include the configuration file

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // password should not be sanitized as it's hashed

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.');</script>";
    } else {
        // Use a prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password using password_verify for secure hashing
            if (password_verify($password, $user['password'])) {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);

                // Store session variables
                $_SESSION['loggedin'] = true;
                $_SESSION['email'] = $user['email'];

                // Redirect to dashboard page
                header("Location: dashboard.php");
                exit();
            } else {
                echo "<script>alert('Invalid password.');</script>";
            }
        } else {
            echo "<script>alert('No account found with this email.');</script>";
        }

        // Close statement and database connection securely
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
    <title>Deanslist Login</title>
    <link rel="stylesheet" href="../css/login_student.css?v=time();">
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">

    <!-- Security headers -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self'; style-src 'self'; script-src 'self';">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
    
</head>
<body>
    <!-- Home icon -->
    <a href="../index.php">
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
            <h2>Student Login</h2>
            <form action="login.php" method="POST" autocomplete="off">
                <input type="email" name="email" id="email" maxlength="50" placeholder="Email" required><br>
                <input type="password" name="password" maxlength="20" placeholder="Password" required><br>
                <a href="forgot_password.php" class="forgot-password-link">Forgot Password?</a>
                <button type="submit">Login</button>
            </form>
            <a href="signup.php" class="signup-link">Don't have an account? Sign up</a>
        </div>
    </div>
</body>
</html>