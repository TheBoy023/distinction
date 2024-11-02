<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "finals";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming the admin is logged in and their ID is stored in the session
$studentId = $_SESSION['student_id'] ?? null;

if (!$studentId) {
    die("Unauthorized access. Please login.");
}

// Fetch the current admin data
$sql = "SELECT * FROM users WHERE student_id = '$studentId'";
$result = $conn->query($sql);
$admin = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update admin account details
    if (isset($_POST['update_account'])) {
        $studentName = $_POST['name'];
        $email = $_POST['email'];

        $update_sql = "UPDATE users SET name='$studentName', email='$email' WHERE student_id='$studentId'";

        if ($conn->query($update_sql) === TRUE) {
            echo "<div style='color: green; text-align: center;'>Account updated successfully.</div>";
            // Update session variables if needed
            $_SESSION['student_name'] = $studentName;
        } else {
            echo "<div style='color: red; text-align: center;'>Error updating account: " . $conn->error . "</div>";
        }
    }

    // Change password
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Verify current password
        if (password_verify($current_password, $admin['password'])) {
            if ($new_password === $confirm_password) {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password in the database
                $update_password_sql = "UPDATE users SET password='$hashed_password' WHERE student_id='$studentId'";
                if ($conn->query($update_password_sql) === TRUE) {
                    echo "<div style='color: green; text-align: center;'>Password changed successfully.</div>";
                } else {
                    echo "<div style='color: red; text-align: center;'>Error updating password: " . $conn->error . "</div>";
                }
            } else {
                echo "<div style='color: red; text-align: center;'>New passwords do not match.</div>";
            }
        } else {
            echo "<div style='color: red; text-align: center;'>Current password is incorrect.</div>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Account</title>
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            background-color: #ffffff;
            margin: 0;
        }

        h2 {
            color: maroon;
            text-align: center;
            margin-bottom: 30px;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            display: flex; /* Added */
            flex-direction: column; /* Ensure vertical stacking of elements */
            align-items: center; /* Center content horizontally */
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: maroon;
            font-weight: bold;
        }

        input[type="text"], input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input:focus {
            border-color: maroon;
            outline: none;
        }

        button {
            background-color: maroon;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        button:hover {
            background-color: red;
        }

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
    </style>
</head>
<body>
    <a href="dashboard.php">
        <img src="../img/homeicon.png" alt="Home" class="home-icon">
    </a>

    <h2>Account Details</h2>

    <!-- Form to update admin details -->
    <form method="POST" action="account.php">
        <input type="text" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required>
        <input type="text" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
        <button type="submit" name="update_account">Update Account</button>
    </form>

    <h2>Change Password</h2>

    <!-- Form to change password -->
    <form method="POST" action="account.php">
        <input type="password" name="current_password" placeholder="Current Password" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
        <button type="submit" name="change_password">Change Password</button>
    </form>

</body>
</html>