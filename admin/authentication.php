<?php
ini_set('session.cookie_httponly', 1);
session_start();
include('config/dbcon.php');

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
