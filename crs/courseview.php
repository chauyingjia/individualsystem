<?php 
include('crssession.php');

if(!session_id())
{
  session_start();
}

include 'headerstudentviewcourse.php';
include 'dbconnect.php';

$uic = $_SESSION['funame'];

// Get all distinct semesters for this student
$semester_query = "SELECT DISTINCT r_sem 
                  FROM tb_registration 
                  WHERE r_student = '$uic' 
                  ORDER BY r_sem DESC";
$semester_result = mysqli_query($con, $semester_query);

?>

<div class="container mt-5">
    <?php
    while($sem = mysqli_fetch_assoc($semester_result)) {
        // Query for courses in this semester
        $sql = "SELECT r.*, c.c_name, c.c_credit, s.s_desc 
                FROM tb_registration r
                LEFT JOIN tb_course c ON r.r_course = c.c_code
                LEFT JOIN tb_status s ON r.r_status = s.s_id
                WHERE r.r_student = '$uic' 
                AND r.r_sem = '" . $sem['r_sem'] . "'";
        
        $result = mysqli_query($con, $sql);
        
        // Display semester header
        echo "<h5 class='mb-3'>Semester: " . $sem['r_sem'] . "</h5>";
    ?>
        <table class="table table-striped table-hover mb-5">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Course Name</th>
                    <th>Section</th>
                    <th>Credit</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $semester_credit = 0; // Initialize credit counter for this semester
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['r_course'] . "</td>";
                    echo "<td>" . $row['c_name'] . "</td>";
                    echo "<td>" . $row['r_section'] . "</td>";
                    echo "<td>" . $row['c_credit'] . "</td>";
                    echo "<td>" . $row['s_desc'] . "</td>";
                    echo "</tr>";
                    $semester_credit += $row['c_credit'];
                }
                ?>
                <tr class="table-light">
                    <td colspan="3" class="text-end"><strong>Total Credits for Semester:</strong></td>
                    <td><strong><?php echo $semester_credit; ?></strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    <?php
    }
    ?>
</div>

<?php include 'footer.php';?>
