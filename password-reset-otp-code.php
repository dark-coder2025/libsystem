yods rsbo slja qgfcyods rsbo slja qgfcyods rsbo slja qgfcyods rsbo slja qgfc<?php
ini_set('session.cookie_httponly', 1);
session_start();
include('./admin/config/dbcon.php');
include('includes/url.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
    require 'phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require 'phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

function send_password_reset($get_name, $get_email, $token) {
    $mail = new PHPMailer(true);

    try {
        // SMTP server configuration
        $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Outlook/Microsoft 365 SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'mcclearningresourcecenter2.0@gmail.com'; // Your Outlook/Microsoft 365 email address
            $mail->Password   = 'mbuq bvbh wtst tnsr'; // Your email account password or app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS encryption
            $mail->Port       = 587; // Port for TLS

            //Recipients
            $mail->setFrom('mcclearningresourcecenter2.0@gmail.com', 'MCC Learning Resource Center');
            $mail->addAddress($get_email, $get_name); // Recipient's email address

        // Email content settings
        $mail->isHTML(true);
        $mail->Subject = 'Here is your link to Reset the password of your MCC-LRC Account';
        $mail->Body = "
        <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f4;
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        width: 80%;
                        margin: 20px auto;
                        padding: 20px;
                        background-color: #fff;
                        border-radius: 8px;
                        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    }
                    .header {
                        text-align: center;
                        padding-bottom: 20px;
                        border-bottom: 1px solid #ddd;
                    }
                    .logo {
                        max-width: 150px;
                        height: auto;
                    }
                    .content {
                        padding: 20px 0;
                    }
                    .button {
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: #007bff;
                        text-decoration: none;
                        color: white;
                        border-radius: 4px;
                    }
                    .token {
                        padding: 3%;
                        border: 2px solid black;
                        background-color: #dee2e6;
                        border-radius: 10px;
                        font-size: 2rem;
                        font-weight: bold;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <img src='https://mcc-lrc.com/images/mcc-lrc.png' alt='Logo'>
                    </div>
                    <div class='content'>
                        <p>Hello,</p>
                        <p>We received a request to reset your password. Use the OTP below to proceed:</p>
                        <p class='token'>$token</p>
                        <p>If you did not request a password reset, please ignore this email.</p>
                    </div>
                </div>
            </body>
        </html>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

if (isset($_POST['password_reset_link'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $token = rand(10000000, 99999999);

    // User table check
    $check_email_user = "SELECT firstname, email FROM user WHERE email='$email'";
    $check_email_run_user = mysqli_query($con, $check_email_user);

    if (mysqli_num_rows($check_email_run_user) > 0) {
        $row = mysqli_fetch_array($check_email_run_user);
        $get_name = $row['firstname'];
        $get_email = $row['email'];

        $update_token_user = "UPDATE user SET verify_token='$token', token_used=0 WHERE email='$get_email'";
        $update_token_run_user = mysqli_query($con, $update_token_user);

        if ($update_token_run_user) {
            if (send_password_reset($get_name, $get_email, $token)) {
                $_SESSION['email_success'] = true;
                header('Location: password-reset-otp.php');
                exit(0);
            } else {
                $_SESSION['status'] = 'Email sending failed. Please try again.';
                $_SESSION['status_code'] = 'error';
                header('Location: password-reset-otp.php');
                exit(0);
            }
        }
    }

    // Faculty table check
    $check_email_faculty = "SELECT firstname, email FROM faculty WHERE email='$email'";
    $check_email_run_faculty = mysqli_query($con, $check_email_faculty);

    if (mysqli_num_rows($check_email_run_faculty) > 0) {
        $row = mysqli_fetch_array($check_email_run_faculty);
        $get_name = $row['firstname'];
        $get_email = $row['email'];

        $update_token_faculty = "UPDATE faculty SET verify_token='$token', token_used = 0 WHERE email='$get_email'";
        $update_token_run_faculty = mysqli_query($con, $update_token_faculty);

        if ($update_token_run_faculty) {
            if (send_password_reset($get_name, $get_email, $token)) {
                $_SESSION['status'] = "We e-mailed you a password reset link";
                $_SESSION['status_code'] = "success";
                header('Location: password-reset.php');
                exit(0);
            } else {
                $_SESSION['status'] = "Email sending failed. Please try again.";
                $_SESSION['status_code'] = "error";
                header('Location: password-reset.php');
                exit(0);
            }
        }
    } else {
        $_SESSION['status'] = "No email found";
        $_SESSION['status_code'] = "error";
        header('Location: password-reset.php');
        exit(0);
    }
}



if (isset($_POST['password-change'])) {
    $email = $_SESSION['email_for_reset'];
    $new_password = mysqli_real_escape_string($con, $_POST['new_password']);
    $cpassword = mysqli_real_escape_string($con, $_POST['cpassword']);
    $hashed_password = password_hash($new_password, PASSWORD_ARGON2I);

    // Validate if the passwords match
    if ($new_password !== $cpassword) {
        $_SESSION['status'] = "Passwords do not match. Please try again.";
        $_SESSION['status_code'] = "warning";
        header("Location: password-change-otp.php");  // Redirect back to the form
        exit(0);
    }

    // Password strength validation (at least 8 characters, one uppercase, one number)
    if (strlen($new_password) < 10) {
        $_SESSION['status'] = "Password must be at least 8 characters long.";
        $_SESSION['status_code'] = "warning";
        header("Location: password-change-otp.php");  // Redirect back to the form
        exit(0);
    }

    if (!preg_match('/[A-Z]/', $new_password)) {
        $_SESSION['status'] = "Password must contain at least one uppercase letter.";
        $_SESSION['status_code'] = "warning";
        header("Location: password-change-otp.php");  // Redirect back to the form
        exit(0);
    }

    if (!preg_match('/[a-z]/', $new_password)) {
        $_SESSION['status'] = "Password must contain at least one lowercase letter.";
        $_SESSION['status_code'] = "warning";
        header("Location: password-change-otp.php");  // Redirect back to the form
        exit(0);
    }

    if (!preg_match('/[0-9]/', $new_password)) {
        $_SESSION['status'] = "Password must contain at least one number.";
        $_SESSION['status_code'] = "warning";
        header("Location: password-change-otp.php");  // Redirect back to the form
        exit(0);
    }

    // Check if the password contains at least one special character
    if (!preg_match('/[\W_]/', $new_password)) {  // \W matches any non-word character (not a letter or number), _ includes the underscore
        $_SESSION['status'] = "Password must contain at least one special character.";
        $_SESSION['status_code'] = "warning";
        header("Location: password-change-otp.php");  // Redirect back to the form
        exit(0);
    }

    // User table check
    $check_email_user = "SELECT email, token_used FROM user WHERE email='$email'";
    $check_email_run_user = mysqli_query($con, $check_email_user);

    if (mysqli_num_rows($check_email_run_user) > 0) {
        $row = mysqli_fetch_array($check_email_run_user);
        $get_email = $row['email'];
        $token_used = $row['token_used'];

        // Check if token is used
        if ($token_used == 0) {

            $update_password_user = "UPDATE user SET password= ?, token_used = 1 WHERE email = ?";
            $stmt = $con->prepare($update_password_user);
            $stmt->bind_param("ss", $hashed_password, $get_email);
            $update_run = $stmt->execute();

            if ($update_run) {
                $_SESSION['status'] = "Your password has been successfully updated.";
                $_SESSION['status_code'] = "success";
                unset($_SESSION['email_for_reset']);
                header("Location: login.php");
                exit(0);
            } else {
                $_SESSION['status'] = "Failed to update password. Please try again.";
                $_SESSION['status_code'] = "error";
                header("Location: password-change-otp.php");
                exit(0);
            }
        } else {
            $_SESSION['status'] = 'OTP already been used. Please request a new password reset otp.';
            $_SESSION['status_code'] = 'error';
            header('Location: password-reset-otp.php');
            exit(0);
        }
    }

    // Faculty table check
    $check_email_faculty = "SELECT email, token_used FROM faculty WHERE email='$email'";
    $check_email_run_faculty = mysqli_query($con, $check_email_faculty);

    if (mysqli_num_rows($check_email_run_faculty) > 0) {
        $row = mysqli_fetch_array($check_email_run_faculty);
        $get_email = $row['email'];
        $token_used = $row['token_used'];

        // Check if token is used
        if ($token_used == 0) {
            $update_password_faculty = "UPDATE faculty SET password= ?, token_used = 1 WHERE email = ?";
            $stmt = $con->prepare($update_password_faculty);
            $stmt->bind_param("ss", $hashed_password, $get_email);
            $update_password_run_faculty = $stmt->execute();

            if ($update_password_run_faculty) {
                $_SESSION['status'] = 'Password successfully changed.';
                $_SESSION['status_code'] = 'success';
                unset($_SESSION['email_for_reset']);
                header('Location: login.php');
                exit(0);
            } else {
                $_SESSION['status'] = 'Failed to update the password. Please try again.';
                $_SESSION['status_code'] = 'error';
                header('Location: password-change-otp.php');
                exit(0);
            }
        } else {
            $_SESSION['status'] = 'OTP already been used. Please request a new password reset otp.';
            $_SESSION['status_code'] = 'error';
            header('Location: password-reset-otp.php');
            exit(0);
        }
    } else {
        $_SESSION['status'] = 'Something went wrong.';
        $_SESSION['status_code'] = 'error';
        header('Location: password-change-otp.php');
        exit(0);
    }
}



if (isset($_GET['token'])) {
    $token_entered = mysqli_real_escape_string($con, $_GET['token']);

    // Query the database to check if the OTP is valid and matches
    $check_otp_query = "SELECT * FROM user WHERE verify_token = ? AND token_used = 0";
    $stmt = $con->prepare($check_otp_query);
    $stmt->bind_param("s", $token_entered);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // OTP is valid and not used
        $row = $result->fetch_assoc();
        $user_email = $row['email'];

        $_SESSION['email_for_reset'] = $user_email;  // Store email in session to use for password reset
        $_SESSION['status'] = "OTP verified successfully!";
        $_SESSION['status_code'] = "success";
        header("Location: password-change-otp.php");  // Redirect to the page for password reset
        exit(0);
    } else {
        // OTP is either invalid or already used
        $_SESSION['status'] = "Invalid OTP or OTP has already been used.";
        $_SESSION['status_code'] = "error";
        header("Location: password-reset-otp.php");  // Redirect back to OTP page
        exit(0);
    }

    // Query the database to check if the OTP is valid and matches
    $check_otp_query = "SELECT * FROM faculty WHERE verify_token = ? AND token_used = 0";
    $stmt = $con->prepare($check_otp_query);
    $stmt->bind_param("s", $token_entered);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // OTP is valid and not used
        $row = $result->fetch_assoc();
        $user_email = $row['email'];

        $_SESSION['email_for_reset'] = $user_email;  // Store email in session to use for password reset
        $_SESSION['status'] = "OTP verified successfully!";
        $_SESSION['status_code'] = "success";
        header("Location: password-change-otp.php");  // Redirect to the page for password reset
        exit(0);
    } else {
        // OTP is either invalid or already used
        $_SESSION['status'] = "Invalid OTP or OTP has already been used.";
        $_SESSION['status_code'] = "error";
        header("Location: password-reset-otp.php");  // Redirect back to OTP page
        exit(0);
    }
} else {
    // If OTP is not provided
    $_SESSION['status'] = "OTP is missing.";
    $_SESSION['status_code'] = "error";
    header("Location: password-reset-otp.php");  // Redirect back to OTP page
    exit(0);
}
?>
