<?php 
include('crssession.php');

if(!session_id())
{
  session_start();
}

include 'headeradmin.php';
include 'dbconnect.php';

// Auto-approve eligible registrations
$auto_approve_query = "SELECT r.r_tid, r.r_course, c.c_maxStudent,
                             (SELECT COUNT(*) FROM tb_registration 
                              WHERE r_course = r.r_course 
                              AND r_status = 'Approved') as enrolled
                      FROM tb_registration r
                      JOIN tb_course c ON r.r_course = c.c_code
                      WHERE r.r_status = 'Pending'";

$auto_result = mysqli_query($con, $auto_approve_query);

while($registration = mysqli_fetch_assoc($auto_result)) {
    // Check if course has space
    if($registration['enrolled'] < $registration['c_maxStudent']) {
        // Auto-approve the registration
        $update_query = "UPDATE tb_registration 
                        SET r_status = 'Approved' 
                        WHERE r_tid = ?";
        $stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt, "i", $registration['r_tid']);
        mysqli_stmt_execute($stmt);
        
        // Log the auto-approval
        error_log("Auto-approved registration ID: " . $registration['r_tid']);
    }
}

// Get all registrations with student and course details
$query = "SELECT r.r_tid, r.r_student, r.r_sem, r.r_course, r.r_section, r.r_status,
                 c.c_name, c.c_credit, c.c_code,
                 u.u_sno
          FROM tb_registration r
          LEFT JOIN tb_course c ON r.r_course = c.c_code
          LEFT JOIN tb_user u ON r.r_student = u.u_sno
          ORDER BY r.r_student, r.r_sem DESC";
$result = mysqli_query($con, $query);

// Get available semesters for filtering
$semester_query = "SELECT DISTINCT r_sem FROM tb_registration ORDER BY r_sem DESC";
$semester_result = mysqli_query($con, $semester_query);
$semesters = array();
while($sem_row = mysqli_fetch_assoc($semester_result)) {
    $semesters[] = $sem_row['r_sem'];
}

// Get selected semester (default to latest)
$selected_sem = isset($_GET['semester']) ? $_GET['semester'] : (empty($semesters) ? '' : $semesters[0]);

// Get current enrollment count for each course
$enrollment_query = "SELECT r_course, COUNT(*) as enrolled_count 
                    FROM tb_registration 
                    WHERE r_status = 2 
                    GROUP BY r_course";
$enrollment_result = mysqli_query($con, $enrollment_query);
$course_enrollments = array();
while($enroll = mysqli_fetch_assoc($enrollment_result)) {
    $course_enrollments[$enroll['r_course']] = $enroll['enrolled_count'];
}

// Get max students for each course
$max_students_query = "SELECT c_code, c_maxStudent FROM tb_course";
$max_students_result = mysqli_query($con, $max_students_query);
$course_max_students = array();
while($max = mysqli_fetch_assoc($max_students_result)) {
    $course_max_students[$max['c_code']] = $max['c_maxStudent'];
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="admin.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h2>Course Registration Approval</h2>
        <div style="width: 100px;"></div>
    </div>

    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
            <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <form method="GET" class="d-flex align-items-center">
                    <label class="me-2">Filter by Semester:</label>
                    <select name="semester" onchange="this.form.submit()" class="form-select form-select-sm" style="width: auto;">
                        <?php foreach($semesters as $sem): ?>
                            <option value="<?php echo $sem; ?>" <?php echo ($selected_sem == $sem) ? 'selected' : ''; ?>>
                                Semester <?php echo $sem; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Semester</th>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Section</th>
                            <th>Credit</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 1;
                        while($row = mysqli_fetch_assoc($result)): 
                            if($selected_sem && $row['r_sem'] != $selected_sem) continue;
                        ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><?php echo $row['r_student']; ?></td>
                                <td><?php echo $row['u_sno']; ?></td>
                                <td><?php echo $row['r_sem']; ?></td>
                                <td><?php echo $row['r_course']; ?></td>
                                <td><?php echo $row['c_name']; ?></td>
                                <td><?php echo $row['r_section']; ?></td>
                                <td><?php echo $row['c_credit']; ?></td>
                                <td>
                                    <?php 
                                    $current_enrollment = isset($course_enrollments[$row['r_course']]) ? $course_enrollments[$row['r_course']] : 0;
                                    $max_students = isset($course_max_students[$row['r_course']]) ? $course_max_students[$row['r_course']] : 0;
                                    $is_full = $current_enrollment >= $max_students;
                                    ?>
                                    <select class="form-select form-select-sm status-select" 
                                            onchange="updateStatus(<?php echo $row['r_tid']; ?>, this.value)">
                                        <option value="Pending" <?php echo ($row['r_status'] == 'Pending') ? 'selected' : ''; ?>>
                                            Pending
                                        </option>
                                        <option value="Approved" <?php echo ($row['r_status'] == 'Approved') ? 'selected' : ''; ?>>
                                            Approved
                                        </option>
                                        <option value="Rejected" <?php echo ($row['r_status'] == 'Rejected') ? 'selected' : ''; ?>>
                                            Rejected
                                        </option>
                                    </select>
                                    <small class="text-muted d-block">
                                        Enrolled: <?php echo $current_enrollment; ?>/<?php echo $max_students; ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <?php if($row['r_status'] == 'Pending'): ?>
                                        <?php if(!$is_full): ?>
                                            <button onclick="autoApprove(<?php echo $row['r_tid']; ?>)" 
                                                    class="btn btn-sm btn-success me-1">
                                                <i class="fas fa-magic"></i> Auto-Approve
                                            </button>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Course Full</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge <?php echo ($row['r_status'] == 'Approved') ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo ($row['r_status'] == 'Approved') ? 'Approved' : 'Rejected'; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rejection Reason</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="rejectForm" action="courseregistrationstatus.php" method="POST">
                    <input type="hidden" name="tid" id="reject_tid">
                    <input type="hidden" name="status" value="Rejected">
                    <div class="mb-3">
                        <label class="form-label">Please state the reason for rejection:</label>
                        <textarea class="form-control" name="reject_reason" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function approveRegistration(tid) {
    if(confirm('Are you sure you want to approve this registration?')) {
        window.location.href = 'courseregistrationstatus.php?tid=' + tid + '&status=2';
    }
}

function rejectRegistration(tid) {
    if(confirm('Are you sure you want to reject this registration?')) {
        window.location.href = 'courseregistrationstatus.php?tid=' + tid + '&status=3';
    }
}

function resetStatus(tid) {
    if(confirm('Are you sure you want to reset this registration status to pending?')) {
        window.location.href = 'courseregistrationstatus.php?tid=' + tid + '&status=1';
    }
}

function updateStatus(tid, status) {
    if(confirm('Are you sure you want to update this registration status?')) {
        window.location.href = 'courseregistrationstatus.php?tid=' + tid + '&status=' + status + '&mode=manual';
    }
}

function autoApprove(tid) {
    if(confirm('Are you sure you want to auto-approve this registration?')) {
        window.location.href = 'courseregistrationstatus.php?tid=' + tid + '&status=2&mode=auto';
    }
}
</script>

<?php include 'footer.php';?>
