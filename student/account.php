<?php
session_start();
$servername = "localhost";
$username = "u132092183_distinct";
$password = "Distinct@2024";
$dbname = "u132092183_distinct";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming the user is logged in and their ID is stored in the session
$email = $_SESSION['email'] ?? null;

if (!$email) {
    die("Unauthorized access. Please login.");
}

// Fetch the current user data
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_account'])) {
        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("UPDATE users SET student_name=?, student_id=?, email=?, course=?, major=?, year_level=?, section=?, program=? WHERE email=?");
        
        // Bind parameters
        $stmt->bind_param("sssssssss", 
            $studentName, 
            $studentId, 
            $email, 
            $course, 
            $major, 
            $yearlevel, 
            $section, 
            $program,
            $email  // Using email for WHERE clause
        );

        // Retrieve and sanitize inputs
        $studentName = htmlspecialchars($_POST['student_name']);
        $studentId = htmlspecialchars($_POST['student_id']);
        $email = htmlspecialchars($_POST['email']);
        $course = htmlspecialchars($_POST['course']);
        $major = htmlspecialchars($_POST['major']);
        $yearlevel = htmlspecialchars($_POST['year_level']);
        $section = htmlspecialchars($_POST['section']);
        $program = htmlspecialchars($_POST['program']);

        // Execute and check for errors
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "<div style='color: green; text-align: center;'>Account updated successfully.</div>";
                // Update session variables if needed
                $_SESSION['student_name'] = $studentName;
            } else {
                echo "<div style='color: red; text-align: center;'>No changes made. Check if data is different.</div>";
            }
        } else {
            echo "<div style='color: red; text-align: center;'>Error updating account: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }

    // Change password
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Verify current password
        if (password_verify($current_password, $user['password'])) {
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

    <!-- Form to update users details -->
    <form method="POST" action="account.php">
        <input type="text" name="student_name" maxlength="50" value="<?= htmlspecialchars($user['student_name']) ?>" required>
        <input type="text" name="student_id" maxlength="7" value="<?= htmlspecialchars($user['student_id']) ?>" required>
        <input type="text" name="email" maxlength="50" value="<?= htmlspecialchars($user['email']) ?>" required>
        <input type="text" name="course" maxlength="50" value="<?= htmlspecialchars($user['course']) ?>" required>
        <input type="text" name="major" maxlength="15" value="<?= htmlspecialchars($user['major']) ?>" required>
        <input type="text" name="year_level" maxlength="10" value="<?= htmlspecialchars($user['year_level']) ?>" required>
        <input type="text" name="section" maxlength="1" value="<?= htmlspecialchars($user['section']) ?>" required>
        <input type="text" name="program" maxlength="6" value="<?= htmlspecialchars($user['program']) ?>" required>
        <input type="text" name="department" value="<?= htmlspecialchars($user['department']) ?>" disabled>
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