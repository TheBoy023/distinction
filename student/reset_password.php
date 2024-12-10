<?php
// Start a secure session with cookies only (disable access via JavaScript)
session_start([
    'cookie_lifetime' => 0,
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']), // Only use secure cookies over HTTPS
    'use_strict_mode' => true
]);

include '../assets/config.php'; // Securely include the configuration file

// Initialize variables
$error_message = '';
$success_message = '';
$show_reset_form = false;

// Validate reset token
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $reset_token = trim($_GET['token']);

    try {
        // Prepare statement to check token validity
        $stmt = $conn->prepare("SELECT id, email, reset_token_expiry FROM users WHERE reset_token = ?");
        $stmt->bind_param("s", $reset_token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $token_expiry = strtotime($user['reset_token_expiry']);

            // Check if token is still valid
            if ($token_expiry > time()) {
                $show_reset_form = true;
            } else {
                $error_message = 'Password reset link has expired. Please request a new one.';
            }
        } else {
            $error_message = 'Invalid or used reset token.';
        }

        $stmt->close();
    } catch (Exception $e) {
        $error_message = 'An unexpected error occurred. Please try again.';
        error_log($e->getMessage());
    }
}

// Handle password reset form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reset_token'])) {
    $reset_token = trim($_POST['reset_token']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords match
    if ($new_password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
        $show_reset_form = true;
    } elseif (strlen($new_password) < 8) {
        $error_message = 'Password must be at least 8 characters long.';
        $show_reset_form = true;
    } else {
        try {
            // Begin transaction for added security
            $conn->begin_transaction();

            // Prepare statement to find user by token
            $stmt = $conn->prepare("SELECT id, email FROM users WHERE reset_token = ?");
            $stmt->bind_param("s", $reset_token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_ARGON2ID);

                // Update password and clear reset token
                $update_stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
                $update_stmt->bind_param("si", $hashed_password, $user['id']);

                if ($update_stmt->execute()) {
                    // Commit the transaction
                    $conn->commit();

                    // Optional: Send confirmation email
                    $subject = "Password Reset Confirmation - DistinctionHub";
                    $message = "Hello, Technologist!\n\n";
                    $message .= "Your password for your account has been successfully reset.\n";
                    $message .= "If you did not make this change, please contact support immediately.\n\n";
                    $message .= "Best regards,\nDistinction Support Team";

                    $headers = "From: noreply@distinction.com\r\n";
                    $headers .= "Reply-To: noreply@distinction.com\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion();

                    @mail($user['email'], $subject, $message, $headers);

                    $success_message = 'Password reset successfully. You can now log in with your new password.';
                    $show_reset_form = false;
                } else {
                    // Rollback the transaction
                    $conn->rollback();
                    $error_message = 'Failed to reset password. Please try again.';
                    $show_reset_form = true;
                }

                $update_stmt->close();
            } else {
                $error_message = 'Invalid reset token.';
                $show_reset_form = true;
            }

            $stmt->close();
        } catch (Exception $e) {
            // Rollback the transaction
            $conn->rollback();
            $error_message = 'An unexpected error occurred. Please try again.';
            error_log($e->getMessage());
            $show_reset_form = true;
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
    <title>Reset Password - Deanslist</title>
    <link rel="stylesheet" href="../css/login_student.css">
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
                "A secure password is the key to protecting your digital identity." 
                <br><span>- DistinctionHub Security</span>
            </div>
        </div>

        <!-- Right section with reset password form -->
        <div class="right-section">
            <h2>Reset Password</h2>
            
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

            <?php if ($show_reset_form): ?>
                <form action="reset_password.php" method="POST" autocomplete="off">
                    <input type="hidden" name="reset_token" value="<?php echo htmlspecialchars($reset_token); ?>">
                    <input type="password" name="new_password" maxlength="20" placeholder="New Password" required><br>
                    <input type="password" name="confirm_password" maxlength="20" placeholder="Confirm New Password" required><br>
                    <button type="submit">Reset Password</button>
                </form>
            <?php endif; ?>

            <?php if (!$show_reset_form && !$success_message): ?>
                <div style="text-align: center;">
                    <p>Invalid or expired reset link.</p>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <a href="login.php" class="signup-link">Go to Login</a>
            <?php else: ?>
                <a href="forgot_password.php" class="signup-link">Request a new reset link</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>