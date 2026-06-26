## Overview

This PHP monolith uses a **session-based flash message pattern** for error/success communication, combined with **inline validation**, **manual control flow termination** (`exit(0)`), and **sparse exception handling** limited to third-party libraries (PHPMailer). There is no centralized error handler, no custom error types, and no middleware-based error processing.

---

## Core Approach

### 1. Session Flash Messages (Primary Pattern)

Errors and status messages are communicated via `$_SESSION` variables:

- **`$_SESSION['status']`** — Human-readable message text
- **`$_SESSION['status_code']`** — Severity/type indicator: `"success"`, `"error"`, `"warning"`, or `"danger"`

These are set in backend logic files (`*_code.php`) before redirecting, then consumed by SweetAlert2 in the included `script.php` template:

```php
// Backend (e.g., admin/admin_code.php)
$_SESSION['status'] = 'Admin Not Deleted';
$_SESSION['status_code'] = "error";
header("Location: admin.php");
exit(0);
```

```php
// Frontend rendering (admin/includes/script.php, includes/script.php)
<?php if(isset($_SESSION['status']) && $_SESSION['status'] !='') { ?>
    <script>
        Swal.fire({
            title: "<?php echo $_SESSION['status']; ?>",
            icon: "<?php echo $_SESSION['status_code']; ?>",
            confirmButtonText: "OK"
        });
    </script>
<?php unset($_SESSION['status']); } ?>
```

A secondary variant uses `$_SESSION['message_error']` and `$_SESSION['message_success']` in some admin pages (e.g., `admin/authentication.php`, `admin/circulation_borrow.php`).

### 2. Control Flow Termination

Every branch that sets a session message immediately calls `header("Location: ...")` followed by `exit(0)`. This is a consistent convention across all `*_code.php` files to prevent fall-through execution after redirects.

### 3. Database Connection Error Handling

The database connection file (`admin/config/dbcon.php`) uses `die()` on connection failure:

```php
$con = mysqli_connect($host, $username, $password, $database);
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}
```

This is a hard-stop approach — no recovery, no logging, no graceful degradation.

### 4. Input Validation and Sanitization

Two approaches coexist:

- **`mysqli_real_escape_string()`** — Used extensively in older-style query construction (e.g., `allcode.php`, `admin/admin_code.php`)
- **Prepared statements with `mysqli_stmt_*`** — Used in authentication flows (`logincode.php`, `admin_login_code.php`) and newer code (`admin/add_account.php`)

Validation errors (missing fields, invalid formats, duplicate entries) are caught inline and reported via the session message pattern.

### 5. Exception Handling (Limited Scope)

`try/catch` blocks appear only around **PHPMailer** operations (e.g., `admin/archived_code.php`, `admin/circulation_borrow.php`):

```php
$mail = new PHPMailer(true);
try {
    // SMTP configuration and send
    $mail->send();
} catch (Exception $e) {
    error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
}
```

Caught exceptions are logged via `error_log()` but **never surfaced to the user**. The application continues silently, which can lead to undetected email delivery failures.

### 6. Authentication Guard Middleware

`admin/authentication.php` acts as a rudimentary auth guard included at the top of protected admin pages:

```php
if(!isset($_SESSION['auth'])) {
  $_SESSION['message_error'] = "Login to Access Dashboard";
  header("Location:../admin_login.php");
  exit(0);
}
```

This is not a true middleware layer but an include-based access control check repeated per page.

### 7. Login Rate Limiting

Both `logincode.php` and `admin_login_code.php` implement session-based brute-force protection:

- Tracks `$_SESSION['login_attempts']`
- After 3 failed attempts, sets `$_SESSION['lockout_times']` (or `lockout_time`) to `time() + 300` (5-minute lockout)
- No persistent storage of lockout state — resets on session destruction

### 8. Maintenance/Error Page

`404.php` serves as a maintenance landing page (despite its name), returning HTTP 503 with a `Retry-After: 3600` header. It is not dynamically triggered by application errors.

---

## Key Files

| File | Role |
|------|------|
| `admin/config/dbcon.php` | Database connection with `die()` on failure |
| `admin/authentication.php` | Auth guard include for admin pages |
| `admin/includes/script.php` | SweetAlert2 rendering of `$_SESSION['status']` / `$_SESSION['status_code']` |
| `includes/script.php` | Same SweetAlert2 rendering for public-facing pages |
| `logincode.php` | Student/faculty login with rate limiting, CAPTCHA verification, session-based error messages |
| `admin_login_code.php` | Admin login with identical rate limiting pattern |
| `admin/admin_code.php` | CRUD operations with inline validation and session error reporting |
| `admin/add_account.php` | Demonstrates prepared statement error capture (`$stmt->error`, `$con->error`) exposed to users |
| `admin/archived_code.php` | Only file with `try/catch` (PHPMailer), using `error_log()` for failures |
| `admin/circulation_borrow.php` | Inline PHPMailer try/catch with `error_log()` |
| `404.php` | Static maintenance page (HTTP 503) |

---

## Conventions Developers Should Follow

1. **Always pair `header("Location: ...")` with `exit(0)`** — Prevents unintended code execution after redirects.
2. **Use `$_SESSION['status']` + `$_SESSION['status_code']`** for all user-facing messages. Valid codes: `"success"`, `"error"`, `"warning"`, `"danger"`.
3. **Prefer prepared statements** (`mysqli_stmt_prepare`, `mysqli_stmt_bind_param`) over `mysqli_real_escape_string()` for SQL injection prevention.
4. **Catch PHPMailer exceptions** and log via `error_log()`. Do not expose raw error details to users unless sanitized.
5. **Include `admin/authentication.php`** at the top of every admin-only page to enforce access control.
6. **Validate input inline** before database operations. Set appropriate `$_SESSION['status_code']` values for different failure modes.
7. **Do not rely on `die()`** for production error handling — it exposes internal details and halts execution abruptly. Consider replacing with session-based error messages and redirects.
8. **Be aware that email failures are silent** — `error_log()` output may not be monitored. Consider adding user-visible fallback notifications.

---

## Gaps and Risks

- **No centralized error handler** — No `set_error_handler()`, `set_exception_handler()`, or unified logging strategy.
- **Inconsistent error exposure** — Some files expose raw DB errors to users (`$stmt->error` in `admin/add_account.php`), while others suppress them.
- **No structured logging** — `error_log()` is used sporadically with no log rotation, levels, or aggregation.
- **SQL injection risk** — Many queries still use string interpolation with `mysqli_real_escape_string()` instead of prepared statements.
- **Silent failures** — Email sending errors are logged but never surfaced; users may assume notifications were sent when they were not.