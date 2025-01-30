<?php
ini_set('session.cookie_httponly', 1);
session_start();

include('./admin/config/dbcon.php');
include('includes/url.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require 'phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

function send_password_reset($get_email, $code)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // Replace with environment-stored credentials
        $mail->Username = 'mcclearningresourcecenter2.0@gmail.com';
        $mail->Password = 'eqin ygpp kyem mcul';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email details
        $mail->setFrom('mcclearningresourcecenter2.0@gmail.com', 'MCC Learning Resource Center');
        $mail->addAddress($get_email); // Corrected this line

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
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='https://mcc-lrc.com/images/mcc-lrc.png' alt='Logo'>
                </div>
                <div class='content'>
                    <p>Hello,</p>
                    <p>We received a request to reset your password. Click the button below to reset it:</p>
                    <p><a style='color: white;' href='https://mcc-lrc.com/admin-pass-change.php?token=$code' class='button'>Reset Password</a></p>
                    <p>If you did not request a password reset, please ignore this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

if (isset($_POST['password_reset_link'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);

    // Generate a secure token
    $token = bin2hex(random_bytes(16));
    $code = encryptor('encrypt', $token);

    // Prepared statement to check email in the admin table
    $query = "SELECT email FROM admin WHERE email=?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $get_email = $row['email'];

        // Update token in the database
        $update_query = "UPDATE admin SET confirm_password=? WHERE email=?";
        $update_stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($update_stmt, 'ss', $token, $get_email);
        $update_success = mysqli_stmt_execute($update_stmt);

        if ($update_success) {
            if (send_password_reset($get_email, $code)) {
                $_SESSION['status'] = 'We e-mailed you a password reset link.';
                $_SESSION['status_code'] = 'success';
                header('Location: admin-forgot-pass.php');
                exit(0);
            } else {
                $_SESSION['status'] = 'Email sending failed. Please try again.';
                $_SESSION['status_code'] = 'error';
                header('Location: admin-forgot-pass.php');
                exit(0);
            }
        } else {
            $_SESSION['status'] = 'Failed to update the reset token. Please try again.';
            $_SESSION['status_code'] = 'error';
            header('Location: admin-forgot-pass.php');
            exit(0);
        }
    } else {
        $_SESSION['status'] = 'Email not found in our records.';
        $_SESSION['status_code'] = 'error';
        header('Location: admin-forgot-pass.php');
        exit(0);
    }
}


if (isset($_POST['password-change'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $new_password = mysqli_real_escape_string($con, $_POST['new_password']);
    $hashed_password = password_hash($new_password, PASSWORD_ARGON2I);

    // User table check
    $check_email_user = "SELECT email FROM admin WHERE email='$email'";
    $check_email_run_user = mysqli_query($con, $check_email_user);

    if (mysqli_num_rows($check_email_run_user) > 0) {
        $row = mysqli_fetch_array($check_email_run_user);
        $get_email = $row['email'];

        // Check if token is used
            $update_password_user = "UPDATE admin SET password='$hashed_password' WHERE email='$get_email'";
            $update_password_run_user = mysqli_query($con, $update_password_user);

            if ($update_password_run_user) {
                $_SESSION['status'] = 'Password successfully changed.';
                $_SESSION['status_code'] = 'success';
                header('Location: admin_login.php');
                exit(0);
            } else {
                $_SESSION['status'] = 'Failed to update the password. Please try again.';
                $_SESSION['status_code'] = 'error';
                header('Location: admin-pass-change.php');
                exit(0);
            }
    }
}
?>