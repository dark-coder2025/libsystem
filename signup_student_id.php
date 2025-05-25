<?php
// Include your database connection file
include('./admin/config/dbcon.php');

// Get the raw POST data (JSON)
$data = json_decode(file_get_contents('php://input'), true);

// Check if student_id is provided
if (isset($data['student_id'])) {
    $studentId = $data['student_id'];

    // Prepare SQL query to check if the student ID exists in the 'student_id_no' column
    $sql = "SELECT COUNT(*) FROM user WHERE student_id_no = ?";
    if ($stmt = $con->prepare($sql)) {
        // Bind the student_id parameter to the prepared statement
        $stmt->bind_param("s", $studentId);

        // Execute the statement
        $stmt->execute();

        // Bind the result to a variable
        $stmt->bind_result($count);

        // Fetch the result
        $stmt->fetch();

        // Close the statement
        $stmt->close();

        // Return the response
        if ($count > 0) {
            echo json_encode(['exists' => true]);
        } else {
            echo json_encode(['exists' => false]);
        }
    } else {
        // Handle SQL preparation failure
        echo json_encode(['error' => 'Failed to prepare the SQL query']);
    }
} else {
    // If student_id is not provided
    echo json_encode(['error' => 'Student ID not provided']);
}

// Close the database connection
$con->close();
?>
