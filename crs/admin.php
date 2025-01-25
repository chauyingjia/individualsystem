<?php 

require_once('checksession.php');
checkUserAccess("1"); // Check for admin access

include('crssession.php');

if(!session_id())
{
  session_start();
}

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include 'headeradmin.php';
include 'dbconnect.php';

// Get quick statistics
$total_courses_query = "SELECT COUNT(*) as total FROM tb_course";
$total_courses_result = mysqli_query($con, $total_courses_query);
$total_courses = mysqli_fetch_assoc($total_courses_result)['total'];

$pending_approvals_query = "SELECT COUNT(*) as pending FROM tb_registration WHERE r_status = 'Pending'";
$pending_result = mysqli_query($con, $pending_approvals_query);
$pending_approvals = mysqli_fetch_assoc($pending_result)['pending'];

// Modified query to count unique students
$total_students_query = "SELECT COUNT(DISTINCT u_sno) as total FROM tb_user WHERE u_utype = 2"; // Assuming u_utype 2 is for students
$total_students_result = mysqli_query($con, $total_students_query);
$total_students = mysqli_fetch_assoc($total_students_result)['total'];
?>

<div class="container mt-4">
    <h2 class="mb-4 text-center">IT Staff Dashboard</h2>
    
    <!-- Statistics Cards -->
    <div class="row mb-4 justify-content-center align-items-center">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Approvals</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pending_approvals; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Courses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_courses; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Students</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_students; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4 justify-content-center">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Registration Management</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="courseapproval.php" class="btn btn-outline-warning mb-2">
                            <i class="fas fa-check-circle me-2"></i>Pending Approvals
                            <?php if($pending_approvals > 0): ?>
                                <span class="badge bg-danger"><?php echo $pending_approvals; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="registrationhistory.php" class="btn btn-outline-secondary mb-2">
                            <i class="fas fa-history me-2"></i>Registration History
                        </a>
                        <!-- <a href="registrationreport.php" class="btn btn-outline-dark">
                            <i class="fas fa-file-alt me-2"></i>Generate Reports
                        </a> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Course Management</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="admincourselist.php" class="btn btn-outline-primary mb-2">
                            <i class="fas fa-list me-2"></i>View Course List
                        </a>
                        <a href="addcourse.php" class="btn btn-outline-success mb-2">
                            <i class="fas fa-plus me-2"></i>Add New Course
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Recent Activities</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Activity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Add your recent activities here -->
                        <tr>
                            <td>2024-03-20</td>
                            <td>New course registration request</td>
                            <td><span class="badge bg-warning">Pending</span></td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add custom CSS for card styling -->
<style>
.border-left-primary {
    border-left: 4px solid #4e73df;
}
.border-left-success {
    border-left: 4px solid #1cc88a;
}
.border-left-warning {
    border-left: 4px solid #f6c23e;
}
.card {
    transition: transform .2s;
}
.card:hover {
    transform: translateY(-5px);
}
</style>

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
<?php include 'footer.php';?>
</body>
