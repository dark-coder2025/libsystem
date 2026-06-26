<?php
session_start();

include('admin/config/dbcon.php');

if (isset($_SESSION['auth_stud']['stud_id'])) {
    $id_session = $_SESSION['auth_stud']['stud_id'];
} elseif (isset($_SESSION['auth_faculty']['faculty_id'])) {
    $id_session = $_SESSION['auth_faculty']['faculty_id'];
}

if (isset($_POST['save_changes'])) {
    $firstname = mysqli_real_escape_string($con, $_POST['firstname']);
    $middlename = mysqli_real_escape_string($con, $_POST['middlename']);
    $lastname = mysqli_real_escape_string($con, $_POST['lastname']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $contact_person = mysqli_real_escape_string($con, $_POST['contact_person']);
    $contact_person_cell = mysqli_real_escape_string($con, $_POST['contact_person_cell']);


    // Update profile for user or faculty
    if (isset($_SESSION['auth_stud']['stud_id'])) {
        // Update user profile
        $query = "UPDATE `user` SET firstname='$firstname', middlename='$middlename', lastname='$lastname', address='$address', cell_no='$phone', email='$email', contact_person='$contact_person', person_cell_no='$contact_person_cell' WHERE user_id ='$id_session'";
    } else {
        // Update faculty profile
        $query = "UPDATE `faculty` SET firstname='$firstname', middlename='$middlename', lastname='$lastname', address='$address', cell_no='$phone', email='$email', contact_person='$contact_person', person_cell_no='$contact_person_cell' WHERE faculty_id ='$id_session'";
    }
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['status'] = "Updated Successfully";
        $_SESSION['status_code'] = "success";
        header("Location: myprofile.php");
        exit(0);
    } else {
        $_SESSION['status'] = "Not Updated";
        $_SESSION['status_code'] = "error";
        header("Location: myprofile.php");
        exit(0);
    }
}

if (isset($_POST['logout_btn'])) {
    // Clear all session data
    $_SESSION = array();

    // Clear the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Destroy the session completely
    session_destroy();

    // Start a new session to carry the success message
    session_start();
    $_SESSION['message_success'] = "Logout Successfully";
    header("Location: .");
    exit(0);
}
?>
