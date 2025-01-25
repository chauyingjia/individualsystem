<?php
include('dbconnect.php');

if(isset($_POST['submit'])) {
    $studentId = $_POST['id'];
    $newPassword = $_POST['new_password'];
    
    // Update password in database
    $query = "UPDATE tb_user SET u_pwd = ? WHERE u_sno = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ss", $newPassword, $studentId);
    
    if(mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Password updated successfully!'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Error updating password!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Reset Password</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            
                            <button type="submit" name="submit" class="btn btn-primary">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
