<?php
// Start a secure session
session_start([
    'cookie_lifetime' => 0,
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']), // Only use secure cookies if using HTTPS
    'use_strict_mode' => true
]);

include '../assets/config.php'; // Securely include the database configuration

// Only proceed if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input
    $adminName = htmlspecialchars(trim($_POST['admin_name']), ENT_QUOTES, 'UTF-8');
    $adminDepartment = htmlspecialchars(trim($_POST['admin_department']), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password']; // Password should be handled securely, no trimming or escaping

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_name = ?");
    $stmt->bind_param("s", $adminName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);

            // Store session variables securely
            $_SESSION['loggedin'] = true;
            $_SESSION['admin_name'] = $user['admin_name'];
            $_SESSION['admin_department'] = $user['admin_department'];

            // Redirect to the admin dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid password.');</script>";
        }
    } else {
        echo "<script>alert('Admin user not found.');</script>";
    }

    // Close statement and database connection securely
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/login_admin.css">
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

    <div class="container">
        <!-- Left Section with the Quote -->
        <div class="left-section">
            <div class="quote">
                "Leaders are made through diligence and hard work." <br>
                <span>- Admin's Code of Honor</span>
            </div>
        </div>

        <!-- Right Section with the Admin Login Form -->
        <div class="right-section">
            <h2>Admin Login</h2>
            <form action="login.php" method="POST" autocomplete="off">
                <input type="text" name="admin_name" placeholder="Admin Name" required><br>
                <input type="text" name="admin_department" placeholder="Admin Department" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>