<?php
// delete_borrow.php

// Include necessary files (database connection, PHPMailer, etc.)
include('authentication.php'); // Assuming you have a config file for database connection

// Check if the borrow_id is set and valid
if (isset($_POST['borrow_id'])) {
    $borrow_id = $_POST['borrow_id'];

    // Sanitize the borrow_id to prevent SQL injection
    $borrow_id = mysqli_real_escape_string($con, $borrow_id);

    // SQL query to delete the record from the 'borrow_book' table
    $delete_query = "DELETE FROM borrow_book WHERE borrow_book_id = '$borrow_id'";

    // Execute the delete query
    if (mysqli_query($con, $delete_query)) {
        // If the deletion is successful, redirect back to the previous page or display a success message
        echo "<script>alert('Record deleted successfully.'); window.location.href = 'circulation_borrow.php';</script>";
    } else {
        // If the deletion fails, display an error message
        echo "<script>alert('Error deleting record: " . mysqli_error($con) . "'); window.location.href = 'circulation_borrow.php';</script>";
    }
} else {
    // If the borrow_id is not set, display an error message
    echo "<script>alert('Invalid request.'); window.location.href = 'circulation_borrow.php';</script>";
}
?>
