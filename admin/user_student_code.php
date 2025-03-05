<?php
include('authentication.php');
include('includes/url.php');
require_once('../qrcode/qrlib.php');
header('Content-Type: application/json');
use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
    require 'phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require 'phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

function sendEmail($student_email, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'resourcecentermcclearning@gmail.com';
            $mail->Password   = 'gaai wmiv oqql rhvu'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port       = 587;

            $mail->setFrom('resourcecentermcclearning@gmail.com', 'MCC Learning Resource Center');
            $mail->addAddress($student_email); 

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}

if (isset($_POST['deny'])) {

    $student_id = mysqli_real_escape_string($con, $_POST['user_id']);
    $deny_reason = mysqli_real_escape_string($con, $_POST['deny_reason']);

    $email_query = "SELECT email FROM user WHERE user_id=?";
    $stmt = mysqli_prepare($con, $email_query);
    mysqli_stmt_bind_param($stmt, 'i', $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $email_row = mysqli_fetch_assoc($result);

        if ($email_row) {
            $student_email = $email_row['email'];
            $stu_email = encryptor('encrypt', $student_email);
            
            $subject = "Account Denied Notification";
            $message = " <html>
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
                            <p><strong>Reason:</strong> <b>{$deny_reason}</b></p>
                            <p>Click this button to update the reason why you deny:</p>
                            <p><a style='color: white;' href='https://mcc-lrc.com/signup_update.php?a=$stu_email' class='button'>Update</a></p>
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

            sendEmail($student_email, $subject, $message);

            $update_query = "UPDATE user SET status = 'archived', user_added = NULL WHERE user_id = ?";
            $update_stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($update_stmt, 'i', $student_id);
            mysqli_stmt_execute($update_stmt);

            $_SESSION['status'] = 'Student Denied';
            $_SESSION['status_code'] = "success";
            header("Location: user_student_approval.php");
            exit(0);
        } else {
            $_SESSION['status'] = 'Email Failed to Send';
            $_SESSION['status_code'] = "error";
            header("Location: user_student_approval.php");
            exit(0);
        }
}

// Student Approval
if(isset($_POST['approved'])) {
    $student_id = $_POST['user_id'];

    // Fetch student email
    $email_query = "SELECT email FROM user WHERE user_id='$student_id'";
    $email_result = mysqli_query($con, $email_query);
    $email_row = mysqli_fetch_assoc($email_result);
    $student_email = $email_row['email'];

    $query = "UPDATE user SET status = 'approved' WHERE user_id = '$student_id'";
    $query_run = mysqli_query($con, $query);

    if($query_run) {
        // Send email notification
        $subject = "Account Approved Notification";
        $message = " <html>
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
                        <p>Dear Student,</p>
                        <p>Your MCC-LRC account registration has been approved. You can now log in to your account.</p>
                        <p><a  style='color: white;' href='http://mcc-lrc.com/login.php' class='button'>Login</a></p>
                        <p>Thank you.</p>
                    </div>
                </div>
            </body>
        </html>
        ";
        sendEmail($student_email, $subject, $message);

        $_SESSION['status'] = 'Student approved successfully';
        $_SESSION['status_code'] = "success";
        header("Location: user_student_approval.php");
        exit(0);
    } else {
        $_SESSION['status'] = 'Student not approved';
        $_SESSION['status_code'] = "error";
        header("Location: user_student_approval.php");
        exit(0);
    }
}

// Block student
if(isset($_POST['block_student'])) {
    $user_id = $_POST['block_student'];
    $query = "UPDATE user SET status='blocked' WHERE user_id='$user_id'";
    $query_run = mysqli_query($con, $query);

    if($query_run) {
        // Fetch student email
        $email_query = "SELECT email FROM user WHERE user_id='$user_id'";
        $email_result = mysqli_query($con, $email_query);
        $email_row = mysqli_fetch_assoc($email_result);
        $student_email = $email_row['email'];

        // Send email notification
        $subject = "Account Blocked Notification";
        $message = " <html>
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
                        <p>Dear Student,</p>
                        <p>Your MCC-LRC account has been blocked for a while. Please contact the library for more details.</p>
                        <p>You can also contact us on our facebook page <a href='https://www.facebook.com/MCCLRC' target='_blank'>Madridejos Community College - Learning Resource Center</a>.</p>
                        <p>Thank you.</p>
                    </div>
                </div>
            </body>
        </html>
        ";
        sendEmail($student_email, $subject, $message);

        $_SESSION['status'] = "Student has been blocked successfully.";
        $_SESSION['status_code'] = "success";
        header("Location: user_student.php");
        exit(0);
    } else {
        $_SESSION['status'] = "Something went wrong.";
        $_SESSION['status_code'] = "error";
        header("Location: user_student.php");
        exit(0);
    }
}

// Unblock student
if(isset($_POST['unblock_student'])) {
    $user_id = $_POST['unblock_student'];
    $query = "UPDATE user SET status='approved' WHERE user_id='$user_id'";
    $query_run = mysqli_query($con, $query);

    if($query_run) {
        // Fetch student email
        $email_query = "SELECT email FROM user WHERE user_id='$user_id'";
        $email_result = mysqli_query($con, $email_query);
        $email_row = mysqli_fetch_assoc($email_result);
        $student_email = $email_row['email'];

        // Send email notification
        $subject = "Account Unblocked Notification";
        $message = " <html>
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
                        <p>Dear Student,</p>
                        <p>Your MCC-LRC account has been unblocked. You can now log in to your account.</p>
                        <p>Thank you.</p>
                    </div>
                </div>
            </body>
        </html>
        ";

        sendEmail($student_email, $subject, $message);

        $_SESSION['status'] = "Student has been unblocked successfully.";
        $_SESSION['status_code'] = "success";
        header("Location: user_student.php");
        exit(0);
    } else {
        $_SESSION['status'] = "Something went wrong.";
        $_SESSION['status_code'] = "error";
        header("Location: user_student.php");
        exit(0);
    }
}

// Delete Action
if (isset($_POST['delete_student_id'])) {

    $student_id = mysqli_real_escape_string($con, $_POST['delete_student_id']);
    $delete_reason = mysqli_real_escape_string($con, $_POST['delete_reason']);

    // Fetch the user's email
    $email_query = "SELECT email FROM user WHERE user_id=?";
    $stmt = mysqli_prepare($con, $email_query);
    mysqli_stmt_bind_param($stmt, 'i', $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $email_row = mysqli_fetch_assoc($result);

    if ($email_row) {
        $student_email = $email_row['email'];

        // Update the MS account status
        $used_query = "UPDATE ms_account SET used=0 WHERE username=?";
        $stmt = mysqli_prepare($con, $used_query);
        mysqli_stmt_bind_param($stmt, 's', $student_email);
        mysqli_stmt_execute($stmt);

        // Delete the user
        $query = "DELETE FROM user WHERE user_id=?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $student_id);
        $query_run = mysqli_stmt_execute($stmt);

        if ($query_run) {
            // Prepare and send email notification
            $subject = "Account Delete Notification";
            $message = "<html>
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
                            <p>Dear Student,</p>
                            <p>Your MCC-LRC account has been deleted. Below is the reason for deletion:</p>
                            <p><strong>Reason:</strong> <b>{$delete_reason}</b></p>
                            <p>Please contact the library for more details.</p>
                            <p>You can also contact us on our Facebook page <a href='https://www.facebook.com/MCCLRC' target='_blank'>Madridejos Community College - Learning Resource Center</a>.</p>
                            <p>Thank you.</p>
                        </div>
                    </div>
                </body>
            </html>";

            sendEmail($student_email, $subject, $message);
                $_SESSION['status'] = 'Student Deleted Successfully';
                $_SESSION['status_code'] = "success";
                header("Location: user_student.php");
                exit(0);
            } else {
                $_SESSION['status'] = 'Failed to delete student';
                $_SESSION['status_code'] = "error";
                header("Location: user_student.php");
                exit(0);
            }
    } else {
        $_SESSION['status'] = 'Student Not Found';
        $_SESSION['status_code'] = "error";
        header("Location: user_student.php");
        exit(0);
    }
}

// Edit Student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are set
    if (isset($_POST['editLName'], $_POST['editStuID'], $_POST['editFName'])) {
        // Sanitize and validate input to prevent SQL injection
        $studentId = mysqli_real_escape_string($con, $_POST['user_id']);
        $lName = mysqli_real_escape_string($con, $_POST['editLName']);
        $fName = mysqli_real_escape_string($con, $_POST['editFName']);
        $mName = mysqli_real_escape_string($con, $_POST['editMName']);
        $stuIdNo = mysqli_real_escape_string($con, $_POST['editStuID']); // Corrected variable name for student ID no.

        // Prepare the SQL UPDATE query
        $sql = "UPDATE user 
                SET firstname = '$fName', 
                    lastname = '$lName', 
                    middlename = '$mName', 
                    student_id_no = '$stuIdNo' 
                WHERE user_id = '$studentId'";

        // Execute the query
        if (mysqli_query($con, $sql)) {
            // Generate QR Code
            $identifier = $stuIdNo; // Adjust username if needed for faculty
            $qrdata = "$identifier"; // Example data to encode in QR code
            $qrfile = "../qrcodes/$identifier.png"; // Path to save QR code image
            $qrimage = "$identifier.png";
            QRcode::png($qrdata, $qrfile); // Generate QR code

            // Insert QR code path into database
            $update_query = "";
            if ($role_as == 'student') {
                $update_query = "UPDATE user SET qr_code = ? WHERE student_id_no = ?";
            }

            $stmt_update = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($stmt_update, 'ss', $qrimage, $stuIdNo);

            // Set success message in the session and redirect
            $_SESSION['status'] = "Updated successfully.";
            $_SESSION['status_code'] = "success";
            header('Location: user_student.php');
            exit();
        } else {
            // Handle SQL execution errors
            echo "Error updating record: " . mysqli_error($con);
        }
    } else {
        // Handle missing form data
        echo "Error: Missing required fields.";
    }
}

if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];

    $userId = mysqli_real_escape_string($con, $userId);

    $sql = "SELECT * FROM user WHERE user_id = '$userId'";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode($row);
    } else {
        $_SESSION['status'] = "Student not found";
        $_SESSION['status_code'] = "error";
        header('Location: user_student.php');
        exit();
    }
} else {
    $_SESSION['status'] = "Invalid request: No ID provided";
    $_SESSION['status_code'] = "error";
    header('Location: user_student.php');
    exit();
}
?>
