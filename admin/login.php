<?php
include '../assets/config.php'; // Include your database configuration

session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $adminName = $_POST['admin_name'];
    $adminDepartment = $_POST['admin_department'];
    $password = $_POST['password'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_name = ?");
    $stmt->bind_param("s", $adminName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            // Valid login
            $_SESSION['loggedin'] = true;
            $_SESSION['admin_name'] = $adminName;
            $_SESSION['admin_department'] = $adminDepartment;
            header("Location: dashboard.php"); // Redirect to admin dashboard
            exit();
        } else {
            echo "<script>alert('Invalid password.');</script>";
        }
    } else {
        echo "<script>alert('Admin user not found.');</script>";
    }
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
            <form action="login.php" method="POST">
                <input type="text" name="admin_name" placeholder="Admin Name" required><br>
                <input type="text" name="admin_department" placeholder="Admin Department" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
