The Library Resource Center Management System employs a rudimentary, file-based configuration approach typical of legacy PHP applications. There is no centralized configuration framework, environment variable management, or feature flag system.

### Database Configuration
The core runtime configuration is located in `admin/config/dbcon.php`. This file defines database credentials (host, username, password, database name) as hardcoded global variables (`$host`, `$username`, `$password`, `$database`). It immediately establishes a `mysqli` connection stored in the global `$con` variable. 
- **No Environment Separation**: Development and production credentials are toggled by manually commenting/uncommenting lines within the same file, rather than using environment-specific files or `.env` variables.
- **Global State**: The database connection is a global side-effect, relied upon by including this file in nearly every script.

### Security and Encryption Keys
Cryptographic keys for URL parameter encryption/decryption are hardcoded directly in utility files (`includes/url.php`, `admin/includes/url.php`, `attendance/url.php`). The AES-256-CBC secret key (`'MCC LRC'`) and initialization vector (`'mcc-lrc'`) are static strings present in multiple duplicated files across the repository.

### Dependency Management
Third-party libraries (PHPMailer, TCPDF, PHP QR Code) are managed via a hybrid approach:
- **Composer**: A `composer.json` file exists at the root and in subdirectories (`admin/`, `phpmailer/`) to declare dependencies like `phpmailer/phpmailer`.
- **Manual Vendoring**: Despite the presence of Composer, many libraries (e.g., TCPDF, PHP QR Code) are manually copied into the repository root or specific modules (`admin/tcpdf/`, `phpqrcode/`), bypassing the autoloader in favor of manual `require`/`include` statements.

### Architectural Conventions
- **Include-Based Loading**: Configuration and layout components are loaded using relative `include` or `require` statements (e.g., `include('config/dbcon.php')`).
- **Duplicated Logic**: Critical configuration logic, such as the `encryptor` function and database connection paths, is duplicated across the `admin`, `attendance`, and root `includes` directories, leading to maintenance challenges and potential inconsistency.
- **No Centralized Config Loader**: There is no single entry point that loads and validates configuration; each script is responsible for its own dependency inclusion.