<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}

include 'dbconnect.php';

if(isset($_GET['tid']) && isset($_GET['status'])) {
    $tid = $_GET['tid'];
    $status = $_GET['status'];
    $mode = $_GET['mode'] ?? 'manual';
    
    // Add debug logging
    error_log("Updating registration ID: $tid to status: $status (Mode: $mode)");
    
    // Get course details and current enrollment
    $check_query = "SELECT r.r_course, c.c_maxStudent, 
                          (SELECT COUNT(*) FROM tb_registration 
                           WHERE r_course = r.r_course AND r_status = 'Approved') as enrolled
                   FROM tb_registration r
                   JOIN tb_course c ON r.r_course = c.c_code
                   WHERE r.r_tid = ?";
    
    $stmt = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($stmt, "i", $tid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $course_info = mysqli_fetch_assoc($result);
    
    $can_approve = true;
    if($status == 'Approved' && $mode == 'auto') {
        if($course_info['enrolled'] >= $course_info['c_maxStudent']) {
            $can_approve = false;
            $_SESSION['message'] = "Auto-approval failed: Course is full";
            $_SESSION['message_type'] = "warning";
            error_log("Auto-approval failed: Course is full");
        }
    }
    
    if($can_approve) {
        // Update registration status
        $update_query = "UPDATE tb_registration SET r_status = ? WHERE r_tid = ?";
        $update_stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($update_stmt, "si", $status, $tid);
        
        if(mysqli_stmt_execute($update_stmt)) {
            // Create notification
            $notification_message = "";
            $reason = isset($_POST['reject_reason']) ? $_POST['reject_reason'] : null;
            
            if($status == 'Approved') {
                $notification_message = "Your course registration for {$course_info['r_course']} has been approved.";
            } elseif($status == 'Rejected') {
                $notification_message = "Your course registration for {$course_info['r_course']} has been rejected.";
            }
            
            if($notification_message) {
                $notify_query = "INSERT INTO tb_notifications (n_user, n_message, n_type, n_reason) 
                                VALUES (?, ?, ?, ?)";
                $notify_stmt = mysqli_prepare($con, $notify_query);
                mysqli_stmt_bind_param($notify_stmt, "ssss", 
                    $student_id, 
                    $notification_message, 
                    $status,
                    $reason
                );
                mysqli_stmt_execute($notify_stmt);
            }
            $_SESSION['message'] = "Registration has been " . $status;
            $_SESSION['message_type'] = "success";
            error_log("Successfully updated status to: $status");
        } else {
            $_SESSION['message'] = "Error updating registration status: " . mysqli_error($con);
            $_SESSION['message_type'] = "danger";
            error_log("Error updating status: " . mysqli_error($con));
        }
    }
}

// Add this before redirecting to see if there are any messages
if(isset($_SESSION['message'])) {
    error_log("Session message: " . $_SESSION['message']);
}

header("Location: courseapproval.php");
exit();
?>