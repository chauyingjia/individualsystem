<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}

include 'dbconnect.php';
include 'headeradmin.php';

// Get semester list from tb_course instead of tb_registration
$sem_query = "SELECT DISTINCT c_sem FROM tb_course ORDER BY c_sem DESC";
$sem_result = mysqli_query($con, $sem_query);

// Get the most recent semester
$recent_sem_query = "SELECT DISTINCT c_sem FROM tb_course ORDER BY c_sem DESC LIMIT 1";
$recent_sem_result = mysqli_query($con, $recent_sem_query);
$recent_sem = mysqli_fetch_assoc($recent_sem_result)['c_sem'];

// If no semesters exist yet, set a default one
if (!$recent_sem) {
    $current_year = date('Y');
    $next_year = $current_year + 1;
    $current_month = date('n');
    $semester = ($current_month <= 6) ? "1" : "2";
    $recent_sem = "$current_year/$next_year-$semester";
}

// Get selected semester or default to most recent
$selected_sem = isset($_GET['semester']) ? $_GET['semester'] : $recent_sem;

// Get all courses for selected semester
$query = "SELECT c.*, 
          (SELECT COUNT(*) FROM tb_registration r 
           WHERE r.r_course = c.c_code 
           AND r.r_status = 2) as enrolled_students
          FROM tb_course c 
          WHERE c.c_sem = ?
          ORDER BY c.c_lec ASC, c.c_code ASC";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $selected_sem);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="admin.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h2>Course List</h2>
        <a href="addcourse.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Course
        </a>
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
        <div class="card-header bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Courses for Semester <?php echo $selected_sem; ?></h5>
                <form method="GET" class="d-flex align-items-center">
                    <label class="text-white me-2">Select Semester:</label>
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
                            <th>No</th>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credit Hours</th>
                            <th>IT Admin</th>
                            <th>Section</th>
                            <th>Max Students</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 1;
                        while($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo $row['c_code']; ?></td>
                            <td><?php echo $row['c_name']; ?></td>
                            <td><?php echo $row['c_credit']; ?></td>
                            <td><?php echo $row['c_lec']; ?></td>
                            <td><?php echo $row['c_section']; ?></td>
                            <td><?php echo $row['c_maxStudent']; ?></td>
                            <td>
                                <a href="editcourse.php?code=<?php echo $row['c_code']; ?>&section=<?php echo $row['c_section']; ?>" 
                                   class="btn btn-sm btn-primary me-2">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="confirmDelete('<?php echo $row['c_code']; ?>', '<?php echo $row['c_section']; ?>')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this course?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="deleteLink" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(code, section) {
    var deleteLink = document.getElementById('deleteLink');
    deleteLink.href = 'deletecourse.php?code=' + code + '&section=' + section;
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>

<?php include 'footer.php'; ?>
