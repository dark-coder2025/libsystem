<?php
// Database credentials from Hostinger
$host = "localhost"; // Example: "mysql.hostinger.com"
$username = "root";
$password = "";
$database = "tabulation_db";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get list of tables
$result = $conn->query("SHOW TABLES");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
        $table = $row[0];
        $conn->query("DROP TABLE IF EXISTS $table"); // Delete each table
    }
    echo "All tables deleted successfully!";
} else {
    echo "No tables found in the database.";
}

// Close connection
$conn->close();
?>
