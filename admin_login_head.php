<?php
// Ensure session cookie expires when the browser closes (prevents auto-login after browser restart)
ini_set('session.cookie_lifetime', 0);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
session_start();

// Prevent browser/proxy caching of login page (security: prevents stale cached login forms)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Wed, 11 Jan 1984 05:00:00 GMT");

include('admin/config/dbcon.php');

// --- Session timeout: destroy session after 30 minutes of inactivity ---
$session_timeout = 1800; // 30 minutes in seconds
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_timeout)) {
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
    session_start();
    session_regenerate_id(true);
    $_SESSION['message_error'] = "Your session has expired. Please log in again.";
    header("Location: admin_login.php");
    exit(0);
}
if (isset($_SESSION['auth'])) {
    $_SESSION['LAST_ACTIVITY'] = time();
}

// --- If already authenticated as Admin/Staff, redirect to dashboard ---
if (isset($_SESSION['auth']) && $_SESSION['auth'] === true &&
    ($_SESSION['auth_role'] === 'Admin' || $_SESSION['auth_role'] === 'Staff')) {
    // Don't redirect if we just set login_success (needed for the JS progress bar flow)
    if (!isset($_SESSION['login_success']) || !$_SESSION['login_success']) {
        header("Location: admin/");
        exit(0);
    }
}

$request = $_SERVER['REQUEST_URI'];

if (strpos($request, '.php') !== false) {
    // Redirect to remove .php extension
    $new_url = str_replace('.php', '', $request);
    header("Location: $new_url", true, 301);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="icon" href="images/mcc-lrc.png">
     <title>MCC Learning Resource Center</title>

     <!-- Alertify JS link -->
     <link rel="stylesheet" href="assets/css/alertify.min.css" />
     <link rel="stylesheet" href="assets/css/alertify.bootstraptheme.min.css" />
     <link rel="stylesheet" href="assets/css/bootstrap-icons.min.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">

     <!-- Iconscout cdn link -->
     <link rel="stylesheet" href="assets/css/line.css">
     <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
     
     <!-- Bootstrap CSS -->
     <link rel="stylesheet" href="assets/css/bootstrap5.min.css" />

     <!-- Bootstrap Icon -->
     <link rel="stylesheet" href="assets/font/bootstrap-icons.css">

     <!-- Custom CSS Styling -->
     <link rel="stylesheet" href="assets/css/login.css">
     <script src="https://hcaptcha.com/1/api.js" async defer></script>
</head>
<body>