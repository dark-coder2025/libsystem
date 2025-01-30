<?php 
ini_set('session.cookie_httponly', 1);
session_start();
include('./admin/config/dbcon.php');
require_once('./qrcode/qrlib.php');

if (isset($_POST['register_btn'])) {
    // Fetch and sanitize input
    $lastname = mysqli_real_escape_string($con, $_POST['lastname']);
    $firstname = mysqli_real_escape_string($con, $_POST['firstname']);
    $middlename = mysqli_real_escape_string($con, $_POST['middlename']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $birthdate = mysqli_real_escape_string($con, $_POST['birthdate']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $cell_no = mysqli_real_escape_string($con, $_POST['cell_no']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $year_level = mysqli_real_escape_string($con, $_POST['year_level']);
    $course = mysqli_real_escape_string($con, $_POST['course']);
    $student_id_no = mysqli_real_escape_string($con, $_POST['student_id_no']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $cpassword = mysqli_real_escape_string($con, $_POST['cpassword']);
    $role_as = mysqli_real_escape_string($con, $_POST['role']);
    $profile_image = isset($_FILES['profile_image']) ? $_FILES['profile_image'] : null;
    $contact_person = mysqli_real_escape_string($con, $_POST['contact_person']);
    $person_cell_no = mysqli_real_escape_string($con, $_POST['person_cell_no']);

    // Validate mandatory fields
    if (empty($lastname) || empty($firstname) || empty($gender) || empty($birthdate) || empty($address) || empty($cell_no) || empty($email) || empty($student_id_no) || empty($password) || empty($cpassword) || empty($role_as) || empty($profile_image) || empty($contact_person) || empty($person_cell_no)) {
        $_SESSION['status'] = "Please fill up all fields";
        $_SESSION['status_code'] = "warning";
        header("Location: login.php");
        exit(0);
    }

    // Check if student ID, username, or email already exists
    $check_query = "";
    $email_check_query = "SELECT email FROM user WHERE email = ? UNION SELECT email FROM faculty WHERE email = ?";

    if ($role_as == 'student') {
        $check_query = "SELECT student_id_no FROM user WHERE student_id_no = ?";
    } elseif ($role_as == 'faculty' || $role_as == 'staff') {
        $check_query = "SELECT username FROM faculty WHERE username = ?"; // Assuming username is used for faculty
    }

    // Prepare statements to avoid SQL injection
    $stmt_check = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($stmt_check, 's', $student_id_no);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    $stmt_email_check = mysqli_prepare($con, $email_check_query);
    mysqli_stmt_bind_param($stmt_email_check, 'ss', $email, $email);
    mysqli_stmt_execute($stmt_email_check);
    mysqli_stmt_store_result($stmt_email_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        $_SESSION['status'] = ($role_as == 'student') ? "Student ID No. already exists" : "Username already exists";
        $_SESSION['status_code'] = "warning";
        header("Location: login.php");
        exit(0);
    } elseif (mysqli_stmt_num_rows($stmt_email_check) > 0) {
        $_SESSION['status'] = "Email already exists";
        $_SESSION['status_code'] = "warning";
        header("Location: login.php");
        exit(0);
    }

    // Check email verification
    $check_verify = "SELECT used FROM ms_account WHERE username = ?";
    $stmt_verify = mysqli_prepare($con, $check_verify);
    mysqli_stmt_bind_param($stmt_verify, 's', $email);
    mysqli_stmt_execute($stmt_verify);
    $result_verify = mysqli_stmt_get_result($stmt_verify);

    if (mysqli_num_rows($result_verify) > 0) {
        $row = mysqli_fetch_array($result_verify);
        $used = $row['used'];

        if ($used == 0) {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Handle image upload
            $image_path = "";
            if (isset($profile_image) && $profile_image['error'] == 0) {
                $target_dir = "./uploads/profile_images/";
                $image_name = basename($profile_image['name']);
                $target_file = $target_dir . $image_name;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $check = getimagesize($profile_image['tmp_name']);
                
                // Check if image file is a actual image or fake image
                if($check !== false) {
                    // Check file size (limit to 50MB)
                    if ($profile_image["size"] <= 50997152) {
                        // Allow certain file formats
                        if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" ) {
                            if (move_uploaded_file($profile_image["tmp_name"], $target_file)) {
                                $image_path = $image_name;
                            } else {
                                $_SESSION['status'] = "Sorry, there was an error uploading your file.";
                                $_SESSION['status_code'] = "error";
                                header("Location: login.php");
                                exit(0);
                            }
                        } else {
                            $_SESSION['status'] = "Sorry, only JPG, JPEG & PNG files are allowed.";
                            $_SESSION['status_code'] = "error";
                            header("Location: login.php");
                            exit(0);
                        }
                    } else {
                        $_SESSION['status'] = "Sorry, your file is too large. Maximum size is 50MB.";
                        $_SESSION['status_code'] = "error";
                        header("Location: login.php");
                        exit(0);
                    }
                } else {
                    $_SESSION['status'] = "File is not an image.";
                    $_SESSION['status_code'] = "error";
                    header("Location: login.php");
                    exit(0);
                }
            }

            // Prepare and execute INSERT query
            $insert_query = "";
            if ($role_as == 'student') {
                $insert_query = "INSERT INTO user (lastname, firstname, middlename, gender, course, address, cell_no, birthdate, email, year_level, student_id_no, password, role_as, status, user_added, profile_image, contact_person, person_cell_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), ?, ?, ?)";
            } elseif ($role_as == 'faculty' || $role_as == 'staff') {
                $insert_query = "INSERT INTO faculty (lastname, firstname, middlename, gender, course, address, cell_no, birthdate, email, username, password, role_as, status, faculty_added, profile_image, contact_person, person_cell_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), ?, ?, ?)";
            }

            $stmt_insert = mysqli_prepare($con, $insert_query);

            if ($role_as == 'student') {
            mysqli_stmt_bind_param($stmt_insert, 'ssssssssssssssss', $lastname, $firstname, $middlename, $gender, $course, $address, $cell_no, $birthdate, $email, $year_level, $student_id_no, $hashed_password, $role_as, $image_path, $contact_person, $person_cell_no);
            } else {
                mysqli_stmt_bind_param($stmt_insert, 'sssssssssssssss', $lastname, $firstname, $middlename, $gender, $course, $address, $cell_no, $birthdate, $email, $student_id_no, $hashed_password, $role_as, $image_path, $contact_person, $person_cell_no);
            }
            
            if (mysqli_stmt_execute($stmt_insert)) {
                // Generate QR Code
                $identifier = $student_id_no; // Adjust username if needed for faculty
                $qrdata = "$identifier"; // Example data to encode in QR code
                $qrfile = "./qrcodes/$identifier.png"; // Path to save QR code image
                $qrimage = "$identifier.png";
                QRcode::png($qrdata, $qrfile); // Generate QR code

                // Insert QR code path into database
                $update_query = "";
                if ($role_as == 'student') {
                    $update_query = "UPDATE user SET qr_code = ? WHERE student_id_no = ?";
                } elseif ($role_as == 'faculty' || $role_as == 'staff') {
                    $update_query = "UPDATE faculty SET qr_code = ? WHERE username = ?";
                }

                $stmt_update = mysqli_prepare($con, $update_query);
                mysqli_stmt_bind_param($stmt_update, 'ss', $qrimage, $student_id_no);
                
                if (mysqli_stmt_execute($stmt_update)) {
                    $update_verify = "UPDATE ms_account SET used = 1, created_at = NOW() WHERE username = ?";
                    $stmt_update_verify = mysqli_prepare($con, $update_verify);
                    mysqli_stmt_bind_param($stmt_update_verify, 's', $email);
                    mysqli_stmt_execute($stmt_update_verify);
                    
                    $_SESSION['status'] = "Registered successfully, wait for approval.";
                    $_SESSION['status_code'] = "success";
                    header("Location: login.php");
                    exit(0);
                } else {
                    $_SESSION['status'] = "Failed to update QR code path in database";
                    $_SESSION['status_code'] = "error";
                    header("Location: login.php");
                    exit(0);
                }
            } else {
                $_SESSION['status'] = "Failed to register user";
                $_SESSION['status_code'] = "error";
                header("Location: login.php");
                exit(0);
            }
        } else {
            $_SESSION['status'] = "Link already been used.";
            $_SESSION['status_code'] = "error";
            header("Location: login.php");
            exit(0);
        }
    } else {
        $_SESSION['status'] = "Email verification not found.";
        $_SESSION['status_code'] = "error";
        header("Location: login.php");
        exit(0);
    }
} else {
    $_SESSION['status'] = "Please fill up all the fields";
    $_SESSION['status_code'] = "warning";
    header("Location: login.php");
    exit(0);
}
?>