<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}

include 'dbconnect.php';

if(isset($_GET['code']) && isset($_GET['section'])) {
    $code = $_GET['code'];
    $section = $_GET['section'];
    
    // Check if course is being used in registrations
    $check_query = "SELECT r.*, c.c_sem 
                   FROM tb_registration r
                   JOIN tb_course c ON r.r_course = c.c_code 
                   WHERE r.r_course = ? AND r.r_section = ?";
    $check_stmt = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($check_stmt, "ss", $code, $section);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if(mysqli_num_rows($check_result) > 0) {
        $_SESSION['message'] = "Cannot delete course: It is currently registered by students.";
        $_SESSION['message_type'] = "danger";
    } else {
        // Delete the course
        $delete_query = "DELETE FROM tb_course WHERE c_code = ? AND c_section = ?";
        $delete_stmt = mysqli_prepare($con, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "ss", $code, $section);
        
        if(mysqli_stmt_execute($delete_stmt)) {
            $_SESSION['message'] = "Course deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting course: " . mysqli_error($con);
            $_SESSION['message_type'] = "danger";
        }
    }
}

header("Location: admincourselist.php");
exit();
?> 