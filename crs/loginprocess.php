<?php

session_start();

//Connect to DB
include('dbconnect.php');

// Remove all header redirects temporarily for debugging
$username = $_POST['funame'];
$password = $_POST['fpwd'];

// Add debug output
echo "<pre>";
echo "Debug Information:\n";
echo "Username entered: " . $username . "\n";
echo "Password entered: " . $password . "\n";

// Check database connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Database connection: OK\n";

// Only select necessary fields, excluding u_propic
$sql = "SELECT * FROM tb_user WHERE u_sno='$username' AND u_pwd='$password'";
echo "SQL Query: " . $sql . "\n";

$result = mysqli_query($con, $sql);

if (!$result) {
    echo "Query error: " . mysqli_error($con) . "\n";
    die();
}

echo "Number of rows found: " . mysqli_num_rows($result) . "\n";

if(mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo "User found:\n";
    print_r($row);
    
    $_SESSION['funame'] = $username;
    $_SESSION['u_type'] = $row['u_utype'];
    $_SESSION['logged_in'] = true;
    echo "\nSession variables set:\n";
    print_r($_SESSION);
    
    echo "\nUser type: " . $row['u_utype'] . "\n";
    
    // Redirect based on correct user types:
    // 1 = Academic Advisor
    // 2 = Student
    // 3 = IT Staff (Admin)
    switch($row['u_utype']) {
        case '1':
            header("Location: advisor.php");  // Academic Advisor
            break;
        case '2':
            header("Location: student.php");  // Student
            break;
        case '3':
            header("Location: admin.php");    // IT Staff (Admin)
            break;
    }
    exit();
} else {
    echo "Login failed - no matching user found\n";
    $_SESSION['login_error'] = "Invalid username or password";
    header("Location: login.php");
    exit();
}

echo "</pre>";
mysqli_close($con);

//Confirmation registration successful or fail (individual project)

//Redirect user to login page
// header('Location: login.php');

?>