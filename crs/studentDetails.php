<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}
include('dbconnect.php');
include('headeradvisor.php');

// Get student ID from URL
$student_id = $_GET['id'] ?? '';

// Get the referring course parameters
$return_course = $_GET['course'] ?? '';
$return_section = $_GET['section'] ?? '';

// Get student details with all enrolled courses
$query = "SELECT u.*, 
          GROUP_CONCAT(DISTINCT CONCAT(c.c_code, ' - ', c.c_name, ' (Section ', c.c_section, ')') 
          ORDER BY c.c_code ASC SEPARATOR '<br>') as enrolled_courses
          FROM tb_user u
          LEFT JOIN tb_registration r ON u.u_sno = r.r_student
          LEFT JOIN tb_course c ON r.r_course = c.c_code
          WHERE u.u_sno = ? AND c.c_lec = ?
          GROUP BY u.u_sno";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ss", $student_id, $_SESSION['funame']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if(!$student) {
    $_SESSION['error'] = "Student not found or not enrolled in your courses.";
    header("Location: studentlist.php");
    exit();
}
?>

<div class="container mt-4">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="studentlist.php?course=<?php echo urlencode($return_course . '|' . $return_section); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="row">
        <!-- Student Profile Card -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Student Profile</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="fas fa-user-circle fa-6x text-primary"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h4><?php echo $student['u_name']; ?></h4>
                            <p class="text-muted">Student ID: <?php echo $student['u_sno']; ?></p>
                        </div>
                    </div>

                    <hr>

                    <!-- Personal Information -->
                    <h5 class="mb-3">Personal Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Email:</strong><br> <?php echo $student['u_email']; ?></p>
                            <p><strong>Contact Number:</strong><br> <?php echo $student['u_contact']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>State:</strong><br> <?php echo $student['u_state']; ?></p>
                            <p><strong>Registration Date:</strong><br> 
                                <?php echo date('d M Y', strtotime($student['u_req'])); ?>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <!-- Enrolled Courses -->
                    <h5 class="mb-3">Enrolled Courses</h5>
                    <div class="enrolled-courses">
                        <?php echo $student['enrolled_courses'] ?: 'No courses enrolled.'; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registration Status Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Registration Status</h5>
                </div>
                <div class="card-body">
                    <?php
                    // Get registration status for each course
                    $status_query = "SELECT c.c_code, c.c_name, c.c_section, r.r_status
                                   FROM tb_registration r
                                   JOIN tb_course c ON r.r_course = c.c_code
                                   WHERE r.r_student = ? AND c.c_lec = ?
                                   ORDER BY c.c_code ASC";
                    $status_stmt = mysqli_prepare($con, $status_query);
                    mysqli_stmt_bind_param($status_stmt, "ss", $student_id, $_SESSION['funame']);
                    mysqli_stmt_execute($status_stmt);
                    $status_result = mysqli_stmt_get_result($status_stmt);
                    
                    while($status = mysqli_fetch_assoc($status_result)):
                    ?>
                        <div class="mb-3">
                            <p class="mb-1"><strong><?php echo $status['c_code']; ?></strong></p>
                            <p class="mb-1">Section <?php echo $status['c_section']; ?></p>
                            <span class="badge <?php 
                                echo $status['r_status'] == 'Approved' ? 'bg-success' : 
                                    ($status['r_status'] == 'Rejected' ? 'bg-danger' : 'bg-warning'); 
                                ?>">
                                <?php echo $status['r_status']; ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.enrolled-courses {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    line-height: 2;
}
.fa-6x {
    font-size: 6em;
}
</style>

<?php include 'footer.php'; ?> 