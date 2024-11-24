<?php
$servername = "localhost"; // Use 'localhost' or your database server address
$username = "root";         // Your database username
$password = "";             // Your database password (none in your case)
$dbname = "shop_project";   // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
