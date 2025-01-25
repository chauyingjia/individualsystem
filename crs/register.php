<?php 
include 'headermain.php';

// Add these headers at the top of the page
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Add session check
session_start();
if(isset($_SESSION['registration_completed'])) {
    // Redirect to login if they try to access registration page after completing registration
    header("Location: login.php");
    exit();
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?php
            if(isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ' . $_SESSION['error_message'] . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
                unset($_SESSION['error_message']);
            }
            ?>
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Registration Form</h4>
                </div>
                <div class="card-body p-4">

                    <form method="POST" action="registerprocess.php">
                        <!-- Student/Staff ID -->
                        <div class="mb-3">
                            <label class="form-label">Staff or Student Number</label>
                            <input type="text" name="funame" class="form-control" 
                                   placeholder="Enter your staff or student ID" required
                                   pattern="[A-Za-z][0-9]{3}"
                                   title="Format: Letter followed by 3 numbers (e.g., S001)">
                            <small class="text-muted">Format: Letter followed by 3 numbers (e.g., S001)</small>
                        </div>

                        <!-- Full Name -->
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="fname" class="form-control" 
                                   placeholder="Enter your full name according to IC" required>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label">Create Password</label>
                            <div class="input-group">
                                <input type="password" name="fpwd" class="form-control" id="password" 
                                       placeholder="Password" autocomplete="off" required
                                       pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                                       title="Password must contain at least 8 characters, including uppercase/lowercase letters, numbers and symbols">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            <div class="password-requirements mt-2 small">
                                Password must contain:
                                <ul class="mb-0">
                                    <li id="length">At least 8 characters</li>
                                    <li id="uppercase">At least one uppercase letter</li>
                                    <li id="lowercase">At least one lowercase letter</li>
                                    <li id="number">At least one number</li>
                                    <li id="symbol">At least one symbol (@$!%*?&)</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="femail" class="form-control" 
                                   placeholder="Enter your academic email" required
                                   pattern="[a-zA-Z0-9._%+-]+@(graduate\.utm\.my)$"
                                   title="Please use your academic email address">
                            <small class="text-muted">Format: yourname@graduate.utm.my</small>
                        </div>

                        <!-- Contact Number -->
                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="tel" name="fcontact" class="form-control" 
                                   placeholder="Enter your mobile or fixed line number" required
                                   pattern="[0-9]{10,11}"
                                   title="Please enter a valid Malaysian phone number (e.g., 0123456789)">
                            <small class="text-muted">Format: 10-11 digits without spaces or dashes (e.g., 0123456789)</small>
                        </div>

                        <!-- State -->
                        <div class="mb-4">
                            <label class="form-label">Select State</label>
                            <select class="form-select" name="fstate" required>
                                <option value="">Choose your state</option>
                                <option>Johor</option>
                                <option>Kedah</option>
                                <option>Kelantan</option>
                                <option>Melaka</option>
                                <option>Negeri Sembilan</option>
                                <option>Pahang</option>
                                <option>Pulau Pinang</option>
                                <option>Perak</option>
                                <option>Perlis</option>
                                <option>Sabah</option>
                                <option>Sarawak</option>
                                <option>Selangor</option>
                                <option>Terengganu</option>
                                <option>W.P. Kuala Lumpur</option>
                                <option>W.P. Labuan</option>
                                <option>W.P. Putrajaya</option>
                            </select>
                        </div>

                        <div class="text-center mb-4">
                        
                        <small class="text-muted">All fields are required</small>
                    </div>

                        <!-- Submit Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Register</button>
                            <button type="reset" class="btn btn-secondary">Clear Form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .password-requirements {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-top: 10px;
        border: 1px solid #dee2e6;
    }
    .password-requirements ul {
        list-style-type: none;
        padding-left: 15px;
        margin-top: 8px;
    }
    .password-requirements li {
        margin: 5px 0;
        font-size: 0.85rem;
    }
    .card {
        border: none;
        border-radius: 10px;
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
    }
    .form-label {
        font-weight: 500;
        color: #495057;
    }
    .form-control, .form-select {
        border-radius: 7px;
        padding: 0.6rem 1rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    .btn {
        padding: 0.6rem 1.5rem;
        border-radius: 7px;
    }
    .input-group .btn {
        border-top-right-radius: 7px !important;
        border-bottom-right-radius: 7px !important;
        padding: 0.6rem 1rem;
    }
    .input-group .form-control {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    .form-control:focus + small,
    .form-control:hover + small {
        color: #0d6efd !important;
    }
    
    small.text-muted {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.75rem;
    }
</style>

<script>
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        
        // Check each requirement
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /\d/.test(password),
            symbol: /[@$!%*?&]/.test(password)
        };

        // Update the visual feedback
        for (const [requirement, met] of Object.entries(requirements)) {
            const element = document.getElementById(requirement);
            if (met) {
                element.style.color = '#198754';  // Bootstrap success color
                element.innerHTML = '✓ ' + element.innerHTML.replace('✓ ', '').replace('✗ ', '');
            } else {
                element.style.color = '#dc3545';  // Bootstrap danger color
                element.innerHTML = '✗ ' + element.innerHTML.replace('✓ ', '').replace('✗ ', '');
            }
        }
    });

    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        // Toggle the password visibility
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    });

    document.querySelector('input[name="fcontact"]').addEventListener('input', function(e) {
        // Remove any non-digit characters
        this.value = this.value.replace(/\D/g, '');
        
        // Limit to 11 digits
        if (this.value.length > 11) {
            this.value = this.value.slice(0, 11);
        }
    });

    document.querySelector('input[name="funame"]').addEventListener('input', function(e) {
        // Convert first character to uppercase and remove any non-alphanumeric characters
        let value = this.value.replace(/[^a-zA-Z0-9]/g, '');
        if (value.length > 0) {
            value = value[0].toUpperCase() + value.slice(1);
        }
        
        // Limit to 4 characters (1 letter + 3 numbers)
        if (value.length > 4) {
            value = value.slice(0, 4);
        }
        
        this.value = value;
    });

    document.querySelector('input[name="femail"]').addEventListener('input', function(e) {
        const email = this.value.toLowerCase();
        const academicDomains = ['@graduate.utm.my'];
        const isAcademic = academicDomains.some(domain => email.endsWith(domain));
        
        if (email && !isAcademic) {
            this.setCustomValidity('Please use your academic email address');
        } else {
            this.setCustomValidity('');
        }
    });

    // Prevent going back to this page
    window.onload = function() {
        if (window.history && window.history.pushState) {
            window.history.pushState('forward', null, window.location.href);
            window.onpopstate = function() {
                window.history.pushState('forward', null, window.location.href);
            };
        }
    }

    // Disable form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    // Warn users before leaving the page
    window.onbeforeunload = function() {
        return "Are you sure you want to leave? Your registration progress will be lost.";
    };

    // Remove warning when form is submitted
    document.querySelector('form').addEventListener('submit', function() {
        window.onbeforeunload = null;
    });
</script>

<?php include 'footer.php';?>