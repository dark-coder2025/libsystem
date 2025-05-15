<?php
include('authentication.php');
include('includes/url.php');

if (isset($_POST['user_id']) && isset($_FILES['profile_image'])) {
    $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    $image = $_FILES['profile_image'];

    // Validate image size (max 2MB)
    if ($image['size'] > 2097152) { // 2 * 1024 * 1024
        $_SESSION['status'] = 'Image size should not exceed 2MB.';
        $_SESSION['status_code'] = "error";
        header("Location: user_student_view.php?b=" . urlencode(encryptor('encrypt', $user_id)));
        exit();
    }

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    $imageMimeType = mime_content_type($image['tmp_name']);

    if (!in_array($imageMimeType, $allowedTypes)) {
        $_SESSION['status'] = 'Only JPG, JPEG, and PNG files are allowed.';
        $_SESSION['status_code'] = "error";
        header("Location: user_student_view.php?b=" . urlencode(encryptor('encrypt', $user_id)));
        exit();
    }

    $imageName = time() . '_' . basename($image['name']);
    $targetDirectory = "../uploads/profile_images/";
    $targetFile = $targetDirectory . $imageName;

    if (move_uploaded_file($image["tmp_name"], $targetFile)) {
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
        exit();
    }
} else {
    $_SESSION['status'] = 'Invalid request.';
    $_SESSION['status_code'] = "error";
    header("Location: user_student_view.php?b=" . urlencode(encryptor('encrypt', $user_id)));
    exit();
}
?>
