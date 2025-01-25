<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}

include 'dbconnect.php';
include 'headeradmin.php';

// Get both parts of the composite key from URL
$code = $_GET['code'] ?? '';
$section = $_GET['section'] ?? '';

if($code && $section) {
    $query = "SELECT * FROM tb_course WHERE c_code = ? AND c_section = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ss", $code, $section);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $course = mysqli_fetch_assoc($result);
}

// Get semester list from tb_course
$sem_query = "SELECT DISTINCT c_sem FROM tb_course ORDER BY c_sem DESC";
$sem_result = mysqli_query($con, $sem_query);

// If no semesters exist yet, set a default one
if (mysqli_num_rows($sem_result) == 0) {
    $current_year = date('Y');
    $next_year = $current_year + 1;
    $current_month = date('n');
    $semester = ($current_month <= 6) ? "1" : "2";
    $default_sem = "$current_year/$next_year-$semester";
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_name = strtoupper($_POST['course_name']);
    $credit_hours = $_POST['credit_hours'];
    $lecture_group = strtoupper($_POST['lecture_group']);
    $max_students = $_POST['max_students'];
    $semester = $_POST['semester'];
    
    // Modify your update query:
    $update_query = "UPDATE tb_course 
                    SET c_name = ?, 
                        c_credit = ?, 
                        c_lec = ?, 
                        c_maxStudent = ?,
                        c_sem = ?
                    WHERE c_code = ? AND c_section = ?";
                    
    $update_stmt = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($update_stmt, "sisssss", 
                          $course_name, 
                          $credit_hours, 
                          $lecture_group, 
                          $max_students,
                          $semester,
                          $code, 
                          $section);
    
    if(mysqli_stmt_execute($update_stmt)) {
        $_SESSION['message'] = "Course updated successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: admincourselist.php");
        exit();
    } else {
        $_SESSION['message'] = "Error updating course: " . mysqli_error($con);
        $_SESSION['message_type'] = "danger";
    }
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h2>Edit Course</h2>
        <div style="width: 100px;"></div> <!-- This helps center the heading -->
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

    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Course Details</h5>
        </div>
        <div class="card-body">
            <?php if($course): ?>
                <form method="POST" action="">
                    <!-- Add hidden fields for the composite key -->
                    <input type="hidden" name="code" value="<?php echo $code; ?>">
                    <input type="hidden" name="section" value="<?php echo $section; ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Course Code</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($course['c_code']); ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Course Name</label>
                            <input type="text" class="form-control" name="course_name" value="<?php echo htmlspecialchars($course['c_name']); ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Credit Hours</label>
                            <select class="form-select" name="credit_hours" required>
                                <?php for($i = 1; $i <= 4  ; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($course['c_credit'] == $i) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Lecture Group</label>
                            <input type="text" class="form-control" name="lecture_group" 
                                   value="<?php echo htmlspecialchars($course['c_lec']); ?>"
                                   pattern="[A-Za-z]{1,2}[0-9]{3}" required>
                            <small class="text-muted">Format: 1-2 letters followed by 3 numbers (e.g., L002 or IT002)</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Section</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($course['c_section']); ?>" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Maximum Students</label>
                            <input type="number" class="form-control" name="max_students" 
                                   value="<?php echo htmlspecialchars($course['c_maxStudent']); ?>"
                                   min="1" max="100" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="semester" class="form-label">Semester</label>
                            <select class="form-select" id="semester" name="semester" required>
                                <option value="">Select Semester</option>
                                <?php while($sem = mysqli_fetch_assoc($sem_result)): ?>
                                    <option value="<?php echo $sem['c_sem']; ?>" 
                                            <?php echo ($course['c_sem'] == $sem['c_sem']) ? 'selected' : ''; ?>>
                                        <?php echo $sem['c_sem']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary me-md-2">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="admincourselist.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-danger">Course not found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?> 