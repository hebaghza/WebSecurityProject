<?php
// Database configuration
$dbHost     = "localhost";
$dbport = "3306"; // Change this to your database host
$dbUsername = "Heba"; // Change this to your database username
$dbPassword = "123456"; // Change this to your database password
$dbName     = "bankdb"; // Change this to your database name

// Create database connection
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $dbport);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
