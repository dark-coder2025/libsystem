<?php
include('authentication.php');

// ---- Delete a single error log entry ----
if (isset($_POST['delete_btn'])) {
    $error_id = (int)$_POST['error_id'];

    $stmt = $con->prepare("DELETE FROM error_logs WHERE id = ?");
    $stmt->bind_param("i", $error_id);
    
    if ($stmt->execute()) {
        $_SESSION['status'] = "Error log entry deleted successfully.";
        $_SESSION['status_code'] = "success";
    } else {
        $_SESSION['status'] = "Failed to delete error log entry.";
        $_SESSION['status_code'] = "error";
    }

    header("Location: error_logs.php");
    exit(0);
}

// ---- Clear all error log entries ----
if (isset($_POST['clear_all_btn'])) {
    // Only Admin role can clear all logs
    if ($_SESSION['auth_role'] !== 'Admin') {
        $_SESSION['status'] = "Only Admins can clear all error logs.";
        $_SESSION['status_code'] = "warning";
        header("Location: error_logs.php");
        exit(0);
    }

    $con->query("TRUNCATE TABLE error_logs");

    $_SESSION['status'] = "All error logs have been cleared.";
    $_SESSION['status_code'] = "success";

    header("Location: error_logs.php");
    exit(0);
}

// If accessed directly without action, redirect back
header("Location: error_logs.php");
exit(0);
?>
