<?php
ini_set('session.cookie_httponly', 1);
session_start();
include('./admin/config/dbcon.php');

// Initialize session variables if not already set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lockout_time'] = null;
}

// Check if user is locked out
if ($_SESSION['lockout_time'] && time() < $_SESSION['lockout_time']) {
    $lockout_time_remaining = $_SESSION['lockout_time'] - time();
    $minutes_remaining = ceil($lockout_time_remaining / 60);
    header("Location: admin_login.php");
    exit(0);
}

if (isset($_POST['admin_login_btn'])) {

    $secret_key = 'ES_107e8aafc7d14c13b7e3f856836d88bf';
    $hcaptcha_response = $_POST['h-captcha-response'];

    $response = file_get_contents("https://hcaptcha.com/siteverify?secret=$secret_key&response=$hcaptcha_response");
    $response_keys = json_decode($response, true);

    if (intval($response_keys["success"]) !== 1) {
        $_SESSION['status'] = "Please complete the CAPTCHA.";
        $_SESSION['status_code'] = "error";
        header("Location: admin_login.php");
        exit(0);
    }

    $email = $_POST['email'];
    $password = $_POST['password'];
    $admin_type = $_POST['admin_type'];

    $admin_login_query = "SELECT * FROM admin WHERE email = ? AND admin_type = ?";

    if ($stmt = mysqli_prepare($con, $admin_login_query)) {
        mysqli_stmt_bind_param($stmt, 'ss', $email, $admin_type);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_array($result);
            if (password_verify($password, $data['password'])) {
                session_regenerate_id(true); // Regenerate session ID
                // Reset login attempts on successful login
                $_SESSION['login_attempts'] = 0;
                $_SESSION['lockout_time'] = null;

                $admin_id = $data['admin_id'];
                $admin_name = $data['firstname'] . ' ' . $data['lastname'];
                $admin_email = $data['email'];
                $admin_type = $data['admin_type'];

                $_SESSION['auth'] = true;
                $_SESSION['auth_role'] = "$admin_type";
                $_SESSION['auth_admin'] = [
                    'admin_id' => $admin_id,
                    'admin_name' => $admin_name,
                    'email' => $admin_email,
                ];

                $_SESSION['login_success'] = true;
                header("Location: admin_login.php");
                exit(0);
            } else {
                // Increment login attempts on failure
                $_SESSION['login_attempts']++;
                if ($_SESSION['login_attempts'] >= 3) {
                    $_SESSION['lockout_time'] = time() + 300; // Lock out for 5 minutes
                } else {
                    $_SESSION['status'] = "Invalid Credentials. Please try again...";
                }
                $_SESSION['status_code'] = "error";
                header("Location: admin_login.php");
                exit(0);
            }
        } else {
            // Increment login attempts on failure
            $_SESSION['login_attempts']++;
            if ($_SESSION['login_attempts'] >= 3) {
                $_SESSION['lockout_time'] = time() + 300; // Lock out for 5 minutes
            } else {
                $_SESSION['status'] = "Invalid Credentials. Please try again...";
            }
            $_SESSION['status_code'] = "error";
            header("Location: admin_login.php");
            exit(0);
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['status'] = "Something went wrong.";
        $_SESSION['status_code'] = "error";
        header("Location: admin_login.php");
        exit(0);
    }
}
?>
