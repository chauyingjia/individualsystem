<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}
include('dbconnect.php');
include('headeradvisor.php');

$advisor_id = $_SESSION['funame'];

// Get semester list for dropdown
$sem_query = "SELECT DISTINCT c_sem FROM tb_course ORDER BY c_sem DESC";
$sem_result = mysqli_query($con, $sem_query);

// Get the most recent semester
$recent_sem_query = "SELECT DISTINCT c_sem FROM tb_course ORDER BY c_sem DESC LIMIT 1";
$recent_sem_result = mysqli_query($con, $recent_sem_query);
$recent_sem = mysqli_fetch_assoc($recent_sem_result)['c_sem'];

// Get selected semester or default to most recent semester
$selected_sem = isset($_GET['semester']) ? $_GET['semester'] : $recent_sem;

// Get all courses assigned to this advisor for selected semester
$query = "SELECT c.*, 
          COALESCE((SELECT COUNT(*) FROM tb_registration r 
           WHERE r.r_course = c.c_code 
           AND r.r_status = 2), 0) as enrolled_students
          FROM tb_course c 
          WHERE c.c_lec = ? 
          AND c.c_sem = ?
          ORDER BY c.c_code ASC";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ss", $advisor_id, $selected_sem);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="advisor.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h2>My Courses</h2>
        <div style="width: 100px;"></div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Assigned Courses for Semester <?php echo $selected_sem; ?></h5>
                <form method="GET" class="d-flex align-items-center">
                    <label class="me-2 text-white">Select Semester:</label>
                    <select name="semester" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <?php while($sem = mysqli_fetch_assoc($sem_result)): ?>
                            <option value="<?php echo $sem['c_sem']; ?>" 
                                    <?php echo $selected_sem == $sem['c_sem'] ? 'selected' : ''; ?>>
                                <?php echo $sem['c_sem']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Section</th>
                            <th>Credit Hours</th>
                            <th>Enrolled/Max</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['c_code']; ?></td>
                                <td><?php echo $row['c_name']; ?></td>
                                <td><?php echo $row['c_section']; ?></td>
                                <td><?php echo $row['c_credit']; ?></td>
                                <td>
                                    <?php echo $row['enrolled_students']; ?>/<?php echo $row['c_maxStudent']; ?>
                                    <?php if($row['enrolled_students'] >= $row['c_maxStudent']): ?>
                                        <span class="badge bg-danger">Full</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="viewstudents.php?code=<?php echo $row['c_code']; ?>&section=<?php echo $row['c_section']; ?>&semester=<?php echo $selected_sem; ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-users"></i> View Students
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if(mysqli_num_rows($result) == 0): ?>
                            <tr>
                                <td colspan="6" class="text-center">No courses assigned for this semester.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
