<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}

include 'dbconnect.php';

if(isset($_GET['tid'])) {
    $tid = $_GET['tid'];
    
    // Delete the course registration
    $query = "DELETE FROM tb_registration WHERE r_tid = '$tid'";
    if(mysqli_query($con, $query)) {
        $_SESSION['message'] = "Course successfully deleted.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting course.";
        $_SESSION['message_type'] = "danger";
    }
}

header("Location: student.php");
exit();
?> 