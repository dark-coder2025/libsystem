<?php
ini_set('session.cookie_httponly', 1);
ob_start();
session_start();
include('./admin/config/dbcon.php');
include('includes/url.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require 'phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

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

    // Check if email exists and get `used` status
    $stmt = $con->prepare("SELECT used, verification_code, created_at FROM ms_account WHERE username = ?");
    if (!$stmt) {
        error_log("MySQL prepare error: " . $con->error);
        $_SESSION['status'] = "Database error. Please try again later.";
        $_SESSION['status_code'] = "error";
        header("Location: ms_verify.php");
        exit(0);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($used, $current_code, $created_at);
    $stmt->fetch();
    $stmt->close();

    // Check if email is found
    if ($used === null) {
        $_SESSION['status'] = "Email not found. Please visit the BSIT office to get MS365 Account.";
        $_SESSION['status_code'] = "error";
        header("Location: ms_verify.php");
        exit(0);
    }

    // Check if email is already used
    if ($used == 1) {
        $_SESSION['status'] = "This email has already been used.";
        $_SESSION['status_code'] = "error";
        header("Location: ms_verify.php");
        exit(0);
    }

    // Generate a new verification code
    $verification_code = md5(rand());

    $code = encryptor('encrypt', $verification_code);

    $stmt = $con->prepare("UPDATE ms_account SET verification_code = ?, created_at = NOW() WHERE username = ?");
    if (!$stmt) {
        error_log("MySQL prepare error: " . $con->error);
        $_SESSION['status'] = "Database error. Please try again later.";
        $_SESSION['status_code'] = "error";
        header("Location: ms_verify.php");
        exit(0);
    }

    $stmt->bind_param("ss", $verification_code, $email);

    if ($stmt->execute()) {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 2; // Set to 2 for detailed debug output
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
            $mail->Subject = 'MCC-LRC Creating Account';
            $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
                    .container { width: 80%; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                    .header { text-align: center; padding-bottom: 20px; border-bottom: 1px solid #ddd; }
                    .logo { max-width: 150px; height: auto; }
                    .content { padding: 20px 0; }
                    .button { display: inline-block; padding: 10px 20px; background-color: #007bff; text-decoration: none; color: white; border-radius: 4px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <img src='https://mcc-lrc.com/images/mcc-lrc.png' alt='Logo'>
                    </div>
                    <div class='content'>
                        <p>Hello,</p>
                        <p><b>This registration will expire within 1 hour.</b></p>
                        <p>Please click the button below to create a MCC-LRC Account:</p>
                        <p><a style='color: white;' href='https://mcc-lrc.com/signup.php?code=$code' class='button'>Register</a></p>
                        <p>If you did not request this registration, please ignore this email.</p>
                    </div>
                </div>
            </body>
        </html>
        ";

            $mail->send();
            $_SESSION['status'] = "Registration link sent. Please check your email on Outlook.";
            $_SESSION['status_code'] = "success";
            header("Location: ms_verify.php");
            exit(0);
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
            $_SESSION['status'] = "Sending registration link limit exceed. Comeback tomorrow.";
            $_SESSION['status_code'] = "error";
            header("Location: ms_verify.php");
            exit(0);
        }
    } else {
        error_log("MySQL execute error: " . $stmt->error);
        $_SESSION['status'] = "Database error. Please try again later.";
        $_SESSION['status_code'] = "error";
        header("Location: ms_verify.php");
        exit(0);
    }

    $stmt->close();
    $con->close();
} else {
    $_SESSION['status'] = "Invalid request.";
    $_SESSION['status_code'] = "error";
    header("Location: ms_verify.php");
    exit(0);
}

ob_end_flush();
?>
