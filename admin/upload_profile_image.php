<?php
include('authentication.php');
include('includes/url.php');

if(isset($_POST['user_id']) && isset($_FILES['profile_image']))
{
    $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);

    $image = $_FILES['profile_image'];
    $imageName = time() . '_' . basename($image['name']);
    $targetDirectory = "../uploads/profile_images/";
    $targetFile = $targetDirectory . $imageName;

    if(move_uploaded_file($image["tmp_name"], $targetFile)) {
        // Update the image name in the database
        $query = "UPDATE user SET profile_image = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "si", $imageName, $user_id);
        mysqli_stmt_execute($stmt);

        $_SESSION['status'] = 'Change Profile Successfully';
        $_SESSION['status_code'] = "success";
        header("Location: user_student_view.php?b=" . urlencode(encryptor('encrypt', $user_id)));
        exit();
    } else {
        $_SESSION['status'] = 'Error uploading file.';
        $_SESSION['status_code'] = "error";
        header("Location: user_student_view.php?b=" . urlencode(encryptor('encrypt', $user_id)));
    }
} else {
    $_SESSION['status'] = 'Invalid request.';
    $_SESSION['status_code'] = "error";
    header("Location: user_student_view.php?b=" . urlencode(encryptor('encrypt', $user_id)));
}
?>
