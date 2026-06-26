# Attendance Tracking System

<cite>
**Referenced Files in This Document**
- [index.php](file://attendance/index.php)
- [process_qr.php](file://attendance/process_qr.php)
- [view.php](file://attendance/view.php)
- [view_faculty.php](file://attendance/view_faculty.php)
- [url.php](file://attendance/url.php)
- [dbcon.php](file://admin/config/dbcon.php)
- [attendance.php](file://admin/attendance.php)
- [authentication.php](file://admin/authentication.php)
- [user_student.php](file://admin/user_student.php)
- [user_faculty.php](file://admin/user_faculty.php)
</cite>

## Table of Contents
1. [Introduction](#introduction)
2. [Project Structure](#project-structure)
3. [Core Components](#core-components)
4. [Architecture Overview](#architecture-overview)
5. [Detailed Component Analysis](#detailed-component-analysis)
6. [Dependency Analysis](#dependency-analysis)
7. [Performance Considerations](#performance-considerations)
8. [Troubleshooting Guide](#troubleshooting-guide)
9. [Conclusion](#conclusion)

## Introduction
This document describes a QR code-based attendance tracking system designed for students and faculty members within a library resource center environment. The system enables quick presence verification via QR scanning, logs attendance with timestamps, prevents duplicate entries for the same day, and provides administrative views for filtering and reporting. It also includes faculty-specific workflows and reporting capabilities, along with secure URL parameter handling for user identification.

## Project Structure
The attendance system spans two primary areas:
- Public-facing QR scanner and result viewers under the `attendance/` directory
- Administrative dashboards and reporting under the `admin/` directory

Key directories and files:
- `attendance/`: Scanner UI, QR processing, and user result displays
- `admin/`: Attendance listing, filtering, and reporting interfaces
- `admin/config/dbcon.php`: Database connection configuration
- `admin/authentication.php`: Session and access control enforcement
- `admin/user_student.php`, `admin/user_faculty.php`: User management for students and faculty

```mermaid
graph TB
subgraph "Public Scanner"
A_index["attendance/index.php"]
A_process["attendance/process_qr.php"]
A_view["attendance/view.php"]
A_viewf["attendance/view_faculty.php"]
A_url["attendance/url.php"]
end
subgraph "Admin Dashboard"
D_attendance["admin/attendance.php"]
D_auth["admin/authentication.php"]
D_db["admin/config/dbcon.php"]
D_users["admin/user_student.php"]
D_usersf["admin/user_faculty.php"]
end
A_index --> A_process
A_process --> A_view
A_process --> A_viewf
A_view --> A_url
A_viewf --> A_url
D_attendance --> D_auth
D_attendance --> D_db
D_users --> D_db
D_usersf --> D_db
```

**Diagram sources**
- [index.php:1-179](file://attendance/index.php#L1-L179)
- [process_qr.php:1-133](file://attendance/process_qr.php#L1-L133)
- [view.php:1-110](file://attendance/view.php#L1-L110)
- [view_faculty.php:1-109](file://attendance/view_faculty.php#L1-L109)
- [url.php:1-22](file://attendance/url.php#L1-L22)
- [attendance.php:1-234](file://admin/attendance.php#L1-L234)
- [authentication.php:1-28](file://admin/authentication.php#L1-L28)
- [dbcon.php:1-19](file://admin/config/dbcon.php#L1-L19)
- [user_student.php:1-412](file://admin/user_student.php#L1-L412)
- [user_faculty.php:1-371](file://admin/user_faculty.php#L1-L371)

**Section sources**
- [index.php:1-179](file://attendance/index.php#L1-L179)
- [process_qr.php:1-133](file://attendance/process_qr.php#L1-L133)
- [view.php:1-110](file://attendance/view.php#L1-L110)
- [view_faculty.php:1-109](file://attendance/view_faculty.php#L1-L109)
- [url.php:1-22](file://attendance/url.php#L1-L22)
- [attendance.php:1-234](file://admin/attendance.php#L1-L234)
- [authentication.php:1-28](file://admin/authentication.php#L1-L28)
- [dbcon.php:1-19](file://admin/config/dbcon.php#L1-L19)
- [user_student.php:1-412](file://admin/user_student.php#L1-L412)
- [user_faculty.php:1-371](file://admin/user_faculty.php#L1-L371)

## Core Components
- QR Scanner UI: Initializes camera feed, captures QR codes, and submits scanned data to the server.
- QR Processing Engine: Validates scanned identifiers against student/faculty databases, enforces daily duplicate prevention, and logs attendance with timestamps.
- Result Views: Displays verified user information after successful scan for both students and faculty.
- Attendance Listing: Provides filtered attendance logs by date range and supports export/printing.
- Security Utilities: URL-safe encryption/decryption for passing identifiers securely in URLs.
- Authentication Layer: Enforces session-based access control for administrative pages.

**Section sources**
- [index.php:120-137](file://attendance/index.php#L120-L137)
- [process_qr.php:10-129](file://attendance/process_qr.php#L10-L129)
- [view.php:15-34](file://attendance/view.php#L15-L34)
- [view_faculty.php:15-34](file://attendance/view_faculty.php#L15-L34)
- [attendance.php:84-132](file://admin/attendance.php#L84-L132)
- [url.php:2-21](file://attendance/url.php#L2-L21)
- [authentication.php:1-28](file://admin/authentication.php#L1-L28)

## Architecture Overview
The system follows a client-server model:
- Client-side (scanner): Uses a JavaScript QR library to capture camera input and submit QR payload to the backend.
- Server-side (processing): Validates identity, checks for existing daily log, inserts or updates attendance, and redirects to appropriate result page.
- Admin-side (dashboard): Displays attendance logs with filtering and export capabilities.

```mermaid
sequenceDiagram
participant U as "User"
participant S as "Scanner UI<br/>attendance/index.php"
participant P as "Processor<br/>attendance/process_qr.php"
participant DB as "Database<br/>admin/config/dbcon.php"
participant V as "Viewer<br/>attendance/view.php / view_faculty.php"
U->>S : Open scanner page
S->>S : Initialize camera and QR capture
U->>S : Scan QR code
S->>P : POST scanned text
P->>DB : Query user by identifier
DB-->>P : User record (student/faculty)
P->>DB : Check existing log for today (duplicate prevention)
alt Found existing log
P->>DB : Update time_out
DB-->>P : Success
P-->>V : Redirect to viewer with encrypted identifier
else New log
P->>DB : Insert attendance log with timestamps
DB-->>P : Success
P-->>V : Redirect to viewer with encrypted identifier
end
V-->>U : Display user info and redirect
```

**Diagram sources**
- [index.php:120-137](file://attendance/index.php#L120-L137)
- [process_qr.php:10-129](file://attendance/process_qr.php#L10-L129)
- [view.php:15-34](file://attendance/view.php#L15-L34)
- [view_faculty.php:15-34](file://attendance/view_faculty.php#L15-L34)
- [dbcon.php:1-19](file://admin/config/dbcon.php#L1-L19)

## Detailed Component Analysis

### QR Scanner Interface
- Camera initialization and QR capture using a JavaScript library.
- Submits captured QR text to the server via a hidden form.
- Displays live clock and branding for the scanner environment.

```mermaid
flowchart TD
Start(["Page Load"]) --> InitCam["Initialize Camera"]
InitCam --> Capture["Capture QR Code"]
Capture --> Submit["Submit QR Text to Server"]
Submit --> End(["Redirect to Processor"])
```

**Diagram sources**
- [index.php:120-137](file://attendance/index.php#L120-L137)

**Section sources**
- [index.php:120-137](file://attendance/index.php#L120-L137)

### QR Processing and Attendance Logging
- Accepts QR text and queries both student and faculty tables.
- Implements duplicate detection by checking for an open log entry for the current date.
- Inserts new attendance records with timestamps and roles, or updates existing records with time_out.
- Redirects to result viewers with encrypted identifiers.

```mermaid
flowchart TD
Receive["Receive QR Text"] --> LookupStudent["Lookup Student by ID"]
LookupStudent --> FoundStudent{"Student Found?"}
FoundStudent --> |Yes| CheckDuplicateS["Check Today's Log (Student)"]
FoundStudent --> |No| LookupFaculty["Lookup Faculty by Username"]
LookupFaculty --> FoundFaculty{"Faculty Found?"}
FoundFaculty --> |Yes| CheckDuplicateF["Check Today's Log (Faculty)"]
FoundFaculty --> |No| Error["Exit: User Not Found"]
CheckDuplicateS --> HasEntryS{"Existing Entry?"}
HasEntryS --> |Yes| UpdateOutS["Update time_out"]
HasEntryS --> |No| InsertInS["Insert Attendance Log (Student)"]
CheckDuplicateF --> HasEntryF{"Existing Entry?"}
HasEntryF --> |Yes| UpdateOutF["Update time_out"]
HasEntryF --> |No| InsertInF["Insert Attendance Log (Faculty)"]
UpdateOutS --> EncryptS["Encrypt Identifier"]
InsertInS --> EncryptS
UpdateOutF --> EncryptF["Encrypt Identifier"]
InsertInF --> EncryptF
EncryptS --> RedirectS["Redirect to Student Viewer"]
EncryptF --> RedirectF["Redirect to Faculty Viewer"]
```

**Diagram sources**
- [process_qr.php:10-129](file://attendance/process_qr.php#L10-L129)

**Section sources**
- [process_qr.php:10-129](file://attendance/process_qr.php#L10-L129)

### Result Viewers (Student and Faculty)
- Decrypts identifier from URL and retrieves user details.
- Displays user profile information and redirects back to scanner after a timeout.
- Different layouts for student and faculty result pages.

```mermaid
sequenceDiagram
participant R as "Result Page<br/>view.php / view_faculty.php"
participant U as "URL Handler<br/>url.php"
participant DB as "Database<br/>dbcon.php"
R->>U : Decrypt identifier from URL
U-->>R : Decrypted identifier
R->>DB : Fetch user details
DB-->>R : User record
R-->>R : Render user info
R-->>R : Auto-redirect after delay
```

**Diagram sources**
- [view.php:15-34](file://attendance/view.php#L15-L34)
- [view_faculty.php:15-34](file://attendance/view_faculty.php#L15-L34)
- [url.php:2-21](file://attendance/url.php#L2-L21)
- [dbcon.php:1-19](file://admin/config/dbcon.php#L1-L19)

**Section sources**
- [view.php:15-34](file://attendance/view.php#L15-L34)
- [view_faculty.php:15-34](file://attendance/view_faculty.php#L15-L34)
- [url.php:2-21](file://attendance/url.php#L2-L21)
- [dbcon.php:1-19](file://admin/config/dbcon.php#L1-L19)

### Attendance Listing and Filtering
- Admin page lists attendance logs with date sorting and pagination.
- Supports filtering by date range with a form submission.
- Integrates with DataTables for export/printing and statistics.

```mermaid
flowchart TD
Admin["Admin Attendance Page"] --> Filter["Apply Date Range Filter"]
Filter --> Query["Query Logs Between Dates"]
Query --> Display["Render Table with Sorting"]
Display --> Export["Export/Print Actions"]
```

**Diagram sources**
- [attendance.php:84-132](file://admin/attendance.php#L84-L132)

**Section sources**
- [attendance.php:84-132](file://admin/attendance.php#L84-L132)

### Security and Access Control
- Session-based authentication ensures only authorized users access admin pages.
- URL parameter encryption/decryption prevents tampering and improves privacy.
- Database credentials are centralized for secure connection management.

```mermaid
graph LR
Auth["authentication.php"] --> Dash["Admin Pages"]
URLUtil["url.php"] --> Views["Result Views"]
DBConf["dbcon.php"] --> Proc["Processing Scripts"]
```

**Diagram sources**
- [authentication.php:1-28](file://admin/authentication.php#L1-L28)
- [url.php:2-21](file://attendance/url.php#L2-L21)
- [dbcon.php:1-19](file://admin/config/dbcon.php#L1-L19)

**Section sources**
- [authentication.php:1-28](file://admin/authentication.php#L1-L28)
- [url.php:2-21](file://attendance/url.php#L2-L21)
- [dbcon.php:1-19](file://admin/config/dbcon.php#L1-L19)

## Dependency Analysis
- Scanner UI depends on the processor endpoint and the encryption utility.
- Processor depends on database configuration and performs dual-table lookups.
- Result viewers depend on decryption and database retrieval.
- Admin dashboard depends on authentication and database connectivity.

```mermaid
graph TB
Scanner["attendance/index.php"] --> Processor["attendance/process_qr.php"]
Processor --> DB["admin/config/dbcon.php"]
Processor --> Encrypt["attendance/url.php"]
ViewerS["attendance/view.php"] --> Encrypt
ViewerS --> DB
ViewerF["attendance/view_faculty.php"] --> Encrypt
ViewerF --> DB
AdminDash["admin/attendance.php"] --> Auth["admin/authentication.php"]
AdminDash --> DB
UsersS["admin/user_student.php"] --> DB
UsersF["admin/user_faculty.php"] --> DB
```

**Diagram sources**
- [index.php:1-179](file://attendance/index.php#L1-L179)
- [process_qr.php:1-133](file://attendance/process_qr.php#L1-L133)
- [view.php:1-110](file://attendance/view.php#L1-L110)
- [view_faculty.php:1-109](file://attendance/view_faculty.php#L1-L109)
- [url.php:1-22](file://attendance/url.php#L1-L22)
- [dbcon.php:1-19](file://admin/config/dbcon.php#L1-L19)
- [attendance.php:1-234](file://admin/attendance.php#L1-L234)
- [authentication.php:1-28](file://admin/authentication.php#L1-L28)
- [user_student.php:1-412](file://admin/user_student.php#L1-L412)
- [user_faculty.php:1-371](file://admin/user_faculty.php#L1-L371)

**Section sources**
- [index.php:1-179](file://attendance/index.php#L1-L179)
- [process_qr.php:1-133](file://attendance/process_qr.php#L1-L133)
- [view.php:1-110](file://attendance/view.php#L1-L110)
- [view_faculty.php:1-109](file://attendance/view_faculty.php#L1-L109)
- [url.php:1-22](file://attendance/url.php#L1-L22)
- [dbcon.php:1-19](file://admin/config/dbcon.php#L1-L19)
- [attendance.php:1-234](file://admin/attendance.php#L1-L234)
- [authentication.php:1-28](file://admin/authentication.php#L1-L28)
- [user_student.php:1-412](file://admin/user_student.php#L1-L412)
- [user_faculty.php:1-371](file://admin/user_faculty.php#L1-L371)

## Performance Considerations
- Camera initialization and QR scanning occur client-side; server-side processing is lightweight but should handle concurrent requests efficiently.
- Database queries use prepared statements to mitigate injection risks and improve performance.
- Duplicate detection avoids unnecessary writes by checking for existing logs per day.
- Admin reports leverage DataTables with export/print capabilities; consider server-side pagination for very large datasets.

## Troubleshooting Guide
Common issues and resolutions:
- No cameras found: Verify device permissions and HTTPS deployment; the camera library requires secure contexts.
- Scan Error alerts: Occur when the scanned identifier does not match an approved student or faculty record; ensure user status is approved.
- Timeout notifications: Appear when an existing log is updated with time_out; confirm successful logout.
- Database connection failures: Check credentials and network connectivity in the database configuration file.
- Access denied errors: Ensure proper session and role-based authentication before accessing admin pages.

**Section sources**
- [index.php:123-131](file://attendance/index.php#L123-L131)
- [index.php:144-156](file://attendance/index.php#L144-L156)
- [index.php:162-175](file://attendance/index.php#L162-L175)
- [dbcon.php:1-19](file://admin/config/dbcon.php#L1-L19)
- [authentication.php:12-26](file://admin/authentication.php#L12-L26)

## Conclusion
The QR-based attendance system provides a streamlined solution for capturing presence with robust duplicate prevention, secure identifier handling, and integrated administrative reporting. Its modular design separates concerns between scanning, processing, viewing, and administration, enabling maintainability and scalability. Administrators can filter and export attendance logs, while faculty and students benefit from fast, reliable check-in/check-out experiences.