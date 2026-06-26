The Library Resource Center Management System lacks a formal automated build system, CI/CD pipeline, or containerization strategy. It is a traditional monolithic PHP application designed for manual deployment to an Apache web server.

### Dependency Management
- **Composer**: Used sporadically for third-party libraries. 
  - Root `composer.json` requires `phpmailer/phpmailer`.
  - `admin/composer.json` requires `phpoffice/phpspreadsheet`.
  - `phpmailer/composer.json` also requires `phpmailer/phpmailer`.
  - **Note**: The `vendor/` directories in these locations are empty or missing in the repository structure, suggesting dependencies are either not committed, managed via manual inclusion (e.g., the `phpmailer/` and `tcpdf/` directories appear to be manually vendored copies), or installed in a production environment only.

### Build & Packaging
- **No Build Scripts**: There are no `Makefile`, `build.sh`, or similar automation scripts for compiling assets or packaging the application.
- **No Containerization**: No `Dockerfile` or `docker-compose.yml` is present.
- **Asset Management**: CSS, JavaScript, and image assets are stored in static directories (`assets/`, `admin/assets/`) and served directly. There is no asset bundling, minification, or compilation step (e.g., Webpack, Vite) evident in the repository.

### Deployment Configuration
- **Apache `.htaccess`**: The primary configuration mechanism is via `.htaccess` files.
  - **Root `.htaccess`**: Handles URL rewriting to append `.php` extensions (enabling clean URLs), enforces HSTS, sets security headers (X-Content-Type-Options, X-Frame-Options, etc.), and configures caching policies for static vs. dynamic content.
  - **Admin `.htaccess`**: Provides similar URL rewriting for the admin subdirectory.
- **Manual Setup**: Deployment likely involves copying all files to a web-accessible directory on a LAMP (Linux, Apache, MySQL, PHP) stack and ensuring `mod_rewrite` is enabled.

### Developer Conventions
- **Direct PHP Execution**: The application relies on direct PHP file execution by the web server. There is no entry-point abstraction (like `index.php` routing all requests) beyond simple rewrite rules.
- **Vendor Libraries**: Third-party libraries like TCPDF and PHP QR Code are included as full source trees within the project root, indicating a manual dependency management approach for these components rather than using Composer autoloading consistently across the entire project.