<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "db_crs";

// Create connection
$con = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if(!session_id()) {
    session_start();
}

// Check if user is logged in
if(!isset($_SESSION['funame'])) {
    header('Location: login.php');
    exit();
}

?>