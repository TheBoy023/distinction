<?php
// Start a secure session with cookies only (disable access via JavaScript)
session_start([
    'cookie_lifetime' => 0,
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']), // Only use secure cookies over HTTPS
    'use_strict_mode' => true
]);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include '../assets/config.php'; // Securely include the configuration file

//Load Composer's autoloader
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Generate a secure, cryptographically strong token
function generateResetToken() {
    return bin2hex(random_bytes(32)); // 64 character token
}

// Send password reset email
function sendResetEmail($email, $reset_token) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'distinctionhub.online@gmail.com'; // Use a constant from config file
        $mail->Password   = 'toax otcp mcqu ovza'; // Use a constant from config file
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Debugging - enable verbose debug output
        $mail->SMTPDebug = SMTP::DEBUG_OFF;

        // Create a secure reset link with expiration
        $reset_link = "https://" . $_SERVER['HTTP_HOST'] . "/DEANSLIST/admin/reset_password.php?token=" . $reset_token;
        
        // Recipients
        $mail->setFrom('distinctionhub.online@gmail.com', 'Distinction Support');
        $mail->addAddress($email);
        $mail->addReplyTo('distinctionhub.online@gmail.com', 'Distinction Support');

        // Content
        $mail->isHTML(true); // Changed to HTML for better formatting
        $mail->Subject = 'Password Reset for account';
        $mail->Body    = "
            <html>
            <body>
                <h2>Hello, Admin!</h2>
                <p>You have requested to reset your password. Click the link below to reset:</p>
                <p><a href='{$reset_link}'>Reset Password</a></p>
                <p>This link will expire in 1 hour.</p>
                <p>If you did not request this password reset, please ignore this email.</p>
                <p>Best regards,<br>Distinction Support Team</p>
            </body>
            </html>
        ";
        $mail->AltBody = "Hello, Admin!\n\n"
                       . "You have requested to reset your password. Click the link below to reset:\n\n"
                       . $reset_link . "\n\n"
                       . "This link will expire in 1 hour.\n\n"
                       . "If you did not request this password reset, please ignore this email.\n\n"
                       . "Best regards,\nDistinction Support Team";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the detailed error for debugging
        error_log('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}

// Handle form submission
$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Invalid email format.';
    } else {
        try {
            // Use a prepared statement to prevent SQL injection
            $stmt = $conn->prepare("SELECT admin_id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                // Generate a unique, secure reset token
                $reset_token = generateResetToken();
                $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Prepare statement to store reset token
                $update_stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
                $update_stmt->bind_param("sss", $reset_token, $token_expiry, $email);
                
                if ($update_stmt->execute()) {
                    // Attempt to send reset email
                    if (sendResetEmail($email, $reset_token)) {
                        $success_message = 'Password reset instructions have been sent to your email.';
                    } else {
                        $error_message = 'Failed to send reset email. Please contact support.';
                    }
                } else {
                    $error_message = 'Unable to process your request. Please try again.';
                }
                
                $update_stmt->close();
            } else {
                // Prevent email enumeration
                $error_message = 'If an account exists with this email, reset instructions will be sent.';
            }

            $stmt->close();
        } catch (Exception $e) {
            $error_message = 'An unexpected error occurred. Please try again later.';
            // Log the actual error for admin review
            error_log($e->getMessage());
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Admin</title>
    <link rel="stylesheet" href="../css/login_admin.css">
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">

    <!-- Security headers -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self'; style-src 'self'; script-src 'self';">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
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
                "Carefulness costs you nothing. Carelessness may cost you your life." 
                <br><span>- Admin Security</span>
            </div>
        </div>

        <!-- Right section with forgot password form -->
        <div class="right-section">
            <h2>Forgot Password</h2>
            
            <?php if (!empty($error_message)): ?>
                <div class="error-message" style="color: red; text-align: center; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="success-message" style="color: green; text-align: center; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <form action="forgot_password.php" method="POST" autocomplete="off">
                <input type="email" name="email" id="email" maxlength="50" placeholder="Enter your email" required><br>
                <button type="submit">Reset Password</button>
            </form>
            <a href="login.php" class="signup-link">Back to Login</a>
        </div>
    </div>
</body>
</html>