<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stud";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['insert'])) {
    $roll_num = $_POST['roll_num'];
    $name = $_POST['name'];
    $dept = $_POST['dept'];

    $sql = "INSERT INTO st_data (roll_num, name, dept) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $roll_num, $name, $dept);

    if ($stmt->execute()) {
        echo "<p class='success'>Record inserted successfully</p>";
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['show_combined_data'])) {
    // Fetch and display combined data from st_data and marks
    show_combined_data($conn);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['show_by_roll_sem'])) {
    // Fetch and display data by roll_num and sem
    $roll_num = $_POST['roll_num'];
    $sem = $_POST['sem'];
    show_by_roll_sem($conn, $roll_num, $sem);
}

function show_combined_data($conn) {
    // SQL query to join st_data and marks tables
    $sql = "SELECT st_data.roll_num, st_data.name, st_data.dept, marks.sem, marks.sub, marks.assg1, marks.assg2, marks.assg3, marks.cia1, marks.cia2, marks.model 
            FROM st_data 
            LEFT JOIN marks ON st_data.roll_num = marks.st_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Combined Student Data and Marks</h2>";
        echo "<table>
                <tr>
                    <th>Roll Number</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Semester</th>
                    <th>Subject</th>
                    <th>Assignment 1</th>
                    <th>Assignment 2</th>
                    <th>Assignment 3</th>
                    <th>CIA 1</th>
                    <th>CIA 2</th>
                    <th>Model</th>
                </tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["roll_num"] . "</td>
                    <td>" . $row["name"] . "</td>
                    <td>" . $row["dept"] . "</td>
                    <td>" . $row["sem"] . "</td>
                    <td>" . $row["sub"] . "</td>
                    <td>" . $row["assg1"] . "</td>
                    <td>" . $row["assg2"] . "</td>
                    <td>" . $row["assg3"] . "</td>
                    <td>" . $row["cia1"] . "</td>
                    <td>" . $row["cia2"] . "</td>
                    <td>" . $row["model"] . "</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>No records found.</p>";
    }
}

function show_by_roll_sem($conn, $roll_num, $sem) {
    // SQL query to fetch data based on roll_num and sem
    $sql = "SELECT st_data.roll_num, st_data.name, st_data.dept, marks.sem, marks.sub, marks.assg1, marks.assg2, marks.assg3, marks.cia1, marks.cia2, marks.model 
            FROM st_data 
            LEFT JOIN marks ON st_data.roll_num = marks.st_id
            WHERE st_data.roll_num = ? AND marks.sem = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $roll_num, $sem); // 'i' is for integer type
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2>Student Data and Marks for Roll Number: $roll_num and Semester: $sem</h2>";
        echo "<table>
                <tr>
                    <th>Roll Number</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Semester</th>
                    <th>Subject</th>
                    <th>Assignment 1</th>
                    <th>Assignment 2</th>
                    <th>Assignment 3</th>
                    <th>CIA 1</th>
                    <th>CIA 2</th>
                    <th>Model</th>
                </tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["roll_num"] . "</td>
                    <td>" . $row["name"] . "</td>
                    <td>" . $row["dept"] . "</td>
                    <td>" . $row["sem"] . "</td>
                    <td>" . $row["sub"] . "</td>
                    <td>" . $row["assg1"] . "</td>
                    <td>" . $row["assg2"] . "</td>
                    <td>" . $row["assg3"] . "</td>
                    <td>" . $row["cia1"] . "</td>
                    <td>" . $row["cia2"] . "</td>
                    <td>" . $row["model"] . "</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>No records found for Roll Number: $roll_num and Semester: $sem</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Data Management</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #121212; 
            color: white; 
            margin: 0; 
            padding: 20px; 
        }
        a { 
            display: block; 
            text-align: center; 
            margin: 10px 0; 
            font-size: 18px; 
            text-decoration: none; 
            color: #ff9900; 
        }
        a:hover { 
            color: #ffcc00; 
        }
        form { 
            background-color: #333; 
            padding: 20px; 
            border-radius: 8px; 
            width: 300px; 
            margin: 0 auto;
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
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }
        th { 
            background-color: #ff9900; 
        }
    </style>
</head>
<body>
    <!-- <a href="marks.php">Go to Marks Management</a> -->

    <form method="post" action="">
        <h2>Insert Student Data</h2>
        Roll Number: <input type="text" name="roll_num" required><br>
        Name: <input type="text" name="name" required><br>
        Department: <input type="text" name="dept" required><br>
        <input type="submit" name="insert" value="Insert Data">
    </form>

    <form method="post" action="">
        <h2>Show Combined Data (Student & Marks)</h2>
        <input type="submit" name="show_combined_data" value="Show Combined Data">
    </form>

    <form method="post" action="">
        <h2>Show Data Based on Roll Number and Semester</h2>
        Roll Number: <input type="text" name="roll_num" required><br>
        Semester: <input type="number" name="sem" required><br>
        <input type="submit" name="show_by_roll_sem" value="Show Data">
    </form>
</body>
</html>
