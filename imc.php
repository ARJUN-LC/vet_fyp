<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stud";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for fetched data
$assignment_marks = [];
$cia_marks = [];
$model_exam = null;
$student_name = null;
$subject = null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calculate'])) {
    // Get the inputs
    $roll_num = $_POST['roll_num'];
    $sem = $_POST['sem'];
    $sub = $_POST['sub'];

    // Query to fetch data from st_data table
    $sql_data = "SELECT name FROM st_data WHERE roll_num = ?";
    $stmt_data = $conn->prepare($sql_data);
    $stmt_data->bind_param("s", $roll_num);
    $stmt_data->execute();
    $result_data = $stmt_data->get_result();

    if ($result_data->num_rows > 0) {
        $row_data = $result_data->fetch_assoc();
        $student_name = $row_data['name'];
    } else {
        $student_name = "No student found with this Roll Number.";
    }
    $stmt_data->close();

    // Query to fetch data from marks table
    $sql_marks = "SELECT assg1, assg2, assg3, cia1, cia2, model FROM marks WHERE st_id = ? AND sem = ? AND sub = ?";
    $stmt_marks = $conn->prepare($sql_marks);
    $stmt_marks->bind_param("sis", $roll_num, $sem, $sub);
    $stmt_marks->execute();
    $result_marks = $stmt_marks->get_result();

    if ($result_marks->num_rows > 0) {
        $row_marks = $result_marks->fetch_assoc();
        $assignment_marks = [$row_marks['assg1'], $row_marks['assg2'], $row_marks['assg3']];
        $cia_marks = [$row_marks['cia1'], $row_marks['cia2']];
        $model_exam = $row_marks['model'];
    } else {
        $assignment_marks = [];
        $cia_marks = [];
        $model_exam = null;
    }
    $stmt_marks->close();
}

// Function to calculate internal marks
function calculate_internal_marks($assignments, $cia, $model) {
    if (count($assignments) === 3 && count($cia) === 2 && isset($model)) {
        $total_assignment_marks = (array_sum($assignments) / 30) * 10;
        $average_internal_marks = (array_sum($cia) / 60) * 20;
        $model_marks = ($model / 50) * 20;

        $final_marks = $total_assignment_marks + $average_internal_marks + $model_marks;
        return number_format($final_marks, 2); // Round off to 2 decimal places
    } else {
        return "Data Missing or Invalid";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Marks Calculator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: white;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #ff9900;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container {
            background-color: #333;
            padding: 30px;
            border-radius: 8px;
            width: 350px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        input {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #555;
            width: 100%;
            font-size: 16px;
            background-color: #222;
            color: white;
        }
        input[type="submit"] {
            background-color: #ff9900;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #ffcc00;
        }
        .result {
            text-align: center;
            margin-top: 20px;
        }
        .result p {
            font-size: 20px;
            color: #28a745;
        }
        .error {
            text-align: center;
            color: #dc3545;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <h2>Internal Marks Calculator</h2>

    <div class="form-container">
        <form method="post" action="">
            <label for="roll_num">Roll Number:</label>
            <input type="text" id="roll_num" name="roll_num" required>

            <label for="sem">Semester:</label>
            <input type="number" id="sem" name="sem" required>

            <label for="sub">Subject:</label>
            <input type="text" id="sub" name="sub" required>

            <input type="submit" name="calculate" value="Calculate Internal Marks">
        </form>
    </div>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calculate'])) { ?>
    <div class="result">
        <h3>Student: <?php echo $student_name; ?></h3>
        <?php
            if (!empty($assignment_marks) && !empty($cia_marks) && isset($model_exam)) {
                $internal_marks = calculate_internal_marks($assignment_marks, $cia_marks, $model_exam);
                echo "<p><strong>Calculated Internal Marks: " . $internal_marks . "</strong></p>";
            } else {
                echo "<p class='error'>Data is missing or invalid for the provided Roll Number, Semester, or Subject.</p>";
            }
        ?>
    </div>
    <?php } ?>

</body>
</html>
