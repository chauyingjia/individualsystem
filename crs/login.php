<?php 
session_start();

// If user is already logged in, redirect them based on user type
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    switch($_SESSION['u_type']) {
        case '1':
            header("Location: advisor.php");  // Academic Advisor
            break;
        case '2':
            header("Location: student.php");  // Student
            break;
        case '3':
            header("Location: admin.php");    // IT Staff
            break;
    }
    exit();
}

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");


?>

<?php include 'headermain.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-circle fa-3x text-primary"></i>
                        <h2 class="mt-3 mb-4">Login</h2>
                    </div>

                    <?php
                    if(isset($_SESSION['login_error'])) {
                        echo '<div class="alert alert-danger">' . $_SESSION['login_error'] . '</div>';
                        unset($_SESSION['login_error']);
                    }
                    ?>

                    <form method="POST" action="loginprocess.php">
                        <div class="form-group mb-3">
                            <label class="form-label">ID Number</label>
                            <input type="text" name="funame" class="form-control" required 
                                   placeholder="Enter your ID">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="fpwd" class="form-control" 
                                       id="password" required placeholder="Enter your password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Login</button>
                        </div>

                        <div class="text-center mt-3">
                            <a href="#" class="text-primary" data-bs-toggle="modal" 
                               data-bs-target="#forgotPasswordModal">Forgot password?</a>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <span class="text-muted">Don't have an account?</span>
                            <a href="register.php" class="text-primary ms-2">Register Now</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="forgotPasswordForm">
                    <div class="mb-3">
                        <label class="form-label">ID Number</label>
                        <input type="text" class="form-control" id="resetStudentId" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="resetEmail" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Send Reset Link</button>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    
    const icon = this.querySelector('i');
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
});
</script>

</body>
</html>