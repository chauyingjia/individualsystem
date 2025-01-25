<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}

include 'headeradvisor.php';
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
    $target_dir = "img/profile/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . $uic . "_" . basename($_FILES["profile_picture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    // Check if image file is actual image
    if(isset($_FILES["profile_picture"])) {
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $update_query = "UPDATE tb_user SET profile_picture = ? WHERE u_sno = ?";
                $stmt = mysqli_prepare($con, $update_query);
                mysqli_stmt_bind_param($stmt, "ss", $target_file, $uic);
                mysqli_stmt_execute($stmt);
                header("Location: profile.php");
            }
        }
    }
}

// Handle profile update
if(isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $postcode = $_POST['postcode'];
    $state = $_POST['state'];
    
    $update_query = "UPDATE tb_user SET 
                    u_name = ?, 
                    u_email = ?, 
                    u_phone = ?, 
                    u_address = ?,
                    u_postcode = ?,
                    u_state = ?
                    WHERE u_sno = ?";
                    
    $stmt = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($stmt, "sssssss", $name, $email, $phone, $address, $postcode, $state, $uic);
    
    if(mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Profile updated successfully!');</script>";
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
            <!-- <button class="btn btn-sm btn-primary position-absolute bottom-0 end-0" 
                    data-bs-toggle="modal" 
                    data-bs-target="#uploadPhotoModal">
                <i class="fas fa-camera"></i>
            </button> -->
        </div>
    </div>

    <!-- Personal Information Section -->
    <div class="card">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">PERSONAL INFORMATION</h4>
            <a href="updateprofile.php" class="btn btn-light">
                <i class="fa fa-edit"></i>
            </a>
            
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name:</label>
                        <input type="text" class="form-control" value="<?php echo $user['u_name']; ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Student ID:</label>
                        <input type="text" class="form-control" value="<?php echo $user['u_sno']; ?>" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">State:</label>
                        <input type="text" class="form-control" value="<?php echo $user['u_state']; ?>" readonly>
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

            </form>
        </div>
    </div>
</div>

<!-- Upload Photo Modal -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Image</label>
                        <input type="file" class="form-control" name="profile_picture" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="upload_image" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add this before the closing div.container -->
    <div class="text-center mt-3 mb-4">
        <a href="logout.php" class="btn btn-danger btn-lg">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>
