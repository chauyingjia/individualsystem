<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}
include('dbconnect.php');

// Handle registration cancellation
if(isset($_POST['cancel_registration'])) {
    $tid = $_POST['tid'];
    $cancel_query = "DELETE FROM tb_registration WHERE r_tid = ?";
    $stmt = mysqli_prepare($con, $cancel_query);
    mysqli_stmt_bind_param($stmt, "i", $tid);
    
    if(mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Registration cancelled successfully";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error cancelling registration";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: registrationhistory.php");
    exit();
}

// Get all registrations with related information
$query = "SELECT r.*, c.c_name, c.c_credit, c.c_maxStudent, u.u_name,
                 (SELECT COUNT(*) FROM tb_registration 
                  WHERE r_course = r.r_course 
                  AND r_status = 2) as enrolled
          FROM tb_registration r
          LEFT JOIN tb_course c ON r.r_course = c.c_code
          LEFT JOIN tb_user u ON r.r_student = u.u_sno
          ORDER BY r.r_student, r.r_sem DESC";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include('headeradmin.php'); ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <h2>Registration History</h2>
            <div style="width: 100px;"></div>
        </div>

        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                <?php 
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Registration List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Student ID</th>
                                <th>Course</th>
                                <th>Semester</th>
                                <th>Section</th>
                                <th>Credits</th>
                                <th>Enrollment</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $row['u_name']; ?></td>
                                    <td><?php echo $row['r_student']; ?></td>
                                    <td><?php echo $row['c_name']; ?></td>
                                    <td><?php echo $row['r_sem']; ?></td>
                                    <td><?php echo $row['r_section']; ?></td>
                                    <td><?php echo $row['c_credit']; ?></td>
                                    <td>
                                        <?php echo $row['enrolled']; ?>/<?php echo $row['c_maxStudent']; ?>
                                        <?php if($row['enrolled'] >= $row['c_maxStudent']): ?>
                                            <span class="badge bg-warning">Full</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if($row['r_status'] == "Pending") {
                                            $status_class = 'bg-warning';
                                            $status_text = 'Pending';
                                        } elseif($row['r_status'] == "Approved") {
                                            $status_class = 'bg-success';
                                            $status_text = 'Approved';
                                        } elseif($row['r_status'] == "Rejected") {
                                            $status_class = 'bg-danger';
                                            $status_text = 'Rejected';
                                        } else {
                                            $status_class = 'bg-secondary';
                                            $status_text = 'Unknown';
                                        }
                                        ?>
                                        <span class="badge <?php echo $status_class; ?>">
                                            <?php echo $status_text; ?>
                                        </span>
                                        <?php if($row['r_status'] == "Approved"): ?>
                                            <small class="d-block text-muted mt-1">
                                                <!-- <i class="fas fa-info-circle"></i> Approved automatically -->
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="editcourse.php?code=<?php echo $row['r_course']; ?>&section=<?php echo $row['r_section']; ?>" 
                                           class="btn btn-sm btn-primary me-1">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to cancel this registration?');">
                                            <input type="hidden" name="tid" value="<?php echo $row['r_tid']; ?>">
                                            <button type="submit" name="cancel_registration" 
                                                    class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Cancel
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function updateStatus(tid, status) {
        if(confirm('Are you sure you want to update this registration status?')) {
            window.location.href = 'courseregistrationstatus.php?tid=' + tid + '&status=' + status;
        }
    }
    </script>
</body>
</html>
