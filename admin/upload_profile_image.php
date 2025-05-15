<?php
include('authentication.php');

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

        header("Location: view_student.php?b=" . urlencode(encryptor('encrypt', $user_id)));
        exit();
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "Invalid request.";
}
?>
