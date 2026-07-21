<?php
/**
 * MCC LRC - Centralized Error Logging System
 * 
 * Logs all PHP errors, warnings, notices, and exceptions to:
 *   1. File:   logs/error.log
 *   2. Database: error_logs table
 *
 * Included automatically by dbcon.php after the DB connection is established.
 */

// ============================================================
// 1. PHP Error Reporting Configuration
// ============================================================
error_reporting(E_ALL);
ini_set('display_errors', 0);         // Never show errors to users in production
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);

// ============================================================
// 2. File-based Logging Setup
// ============================================================
$log_dir = dirname(__DIR__, 2) . '/logs';
if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
}
$log_file = $log_dir . '/error.log';
ini_set('error_log', $log_file);

// ============================================================
// 3. Helper: Convert PHP error code to readable name
// ============================================================
function mcc_get_error_type_name($errno) {
    $types = [
        E_ERROR             => 'Fatal Error',
        E_WARNING           => 'Warning',
        E_PARSE             => 'Parse Error',
        E_NOTICE            => 'Notice',
        E_CORE_ERROR        => 'Core Error',
        E_CORE_WARNING      => 'Core Warning',
        E_COMPILE_ERROR     => 'Compile Error',
        E_COMPILE_WARNING   => 'Compile Warning',
        E_USER_ERROR        => 'User Error',
        E_USER_WARNING      => 'User Warning',
        E_USER_NOTICE       => 'User Notice',
        E_STRICT            => 'Strict',
        E_RECOVERABLE_ERROR => 'Recoverable Error',
        E_DEPRECATED        => 'Deprecated',
        E_USER_DEPRECATED   => 'User Deprecated',
    ];
    return $types[$errno] ?? 'Unknown';
}

// ============================================================
// 4. Log error to database
// ============================================================
function mcc_log_error_to_db($errno, $errstr, $errfile, $errline) {
    global $con;

    // Only log to DB if connection is available
    if (!isset($con) || !$con) {
        return;
    }

    $error_type  = mcc_get_error_type_name($errno);
    $user_ip     = $_SERVER['REMOTE_ADDR']    ?? 'CLI';
    $request_uri = $_SERVER['REQUEST_URI']    ?? 'N/A';
    $user_agent  = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';
    $method      = $_SERVER['REQUEST_METHOD']  ?? 'N/A';

    // Gather user info from active session
    $user_id  = null;
    $user_role = null;
    if (session_status() === PHP_SESSION_ACTIVE) {
        if (isset($_SESSION['auth_admin']['admin_id'])) {
            $user_id   = $_SESSION['auth_admin']['admin_id'];
            $user_role = 'admin';
        } elseif (isset($_SESSION['auth_stud']['stud_id'])) {
            $user_id   = $_SESSION['auth_stud']['stud_id'];
            $user_role = 'student';
        } elseif (isset($_SESSION['auth_faculty']['faculty_id'])) {
            $user_id   = $_SESSION['auth_faculty']['faculty_id'];
            $user_role = 'faculty';
        }
    }

    try {
        $stmt = $con->prepare(
            "INSERT INTO error_logs 
                (error_type, error_message, error_file, error_line, 
                 user_ip, request_uri, user_agent, request_method, 
                 user_id, user_role, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        if ($stmt) {
            $stmt->bind_param(
                "sssissssiss",
                $error_type, $errstr, $errfile, $errline,
                $user_ip, $request_uri, $user_agent, $method,
                $user_id, $user_role
            );
            $stmt->execute();
        }
    } catch (Exception $e) {
        // Silently fail — don't cascade errors from the error handler itself
    }
}

// ============================================================
// 5. Create error_logs table if it doesn't exist (runs once)
// ============================================================
function mcc_create_error_table() {
    global $con;
    if (!isset($con) || !$con) return;

    $sql = "CREATE TABLE IF NOT EXISTS error_logs (
        id              INT AUTO_INCREMENT PRIMARY KEY,
        error_type      VARCHAR(50) NOT NULL,
        error_message   TEXT NOT NULL,
        error_file      VARCHAR(500) DEFAULT NULL,
        error_line      INT DEFAULT NULL,
        user_ip         VARCHAR(45) DEFAULT NULL,
        request_uri     VARCHAR(500) DEFAULT NULL,
        user_agent      VARCHAR(500) DEFAULT NULL,
        request_method  VARCHAR(10) DEFAULT NULL,
        user_id         INT DEFAULT NULL,
        user_role       VARCHAR(20) DEFAULT NULL,
        created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_error_type (error_type),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    try {
        $con->query($sql);
    } catch (Exception $e) {
        // Silently fail
    }
}

// ============================================================
// 6. Custom Error Handler
// ============================================================
function mcc_error_handler($errno, $errstr, $errfile, $errline) {
    // Skip suppressed errors (e.g., @function())
    if (!(error_reporting() & $errno)) {
        return false;
    }

    // Log to database
    mcc_log_error_to_db($errno, $errstr, $errfile, $errline);

    // Let PHP's built-in error handler also log to the file
    return false;
}

// ============================================================
// 7. Custom Exception Handler (uncaught exceptions)
// ============================================================
function mcc_exception_handler($exception) {
    mcc_log_error_to_db(
        E_ERROR,
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    );
}

// ============================================================
// 8. Fatal Error Handler (shutdown function)
// ============================================================
function mcc_fatal_handler() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        mcc_log_error_to_db($error['type'], $error['message'], $error['file'], $error['line']);
    }
}

// ============================================================
// 9. Register all handlers
// ============================================================
set_error_handler('mcc_error_handler');
set_exception_handler('mcc_exception_handler');
register_shutdown_function('mcc_fatal_handler');

// ============================================================
// 10. Ensure the error_logs table exists
// ============================================================
mcc_create_error_table();

// ============================================================
// 11. Public helper function for manual logging from any file
//     Usage:  log_app_error('Something went wrong');
// ============================================================
function log_app_error($message, $type = 'User Error') {
    global $con;
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
    $file = $backtrace[0]['file'] ?? 'unknown';
    $line = $backtrace[0]['line'] ?? 0;

    // Log to file
    error_log("[$type] $message in $file on line $line");

    // Log to DB
    mcc_log_error_to_db(
        $type === 'Warning' ? E_USER_WARNING : E_USER_ERROR,
        $message,
        $file,
        $line
    );
}
