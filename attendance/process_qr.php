<?php
session_start();

include('../admin/config/dbcon.php');
include('url.php');

// Set the timezone to the Philippines
date_default_timezone_set('Asia/Manila');

if (isset($_POST['text'])) {
    $qr_code = $_POST['text'];

    // Query to select student based on student_id_no
    $student_query = "SELECT * FROM user WHERE student_id_no = ?";
    $student_query_stmt = $con->prepare($student_query);
    $student_query_stmt->bind_param("s", $qr_code);
    $student_query_stmt->execute();
    $student_query_result = $student_query_stmt->get_result();

    // Query to select faculty based on username
    $faculty_query = "SELECT * FROM faculty WHERE username = ?";
    $faculty_query_stmt = $con->prepare($faculty_query);
    $faculty_query_stmt->bind_param("s", $qr_code);
    $faculty_query_stmt->execute();
    $faculty_query_result = $faculty_query_stmt->get_result();

    $date_log = date("Y-m-d");
    $current_time = date("Y-m-d H:i:s");

    if (mysqli_num_rows($student_query_result) > 0) {
        $user = mysqli_fetch_assoc($student_query_result);

        // Check for existing log entry for today
        $student_id = $user['student_id_no'];
        $log_check_query = "SELECT * FROM user_log WHERE student_id = ? AND date_log = ? AND time_out = ''";
        $log_check_stmt = $con->prepare($log_check_query);
        $log_check_stmt->bind_param("ss", $student_id, $date_log);
        $log_check_stmt->execute();
        $log_check_result = $log_check_stmt->get_result();

        if (mysqli_num_rows($log_check_result) > 0) {
            // Update the existing log with time_out
            $log_update_query = "UPDATE user_log SET time_out = ? WHERE student_id = ? AND date_log = ? AND time_out = ''";
            $log_update_stmt = $con->prepare($log_update_query);
            $log_update_stmt->bind_param("sss", $current_time, $student_id, $date_log);
            $log_update_stmt->execute();

            if ($log_update_stmt->affected_rows > 0) {
                $_SESSION['timeout'] = true;
                header("Location:.");
                exit();
            } else {
                header("Location:.");
                exit("Failed to update time out for student.");
            }
        } else {
            // Insert student log into user_log table
            $firstname = $user['firstname'];
            $middlename = $user['middlename'];
            $lastname = $user['lastname'];
            $course = $user['course'];
            $year_level = $user['year_level'];

            $log_insert_query = "INSERT INTO user_log (student_id, firstname, middlename, lastname, time_log, date_log, time_out, course, year_level, role) 
                                 VALUES (?, ?, ?, ?, ?, ?, '', ?, ?, 'student')";
            $log_insert_stmt = $con->prepare($log_insert_query);
            $log_insert_stmt->bind_param("ssssssss", $student_id, $firstname, $middlename, $lastname, $current_time, $date_log, $course, $year_level);
            $log_insert_stmt->execute();

            if ($log_insert_stmt->affected_rows > 0) {
                $encrpt_id = encryptor('encrypt', $student_id);
                header("Location: view.php?a=" . urlencode($encrpt_id));
                exit();
            } else {
                header("Location:.");
                exit("Failed to insert log for student.");
            }
        }
    } elseif (mysqli_num_rows($faculty_query_result) > 0) {
        $user = mysqli_fetch_assoc($faculty_query_result);

        // Check for existing log entry for today
        $username = $user['username'];
        $log_check_query = "SELECT * FROM user_log WHERE student_id = ? AND date_log = ? AND time_out = ''";
        $log_check_stmt = $con->prepare($log_check_query);
        $log_check_stmt->bind_param("ss", $username, $date_log);
        $log_check_stmt->execute();
        $log_check_result = $log_check_stmt->get_result();

        if (mysqli_num_rows($log_check_result) > 0) {
            // Update the existing log with time_out
            $log_update_query = "UPDATE user_log SET time_out = ? WHERE student_id = ? AND date_log = ? AND time_out = ''";
            $log_update_stmt = $con->prepare($log_update_query);
            $log_update_stmt->bind_param("sss", $current_time, $username, $date_log);
            $log_update_stmt->execute();

            if ($log_update_stmt->affected_rows > 0) {
                $_SESSION['timeout'] = true;
                header("Location:.");
                exit();
            } else {
                header("Location:.");
                exit("Failed to update time out for faculty.");
            }
        } else {
            // Insert faculty log into user_log table
            $firstname = $user['firstname'];
            $middlename = $user['middlename'];
            $lastname = $user['lastname'];
            $course = $user['course'];

            $log_insert_query = "INSERT INTO user_log (student_id, firstname, middlename, lastname, time_log, date_log, time_out, course, role) 
                                 VALUES (?, ?, ?, ?, ?, ?, '', ?, 'faculty')";
            $log_insert_stmt = $con->prepare($log_insert_query);
            $log_insert_stmt->bind_param("sssssss", $username, $firstname, $middlename, $lastname, $current_time, $date_log, $course);
            $log_insert_stmt->execute();

            if ($log_insert_stmt->affected_rows > 0) {
                $encrpt_user = encryptor('encrypt', $username);
                header("Location: view_faculty.php?a=" . urlencode($encrpt_user));
                exit();
            } else {
                header("Location:.");
                exit("Failed to insert log for faculty.");
            }
        }
    } else {
        exit("User not found");
    }
} else {
    exit("No QR code provided");
}
?>