<?php
session_start();

// Simple session check
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['u_type'] != "1") {
    header("Location: login.php");
    exit();
}

include("dbconnect.php");
include("headeradvisor.php");

// Get most recent semester from tb_course
$semester_query = "SELECT c_sem FROM tb_course ORDER BY c_sem DESC LIMIT 1";
$semester_result = mysqli_query($con, $semester_query);
$current_semester = mysqli_fetch_assoc($semester_result)['c_sem'];

// Get total number of courses
$course_query = "SELECT COUNT(*) as total FROM tb_course WHERE c_sem = '$current_semester'";
$course_result = mysqli_query($con, $course_query);
$total_courses = mysqli_fetch_assoc($course_result)['total'];

// Get total number of students
$student_query = "SELECT COUNT(DISTINCT r_student) as total FROM tb_registration r 
                 JOIN tb_course c ON r.r_course = c.c_code 
                 WHERE c.c_sem = '$current_semester'";
$student_result = mysqli_query($con, $student_query);
$total_students = mysqli_fetch_assoc($student_result)['total'];

// Get pending registrations
$pending_query = "SELECT COUNT(*) as total FROM tb_registration r 
                 JOIN tb_course c ON r.r_course = c.c_code 
                 WHERE c.c_sem = '$current_semester' AND r.r_status = 1";
$pending_result = mysqli_query($con, $pending_query);
$pending_registrations = mysqli_fetch_assoc($pending_result)['total'];
?>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Advisor Dashboard</h2>

    <!-- Statistics Cards -->
    <div class="row mb-4 justify-content-center align-items-center">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Courses<br><?php echo $current_semester; ?></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_courses; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Students<br><?php echo $current_semester; ?></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_students; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Registrations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pending_registrations; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4 justify-content-center">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Course Management</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="advisorcourselist.php" class="btn btn-outline-primary mb-2">
                            <i class="fas fa-calendar-alt me-2"></i>View My Courses
                        </a>
                        <a href="semestercourselist.php" class="btn btn-outline-info mb-2">
                            <i class="fas fa-list me-2"></i>View Course List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Student Management</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="studentlist.php" class="btn btn-outline-success mb-2">
                            <i class="fas fa-users me-2"></i>View Student List
                        </a>
                        <!-- <a href="studentdetails.php" class="btn btn-outline-secondary mb-2">
                            <i class="fas fa-user-graduate me-2"></i>Student Details
                        </a> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
// Prevent back button
window.history.forward();
function noBack() {
    window.history.forward();
}

// Additional security for back button
window.onload = function() {
    if(typeof history.pushState === "function") {
        history.pushState("jibberish", null, null);
        window.onpopstate = function() {
            history.pushState('newjibberish', null, null);
            window.location.href = 'logout.php';
        };
    }
}
</script>
