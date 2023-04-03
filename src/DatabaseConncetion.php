<?php 

// Setting credentials to for the database.
$database = "local-notepad-app";
$password = "Kumaresh#143";
$serverName = "localhost";
$userName = "root";

// Create connection
$conn = mysqli_connect($serverName, $userName, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}