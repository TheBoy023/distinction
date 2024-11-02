<?php
session_start(); // Ensure session is started

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}

// Retrieve student's name and ID from session
$studentName = $_SESSION['student_name'] ?? 'Unknown';  // Default to 'Unknown' if not set
$studentId = $_SESSION['student_id'] ?? 'Unknown';
$department = $_SESSION['department'] ?? 'Unknown';

// Database connection parameters
$host = 'localhost';
$db = 'finals';
$user = 'root';
$password = '';

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to get the total number of students
    $stmt = $pdo->query("SELECT COUNT(*) AS student_id FROM users");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_students = $row['student_id'];

    // Query to get the total number of Latin honor awardees
    $stmt_total = $pdo->query("SELECT COUNT(*) AS total FROM latin_grades WHERE honor IN ('Cum Laude', 'Magna Cum Laude', 'Summa Cum Laude')");
    $row_total = $stmt_total->fetch(PDO::FETCH_ASSOC);
    $total_awardees = $row_total['total'];

    // Query to get the number of Cum Laude awardees
    $stmt_cum_laude = $pdo->query("SELECT COUNT(*) AS cum_laude FROM latin_grades WHERE honor = 'Cum Laude'");
    $row_cum_laude = $stmt_cum_laude->fetch(PDO::FETCH_ASSOC);
    $cum_laude_count = $row_cum_laude['cum_laude'];

    // Query to get the number of Magna Cum Laude awardees
    $stmt_magna_cum_laude = $pdo->query("SELECT COUNT(*) AS magna_cum_laude FROM latin_grades WHERE honor = 'Magna Cum Laude'");
    $row_magna_cum_laude = $stmt_magna_cum_laude->fetch(PDO::FETCH_ASSOC);
    $magna_cum_laude_count = $row_magna_cum_laude['magna_cum_laude'];

    // Query to get the number of Summa Cum Laude awardees
    $stmt_summa_cum_laude = $pdo->query("SELECT COUNT(*) AS summa_cum_laude FROM latin_grades WHERE honor = 'Summa Cum Laude'");
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
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">
    <style>
        /* General Styles */
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif; 
            color: #333;
            background-color: #fff7e6; 
        }

        body {
            display: flex;
            flex-direction: column;
            background-color: #fff7e6; 
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #800000; 
            color: #fff;
            padding: 1rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            animation: fadeInDown 1s ease-out;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
        }

        /* Admin Options */
        .admin-options a {
            color: #fff;
            margin-right: 1rem;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease-in-out;
        }

        .admin-options a:hover {
            color: #ffcc00; 
            text-decoration: underline;
        }

        /* Container */
        .container {
            display: flex;
            flex: 1;
            margin-top: 1rem;
            animation: fadeIn 1.5s ease-out;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #800000; 
            color: #fff;
            border-right: 1px solid #ddd;
            padding: 1rem;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 1rem 0;
        }

        .sidebar ul li a {
            color: #ffcc00; 
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease-in-out;
        }

        .sidebar ul li a:hover {
            color: #ffd633; 
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center; /* Centering the content */
        }

        /* Dashboard Overview */
        .dashboard-overview {
            margin-bottom: 2rem;
        }

        /* Overview Stats Cards */
        .overview-stats {
            display: flex;
            justify-content: space-between; /* Evenly space out the cards */
            align-items: stretch; /* Align the cards to the same height */
            gap: 1.5rem;
            flex-wrap: nowrap; /* Prevent wrapping to keep them in a single row */
            margin-bottom: 2rem;
        }

        /* Stat Card */
        .stat-card {
            background-color: #fff;
            border: 2px solid #ffcc00; 
            border-radius: 12px;
            padding: 3%;
            flex: 1; /* Allow cards to grow equally */
            min-width: 200px; /* Ensure a minimum width */
            height: 200px; /* Match the height to the width */
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin: 0 10px; /* Add some margin for spacing */
        }

        .stat-card:hover {
            transform: translateY(-10px); 
        }

        .stat-card h3 {
            margin: 0;
            font-size: calc(1rem + 0.5vw); /* Adjusted for better fit in square format */
            color: #800000; 
        }

        .stat-card .count {
            font-size: calc(1.5rem + 1vw); /* Scales with screen size */
            margin: 0.5rem 0;
            color: #333;
        }

        .stat-card .percentage {
            font-size: calc(0.8rem + 0.3vw); /* Scales with screen size */
            color: #800000; 
        }

        /* Adjusting text size for "Dean’s List Students for this Semester" container */
        .small-text {
            font-size: calc(0.8rem + 0.3vw); /* Scales with screen size for readability */
            margin: 0.3rem 0;
            color: #333;
        }

        /* Honors Breakdown */
        .honors-breakdown {
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: calc(0.8rem + 0.3vw); /* Scales for better fit */
            color: #666;
        }

        /* Recent Updates and Upcoming Events */
        .recent-updates, .upcoming-events {
            margin-bottom: 2rem;
        }

        .recent-updates ul, .upcoming-events ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .recent-updates li, .upcoming-events li {
            margin: 0.8rem 0;
            font-size: 1rem;
            color: #666;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            /* Header */
            header {
                flex-direction: column; /* Stack items vertically */
                align-items: flex-start; /* Align items to the start */
            }

            .logo {
                font-size: 1.5rem; /* Reduce logo size for smaller screens */
            }

            .admin-options a {
                margin-right: 0.5rem; /* Reduce margin for links */
            }

            /* Sidebar */
            .sidebar {
                width: 100%; /* Make sidebar full width */
                border-right: none; /* Remove border for smaller screens */
                padding: 0.5rem; /* Reduce padding */
            }

            /* Main Content */
            .main-content {
                padding: 1rem; /* Reduce padding in main content */
            }

            /* Overview Stats Cards */
            .overview-stats {
                flex-direction: column; /* Stack cards vertically */
                align-items: center; /* Center the cards */
                gap: 1rem; /* Adjust gap */
            }

            /* Stat Card */
            .stat-card {
                min-width: 100%; /* Ensure cards take full width */
                height: auto; /* Allow height to adjust automatically */
                margin: 0; /* Remove horizontal margin */
            }

            .stat-card h3 {
                font-size: calc(1rem + 1vw); /* Slightly increase text size */
            }

            .stat-card .count {
                font-size: calc(1.2rem + 1vw); /* Adjust scaling */
            }

            .stat-card .percentage {
                font-size: calc(0.7rem + 0.3vw); /* Adjust scaling */
            }

            /* Adjusting text size for "Dean’s List Students for this Semester" container */
            .small-text {
                font-size: calc(0.7rem + 0.3vw); /* Adjust size */
            }

            /* Honors Breakdown */
            .honors-breakdown {
                font-size: calc(0.7rem + 0.2vw); /* Scale for better fit */
            }

            /* Recent Updates and Upcoming Events */
            .recent-updates li, .upcoming-events li {
                font-size: 0.9rem; /* Reduce font size for better readability */
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Cebu Technological University</div>
        <div class="admin-options">
            <span>Welcome, <?php echo htmlspecialchars($studentName); ?> (ID: <?php echo htmlspecialchars($studentId); ?>) from: <?php echo htmlspecialchars($department); ?> </span>
            <a href="logout.php">Logout</a>
        </div>
    </header>
    <div class="container">
        <aside class="sidebar">
            <ul>
                <li><a href="dashboard.php" target="_self">Dashboard Overview</a></li>
                <li><a href="determine_deans_list.php">Apply for Dean's List</a></li>
                <li><a href="deans_list_status.php">View Dean's List Status</a></li>
                <li><a href="determine_latin_honor.php">Apply for Latin Honor</a></li>
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
                        <h3>Dean’s List Students for this Semester</h3>
                        
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
                        <h3>Latin Honor Awardees for these School Year</h3>
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
    <script src="../js/dashboard.js"></script>
</body>
</html>
