
<?php
$servername = "127.0.0.1";
$username = "u510162695_mcclrc";
$password = "1Mcclrc_pass";
$dbname = "u510162695_mcclrc";

// $servername = "127.0.0.1";
// $username = "u510162695_D10v1n";
// $password = "MCc_lRCv3rs!on2";
// $dbname = "u510162695_D10v1n_mcC_Lrc";

// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "macwas";

//Macwasbilling
//samuelellum30

// Create connection
$link = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}
?>
