The repository employs a hybrid dependency management strategy, combining **Composer** for specific third-party libraries with **manual vendoring** (direct inclusion of source code) for others. This approach lacks a unified autoloading mechanism across the entire application, leading to fragmented dependency resolution.

### 1. Dependency Declaration & Versioning
- **Composer**: Used in three distinct locations:
  - Root (`composer.json`): Manages `phpmailer/phpmailer` (^6.9).
  - Admin Module (`admin/composer.json`): Manages `phpoffice/phpspreadsheet` (^3.4).
  - PHPMailer Module (`phpmailer/composer.json`): Redundant declaration of `phpmailer/phpmailer`.
- **Lockfiles**: `composer.lock` files are present at the root and admin levels, ensuring deterministic builds for Composer-managed dependencies.
- **Manual Vendoring**: Major libraries like **TCPDF** (`admin/tcpdf/`) and **PHP QR Code** (`qrcode/`, `phpqrcode/`) are included as raw source directories without Composer integration. These libraries maintain their own internal versioning files (e.g., `VERSION`, `CHANGELOG`).

### 2. Autoloading & Inclusion Patterns
- **No Global Autoloader**: The application does not utilize a single `vendor/autoload.php` at the root. Instead, dependencies are loaded via explicit `require` or `require_once` statements scattered throughout the codebase.
- **Path Fragility**: Inclusion paths are often relative and hardcoded (e.g., `require 'phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';` or `require_once('../qrcode/qrlib.php');`). This creates tight coupling between file locations and makes refactoring or moving modules error-prone.
- **Inconsistent Vendor Paths**: While `phpmailer` is installed via Composer, some files manually require individual class files from the `vendor` directory rather than using the Composer autoloader. Other libraries like TCPDF are required directly from their vendored root (e.g., `require_once('tcpdf/tcpdf.php');`).

### 3. Key Dependencies
- **PHPMailer**: Used for email notifications (OTP, password resets). Managed via Composer but often manually required.
- **PHPSpreadsheet**: Used for Excel report generation in the admin panel. Managed via Composer in the `admin` module.
- **TCPDF**: Used for PDF generation (receipts, reports). Manually vendored in `admin/tcpdf/`.
- **PHP QR Code**: Used for generating QR codes for attendance. Manually vendored in `qrcode/` and `phpqrcode/`.
- **Frontend Libraries**: JavaScript and CSS libraries (Bootstrap, jQuery, DataTables, SweetAlert2) are manually downloaded and stored in `assets/` and `admin/assets/` directories, with no package manager (like npm or yarn) managing them.

### 4. Developer Conventions & Risks
- **Manual Updates**: Updating vendored libraries (TCPDF, QR Code, Frontend assets) requires manual download and replacement, increasing the risk of missing security patches.
- **Path Sensitivity**: Developers must be careful with relative paths when including dependencies. Moving a file may break its ability to find required libraries.
- **Redundancy**: The `phpmailer` dependency is declared in both the root and the `phpmailer` subdirectory, suggesting a lack of centralized dependency governance.
- **No Frontend Build Step**: Frontend assets are served directly from the `assets` directories, implying no build tooling (webpack/vite) is used for bundling or minification.