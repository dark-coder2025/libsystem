<?php
include('authentication.php');
include('includes/url.php');
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

    $faculty_id = mysqli_real_escape_string($con, $_POST['faculty_id']);
    $deny_reason = mysqli_real_escape_string($con, $_POST['deny_reason']);

    $email_query = "SELECT email FROM faculty WHERE faculty_id=?";
    $stmt = mysqli_prepare($con, $email_query);
    mysqli_stmt_bind_param($stmt, 'i', $faculty_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $email_row = mysqli_fetch_assoc($result);

        if ($email_row) {
            $faculty_email = $email_row['email'];
            $fac_email = encryptor('encrypt', $faculty_email);
            
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
                            <p>Dear Faculty/Staff,</p>
                            <p>Your MCC-LRC account registration has been denied. Below is the reason for denial:</p>
                            <p><strong>Reason:</strong> {$deny_reason}</p>
                            <p>Click this button to update the reason why you deny:</p>
                            <p><a style='color: white;' href='https://mcc-lrc.com/signup_update.php?a=$fac_email' class='button'>Update</a></p>
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

            sendEmail($faculty_email, $subject, $message);

            $update_query = "UPDATE faculty SET status = 'archived' WHERE faculty_id = ?";
            $update_stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($update_stmt, 'i', $faculty_id);
            mysqli_stmt_execute($update_stmt);

            $_SESSION['status'] = 'Faculty Denied';
            $_SESSION['status_code'] = "success";
            header("Location: user_faculty_approval.php");
            exit(0);
        } else {
            $_SESSION['status'] = 'Email Failed to Send';
            $_SESSION['status_code'] = "error";
            header("Location: user_faculty_approval.php");
            exit(0);
        }
}

// Student Approval
if(isset($_POST['approved'])) {
    $faculty_id = $_POST['faculty_id'];

    // Fetch faculty email
    $email_query = "SELECT email FROM faculty WHERE faculty_id='$faculty_id'";
    $email_result = mysqli_query($con, $email_query);
    $email_row = mysqli_fetch_assoc($email_result);
    $faculty_email = $email_row['email'];

    $query = "UPDATE faculty SET status = 'approved' WHERE faculty_id = '$faculty_id'";
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
                        <p>Dear Faculty/Staff,</p>
                        <p>Your MCC-LRC account registration has been approved. You can now log in to your account.</p>
                        <p><a  style='color: white;' href='http://mcc-lrc.com/login.php' class='button'>Login</a></p>
                        <p>Thank you.</p>
                    </div>
                </div>
            </body>
        </html>
        ";
        sendEmail($faculty_email, $subject, $message);

        $_SESSION['status'] = ' Approved successfully';
        $_SESSION['status_code'] = "success";
        header("Location: user_faculty_approval.php");
        exit(0);
    } else {
        $_SESSION['status'] = 'Faculty not approved';
        $_SESSION['status_code'] = "error";
        header("Location: user_faculty_approval.php");
        exit(0);
    }
}

// Block faculty
if(isset($_POST['block_faculty'])) {
    $faculty_id = $_POST['block_faculty'];
    $query = "UPDATE faculty SET status='blocked' WHERE faculty_id='$faculty_id'";
    $query_run = mysqli_query($con, $query);

    if($query_run) {
        // Fetch faculty email
        $email_query = "SELECT email FROM faculty WHERE faculty_id='$faculty_id'";
        $email_result = mysqli_query($con, $email_query);
        $email_row = mysqli_fetch_assoc($email_result);
        $faculty_email = $email_row['email'];

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
                        <p>Dear Faculty/Staff,</p>
                        <p>Your MCC-LRC account has been blocked for a while. Please contact the library for more details.</p>
                        <p>You can also contact us on our facebook page <a href='https://www.facebook.com/MCCLRC' target='_blank'>Madridejos Community College - Learning Resource Center</a>.</p>
                        <p>Thank you.</p>
                    </div>
                </div>
            </body>
        </html>
        ";

        sendEmail($faculty_email, $subject, $message);

        $_SESSION['status'] = "Faculty staff has been blocked successfully.";
        $_SESSION['status_code'] = "success";
        header("Location: user_faculty.php");
        exit(0);
    } else {
        $_SESSION['status'] = "Something went wrong.";
        $_SESSION['status_code'] = "error";
        header("Location: user_faculty.php");
        exit(0);
    }
}

