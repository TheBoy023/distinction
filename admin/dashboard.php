<?php
session_start(); // Ensure session is started

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}

// Database connection parameters
$host = 'localhost';
$db = 'u132092183_distinct';
$user = 'u132092183_distinct';
$password = 'Distinct@2024';

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve admin details using the session email
    $stmt = $pdo->prepare("SELECT admin_name, admin_department FROM admins WHERE email = :email");
    $stmt->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
 
    // Set admin variables
    $adminName = $admin['admin_name'] ?? 'Unknown';
    $adminDepartment = $admin['admin_department'] ?? 'Unknown';

    // Query to get the total number of students
    $stmt = $pdo->query("SELECT COUNT(*) AS student_id FROM users");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_students = $row['student_id'];

    // Query to get the total number of Latin honor awardees
    $stmt_total = $pdo->query("SELECT COUNT(*) AS total FROM latin_honors WHERE latin_honor IN ('Cum Laude', 'Magna Cum Laude', 'Summa Cum Laude')");
    $row_total = $stmt_total->fetch(PDO::FETCH_ASSOC);
    $total_awardees = $row_total['total'];

    // Query to get the number of Cum Laude awardees
    $stmt_cum_laude = $pdo->query("SELECT COUNT(*) AS cum_laude FROM latin_honors WHERE latin_honor = 'Cum Laude'");
    $row_cum_laude = $stmt_cum_laude->fetch(PDO::FETCH_ASSOC);
    $cum_laude_count = $row_cum_laude['cum_laude'];

    // Query to get the number of Magna Cum Laude awardees
    $stmt_magna_cum_laude = $pdo->query("SELECT COUNT(*) AS magna_cum_laude FROM latin_honors WHERE latin_honor = 'Magna Cum Laude'");
    $row_magna_cum_laude = $stmt_magna_cum_laude->fetch(PDO::FETCH_ASSOC);
    $magna_cum_laude_count = $row_magna_cum_laude['magna_cum_laude'];

    // Query to get the number of Summa Cum Laude awardees
    $stmt_summa_cum_laude = $pdo->query("SELECT COUNT(*) AS summa_cum_laude FROM latin_honors WHERE latin_honor = 'Summa Cum Laude'");
    $row_summa_cum_laude = $stmt_summa_cum_laude->fetch(PDO::FETCH_ASSOC);
    $summa_cum_laude_count = $row_summa_cum_laude['summa_cum_laude'];

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Overview</title>
    <link rel="stylesheet" href="../css/dashboard_admin.css">
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">
</head>
<body>
    <header>
    <div class="logo">Cebu Technological University</div>
    <div class="admin-options">
        <span>Welcome, <?php echo htmlspecialchars($adminName); ?> (Department: <?php echo htmlspecialchars($adminDepartment); ?>)</span>
        <a href="logout.php">Logout</a>
    </div>
   
</header>

    <div class="container">
        <aside class="sidebar" id="sidebar">
            <ul>
                <li><a href="dashboard.php" target="_self">Dashboard Overview</a></li>
                <li><a href="uploaded_application_deans_list.php">Manage Application Dean's List</a></li>
                <li><a href="deans_list_reports.php">View Dean's List Students</a></li>
                <li><a href="uploaded_application_latin_honors.php">Manage Application Latin Honors</a></li>
                <li><a href="account.php">Account</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <section class="dashboard-overview">
                <h2>Dashboard Overview</h2>
                <div class="overview-stats">
                    <div class="stat-card">
                        <h3>Total Students</h3>
                        <p class="count"><?php echo $total_students; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Dean’s List Students for this Semester</br>(1st - 4th Year)</h3>
                        
                        <?php
                        // Function to determine the current semester based on the current date
                        function getCurrentSemester() {
                            $month = date('n'); // Get the current month (1-12)

                            if ($month >= 6 && $month <= 10) {
                                return 'First Semester'; // Typically June to October
                            } elseif ($month >= 11 || $month <= 3) {
                                return 'Second Semester'; // Typically November to March
                            } else {
                                return 'Summer Semester'; // Typically April to May
                            }
                        }

                        // Get the current semester for the admin
                        $semester = getCurrentSemester();

                        // Query to get the count of students in the Dean's List for each year level in the current semester
                        $stmt_deans = $pdo->prepare("
                            SELECT year_level, COUNT(DISTINCT student_id) AS total_deans_list_students
                            FROM deans_list_averages 
                            WHERE deans_list_status = 'Yes' 
                            AND semester = :semester
                            GROUP BY year_level
                        ");
                        $stmt_deans->bindParam(':semester', $semester, PDO::PARAM_STR);
                        $stmt_deans->execute();
                        $rows_deans = $stmt_deans->fetchAll(PDO::FETCH_ASSOC);

                        // Display the results within the stat-card
                        foreach ($rows_deans as $row_deans) {
                            echo '<p class="small-text">' . $row_deans['year_level'] . ' - Total Dean\'s List Students: ' . $row_deans['total_deans_list_students'] . '</p>';
                        }
                        ?>
                        
                    </div>
                    <div class="stat-card">
                        <h3>Latin Honor Awardees</h3>
                        <p class="count"><?php echo $total_awardees; ?></p>
                        <ul class="honors-breakdown">
                            <li>Cum Laude: <?php echo $cum_laude_count; ?></li>
                            <li>Magna Cum Laude: <?php echo $magna_cum_laude_count; ?></li>
                            <li>Summa Cum Laude: <?php echo $summa_cum_laude_count; ?></li>
                        </ul>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <script src="dashboard.js"></script>
</body>
</html>