<?php
ini_set('session.cookie_httponly', 1);
session_start();

// Prevent browser/proxy caching of login page (security: prevents stale cached login forms)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Wed, 11 Jan 1984 05:00:00 GMT");

include('./admin/config/dbcon.php');

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
     <link rel="icon" href="./images/mcc-lrc.png">
     <title>MCC Learning Resource Center</title>
     <script src="https://hcaptcha.com/1/api.js" async defer></script>

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

     <style>
          .back {
               font-size: 30px;
               color: black;
          }
          .back:hover {
               color: gray;
          }
          
          @media (max-width: 900px) {
               #admin {
                    display: none;
               }
          }
     </style>
</head>

<body>