<?php
ini_set('session.cookie_httponly', 1);
ob_start();
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
        $mail->Username   = 'mcclearningresourcecenter2.0@gmail.com'; // Use environment variable
        $mail->Password   = 'eqin ygpp kyem mcul'; // Use environment variable
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port       = 587;

        $mail->setFrom('mcclearningresourcecenter2.0@gmail.com', 'MCC Learning Resource Center');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}

if (isset($_POST['registration_link'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['status'] = "Invalid Email: Please enter a valid MS 365 email address.";
        $_SESSION['status_code'] = "error";
        header("Location: ms_verify.php");
        exit(0);
    }

    $domain = substr(strrchr($email, "@"), 1);
    if ($domain !== 'mcclawis.edu.ph') {
        $_SESSION['status'] = "Invalid Domain: Please enter an email address with the mcclawis.edu.ph domain.";
        $_SESSION['status_code'] = "error";
        header("Location: ms_verify.php");
        exit(0);
    }

    $email_query = "SELECT used, email FROM ms_account WHERE username = ?";
    $stmt = mysqli_prepare($con, $email_query);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $email_row = mysqli_fetch_assoc($result);

    if (!$email_row) {
        $_SESSION['status'] = "Email not found. Please visit the BSIT office to get MS365 Account.";
        $_SESSION['status_code'] = "error";
        header("Location: ms_verify.php");
        exit(0);
    }

    $used = $email_row['used'];
    if ($used == 1) {
        $_SESSION['status'] = "This email has already been used.";
        $_SESSION['status_code'] = "error";
        header("Location: ms_verify.php");
        exit(0);
    }

    $verification_code = sha1(rand());

    $code = encryptor('encrypt', $verification_code);

    $subject = "MCC-LRC Creating Account";
    $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
                .container { width: 80%; margin: auto; background: #fff; padding: 20px; }
                .button { padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>MCC-LRC Account Registration</h2>
                <p>Please click the link below to complete your registration:</p>
                <p><a href='http://mcc-lrc.com/signup.php?code=$code' class='button'>Register</a></p>
                <p>If you did not request this registration, ignore this email.</p>
            </div>
        </body>
        </html>
    ";

    sendEmail($email, $subject, $message);

    $update_query = "UPDATE ms_account SET verification_code = ? WHERE username = ?";
    $update_stmt = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($update_stmt, 'ss', $verification_code, $email);
    mysqli_stmt_execute($update_stmt);

    $_SESSION['status'] = 'Registration link sent. Please check your email on Outlook.';
    $_SESSION['status_code'] = "success";
    header("Location: ms_verify.php");
    exit(0);
} else {
    $_SESSION['status'] = 'Unable to send the registration link at this moment. Comeback tomorrow.';
    $_SESSION['status_code'] = "error";
    header("Location: ms_verify.php");
    exit(0);
}

ob_end_flush();
?>