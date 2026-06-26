<?php
session_start();


include('admin/config/dbcon.php');


if (isset($_SESSION['auth_stud']['stud_id']))
{
     $id_session=$_SESSION['auth_stud']['stud_id'];

 }
                
          


 if (isset($_POST['save_changes'])) {
     $firstname = $_POST['firstname'];
     $middlename = $_POST['middlename'];
     $lastname = $_POST['lastname'];
     $address = $_POST['address'];
     $phone = $_POST['phone'];
     $email = $_POST['email'];
 
     // Prepare the update user information query
     $query = "UPDATE user SET firstname = ?, middlename = ?, lastname = ?, address = ?, cell_no = ?, email = ? WHERE user_id = ?";
     $stmt = $con->prepare($query);
     $stmt->bind_param("ssssssi", $firstname, $middlename, $lastname, $address, $phone, $email, $id_session);
     $query_run = $stmt->execute();
 
     if ($query_run) {
         $_SESSION['message_success'] = 'Updated Successfully';
         header("Location: myprofile.php");
         exit(0);
     } else {
         $_SESSION['message_error'] = 'Not Updated';
         header("Location: myprofile.php");
         exit(0);
     }
 }
 
 // Logout
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

    // Redirect to the login page
    header("Location: ../admin_login.php");
    exit(0);
}

?>