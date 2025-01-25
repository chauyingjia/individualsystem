<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}
include('dbconnect.php');
include('headeradvisor.php');

$advisor_id = $_SESSION['funame'];

// Get all courses taught by this lecturer
$courses_query = "SELECT DISTINCT c_code, c_name, c_section 
                 FROM tb_course 
                 WHERE c_lec = ? 
                 ORDER BY c_code ASC, c_section ASC";
$stmt = mysqli_prepare($con, $courses_query);
mysqli_stmt_bind_param($stmt, "s", $advisor_id);
mysqli_stmt_execute($stmt);
$courses_result = mysqli_stmt_get_result($stmt);

// Get selected course from URL parameters
list($selected_course, $selected_section) = explode('|', $_GET['course'] ?? '|');
?>

<div class="container mt-4">
    <h2 class="mb-4">My Students List</h2>

    <!-- Course Selection Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Select Course and Section:</label>
                    <select name="course" class="form-select" required>
                        <option value="ALL|ALL" <?php echo ($selected_course == 'ALL') ? 'selected' : ''; ?>>All Students</option>
                        <?php while($course = mysqli_fetch_assoc($courses_result)): ?>
                            <?php 
                            $value = $course['c_code'] . '|' . $course['c_section'];
                            $selected = ($selected_course == $course['c_code'] && $selected_section == $course['c_section']) ? 'selected' : '';
                            ?>
                            <option value="<?php echo $value; ?>" <?php echo $selected; ?>>
                                <?php echo $course['c_code'] . ' - ' . $course['c_name'] . ' (Section ' . $course['c_section'] . ')'; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">View Students</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    if($selected_course):
        // Modify query based on selection
        if($selected_course == 'ALL') {
            $students_query = "SELECT DISTINCT u.u_sno, u.u_name, u.u_email, 
                             GROUP_CONCAT(DISTINCT CONCAT(c.c_code, ' (', c.c_section, ')') ORDER BY c.c_code SEPARATOR ', ') as courses,
                             GROUP_CONCAT(DISTINCT r.r_status ORDER BY c.c_code SEPARATOR ', ') as statuses
                             FROM tb_user u 
                             JOIN tb_registration r ON u.u_sno = r.r_student
                             JOIN tb_course c ON r.r_course = c.c_code
                             WHERE c.c_lec = ?
                             GROUP BY u.u_sno, u.u_name, u.u_email
                             ORDER BY u.u_sno ASC";
            $stmt = mysqli_prepare($con, $students_query);
            mysqli_stmt_bind_param($stmt, "s", $advisor_id);
        } else {
            $students_query = "SELECT DISTINCT u.u_sno, u.u_name, u.u_email, r.r_status,
                             CONCAT(c.c_code, ' (', c.c_section, ')') as courses
                             FROM tb_user u 
                             JOIN tb_registration r ON u.u_sno = r.r_student
                             JOIN tb_course c ON r.r_course = c.c_code
                             WHERE c.c_lec = ? AND c.c_code = ? AND c.c_section = ?
                             ORDER BY u.u_sno ASC";
            $stmt = mysqli_prepare($con, $students_query);
            mysqli_stmt_bind_param($stmt, "sss", $advisor_id, $selected_course, $selected_section);
        }
        mysqli_stmt_execute($stmt);
        $students_result = mysqli_stmt_get_result($stmt);
    ?>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <?php echo $selected_course == 'ALL' ? 'All Students' : "Students in $selected_course (Section $selected_section)"; ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th><?php echo $selected_course == 'ALL' ? 'Courses' : 'Status'; ?></th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($students_result) > 0): ?>
                                <?php while($student = mysqli_fetch_assoc($students_result)): ?>
                                    <tr>
                                        <td><?php echo $student['u_sno']; ?></td>
                                        <td><?php echo $student['u_name']; ?></td>
                                        <td><?php echo $student['u_email']; ?></td>
                                        <td>
                                            <?php if($selected_course == 'ALL'): ?>
                                                <?php echo $student['courses']; ?>
                                            <?php else: ?>
                                                <span class="badge <?php 
                                                    echo $student['r_status'] == 'Approved' ? 'bg-success' : 
                                                        ($student['r_status'] == 'Rejected' ? 'bg-danger' : 'bg-warning'); 
                                                    ?>">
                                                    <?php echo $student['r_status']; ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="studentDetails.php?id=<?php echo $student['u_sno']; ?>&course=<?php echo urlencode($selected_course); ?>&section=<?php echo urlencode($selected_section); ?>" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-user"></i> View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No students found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>