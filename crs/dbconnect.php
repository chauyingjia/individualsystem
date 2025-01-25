<?php
//Set DB Parameter
$host = "localhost";
$user = "root";
$password = "";
$database = "db_crs";


//Connect DB
$con = mysqli_connect($host, $user, $password, $database);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

//Connection Check(individual project)



?>