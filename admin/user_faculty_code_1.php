mcclearningresourcecenter2.0@gmail.comeqin ygpp kyem mculeqin ygpp kyem mculeqin ygpp kyem mculeqin ygpp kyem mculgaai wmiv oqql rhvugaai wmiv oqql rhvugaai wmiv oqql rhvugaai wmiv oqql rhvu<?php

session_start();

include('authentication.php');
include('includes/url.php');
header('Content-Type: application/json');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require 'phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

# ======================================== Deny Code ==============================
// Function to send email using PHPMailer
function sendDenyEmail($faculty_email, $deny_reason, $fac_email)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // Replace with environment-stored credentials
        $mail->Username = 'mcclearningresourcecenter2.0@gmail.com';
        $mail->Password = 'oenz pxyh ohro zevi';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email details
        $mail->setFrom('mcclearningresourcecenter2.0@gmail.com', 'MCC Learning Resource Center');
        $mail->addAddress($faculty_email);

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
                            <p>Dear Faculty/Staff,</p>
                            <p>Your MCC-LRC account registration has been denied. Below is the reason for denial:</p>
                            <p><strong>Reason:</strong> <b>" . htmlspecialchars($deny_reason) . "</b></p>
                            <p>Click this button to update the reason why you deny:</p>
                            <p><a style='color: white;' href='https://mcc-lrc.com/signup_update.php?a=" . htmlspecialchars($fac_email) . "' class='button'>Update</a></p>
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
    $faculty_id = mysqli_real_escape_string($con, $_POST['faculty_id']);
    $deny_reason = mysqli_real_escape_string($con, $_POST['deny_reason']);

    // Fetch the student's email
    $email_query = "SELECT email FROM faculty WHERE faculty_id = ?";
    $stmt = mysqli_prepare($con, $email_query);
    mysqli_stmt_bind_param($stmt, 'i', $faculty_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($email_row = mysqli_fetch_assoc($result)) {
        $faculty_email = $email_row['email'];
        $fac_email = encryptor('encrypt', $faculty_email);

        // Send the email
        if (sendDenyEmail($faculty_email, $deny_reason, $fac_email)) {
            // Update the user's status
            $update_query = "UPDATE faculty SET status = 'archived', user_added = NULL WHERE faculty_id = ?";
            $update_stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($update_stmt, 'i', $faculty_id);
            mysqli_stmt_execute($update_stmt);

            $_SESSION['status'] = 'Faculty Denied';
            $_SESSION['status_code'] = "success";
            header("Location: user_faculty_approval.php");
            exit(0);
        } else {
            $_SESSION['status'] = "Failed to send email.";
            $_SESSION['status_code'] = "error";
            header("Location: user_faculty_approval.php");
            exit(0);
        }
    } else {
        $_SESSION['status'] = 'Email not found.';
        $_SESSION['status_code'] = "error";
        header("Location: user_faculty_approval.php");
        exit(0);
    }
}
# ======================================== End Deny Code ==============================


