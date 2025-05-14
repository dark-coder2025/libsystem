<?php
include('authentication.php');

// Set headers for file download
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="database_backup_' . date('Y-m-d_H-i-s') . '.sql"');

// Fetch all table names
$tables = [];
$result = $con->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

$backupSql = "-- Database Backup\n";
$backupSql .= "-- Created: " . date('Y-m-d H:i:s') . "\n\n";
$backupSql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

foreach ($tables as $table) {
    // Drop table if exists
    $backupSql .= "DROP TABLE IF EXISTS `$table`;\n";

    // Get CREATE TABLE statement
    $createTableResult = $con->query("SHOW CREATE TABLE `$table`");
    $createTableRow = $createTableResult->fetch_assoc();
    $backupSql .= $createTableRow['Create Table'] . ";\n\n";

    // Get data from table
    $dataResult = $con->query("SELECT * FROM `$table`");
    while ($row = $dataResult->fetch_assoc()) {
        $values = array_map([$con, 'real_escape_string'], array_values($row));
        $values = array_map(function($val) {
            return "'" . $val . "'";
        }, $values);

        $backupSql .= "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n";
    }

    $backupSql .= "\n\n";
}

$backupSql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

// Output the .sql content
echo $backupSql;

// Close connection
$con->close();
?>
