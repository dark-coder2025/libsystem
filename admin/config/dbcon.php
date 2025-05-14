<?php 

$host = "localhost";
$username = "u510162695_D10v1n";
$password = "MCc_lRCv3rs!on2";
$database = "u510162695_D10v1n_mcC_Lrc";
// $username = "root";
// $password = "";
// $database = "mcclrc";

$con = mysqli_connect($host, $username, $password, $database);

if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
} else {
  // echo "Connected Successfully";
}
?>
