<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stud";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['insert_marks'])) {
    $st_id = $_POST['st_id'];
    $sem = $_POST['sem'];
    $sub = $_POST['sub'];
    $assg1 = $_POST['assg1'];
    $assg2 = $_POST['assg3'];
    $assg3 = $_POST['assg2'];
    $cia1 = $_POST['cia1'];
    $cia2 = $_POST['cia2'];
    $model = $_POST['mode'];

    $sql = "INSERT INTO marks (st_id, sem, sub, assg1, assg2, assg3, cia1, cia2, model) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisiiiiii", $st_id, $sem, $sub, $assg1, $assg2, $assg3, $cia1, $cia2, $model);

    if ($stmt->execute()) {
        echo "<p class='success'>Marks inserted successfully</p>";
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_marks'])) {
    $roll_num = $_POST['roll_num'];
    $sem = $_POST['sem'];
    $sub = $_POST['sub'];
    
    $sql = "DELETE FROM marks WHERE st_id = ? AND sem = ? AND sub = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $roll_num, $sem, $sub);

    if ($stmt->execute()) {
        echo "<p class='success'>Marks deleted successfully</p>";
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marks Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: white;
            margin: 0;
            padding: 20px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin: 10px 0;
            font-size: 18px;
            text-decoration: none;
            color: #ff9900;
        }
        .back-link:hover {
            color: #ffcc00;
        }
        form {
            background-color: #333;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        input {
            padding: 8px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #555;
            width: 100%;
        }
        input[type="submit"] {
            background-color: #ff9900;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #ffcc00;
        }
        h2 {
            color: #ff9900;
            text-align: center;
        }
        .success {
            color: #28a745;
            text-align: center;
        }
        .error {
            color: #dc3545;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- <a href="db.php" class="back-link">Back to Student Management</a> -->

    <form method="post" action="">
        <h2>Insert Student Marks</h2>
        Student ID: <input type="text" name="st_id" required><br>
        Semester: <input type="number" name="sem" required><br>
        Subject: <input type="text" name="sub" required><br>
        Assignment 1: <input type="number" name="assg1" required><br>
        Assignment 2: <input type="number" name="assg2" required><br>
        Assignment 3: <input type="number" name="assg3" required><br>
        CIA 1: <input type="number" name="cia1" required><br>
        CIA 2: <input type="number" name="cia2" required><br>
        Model: <input type="number" name="mode" required><br>
        <input type="submit" name="insert_marks" value="Insert Marks">
    </form>

    <form method="post" action="">
        <h2>Delete Student Marks</h2>
        Roll Number: <input type="text" name="roll_num" required><br>
        Semester: <input type="number" name="sem" required><br>
        Subject: <input type="text" name="sub" required><br>
        <input type="submit" name="delete_marks" value="Delete Marks">
    </form>
</body>
</html>
