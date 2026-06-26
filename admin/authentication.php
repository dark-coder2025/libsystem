<?php
declare(strict_types=1);

ini_set('session.cookie_httponly', '1');

session_start();

require_once 'config/dbcon.php';

// Check if user is logged in
if (
    !isset($_SESSION['auth']) ||
    $_SESSION['auth'] !== true ||
    !isset($_SESSION['auth_role']) ||
    !in_array($_SESSION['auth_role'], ['Admin', 'Staff'], true)
) {
    session_unset();
    session_destroy();

    header("Location: ../admin_login.php");
    exit();
}
?>