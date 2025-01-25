<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}

// Include database connection first
include 'dbconnect.php';

// After your database connection, modify the semester query to use tb_course
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

// Process form submission before any HTML output
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_code = strtoupper($_POST['course_code']);
    $course_name = strtoupper($_POST['course_name']);
    $credit_hours = $_POST['credit_hours'];
    $lecture_group = strtoupper($_POST['lecture_group']);
    $section = $_POST['section'];
    $max_students = $_POST['max_students'];
    $semester = $_POST['semester'];
    
    // Check if course already exists
    $check_query = "SELECT * FROM tb_course WHERE c_code = ? AND c_section = ?";
    $check_stmt = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($check_stmt, "ss", $course_code, $section);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    
    if(mysqli_num_rows($result) > 0) {
        $_SESSION['message'] = "Course with this code and section already exists!";
        $_SESSION['message_type'] = "danger";
        header("Location: addcourse.php");
        exit();
    } else {
        // Insert new course
        $insert_query = "INSERT INTO tb_course (c_code, c_name, c_credit, c_lec, c_section, c_maxStudent, c_sem) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($con, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "ssissis", 
            $course_code, $course_name, $credit_hours, 
            $lecture_group, $section, $max_students, $semester);
        
        if(mysqli_stmt_execute($insert_stmt)) {
            $_SESSION['message'] = "Course added successfully!";
            $_SESSION['message_type'] = "success";
            header("Location: admincourselist.php");
            exit();
        } else {
            $_SESSION['message'] = "Error adding course: " . mysqli_error($con);
            $_SESSION['message_type'] = "danger";
            header("Location: addcourse.php");
            exit();
        }
    }
}

// Include header after processing
include 'headeradmin.php';
?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h2>Add New Course</h2>
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
            <form action="" method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="course_code" class="form-label">Course Code</label>
                        <input type="text" class="form-control" id="course_code" name="course_code" required 
                               placeholder="e.g., SECJ1234" pattern="[A-Za-z]{4}[0-9]{4}">
                        <small class="text-muted">Format: 4 letters followed by 4 numbers (e.g., SECJ1234)</small>
                    </div>
                    <div class="col-md-6">
                        <label for="course_name" class="form-label">Course Name</label>
                        <input type="text" class="form-control" id="course_name" name="course_name" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="credit_hours" class="form-label">Credit Hours</label>
                        <select class="form-select" id="credit_hours" name="credit_hours" required>
                            <option value="">Select Credit Hours</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="lecture_group" class="form-label">Lecturers</label>
                        <input type="text" class="form-control" id="lecture_group" name="lecture_group" required 
                               pattern="L[0-9]{3}" placeholder="e.g., L001">
                        <small class="text-muted">Format: L followed by 3 numbers (e.g., L001)</small>
                    </div>
                    <div class="col-md-3">
                        <label for="section" class="form-label">Section</label>
                        <input type="text" class="form-control" id="section" name="section" required 
                               pattern="[0-9]{2}" placeholder="e.g., 01">
                        <small class="text-muted">Format: 2 digits (e.g., 01, 02)</small>
                    </div>
                    <div class="col-md-3">
                        <label for="max_students" class="form-label">Maximum Students</label>
                        <input type="number" class="form-control" id="max_students" name="max_students" 
                               value="30" min="1" max="30" required>
                        <small class="text-muted">Maximum: 100 students</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="semester" class="form-label">Semester</label>
                        <select class="form-select" id="semester" name="semester" required>
                            <option value="">Select Semester</option>
                            <?php while($row = mysqli_fetch_assoc($sem_result)): ?>
                                <option value="<?php echo $row['c_sem']; ?>"><?php echo $row['c_sem']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary me-md-2">
                        <i class="fas fa-plus"></i> Add Course
                    </button>
                    <a href="admincourselist.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?> 