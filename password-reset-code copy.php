<?php
ini_set('session.cookie_httponly', 1);
session_start();
include('./admin/config/dbcon.php');
include('includes/url.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';
require 'phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';

function sendEmail($email, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mcclearningresourcecenterv2.0@gmail.com'; // Use environment variable
        $mail->Password   = 'oidq tnsz oqyf cazx'; // Use environment variable
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port       = 587;

        $mail->setFrom('mcclearningresourcecenterv2.0@gmail.com', 'MCC Learning Resource Center');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}

if (isset($_POST['password_reset_link'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);

    $email_query = "SELECT firstname, email FROM user WHERE email=?";
    $stmt = mysqli_prepare($con, $email_query);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $email_row = mysqli_fetch_assoc($result);

    if (!$email_row) {
        $_SESSION['status'] = "Email not found. Need to register first.";
        $_SESSION['status_code'] = "error";
        header("Location: ms_verify.php");
        exit(0);
    }

    // Generate a more secure token
    $token = md5(rand()); 

    $code = encryptor('encrypt', $token);

    $subject = "Here is your link to Reset the password of your MCC-LRC Account";
    $message = "
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
                        <p><a style='color: white;' href='https://mcc-lrc.com/password-change.php?token=$code' class='button'>Reset Password</a></p>
                        <p>If you did not request a password reset, please ignore this email.</p>
                    </div>
            </div>
        </body>
        </html>
    ";

    sendEmail($email, $subject, $message);

    $update_query = "UPDATE user SET verify_token=?, token_used=0 WHERE email=?";
    $update_stmt = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($update_stmt, 'ss', $token, $email);
    mysqli_stmt_execute($update_stmt);

    $_SESSION['status'] = 'We e-mailed you a password reset link';
    $_SESSION['status_code'] = "success";
    header("Location: password-reset.php");
    exit(0);
}

?>