<?php 
ini_set('session.cookie_httponly', 1);
session_start();
include('./admin/config/dbcon.php');
include('includes/url.php');

// Check if 'code' is provided in the URL
if (!isset($_GET['code']) || empty($_GET['code'])) {
    header("Location: 404.php");
    exit;
}

$code = encryptor('decrypt', $_GET['code']);

// Prepare query to fetch the verification code and its creation time
$code_query = "SELECT username, created_at FROM ms_account WHERE verification_code = ?";
$code_stmt = $con->prepare($code_query);
$code_stmt->bind_param("s", $code);
$code_stmt->execute();
$code_result = $code_stmt->get_result();

if ($code_result->num_rows > 0) {
    $code_row = $code_result->fetch_assoc();
    $created_at = new DateTime($code_row['created_at']); // Creation time of the verification code
    $current_time = new DateTime(); // Current time

    // Check if the difference is greater than 1 Day
    $created_at_timestamp = $created_at->getTimestamp();
    $current_time_timestamp = $current_time->getTimestamp();

    if (($current_time_timestamp - $created_at_timestamp) > 86400) { // 86400 seconds = 1 Day
        header("Location: expire.php");
        exit;
    }
} else {
    header("Location: 404.php");
    exit;
}

$code_stmt->close();
?>
