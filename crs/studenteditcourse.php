<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}

include 'headerstudent.php';
include 'dbconnect.php';

if(isset($_GET['tid'])) {
    $tid = $_GET['tid'];
    
    // Get course details
    $query = "SELECT r.*, c.c_name, c.c_credit, s.s_desc 
              FROM tb_registration r
              LEFT JOIN tb_course c ON r.r_course = c.c_code
              LEFT JOIN tb_status s ON r.r_status = s.s_id
              WHERE r.r_tid = '$tid'";
    $result = mysqli_query($con, $query);
    $course = mysqli_fetch_assoc($result);
    
    // Get available sections for this course
    $section_query = "SELECT c_section FROM tb_course WHERE c_code = '" . $course['r_course'] . "'";
    $section_result = mysqli_query($con, $section_query);
    $sections = array();
    while($section_row = mysqli_fetch_assoc($section_result)) {
        $sections[] = $section_row['c_section'];
    }
}
?>

<div class="container mt-5">
    
    <h2>Modify Registration</h2>
    
    <ul class="nav nav-tabs mt-5">
        <li class="nav-item">
            <a class="nav-link active">Edit</a>
        </li>
    </ul>

    <div class="card">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">StudentID: <?php echo $course['r_student']; ?> - Semester: <?php echo $course['r_sem']; ?></h6>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <form method="POST" action="update_course.php">
                    <input type="hidden" name="tid" value="<?php echo $tid; ?>">
                    <table class="table table-bordered table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Transaction ID</th>
                                <th>Semester</th>
                                <th>Course</th>
                                <th>Section</th>
                                <th>Credit</th>
                                <th>Course Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $course['r_tid']; ?></td>
                                <td><?php echo $course['r_sem']; ?></td>
                                <td><?php echo $course['r_course']; ?></td>
                                <td>
                                    <select name="section" class="form-select form-select-sm">
                                        <?php
                                        foreach($sections as $section) {
                                            $selected = ($section == $course['r_section']) ? 'selected' : '';
                                            echo "<option value='$section' $selected>$section</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td><?php echo $course['c_credit']; ?></td>
                                <td><?php echo $course['c_name']; ?></td>
                                <td><?php echo $course['s_desc']; ?></td>
                                <td class="text-center">
                                    <button type="submit" class="btn btn-sm btn-success me-2">
                                        <i class="fas fa-save"></i> Save
                                    </button>
                                    <a href="student.php" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?> 