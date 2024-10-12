<?php
$serverName = "127.0.0.1";  // IP address of the MySQL server
$username = "root";         // Username
$password = "";     // Password
$database = "reservation";  // Database Name

// Create connection
$conn = new mysqli($serverName, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the connection charset to UTF-8 for support of Thai language
$conn->set_charset("utf8");
?>
