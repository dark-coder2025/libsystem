<?php
include('includes/url.php');

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
     <meta charset="UTF-8" />
     <meta http-equiv="X-UA-Compatible" content="IE=edge" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0" />
     <meta name="robots" content="noindex, nofollow" />
     <link rel="icon" href="./images/mcc-lrc.png">
     <title>MCC Learning Resource Center</title>
     <link href="https://fonts.gstatic.com" rel="preconnect" />
     <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
          rel="stylesheet" />
     <!-- Bootstrap CSS -->
     <link href="assets/css/bootstrap.min.css" rel="stylesheet" />

     <!-- Boxicons Icon -->
     <link href="assets/css/boxicons.min.css" rel="stylesheet" />

     <!-- Remixicon Icon -->
     <link href="assets/css/remixicon.css" rel="stylesheet" />

     <!-- Bootstrap Icon -->
     <link rel="stylesheet" href="assets/font/bootstrap-icons.css">

     <!-- Alertify JS link -->
     <link rel="stylesheet" href="assets/css/alertify.min.css" />
     <link rel="stylesheet" href="assets/css/alertify.bootstraptheme.min.css" />
     
     <!-- Datatables -->
     <link rel="stylesheet" href="assets/css/bootstrap.min.css">
     <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
     <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.5.0/css/rowReorder.dataTables.css">
     <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.css">

     <!-- Custom CSS -->
     <link href="assets/css/style.css" rel="stylesheet" />
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.0/dist/sweetalert2.min.css">
     <link href="assets/css/sweetalert2.min.css" rel="stylesheet" />

     <!-- Animation -->
     <link rel="stylesheet" href="https://www.cssportal.com/css-loader-generator/" />
     <!-- Loader -->
     <link rel="stylesheet" href="https://www.cssportal.com/css-loader-generator/" />

     <link rel="stylesheet" href="assets/css/bootstrap-datepicker.min.css">

     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>


</head>

<body>


     <?php include('./includes/topnav.php'); ?>