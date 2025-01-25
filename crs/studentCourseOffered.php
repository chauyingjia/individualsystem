<?php 
include('crssession.php');

// Debug session information
echo "<!-- Debug Session Info: ";
echo "Session ID: " . session_id() . "\n";
echo "Student ID: " . $_SESSION['funame'] . "\n";
print_r($_SESSION);
echo " -->";

if(!session_id())
{
  session_start();
}

include 'headerstudent.php';
?>

<div class="container mt-5">
    <div class="d-flex align-items-center mb-5">
        <h2>Course Registration</h2>
    </div>

    <!-- Main Content Tabs -->
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link" href="student.php">
                        <i class="fas fa-edit"></i> Registration
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="studentCourseOffered.php">
                    <i class="fas fa-book"></i> Course Offered
                    </a>
                </li>
            </ul>

            <!-- Course Selection Table -->
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Student ID: <?php echo isset($_SESSION['funame']) ? $_SESSION['funame'] : 'Not Set'; ?> - Semester: 2024/2025-1</h6>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Show entries controls -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <label class="me-2">Show</label>
                                <select class="form-select form-select-sm w-auto">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                                <label class="ms-2">entries</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">Search:</span>
                                <input type="text" class="form-control" id="searchInput" onkeyup="searchTable()">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <form method="post" action="process_registration.php" id="courseRegistrationForm">
                            <table class="table table-bordered table-hover" id="courseTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Credit</th>
                                        <th>Section</th>
                                        <th>Availability</th>
                                        <th>Select</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Get the most recent semester from tb_course
                                    $recent_sem_query = "SELECT DISTINCT c_sem FROM tb_course ORDER BY c_sem DESC LIMIT 1";
                                    $recent_sem_result = mysqli_query($con, $recent_sem_query);
                                    $recent_sem = mysqli_fetch_assoc($recent_sem_result)['c_sem'];

                                    // Get selected semester or default to most recent
                                    $selected_sem = isset($_GET['semester']) ? $_GET['semester'] : $recent_sem;

                                    // Modify the query to get courses for selected semester
                                    $query = "SELECT c.*, 
                                              (SELECT COUNT(*) FROM tb_registration r 
                                               WHERE r.r_course = c.c_code 
                                               AND r.r_status = 2) as enrolled_count 
                                              FROM tb_course c 
                                              WHERE c.c_sem = ?
                                              ORDER BY c_code";

                                    $stmt = mysqli_prepare($con, $query);
                                    mysqli_stmt_bind_param($stmt, "s", $selected_sem);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    $count = 1;

                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $count . "</td>";
                                        echo "<td>" . $row['c_code'] . "</td>";
                                        echo "<td>" . $row['c_name'] . "</td>";
                                        echo "<td>" . $row['c_credit'] . "</td>";
                                        echo "<td>" . $row['c_section'] . "</td>";
                                        echo "<td>" . $row['enrolled_count'] . "/" . $row['c_maxStudent'];
                                        if($row['enrolled_count'] >= $row['c_maxStudent']) {
                                            echo " <span class='badge bg-danger'>Full</span>";
                                        }
                                        echo "</td>";
                                        echo "<td class='text-center'>";
                                        $disabled = ($row['enrolled_count'] >= $row['c_maxStudent']) ? 'disabled' : '';
                                        echo "<input type='checkbox' name='selected_courses[]' value='" . $row['c_code'] . "' $disabled>";
                                        echo "<input type='hidden' name='section[" . $row['c_code'] . "]' value='01'>";
                                        echo "</td>";
                                        echo "</tr>";
                                        $count++;
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <!-- Submit Button -->
                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-primary" onclick="return validateSelection()">
                                    <i class="fas fa-plus me-1"></i> Add Selected Courses
                                </button>
                            </div>

                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-info" onclick="window.location.href='student.php'">Check Registered Course</button>
                            </div>
                        </form>
                    </div>

                    <!-- Message Display -->
                    <?php if(isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show mt-3" role="alert">
                            <?php 
                                echo $_SESSION['message']; 
                                unset($_SESSION['message']);
                                unset($_SESSION['message_type']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function searchTable() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("courseTable");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td");
        for (var j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                    break;
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
}

function validateSelection() {
    var checkboxes = document.getElementsByName('selected_courses[]');
    var checked = false;
    for(var i = 0; i < checkboxes.length; i++) {
        if(checkboxes[i].checked) {
            checked = true;
            break;
        }
    }
    if(!checked) {
        alert('Please select at least one course');
        return false;
    }
    return true;
}

$(document).ready(function() {
    // Handle "Check Registered Course" button click
    $('#checkRegistered').click(function(e) {
        e.preventDefault();
        window.location.href = 'courseview.php';
    });

    // Handle form submission for new course registration
    $('form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'process_registration.php',
            data: $(this).serialize(),
            success: function(response) {
                // Clear all checkboxes
                $('input[type="checkbox"]').prop('checked', false);
                
                // Refresh the page to show the message
                location.reload();
            },
            error: function() {
                alert('An error occurred while processing your request.');
            }
        });
    });
});
</script>

<!-- Add jQuery if not already included -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<?php include 'footer.php';?>
