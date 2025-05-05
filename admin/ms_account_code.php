<?php
include('authentication.php');

// Edit Student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are set
    if (isset($_POST['edit_used'])) {
        // Sanitize and validate input to prevent SQL injection
        $accountId = mysqli_real_escape_string($con, $_POST['edit_account_id']);
        $used = mysqli_real_escape_string($con, $_POST['edit_used']);

        // Prepare the SQL UPDATE query
        $sql = "UPDATE ms_account 
                SET used = '$used' 
                WHERE ms_id = '$accountId'";

        // Execute the query
        if (mysqli_query($con, $sql)) {
            // Set success message in the session and redirect
            $_SESSION['status'] = "Updated successfully.";
            $_SESSION['status_code'] = "success";
            header('Location: ms_account.php');
            exit();
        } else {
            // Handle SQL execution errors
            echo "Error updating record: " . mysqli_error($con);
        }
    } else {
        // Handle missing form data
        echo "Error: Missing required fields.";
    }
}

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    $userId = mysqli_real_escape_string($con, $userId);

    $sql = "SELECT * FROM ms_account WHERE ms_id = '$userId'";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode($row);
    } else {
        $_SESSION['status'] = "Account not found";
        $_SESSION['status_code'] = "error";
        header('Location: ms_account.php');
        exit();
    }
} else {
    $_SESSION['status'] = "Invalid request: No ID provided";
    $_SESSION['status_code'] = "error";
    header('Location: ms_account.php');
    exit();
}
?>