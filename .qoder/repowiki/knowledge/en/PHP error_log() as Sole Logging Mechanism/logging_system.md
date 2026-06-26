## Overview

This PHP monolithic application has **no dedicated logging framework or structured logging system**. The only logging mechanism used is PHP's built-in `error_log()` function, employed sporadically for capturing email delivery failures and database errors.

## What System Is Used

- **PHP `error_log()`** — The sole logging function found across the codebase
- No logging framework (e.g., Monolog, PSR-3 logger)
- No log file rotation, log levels, or structured output
- No centralized logging configuration

## Key Files Using Logging

| File | Usage |
|------|-------|
| `admin/archived_code.php` | `error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}")` |
| `ms_verify_code.php` | `error_log("MySQL prepare error: " . $con->error)` and mailer errors |
| `admin/circulation_borrow.php` | Mailer error logging |
| `admin/user_faculty_code*.php` | Multiple mailer error logs |
| `admin/user_student_code*.php` | Mailer error logs |
| `password-reset-code.php`, `password-reset-otp-code.php` | Mailer error logs |
| `admin-forgot-code.php` | Mailer error logs |

## Architecture and Conventions

### Informal, Ad-Hoc Pattern
Logging is **not systematic**. It appears only in `catch` blocks around PHPMailer operations and occasionally around MySQL prepare/execute failures. There is:
- No consistent log message format
- No log level differentiation (all calls are plain `error_log()`)
- No timestamp prefixing (PHP's `error_log()` may add one depending on server config)
- No correlation IDs or request context

### Error Handling Strategy
The dominant error handling pattern is **session-based user feedback**, not logging:
```php
$_SESSION['status'] = "Database error. Please try again later.";
$_SESSION['status_code'] = "error";
header("Location: some_page.php");
exit(0);
```
Most failures silently redirect with a generic message rather than producing diagnostic logs.

### Database Connection
In `admin/config/dbcon.php`, connection failures use `die()`:
```php
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}
```
This terminates execution immediately without any logging.

## Rules Developers Should Follow

Since no formal logging convention exists, developers currently follow these implicit patterns:

1. **Use `error_log()` sparingly** — Only for exceptional conditions (mailer failures, DB prepare errors)
2. **No structured fields** — Log messages are free-form strings with interpolated variables
3. **Prefer session messages for user-facing errors** — Actual diagnostics go to `error_log()`, users see generic session-stored messages
4. **No log file management** — Relies entirely on PHP's server-level `error_log` destination (typically Apache/Nginx error log or php.ini-configured file)

## Gaps

- No log aggregation or monitoring integration
- No audit trail for administrative actions
- No request-level tracing
- Silent failures in many code paths (errors caught but not logged)
- Hardcoded credentials in `dbcon.php` with no secure secret management
