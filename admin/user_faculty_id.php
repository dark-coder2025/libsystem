<?php
include('authentication.php');
include('includes/url.php');

$request = $_SERVER['REQUEST_URI'];

if (strpos($request, '.php') !== false) {
    // Redirect to remove .php extension
    $new_url = str_replace('.php', '', $request);
    header("Location: $new_url", true, 301);
    exit();
}

// Ensure database connection
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

// Fetch and sanitize GET parameter
$id = filter_var(encryptor('decrypt', $_GET['a']), FILTER_VALIDATE_INT);

// Prepare and execute query with parameter binding to prevent SQL injection
$query = mysqli_prepare($con, "SELECT * FROM faculty WHERE faculty_id = ?");
mysqli_stmt_bind_param($query, 'i', $id);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);

// Check if record exists
$row = mysqli_fetch_assoc($result);

if ($row) {
    // Extract necessary data for printing
    $names = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
    $email = htmlspecialchars($row['email']);
    $contact = htmlspecialchars($row['cell_no']);
    $location_address = htmlspecialchars($row['address']);
    $profile = htmlspecialchars($row['profile_image']);
    $qrcode = htmlspecialchars($row['qr_code']);
    $contact_person = htmlspecialchars($row['contact_person']);
    $person_cell_no = htmlspecialchars($row['person_cell_no']);
    $course = htmlspecialchars($row['course']);
    $bdate = htmlspecialchars($row['birthdate']);
    $type = htmlspecialchars($row['role_as']);
} else {
  die("No record found.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8" />
     <meta http-equiv="X-UA-Compatible" content="IE=edge" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0" />
     <meta name="robots" content="noindex, nofollow" />
     <link rel="icon" href="images/mcc-lrc.png">
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
     <link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">

     <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css" />
     <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/buttons/2.3.3/css/buttons.bootstrap5.min.css" />

     <!-- Custom CSS -->
     <link href="assets/css/style.css" rel="stylesheet" />
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.0/dist/sweetalert2.min.css">
     <link href="assets/css/sweetalert2.min.css" rel="stylesheet" />

     <!-- Animation -->
     <link rel="stylesheet" href="https://www.cssportal.com/css-loader-generator/" />
     <!-- Loader -->
     <link rel="stylesheet" href="https://www.cssportal.com/css-loader-generator/" />

     <link rel="stylesheet" href="assets/css/bootstrap-datepicker.min.css">

</head>

<body>

<style>
  @media print {
    #id {
      color: black;
      background: url('images/bg-id.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      -webkit-print-color-adjust: exact;
    }
    #id::before {
      color: black;
      background: url('images/bg-id.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      -webkit-print-color-adjust: exact;
    }
    .id-1 {
      color: black;
      background: url('images/bg-id.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      -webkit-print-color-adjust: exact;
    }
  }
  body {
    background:cornflowerblue; /* Set the background of the body to white */
    color: black;
  }

  #bg {
    height: 450px;
    margin: 60px;
    float: left;
  }

  #id {
    width: 2.125in;  /* Set width to 2.125 inches (credit card height) */
    height: 3.375in;  /* Set height to 3.375 inches (credit card width) */
    position: absolute;
    opacity: 0.88;
    font-family: sans-serif;
    transition: 0.4s;
    background: url('images/bg-front.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.6);
    transition: 0.4s;
}

  .container {
    font-size: 12px;
    font-family: sans-serif;
  }

  .id-1 {
    width: 2.125in;  /* Set width to 2.125 inches (credit card height) */
    height: 3.375in;  /* Set height to 3.375 inches (credit card width) */
    background: url('images/bg-back.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    text-align: center;
    font-size: 12px;
    font-family: sans-serif;
    float: left;
    margin: auto;
    margin-left: 250px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.6);
    transition: 0.4s;
}

  .img-very-small {
    width: 30px;
    height: 30px;
  }
</style>

<script type="text/javascript">	
  window.print();
  setTimeout(function(){
  window.close()
  },5000)
 </script>

<div id="bg">
    <div id="id">
            <center>
                <table style="margin-top: 10px;">
                    <tr class="d-flex align-items justify-content-between"> 
                        <td>
                        <img src="assets/img/mcc-logo.png" alt="Avatar"  width='30px' height='30px' alt=''>
                        </td>
                        <td><p style="font-size:7px;text-align:center;"><b>MADRIDEJOS COMMUNITY COLLEGE<br>LEARNING RESOURCE CENTER</b><br><small><b>BUNAKAN, MADRIDEJOS, CEBU</b></small></p></td>
                        <td>
                        <img src="images/mcc-lrc.png" alt="Avatar"  width='30px' height='30px' alt=''>
                        </td>
                    </tr>        
                </table>     
            </center>
            <br>
            <center>
                <?php      
                    if ($profile != "") {
                        //echo "<img src='../uploads/$profile' height='175px' width='200px' alt='' style='border: 2px solid black; border-radius: 60%;'>";
                        echo "<img src='../uploads/profile_images/$profile' alt='' style='border: 2px solid #1076c1; width: 1.3in; height: 1.3in;overflow:hidden;position:absolute;left:0;right:0;top:60px;display:block;margin:0 auto;'>";
                        } else {
                        echo "<img src='assets/img/image.png' height='150px' width='150px' alt='' style='border: 4px solid #1176c2;'>";
                        }
                ?> 
            </center> 
            <br>
            <div class="container" align="center">
                <p style="font-size:10px;font-weight:bold;text-transform:uppercase;color:black;position:absolute;left:0;right:0;bottom:68px;"><?php if(isset($names)){ $namez=$names;echo$namez;} ?></p>
                <br>
            </div>
            <div>
            <p style="font-weight:bold;color:black;text-align:center;position:absolute;left:0;right:0;bottom:40px;font-size:10px;text-transform:uppercase;"><?php echo $type; ?></p>
            </div>
            <div>
                <p style="font-size:10px;font-weight:bold;color:black;text-align:center;font-family:Georgia,serif;position:absolute;left:0;right:0;bottom:-15px;letter-spacing:2px;"><?php echo $course; ?></p>
            </div>
    </div>

    <div class="id-1">
        <p class="text-end" style="font-size:8px;margin-top:2px;margin-right:3px;font-weight:bold;">Birthdate: <?php echo date('F j, Y', strtotime($bdate)); ?></p>
        <br>
        <br>
        <center>
            <img src="../qrcodes/<?php echo htmlspecialchars($qrcode); ?>" alt="Avatar" width="200px" height="180px" style="background-color:transparent;mix-blend-mode: multiply;margin-top:-58px;">
            <br>
            <br>
            <br>
                <div class="container" align="center">
                    <p style="color:black;margin-top:-28px;font-size:10px;">In case of emergency, please notify:</p>
                    <p class="text-center" style="color:black;margin-top:-10px;margin-bottom:-4px;font-size:12px;font-weight:bold;"><?php echo $contact_person; ?></p>
                    <p class="text-center" style="color:black;font-size:12px;font-weight:bold;"><?php echo $person_cell_no; ?></p>
        </center>
                </div>
    </div>
</div>


<?php
include('./includes/script.php');
?>

    </body>
    </html>