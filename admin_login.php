<?php
include('admin_login_head.php');
?>
<section class="d-flex mt-4 flex-column justify-content-center align-items-center">
    <div class="container-xl">
        <div class="col mx-auto rounded shadow bg-white">
            <div class="row">
                <div class="col-md-6">
                    <div class="">
                        <img src="images/mcc-lrc.png" alt="logo" class="img-fluid d-none d-md-block p-5" />
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 px-5">
                    <div class="mt-4 mb-4">
                        <center>
                            <h1 class="m-0"><strong>MCC</strong></h1>
                            <p class="fs-4 fw-semibold text-info">Learning Resource Center</p>
                            <p class="m-0 fw-semibold">Admin Login</p>
                        </center>
                    </div>

                    <?php if (isset($_SESSION['lockout_time']) && time() < $_SESSION['lockout_time']): ?>
                        <?php
                        $lockout_time_remaining = $_SESSION['lockout_time'] - time();
                        $minutes_remaining = ceil($lockout_time_remaining / 60);
                        ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const formInputs = document.querySelectorAll('#admin_type, #email, #password');
                                const loginButton = document.getElementById('admin_login_btn');
                                
                                formInputs.forEach(input => input.disabled = true);
                                loginButton.disabled = true;

                                Swal.fire({
                                    title: 'Account Locked',
                                    text: "Your account is locked. Please wait " + <?php echo $minutes_remaining; ?> + " minute(s) before trying again.",
                                    icon: 'warning',
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false, 
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                }).then(() => {
                                    setTimeout(function() {
                                        window.location.reload(); 
                                    }, 1000);
                                });
                            });
                        </script>
                    <?php endif; ?>

                    <form action="admin_login_code.php" method="POST" class="needs-validation" novalidate>
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <select class="form-select" id="admin_type" name="admin_type" required disabled>
                                    <option value="" selected disabled>Select Admin Type</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Staff">Staff</option>
                                </select>
                                <label for="admin_type">Admin Type</label>
                                <div class="invalid-feedback">
                                    Please select an admin type.
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" id="email" class="form-control" name="email" placeholder="Email" autocomplete="off" required disabled>
                                <label for="email">Email</label>
                                <div id="validationServerEmailFeedback" class="invalid-feedback">
                                    Please enter your email
                                </div>
                            </div>
                            <div class="form-floating mb-3 position-relative">
                                <input type="password" id="password" class="form-control" name="password" placeholder="Password" required disabled>
                                <label for="password">Password</label>
                                <span class="password-show-toggle js-password-show-toggle">
                                    <i class="bi bi-eye-slash" id="togglePassword"></i>
                                </span>
                                <div id="validationServerUsernameFeedback" class="invalid-feedback">
                                    Please enter your password.
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <div class="h-captcha" data-sitekey="026a7b60-39a2-4eba-86d8-cc6e29a254fe"></div>
                                <div class="invalid-feedback">Please complete the CAPTCHA.</div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 md-3 mb-3">
                            <button type="submit" name="admin_login_btn" id="admin_login_btn" class="btn btn-primary text-light font-weight-bolder btn-lg" disabled>Login</button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <p>
                                <a href="admin-forgot-pass.php" class="text-primary text-decoration-none fw-semibold">Forgot Password?</a>
                            </p>
                            <p>
                                <a href="login.php" class="text-primary text-decoration-none fw-semibold">User Login</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include('admin_login_script.php'); ?>
