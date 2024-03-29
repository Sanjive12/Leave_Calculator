<?php
// Database connection configuration
$servername = "localhost"; // Server name
$username = "root"; // User name
$password = "root"; // User password
$dbname = "leave_management"; //Db name

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the staff data from the database
$staffs = [];
$sql = "SELECT name, leave_balance FROM staff";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $staffs[$row['name']] = $row['leave_balance'];
    }
}

// Get the holidays data from the database
$holidays = [];
$sql = "SELECT date FROM holidays";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $holidays[] = $row['date'];
    }
}

// Close the database connection
$conn->close();
?>
