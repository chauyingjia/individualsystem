<?php
session_start();

//Connect to DB
include('dbconnect.php');

//Retrieve data from form
$funame = $_POST['funame'];
$fpwd = $_POST['fpwd'];
$femail = $_POST['femail'];
$fname = $_POST['fname'];
$fcontact = $_POST['fcontact'];
$fstate = $_POST['fstate'];

// First check if user already exists
$check_user = "SELECT * FROM tb_user WHERE u_sno = ?";
$stmt = mysqli_prepare($con, $check_user);
mysqli_stmt_bind_param($stmt, "s", $funame);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) > 0) {
    // User already exists
    $_SESSION['error_message'] = "This ID is already registered. Please check your ID.";
    header("Location: register.php");
    exit();
}

// Determine user type based on ID format
// Assuming:
// Staff IDs start with 'L' for Lecturer (u_type = 1)
// Student IDs are numeric (u_type = 2)
// Admin IDs start with 'IT' (u_type = 3)
if(substr($funame, 0, 1) === 'L') {
    $u_type = '1';  // Advisor
} elseif(substr($funame, 0, 2) === 'IT') {
    $u_type = '3';  // Admin
} else {
    $u_type = '2';  // Student (default)
}

// Password validation function
function validatePassword($password) {
    // Minimum 8 characters
    if (strlen($password) < 8) {
        return false;
    }
    
    // Must contain at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    
    // Must contain at least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    
    // Must contain at least one number
    if (!preg_match('/\d/', $password)) {
        return false;
    }
    
    // Must contain at least one symbol
    if (!preg_match('/[@$!%*?&]/', $password)) {
        return false;
    }
    
    return true;
}

// In your registration processing
if (!validatePassword($fpwd)) {
    $_SESSION['error'] = "Password does not meet requirements";
    header("Location: register.php");
    exit();
}

// If password is valid, hash it and continue with registration
$hashed_password = password_hash($fpwd, PASSWORD_DEFAULT);

// Use prepared statement for insert
$sql = "INSERT INTO tb_user(u_sno, u_name, u_pwd, u_email, u_contact, u_state, u_utype) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "sssssss", 
    $funame,
    $fname,
    $fpwd,
    $femail,
    $fcontact,
    $fstate,
    $u_type
);

if(mysqli_stmt_execute($stmt)) {
    $_SESSION['registration_completed'] = true;
    header("Location: login.php");
    exit();
} else {
    $_SESSION['error_message'] = "Registration failed. Please try again. Error: " . mysqli_error($con);
    header("Location: register.php");
    exit();
}

//Close connection
mysqli_close($con);

?>