# ======================================== Approve Code ==============================
// Function to send email using PHPMailer
function sendApproveEmail($faculty_email)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // Replace with environment-stored credentials
        $mail->Username = 'mcclearningresourcecenter2.0@gmail.com';
        $mail->Password = 'oenz pxyh ohro zevi';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email details
        $mail->setFrom('mcclearningresourcecenter2.0@gmail.com', 'MCC Learning Resource Center');
        $mail->addAddress($faculty_email);

        $mail->isHTML(true);
        $mail->Subject = 'Account Approved Notification';
        $mail->Body = " <html>
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
                        <h1 style='color:#198754;text-align:center;'>Your Account has been Approved.</h1>
                        <p>Dear Faculty/Staff,</p>
                        <p>Your MCC-LRC account registration has been approved. You can now log in to your account.</p>
                        <p><a  style='color: white;' href='http://mcc-lrc.com/login.php' class='button'>Login</a></p>
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
if (isset($_POST['approved'])) {
    $faculty_id = mysqli_real_escape_string($con, $_POST['faculty_id']);

    // Fetch the student's email
    $email_query = "SELECT email FROM faculty WHERE faculty_id=?";
    $stmt = mysqli_prepare($con, $email_query);
    mysqli_stmt_bind_param($stmt, 'i', $faculty_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($email_row = mysqli_fetch_assoc($result)) {
        $faculty_email = $email_row['email'];

        // Send the email
        if (sendApproveEmail($faculty_email)) {
            // Update the user's status
            $update_query = "UPDATE faculty SET status = 'approved' WHERE faculty_id = ?";
            $update_stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($update_stmt, 'i', $faculty_id);
            mysqli_stmt_execute($update_stmt);

            $_SESSION['status'] = 'Faculty approved successfully';
            $_SESSION['status_code'] = "success";
            header("Location: user_faculty_approval.php");
            exit(0);
        } else {
            $_SESSION['status'] = "Failed to send email.";
            $_SESSION['status_code'] = "error";
            header("Location: user_faculty_approval.php");
            exit(0);
        }
    } else {
        $_SESSION['status'] = 'Email not found.';
        $_SESSION['status_code'] = "error";
        header("Location: user_faculty_approval.php");
        exit(0);
    }
}
# ======================================== End Approve Code ==============================


# ======================================== Block Code ==============================
// Function to send email using PHPMailer
function sendBlockEmail($faculty_email)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // Replace with environment-stored credentials
        $mail->Username = 'mcclearningresourcecenter2.0@gmail.com';
        $mail->Password = 'oenz pxyh ohro zevi';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email details
        $mail->setFrom('mcclearningresourcecenter2.0@gmail.com', 'MCC Learning Resource Center');
        $mail->addAddress($faculty_email);

        $mail->isHTML(true);
        $mail->Subject = 'Account Blocked Notification';
        $mail->Body = " <html>
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
                        <h1 style='color:#dc3545;text-align:center;'>Your Account has been Blocked!!!</h1>
                        <p>Dear Faculty/Staff,</p>
                        <p>Your MCC-LRC account has been blocked for a while. Please contact the library for more details.</p>
                        <p>You can also contact us on our facebook page <a href='https://www.facebook.com/MCCLRC' target='_blank'>Madridejos Community College - Learning Resource Center</a>.</p>
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
if (isset($_POST['block_faculty'])) {
    $faculty_id = mysqli_real_escape_string($con, $_POST['block_faculty']);

    // Fetch the student's email
    $email_query = "SELECT email FROM faculty WHERE faculty_id=?";
    $stmt = mysqli_prepare($con, $email_query);
    mysqli_stmt_bind_param($stmt, 'i', $faculty_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($email_row = mysqli_fetch_assoc($result)) {
        $faculty_email = $email_row['email'];

        // Send the email
        if (sendBlockEmail($faculty_email)) {
            // Update the user's status
            $update_query = "UPDATE faculty SET status = 'blocked' WHERE faculty_id = ?";
            $update_stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($update_stmt, 'i', $faculty_id);
            mysqli_stmt_execute($update_stmt);

            $_SESSION['status'] = 'Faculty has been blocked successfully.';
            $_SESSION['status_code'] = "success";
            header("Location: user_faculty.php");
            exit(0);
        } else {
            $_SESSION['status'] = "Failed to send email.";
            $_SESSION['status_code'] = "error";
            header("Location: user_faculty.php");
            exit(0);
        }
    } else {
        $_SESSION['status'] = 'Email not found.';
        $_SESSION['status_code'] = "error";
        header("Location: user_faculty.php");
        exit(0);
    }
}
# ======================================== End Block Code ==============================



# ======================================== Unblock Code ==============================
// Function to send email using PHPMailer
function sendUnblockEmail($faculty_email)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // Replace with environment-stored credentials
        $mail->Username = 'mcclearningresourcecenter2.0@gmail.com';
        $mail->Password = 'oenz pxyh ohro zevi';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email details
        $mail->setFrom('mcclearningresourcecenter2.0@gmail.com', 'MCC Learning Resource Center');
        $mail->addAddress($faculty_email);

        $mail->isHTML(true);
        $mail->Subject = 'Account Unblocked Notification';
        $mail->Body = " <html>
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
                        <h1 style='color:#198754;text-align:center;'>Your Account has been Unblocked.</h1>
                        <p>Dear Faculty/Staff,</p>
                        <p>Your MCC-LRC account has been unblocked. You can now log in to your account.</p>
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
if (isset($_POST['unblock_faculty'])) {
    $faculty_id = mysqli_real_escape_string($con, $_POST['unblock_faculty']);

    // Fetch the student's email
    $email_query = "SELECT email FROM faculty WHERE faculty_id=?";
    $stmt = mysqli_prepare($con, $email_query);
    mysqli_stmt_bind_param($stmt, 'i', $faculty_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($email_row = mysqli_fetch_assoc($result)) {
        $faculty_email = $email_row['email'];

        // Send the email
        if (sendBlockEmail($faculty_email)) {
            // Update the faculty's status
            $update_query = "UPDATE faculty SET status = 'approved' WHERE faculty_id = ?";
            $update_stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($update_stmt, 'i', $faculty_id);
            mysqli_stmt_execute($update_stmt);

            $_SESSION['status'] = 'Faculty has been unblocked successfully.';
            $_SESSION['status_code'] = "success";
            header("Location: user_faculty.php");
            exit(0);
        } else {
            $_SESSION['status'] = "Failed to send email.";
            $_SESSION['status_code'] = "error";
            header("Location: user_faculty.php");
            exit(0);
        }
    } else {
        $_SESSION['status'] = 'Email not found.';
        $_SESSION['status_code'] = "error";
        header("Location: user_faculty.php");
        exit(0);
    }
}
# ======================================== End Unblock Code ==============================



# ======================================== Delete Code ==============================
// Function to send email using PHPMailer
function sendDeleteEmail($faculty_email, $delete_reason)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // Use environment-stored credentials
        $mail->Username = 'mcclearningresourcecenter2.0@gmail.com'; // Replace with environment variable
        $mail->Password = 'oenz pxyh ohro zevi'; // Replace with environment variable

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email details
        $mail->setFrom('mcclearningresourcecenter2.0@gmail.com', 'MCC Learning Resource Center');
        $mail->addAddress($faculty_email);

        $safe_reason = htmlspecialchars($delete_reason, ENT_QUOTES, 'UTF-8');

        $mail->isHTML(true);
        $mail->Subject = 'Account Delete Notification';
        $mail->Body = " <html>
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
                            <h1 style='color:#dc3545;text-align:center;'>Your Account has been Deleted!!!</h1>
                            <p>Dear Faculty/Staff,</p>
                            <p>Your MCC-LRC account has been deleted. Below is the reason for deletion:</p>
                            <p><strong>Reason:</strong> {$safe_reason}</p>
                            <p>Please contact the library for more details.</p>
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
if (isset($_POST['delete_faculty_id'])) {
    $faculty_id = mysqli_real_escape_string($con, $_POST['delete_faculty_id']);
    $delete_reason = mysqli_real_escape_string($con, $_POST['delete_reason']);

    // Fetch the student's email
    $email_query = "SELECT email FROM faculty WHERE faculty_id=?";
    $stmt = mysqli_prepare($con, $email_query);
    mysqli_stmt_bind_param($stmt, 'i', $faculty_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($email_row = mysqli_fetch_assoc($result)) {
        $faculty_email = $email_row['email'];

        // Send the email
        if (sendDeleteEmail($faculty_email, $delete_reason)) {
            // Update the MS account status
            $used_query = "UPDATE ms_account SET used=0 WHERE username=?";
            $stmt = mysqli_prepare($con, $used_query);
            mysqli_stmt_bind_param($sfaculty_email);
            mysqli_stmt_execute($stmt);

            // Delete the user
            $query = "DELETE FROM faculty WHERE faculty_id=?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, 'i', $faculty_id);
            mysqli_stmt_execute($stmt);

            $_SESSION['status'] = 'Faculty Deleted Successfully';
            $_SESSION['status_code'] = "success";
            header("Location: user_faculty.php");
            exit(0);
        } else {
            $_SESSION['status'] = "Failed to send email.";
            $_SESSION['status_code'] = "error";
            header("Location: user_faculty.php");
            exit(0);
        }
    } else {
        $_SESSION['status'] = 'Email not found.';
        $_SESSION['status_code'] = "error";
        header("Location: user_faculty.php");
        exit(0);
    }
}
# ======================================== End Delete Code ==============================



# ======================================== Edit Faculty Code =============================
// Edit Faculty
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_faculty_id'])) {
        $facultyId = mysqli_real_escape_string($con, $_POST['edit_faculty_id']);
        $lName = mysqli_real_escape_string($con, $_POST['edit_last_name']);
        $fName = mysqli_real_escape_string($con, $_POST['edit_first_name']);
        $mName = mysqli_real_escape_string($con, $_POST['edit_middle_name']);

        $sql = "UPDATE faculty SET firstname='$fName', lastname='$lName', middlename='$mName' WHERE faculty_id='$facultyId'";
        if (mysqli_query($con, $sql)) {
            $_SESSION['status'] = "Updated successfully.";
            $_SESSION['status_code'] = "success";
            header('Location: user_faculty.php');
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($con);
        }
    }
}
# ======================================== End Edit Faculty Code =============================


if (isset($_GET['id'])) {
    $facultyId = $_GET['id'];

    $facultyId = mysqli_real_escape_string($con, $facultyId);

    $sql = "SELECT * FROM faculty WHERE faculty_id = '$facultyId'";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode($row);
    } else {
        $_SESSION['status'] = "faculty not found";
        $_SESSION['status_code'] = "error";
        header('Location: user_faculty.php');
        exit();
    }
} else {
    $_SESSION['status'] = "Invalid request: No ID provided";
    $_SESSION['status_code'] = "error";
    header('Location: user_faculty.php');
    exit();
}
?>
