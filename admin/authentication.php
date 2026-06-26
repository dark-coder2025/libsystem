<?php
// Ensure session cookie expires when the browser closes (prevents auto-login after browser restart)
ini_set('session.cookie_lifetime', 0);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
session_start();

// Prevent browser/proxy caching of admin pages (defense-in-depth against auto-login via cache)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Wed, 11 Jan 1984 05:00:00 GMT");

include('config/dbcon.php');

// --- Session timeout: destroy session after 30 minutes of inactivity ---
$session_timeout = 1800; // 30 minutes in seconds
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_timeout)) {
    // Session has expired due to inactivity — destroy completely
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
    session_start();
    session_regenerate_id(true);
    $_SESSION['message_error'] = "Your session has expired. Please log in again.";
    header("Location: ../admin_login.php");
    exit(0);
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity timestamp

// --- Session fixation protection: regenerate session ID every 30 minutes ---
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} else if (time() - $_SESSION['CREATED'] > $session_timeout) {
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}

if(!isset($_SESSION['auth']))
{
  $_SESSION['message_error'] = "Login to Access Dashboard";
  header("Location:../admin_login.php");
  exit(0);
}
else
{
  if($_SESSION['auth_role'] != "Admin" && $_SESSION['auth_role'] != "Staff")
  {
    $_SESSION['message_error'] = "<small>You are not authorized to access this page</small>";
    header("Location:../admin_login.php");
    exit(0);
  }
}
?>