// Unblock faculty
if(isset($_POST['unblock_faculty'])) {
    $faculty_id = $_POST['unblock_faculty'];
    $query = "UPDATE faculty SET status='approved' WHERE faculty_id='$faculty_id'";
    $query_run = mysqli_query($con, $query);

    if($query_run) {
        // Fetch faculty email
        $email_query = "SELECT email FROM faculty WHERE faculty_id='$faculty_id'";
        $email_result = mysqli_query($con, $email_query);
        $email_row = mysqli_fetch_assoc($email_result);
        $faculty_email = $email_row['email'];

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
                        <p>Dear Faculty/Staff,</p>
                        <p>Your MCC-LRC account has been unblocked. You can now log in to your account.</p>
                        <p>Thank you.</p>
                    </div>
                </div>
            </body>
        </html>
        ";

        sendEmail($faculty_email, $subject, $message);

        $_SESSION['status'] = "Faculty staff has been unblocked successfully.";
        $_SESSION['status_code'] = "success";
        header("Location: user_faculty.php");
        exit(0);
    } else {
        $_SESSION['status'] = "Something went wrong.";
        $_SESSION['status_code'] = "error";
        header("Location: user_faculty.php");
        exit(0);
    }
}

// Delete Action
if (isset($_POST['delete_faculty_id'])) {

    $faculty_id = mysqli_real_escape_string($con, $_POST['delete_faculty_id']);
    $delete_reason = mysqli_real_escape_string($con, $_POST['delete_reason']);

    // Fetch the faculty's email
    $email_query = "SELECT email FROM faculty WHERE faculty_id=?";
    $stmt = mysqli_prepare($con, $email_query);
    mysqli_stmt_bind_param($stmt, 'i', $faculty_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $email_row = mysqli_fetch_assoc($result);

    if ($email_row) {
        $faculty_email = $email_row['email'];

        // Update the MS account status
        $used_query = "UPDATE ms_account SET used=0 WHERE username=?";
        $stmt = mysqli_prepare($con, $used_query);
        mysqli_stmt_bind_param($stmt, 's', $faculty_email);
        mysqli_stmt_execute($stmt);

        // Delete the faculty
        $query = "DELETE FROM faculty WHERE faculty_id=?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $faculty_id);
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
                            <p>Dear Faculty,</p>
                            <p>Your MCC-LRC account has been deleted. Below is the reason for deletion:</p>
                            <p><strong>Reason:</strong> {$delete_reason}</p>
                            <p>Please contact the library for more details.</p>
                            <p>You can also contact us on our Facebook page <a href='https://www.facebook.com/MCCLRC' target='_blank'>Madridejos Community College - Learning Resource Center</a>.</p>
                            <p>Thank you.</p>
                        </div>
                    </div>
                </body>
            </html>";

            sendEmail($faculty_email, $subject, $message);
                $_SESSION['status'] = 'Faculty Deleted Successfully';
                $_SESSION['status_code'] = "success";
                header("Location: user_faculty.php");
                exit(0);
            } else {
                $_SESSION['status'] = 'Failed to delete faculty';
                $_SESSION['status_code'] = "error";
                header("Location: user_faculty.php");
                exit(0);
            }
    } else {
        $_SESSION['status'] = 'Faculty Not Found';
        $_SESSION['status_code'] = "error";
        header("Location: user_faculty.php");
        exit(0);
    }
}


// Edit Faculty
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are set
    if (isset($_POST['edit_last_name'], $_POST['edit_first_name'], $_POST['edit_user_name'])) {
        // Sanitize and validate input to prevent SQL injection
        $faculty_id = mysqli_real_escape_string($con, $_POST['edit_faculty_id']);
        $lName = mysqli_real_escape_string($con, $_POST['edit_last_name']);
        $fName = mysqli_real_escape_string($con, $_POST['edit_first_name']);
        $mName = mysqli_real_escape_string($con, $_POST['edit_middle_name']);
        $username = mysqli_real_escape_string($con, $_POST['edit_user_name']); // Corrected variable name for student ID no.

        // Prepare the SQL UPDATE query
        $sql = "UPDATE faculty 
                SET lastname = '$lName', 
                    firstname = '$fName',
                    middlename = '$mName', 
                    username = '$username' 
                WHERE faculty_id = '$faculty_id'";

        // Execute the query
        if (mysqli_query($con, $sql)) {
            // Generate QR Code
            $identifier = $username; // Adjust username if needed for faculty
            $qrdata = "$identifier"; // Example data to encode in QR code
            $qrfile = "../qrcodes/$identifier.png"; // Path to save QR code image
            $qrimage = "$identifier.png";
            QRcode::png($qrdata, $qrfile); // Generate QR code

            // Insert QR code path into database
            $qr_update_query = "UPDATE faculty SET qr_code = ? WHERE faculty_id = ?";
            $stmt_qr_update = mysqli_prepare($con, $qr_update_query);
            mysqli_stmt_bind_param($stmt_qr_update, 'si', $qrimage, $faculty_id);
            mysqli_stmt_execute($stmt_qr_update);

            // Set success message in the session and redirect
            $_SESSION['status'] = "Updated successfully.";
            $_SESSION['status_code'] = "success";
            header('Location: user_faculty.php');
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

if (isset($_GET['id'])) {
    $faculty_id = $_GET['id'];

    $faculty_id = mysqli_real_escape_string($con, $faculty_id);

    $sql = "SELECT * FROM faculty WHERE faculty_id = '$faculty_id'";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode($row);
    } else {
        $_SESSION['status'] = "Faculty not found";
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
