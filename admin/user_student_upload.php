<?php
include('authentication.php');
include('includes/url.php');

$userId = $_SESSION['user_id']; // example

if(isset($_FILES['cropped_image'])){
    $uploadDir = '../uploads/profile_images/';
    $fileName = uniqid() . '.jpg';
    $uploadFile = $uploadDir . $fileName;

    if(move_uploaded_file($_FILES['cropped_image']['tmp_name'], $uploadFile)){
        // Update your database if needed
        mysqli_query($conn, "UPDATE user SET profile_image = '$fileName' WHERE user_id=$userId");

        echo json_encode([
            'status' => 'success',
            'image_url' => $uploadFile
        ]);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
?>
