<?php
// Start the session at the beginning
session_start();

// Include necessary files
include('authentication.php');
include('includes/url.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// PHPMailer dependencies
require 'phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require 'phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

// Function to send email using PHPMailer
function sendDenyEmail($student_email, $deny_reason, $stu_email)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // Replace with environment-stored credentials
        $mail->Username = 'resourcecentermcclearning@gmail.com';
        $mail->Password = 'oenz pxyh ohro zevi';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email details
        $mail->setFrom('resourcecentermcclearning@gmail.com', 'MCC Learning Resource Center');
        $mail->addAddress($student_email);

        $mail->isHTML(true);
        $mail->Subject = 'Account Denied Notification';
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
                            <h1 style='color:#dc3545;text-align:center;'>Your Account has been Denied!!!</h1>
                            <p>Dear Student,</p>
                            <p>Your MCC-LRC account registration has been denied. Below is the reason for denial:</p>
                            <p><strong>Reason:</strong> <b>" . htmlspecialchars($deny_reason) . "</b></p>
                            <p>Click this button to update the reason why you deny:</p>
                            <p><a style='color: white;' href='https://mcc-lrc.com/signup_update.php?a=" . htmlspecialchars($stu_email) . "' class='button'>Update</a></p>
                            <div class='header'>
                                <img src='https://mcc-lrc.com/images/valid.jpg' alt='Valid ID'>
                            </div>
                            <p>You can also contact us on our Facebook page <a href='https://www.facebook.com/MCCLRC' target='_blank'>Madridejos Community College - Learning Resource Center</a>.</p>
                            <p>Thank you.</p>
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

// Handle the denial process
if (isset($_POST['deny'])) {
    $student_id = mysqli_real_escape_string($con, $_POST['user_id']);
    $deny_reason = mysqli_real_escape_string($con, $_POST['deny_reason']);

    // Fetch the student's email
    $email_query = "SELECT email FROM user WHERE user_id = ?";
    $stmt = mysqli_prepare($con, $email_query);
    mysqli_stmt_bind_param($stmt, 'i', $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($email_row = mysqli_fetch_assoc($result)) {
        $student_email = $email_row['email'];
        $stu_email = encryptor('encrypt', $student_email);

        // Send the email
        if (sendDenyEmail($student_email, $deny_reason, $stu_email)) {
            // Update the user's status
            $update_query = "UPDATE user SET status = 'archived', user_added = NULL WHERE user_id = ?";
            $update_stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($update_stmt, 'i', $student_id);
            mysqli_stmt_execute($update_stmt);

            $_SESSION['status'] = 'Student Denied';
            $_SESSION['status_code'] = "success";
            header("Location: archived.php");
            exit(0);
        } else {
            $_SESSION['status'] = "Failed to send email.";
            $_SESSION['status_code'] = "error";
            header("Location: archived.php");
            exit(0);
        }
    } else {
        $_SESSION['status'] = 'Email not found.';
        $_SESSION['status_code'] = "error";
        header("Location: archived.php");
        exit(0);
    }
}

// Handle retrieving a specific user's data
if (isset($_GET['id'])) {
    $userId = mysqli_real_escape_string($con, $_GET['id']);
    $sql = "SELECT * FROM user WHERE user_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Student not found']);
    }
}
?>
