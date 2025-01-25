<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}

include 'headerstudent.php';
include 'dbconnect.php';

$uic = $_SESSION['funame'];

// Fetch user data
$query = "SELECT * FROM tb_user WHERE u_sno = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $uic);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Handle profile picture upload
if(isset($_POST['upload_image'])) {
    if(isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
        $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
        $filename = $_FILES["profile_picture"]["name"];
        $filetype = $_FILES["profile_picture"]["type"];
        $filesize = $_FILES["profile_picture"]["size"];

        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) {
            echo "<script>alert('Please select a valid image format (JPG, JPEG, PNG, GIF)');</script>";
            exit;
        }

        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if($filesize > $maxsize) {
            echo "<script>alert('File size is larger than 5MB');</script>";
            exit;
        }

        // Read image data
        $imgData = file_get_contents($_FILES["profile_picture"]["tmp_name"]);

        // Update database
        $update_query = "UPDATE tb_user SET u_propic = ? WHERE u_sno = ?";
        $stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt, "ss", $imgData, $uic);
        
        if(mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Profile picture updated successfully!'); window.location='profile.php';</script>";
        } else {
            echo "<script>alert('Error updating profile picture!');</script>";
        }
    }
}

// Handle profile update
if(isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    $update_query = "UPDATE tb_user SET 
                    u_name = ?, 
                    u_email = ?, 
                    u_contact = ?
                    WHERE u_sno = ?";
                    
    $stmt = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $phone, $uic);
    
    if(mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Profile updated successfully!'); window.location='profile.php';</script>";
    }
}
?>

<div class="container mt-4">
    <!-- Profile Picture Section -->
    <div class="text-center mb-4">
        <div class="position-relative d-inline-block">
            <img src="<?php 
                if(!empty($user['u_propic'])) {
                    echo "data:image/jpeg;base64," . base64_encode($user['u_propic']);
                } else {
                    echo 'img/profile.jpeg';
                }
                ?>" 
                class="rounded-circle" 
                style="width: 150px; height: 150px; object-fit: cover;"
                onerror="this.src='img/profile.jpeg'">
            <form method="POST" enctype="multipart/form-data" class="mt-3">
                <div class="mb-3">
                    <input type="file" class="form-control" name="profile_picture" accept="image/*" required>
                </div>
                <button type="submit" name="upload_image" class="btn btn-primary">
                    <i class="fa fa-upload"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Personal Information Section -->
    <div class="card">
        <div class="card-header bg-light text-black d-flex justify-content-between align-items-center">
            <h4 class="mb-0">PERSONAL INFORMATION</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name:</label>
                        <input type="text" class="form-control" name="name" value="<?php echo $user['u_name']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Student ID:</label>
                        <div class="position-relative">
                            <input type="text" class="form-control" value="<?php echo $user['u_sno']; ?>" readonly>
                            <small class="text-muted position-absolute" style="font-size: 1.0em; top: 100%; left: 65%;">
                                * Student ID cannot be edited
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">State:</label>
                        <input type="text" class="form-control" name="state" value="<?php echo $user['u_state']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number:</label>
                        <input type="text" class="form-control" name="phone" value="<?php echo $user['u_contact']; ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Email Address:</label>
                        <input type="email" class="form-control" name="email" value="<?php echo $user['u_email']; ?>">
                    </div>
                </div>
            
                <div class="text-end mt-4">
                    <a href="profile.php" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?php include 'footer.php'; ?>
