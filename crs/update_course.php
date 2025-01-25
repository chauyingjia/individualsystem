<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}

include 'dbconnect.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tid = $_POST['tid'];
    $section = $_POST['section'];
    
    // Update the section using prepared statement
    $query = "UPDATE tb_registration 
              SET r_section = ? 
              WHERE r_tid = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ss", $section, $tid);
    
    if(mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Course section updated successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating course section: " . mysqli_error($con);
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: student.php");
    exit();
}
?>