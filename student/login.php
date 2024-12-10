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
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">

    <!-- Security headers -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self'; style-src 'self'; script-src 'self';">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">

    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            display: flex;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            width: 90%; /* Use percentage for responsiveness */
            max-width: 1200px;
            overflow: hidden;
            flex-direction: row;
        }

        /* Left section */
        .left-section {
            width: 50%;
            padding: 40px;
            background-color: #f7f7f7;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .quote {
            font-size: 24px;
            color: #333;
            text-align: center;
            font-style: italic;
            font-family: 'Georgia', serif;
            transform: scale(0.9);
            animation: zoomIn 6s ease-in-out infinite;
        }

        .quote span {
            font-style: italic;
            color: maroon;
        }

        @keyframes zoomIn {
            0% {
                transform: scale(0.9);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        /* Right section */
        .right-section {
            width: 50%;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: none;
        }

        h2 {
            color: maroon;
            font-size: 32px;
            margin-bottom: 20px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 20px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 18px;
        }

        button {
            background-color: maroon;
            color: white;
            padding: 18px;
            width: 100%;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 10px;
            position: relative;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #800000;
            animation: move 0.6s ease-in-out;
        }

        @keyframes move {
            0% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
            100% {
                transform: translateY(0);
            }
        }

        .signup-link {
            margin-top: 15px;
            color: maroon;
            font-weight: bold;
            text-decoration: none;
        }

        .signup-link:hover {
            text-decoration: underline;
        }

        /* Home icon */
        .home-icon {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 40px;
            height: 40px;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }

        .home-icon:hover {
            transform: scale(1.1);
        }

        /* Media Queries for responsiveness */
        @media screen and (max-width: 768px) {
            .container {
                flex-direction: column; /* Stack sections vertically */
                width: 100%;
            }

            .left-section, .right-section {
                width: 100%; /* Full width on smaller screens */
                padding: 20px;
            }

            .quote {
                font-size: 18px; /* Adjust font size */
            }

            h2 {
                font-size: 28px;
            }

            input[type="text"], input[type="password"] {
                padding: 15px; /* Adjust padding for smaller screens */
                font-size: 16px;
            }

            button {
                padding: 15px;
                font-size: 16px;
            }
        }

        @media screen and (max-width: 480px) {
            .quote {
                font-size: 16px; /* Smaller font size for very small screens */
            }

            h2 {
                font-size: 24px;
            }

            input[type="text"], input[type="password"] {
                padding: 12px; /* Even smaller padding */
                font-size: 14px;
            }

            button {
                padding: 12px;
                font-size: 14px;
            }

            .home-icon {
                width: 30px;
                height: 30px;
            }
        }
    </style>
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