<?php
include('authentication.php'); // Include your authentication or database connection

if (isset($_POST['update_account'])) {
    $ms_id = $_POST['ms_id'];
    $used = $_POST['used'];

    // Prepare and execute the update query
    $query = "UPDATE ms_account SET used = ? WHERE ms_id = ?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param('si', $used, $ms_id); // 'si' means string and integer
        if ($stmt->execute()) {
            // Redirect or show success message
            $_SESSION['status'] = "Used updated successfully.";
            $_SESSION['status_code'] = "success";
            header('Location: ms_account.php'); // Redirect back to the main page after updating
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
