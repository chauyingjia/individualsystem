<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}

//Connect to DB
include('dbconnect.php');

// Debug logging
error_log("Session funame: " . print_r($_SESSION['funame'], true));
error_log("POST data: " . print_r($_POST, true));

// If it's just checking registered courses, redirect directly
if(isset($_POST['check_registered'])) {
    header("Location: student.php");
    exit();
}

// Only proceed with registration if courses were selected
if (isset($_POST['selected_courses']) && !empty($_POST['selected_courses'])) {
    $student_id = $_SESSION['funame'];
    $selected_courses = $_POST['selected_courses'];
    
    $success_count = 0;
    $duplicate_courses = [];
    
    try {
        // Begin transaction
        mysqli_begin_transaction($con);
        
        foreach($selected_courses as $course_code) {
            if (empty($course_code)) continue;
            
            // Get the section from the course table
            $section_query = "SELECT c_section FROM tb_course WHERE c_code = ?";
            $section_stmt = mysqli_prepare($con, $section_query);
            mysqli_stmt_bind_param($section_stmt, "s", $course_code);
            mysqli_stmt_execute($section_stmt);
            $section_result = mysqli_stmt_get_result($section_stmt);
            $section_data = mysqli_fetch_assoc($section_result);
            $section = $section_data['c_section'];
            
            // Check if already registered
            $check_query = "SELECT * FROM tb_registration 
                          WHERE r_student = ? 
                          AND r_course = ? 
                          AND r_section = ?";
            
            $check_stmt = mysqli_prepare($con, $check_query);
            mysqli_stmt_bind_param($check_stmt, "sss", $student_id, $course_code, $section);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);
            
            if(mysqli_num_rows($check_result) == 0) {
                // Insert new registration
                $insert_query = "INSERT INTO tb_registration 
                               (r_student, r_course, r_status, r_section) 
                               VALUES (?, ?, 'Pending', ?)";
                               
                $insert_stmt = mysqli_prepare($con, $insert_query);
                mysqli_stmt_bind_param($insert_stmt, "sss", 
                    $student_id,
                    $course_code,
                    $section
                );
                
                if (mysqli_stmt_execute($insert_stmt)) {
                    $success_count++;
                } else {
                    throw new Exception("Failed to insert registration: " . mysqli_error($con));
                }
            } else {
                // Course already registered
                $duplicate_courses[] = $course_code;
            }
        }
        
        // Commit transaction
        mysqli_commit($con);
        
        // Prepare success/warning messages
        $messages = [];
        if ($success_count > 0) {
            $messages[] = "$success_count course(s) registered successfully!";
        }
        if (!empty($duplicate_courses)) {
            $messages[] = "Course(s) " . implode(", ", $duplicate_courses) . " already registered!";
        }
        
        // Set session messages
        if (!empty($messages)) {
            $_SESSION['message'] = implode("<br>", $messages);
            $_SESSION['message_type'] = ($success_count > 0) ? "success" : "warning";
        }
        
    } catch (Exception $e) {
        // Something went wrong, rollback the transaction
        mysqli_rollback($con);
        $_SESSION['message'] = "Error registering courses: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}

// Close the database connection
mysqli_close($con);

// Redirect back to student page
header("Location: studentCourseOffered.php");
exit();
?> 