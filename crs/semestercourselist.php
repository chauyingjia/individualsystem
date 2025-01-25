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

// Get courses for current semester
$query = "SELECT c.*, 
          (SELECT COUNT(*) FROM tb_registration r 
           WHERE r.r_course = c.c_code 
           AND r.r_status = 2) as enrolled_students
          FROM tb_course c 
          WHERE c.c_sem = ?
          ORDER BY c.c_code ASC";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $current_semester);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="advisor.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h2>Course List for <?php echo $current_semester; ?></h2>
        <div style="width: 100px;"></div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Available Courses</h5>
        </div>
        <div class="card-body">
            <?php if(mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Section</th>
                                <th>Credit Hours</th>
                                <th>Max Students</th>
                                <th>Enrolled</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['c_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['c_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['c_section']); ?></td>
                                    <td><?php echo htmlspecialchars($row['c_credit']); ?></td>
                                    <td><?php echo htmlspecialchars($row['c_maxStudent']); ?></td>
                                    <td><?php echo htmlspecialchars($row['enrolled_students']); ?></td>
                                    <td>
                                        <?php if($row['enrolled_students'] >= $row['c_maxStudent']): ?>
                                            <span class="badge bg-danger">Full</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Available</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No courses found for the current semester.
                </div>
            <?php endif; ?>
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
