<?php
// Database connection
$host = "localhost"; // Change this to your Hostinger MySQL host when moving online
$username = "root";  // Use Hostinger credentials for an online database
$password = "";
$database = "tabulation_db";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 1: Drop the existing 'admin' table
$dropTableSQL = "DROP TABLE IF EXISTS admin";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Old 'admin' table deleted successfully.<br>";
} else {
    die("Error deleting old table: " . $conn->error);
}

// Step 2: Create a new 'admin' table
$createTableSQL = "CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role varchar(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($createTableSQL) === TRUE) {
    echo "New 'admin' table created successfully.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Step 3: Insert new data into the 'admin' table
$username = "joilingford";
$email = "joilingford.com";
// $password = md5("securepassword"); // Hash password using MD5 (Legacy method)
$role = "Admin";
// $salt = "random_salt_value"; // Use a unique salt per user
// $password = md5($salt . "securepassword"); 
// $password = hash("sha256", "securepassword");
$password = password_hash("securepassword", PASSWORD_ARGON2ID); // Secure Argon2 hashing
// $password = password_hash("securepassword", PASSWORD_BCRYPT); // Hash password

$insertSQL = "INSERT INTO admin (username, email, password, role) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($insertSQL);
$stmt->bind_param("ssss", $username, $email, $password, $role);

if ($stmt->execute()) {
    echo "New data inserted successfully!";
} else {
    echo "Error inserting data: " . $stmt->error;
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
