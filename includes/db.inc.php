<?php

$hostName = "localhost";       // Replace with your database host name
$dbUser = "root";              // Replace with your database username
$dbPassword = "123456789";     // Replace with your database password
$dbName = "kb_t_db";           // Replace with your database name

// Create connection
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
