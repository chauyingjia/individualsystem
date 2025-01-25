<?php 
include('crssession.php');

if(!session_id())
{
  session_start();
}

//Connect to DB
include('dbconnect.php');

//Retrieve data from form
$uic = $_SESSION['funame'];
$fcourse = $_POST['fcourse'];
$fsem = $_POST['fsem'];

//SQL Insert operation
$sql = "INSERT INTO tb_registration(r_student, r_course, r_sem, r_status)
		VALUES ('$uic','$fcourse','$fsem','Pending')";

//Execute SQl
mysqli_query($con, $sql);

//Close connection
mysqli_close($con);

//Confirmation registration successful or fail (individual project)

//Redirect user to login page
header('Location: courseview.php');

?>