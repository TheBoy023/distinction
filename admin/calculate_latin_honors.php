<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the student_id from the form
    $student_id = trim($_POST['student_id']); // Trim whitespace

    // Validate student ID (prevent empty submissions)
    if (empty($student_id)) {
        $error_message = "Please enter a valid Student ID.";
    } else {
        // Database connection
        $conn = new mysqli("localhost", "u132092183_distinct", "Distinct@2024", "u132092183_distinct");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Initialize variables
        $total_grades = 0;
        $total_years = 0;
        $average_grade = 0.0;
        $honor_message = "";
        $year_grades = [];
        $insert_success = false;

        // Prepare the query with variable binding
        $sql = "SELECT year_level, AVG(average_grade) AS yearly_average 
                FROM deans_list_averages 
                WHERE student_id = ? 
                GROUP BY year_level 
                ORDER BY year_level";
        
        // Prepare the statement
        $stmt = $conn->prepare($sql);
        
        // Check if statement preparation was successful
        if ($stmt === false) {
            $error_message = "Error preparing statement: " . $conn->error;
        } else {
            // Bind parameters
            $bind_result = $stmt->bind_param("s", $student_id);
            
            // Check if binding was successful
            if ($bind_result === false) {
                $error_message = "Error binding parameters: " . $stmt->error;
            } else {
                // Execute the query
                $execute_result = $stmt->execute();
                
                // Check if execution was successful
                if ($execute_result === false) {
                    $error_message = "Error executing query: " . $stmt->error;
                } else {
                    // Get the result
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        // Loop through the results to calculate the overall average
                        while ($row = $result->fetch_assoc()) {
                            $year = $row['year_level'];
                            $yearly_average = $row['yearly_average'];
                            $year_grades[$year] = $yearly_average;
                            $total_grades += $yearly_average;
                            $total_years++;
                        }

                        // Calculate the overall average
                        if ($total_years > 0) {
                            $average_grade = $total_grades / $total_years;
                        }

                        // Determine Latin Honor based on the overall average
                        if ($average_grade <= 1.20) {
                            $honor_message = "Summa Cum Laude";
                        } elseif ($average_grade >= 1.21 && $average_grade <= 1.50) {
                            $honor_message = "Magna Cum Laude";
                        } elseif ($average_grade >= 1.51 && $average_grade <= 1.75) {
                            $honor_message = "Cum Laude";
                        } else {
                            $honor_message = "No Latin Honor";
                        }

                        // Prepare the average and honor message variables
                        $formatted_average = number_format($average_grade, 2);

                        // Prepare to insert/update the Latin honors record
                        $insert_sql = "INSERT INTO latin_honors 
                                       (student_id, overall_average, latin_honor, calculation_date) 
                                       VALUES (?, ?, ?, NOW()) 
                                       ON DUPLICATE KEY UPDATE 
                                       overall_average = ?, 
                                       latin_honor = ?, 
                                       calculation_date = NOW()";
                        
                        $insert_stmt = $conn->prepare($insert_sql);
                        
                        // Bind parameters correctly
                        $bind_insert = $insert_stmt->bind_param(
                            "sssss", 
                            $student_id, 
                            $formatted_average, 
                            $honor_message, 
                            $formatted_average, 
                            $honor_message
                        );
                        
                        // Check if binding was successful
                        if ($bind_insert === false) {
                            $error_message = "Error binding insert parameters: " . $insert_stmt->error;
                        } else {
                            // Execute the insert/update
                            $insert_success = $insert_stmt->execute();
                            
                            if (!$insert_success) {
                                $error_message = "Failed to save Latin honors: " . $conn->error;
                            }
                        }
                        
                        $insert_stmt->close();
                    } else {
                        $error_message = "No academic records found for Student ID: $student_id";
                    }
                }
            }
            
            $stmt->close();
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latin Honor Calculation</title>
    <link rel="stylesheet" href="../css/calculate_latin_honors.css">
    <link rel="icon" href="../img/logobr.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <h1>Latin Honor Calculation</h1>

        <div class="search-bar">
            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                <!-- Home Button -->
                <form action="uploaded_application_latin_honors.php" method="get" style="display: inline;">
                    <button type="submit">Back</button>
                </form>
            </div>
        </div>

        <!-- Form to input student ID -->
        <form method="POST" action="">
            <label for="student_id">Enter Student ID:</label>
            <input type="text" id="student_id" name="student_id" required>
            <button type="submit">Calculate</button>
        </form>

        <!-- Error or Success Messages -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Results Section -->
        <?php if (isset($honor_message) && $honor_message !== "No records found for Student ID: $student_id"): ?>
            <?php if ($insert_success): ?>
                <div class="alert alert-success">
                    Latin Honors record successfully saved.
                </div>
            <?php endif; ?>

            <table>
                <tr>
                    <th>Student ID</th>
                    <td><?php echo htmlspecialchars($student_id); ?></td>
                </tr>
                <?php foreach ($year_grades as $year => $grade): ?>
                <tr>
                    <th>Year <?php echo $year; ?> Average</th>
                    <td><?php echo number_format($grade, 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <th>Overall Average</th>
                    <td><?php echo number_format($average_grade, 2); ?></td>
                </tr>
                <tr>
                    <th>Latin Honor</th>
                    <td><?php echo $honor_message; ?></td>
                </tr>
            </table>

            <div class="honor-message">
                <?php echo $honor_message; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>