<?php
require_once('checksession.php');
checkUserAccess("2"); // Check for student access

include("headerstudent.php");

include 'dbconnect.php';

// Retrieve student information
$uic = $_SESSION['funame'];

// Get semester list
$sem_query = "SELECT DISTINCT c_sem FROM tb_course ORDER BY c_sem DESC";
$sem_result = mysqli_query($con, $sem_query);

// Get the most recent semester
$recent_sem_query = "SELECT DISTINCT c_sem FROM tb_course ORDER BY c_sem DESC LIMIT 1";
$recent_sem_result = mysqli_query($con, $recent_sem_query);
$recent_sem = mysqli_fetch_assoc($recent_sem_result)['c_sem'];

// Get selected semester or default to most recent
$selected_sem = isset($_GET['semester']) ? $_GET['semester'] : $recent_sem;

// Updated query to get courses for selected semester
$query = "SELECT r.*, c.*, 
          (SELECT COUNT(*) FROM tb_registration r2 
           WHERE r2.r_course = r.r_course 
           AND r2.r_section = r.r_section 
           AND r2.r_status = 2) as total_registered
          FROM tb_registration r
          JOIN tb_course c ON r.r_course = c.c_code
          WHERE r.r_student = ? 
          AND c.c_sem = ?";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "si", $uic, $selected_sem);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Calculate total credits
$total_credit = 0;

// Add Font Awesome CSS in the header or after Bootstrap CSS
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">';
?>

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
            // If user hits back button, log them out
            window.location.href = 'logout.php';
        };
    }
}
</script>

<body onload="noBack();" onpageshow="if (event.persisted) noBack();" onunload="">
<div class="container mt-5">
    <!-- Add this message display section -->
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

    <h2>Course Registration</h2>
    
    <ul class="nav nav-tabs mt-5">
        <li class="nav-item">
            <a class="nav-link active">Registered List</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="studentCourseOffered.php">Add Course</a>
        </li>
    </ul>

    <div class="card">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    StudentID:<?php echo $uic; ?> - 
                    <form method="GET" class="d-inline">
                        <select name="semester" onchange="this.form.submit()" class="form-select form-select-sm d-inline" style="width: auto;">
                            <?php
                            while($sem_row = mysqli_fetch_assoc($sem_result)) {
                                $sem_value = $sem_row['c_sem'];
                                $semesters[$sem_value] = "Semester " . $sem_value;
                            }
                            foreach($semesters as $sem_value => $sem_name) {
                                $selected = ($selected_sem == $sem_value) ? 'selected' : '';
                                echo "<option value='$sem_value' $selected>$sem_name</option>";
                            }
                            ?>
                        </select>
                    </form>
                </h6>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Transaction ID</th>
                            <th>Semester</th>
                            <th>Course</th>
                            <th>Section</th>
                            <th>Credit</th>
                            <th>Course Name</th>
                            <th>Availability</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 1;
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $count . "</td>";
                            echo "<td>" . $row['r_tid'] . "</td>";
                            echo "<td>" . $row['c_sem'] . "</td>";
                            echo "<td>" . $row['r_course'] . "</td>";
                            echo "<td>" . $row['r_section'] . "</td>";
                            echo "<td>" . $row['c_credit'] . "</td>";
                            echo "<td>" . $row['c_name'] . "</td>";
                            echo "<td>" . $row['total_registered'] . "</td>";
                            echo "<td>" . $row['r_status'] . "</td>";
                            echo "<td class='text-center'>
                                    <a href='studenteditcourse.php?tid=" . $row['r_tid'] . "' class='btn btn-sm btn-primary me-2'>
                                        <i class='fas fa-edit'></i>
                                    </a>
                                    <a href='studentdeletecourse.php?tid=" . $row['r_tid'] . "' class='btn btn-sm btn-danger'>
                                        <i class='fas fa-trash'></i>
                                    </a>
                                  </td>";
                            echo "</tr>";
                            
                            $total_credit += $row['c_credit'];
                            $count++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="row mt-3">
                <div class="col">
                    <div class="d-flex justify-content-end">
                        <div>
                            <p class="mb-1">Total credit register: <?php echo $total_credit; ?></p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add this JavaScript for delete confirmation -->
<script>
function confirmDelete(tid) {
    if (confirm('Are you sure you want to delete this course?')) {
        window.location.href = 'delete_course.php?tid=' + tid;
    }
}
</script>

<?php include 'footer.php'; ?>
</body>
