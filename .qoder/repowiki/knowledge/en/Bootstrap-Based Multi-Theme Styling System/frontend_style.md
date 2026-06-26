## Overview

This PHP-based Library Resource Center Management System uses **Bootstrap 5** as its primary CSS framework, with custom overrides organized into modular stylesheet files. The application has three distinct UI contexts (public portal, admin dashboard, attendance kiosk), each with duplicated but slightly customized asset bundles.

## Architecture & Approach

### Framework Foundation
- **Bootstrap 5** (via `bootstrap5.min.css` and `bootstrap.min.css`) provides the core grid system, components, and utility classes
- No CSS preprocessor build pipeline — all styles are plain CSS with minimal SCSS only in third-party libraries (`dselect.scss`, `bootstrap-icons.scss`)
- No CSS methodology like BEM or SMACSS is formally adopted; selectors use flat class names and component-scoped nesting

### Asset Organization

Assets are **duplicated across three module directories**:
- `assets/` — Public-facing portal (login, signup, OPAC browsing)
- `admin/assets/` — Administrative dashboard (full CRUD interface)
- `attendance/assets/` — QR-based attendance kiosk

Each module maintains its own copies of:
- Bootstrap CSS/JS bundles
- Icon libraries (Bootstrap Icons, Boxicons, Remixicon, Unicons)
- Third-party plugins (DataTables, SweetAlert2, Alertify, AOS animations)
- Custom `style.css` for module-specific overrides

### Layout Composition Pattern

Pages use PHP includes for consistent structure:
- `includes/header.php` / `admin/includes/header.php` — `<head>` with CSS links
- `includes/script.php` / `admin/includes/script.php` — JS bundles before `</body>`
- `includes/navbar.php`, `admin/includes/sidebar.php`, `admin/includes/topnav.php`, `admin/includes/footer.php` — Reusable layout fragments

## Design Tokens & Color Palette

No formal design token system (CSS custom properties) exists beyond Bootstrap's native `--bs-*` variables. Colors are hardcoded throughout stylesheets:

| Token | Value | Usage |
|-------|-------|-------|
| Primary accent | `#05c3dd` (cyan) | Sidebar active states, buttons, links, focus borders |
| Secondary blue | `#4154f1` | Back-to-top button, error page accents, tab active states |
| Dark navy | `#012970` | Headings, logo text, sidebar collapsed state |
| Background | `#f6f9ff` | Page background, hover states |
| Muted text | `#899bbd` | Breadcrumbs, secondary labels |
| Danger | `#e41a2f` | Unreturned book indicators |
| Success | `#2eca6a` / `#0e773d` | Borrowed books, fines cards |

## Typography

Three font families loaded from Google Fonts (admin only):
- **Open Sans** — Body text default
- **Nunito** — Headings (h1–h6)
- **Poppins** — Card titles, alert headings

Public portal (`signup.css`) uses only **Poppins** via `@import`.

## Key Styling Conventions

### Admin Dashboard (`admin/assets/css/style.css`, ~1081 lines)
- Fixed sidebar (300px wide) with collapsible navigation
- Top header bar (60px height) with search, notifications, profile dropdown
- Card-based layout with soft shadows (`box-shadow: 0px 0 30px rgba(1, 41, 112, 0.1)`)
- Dashboard info cards with color-coded icons (books=orange, students=blue, borrowed=gray, unreturned=red, fines=green)
- DataTables integration with Bootstrap 5 theme
- Responsive: sidebar hides off-canvas below 1200px breakpoint

### Public Portal (`assets/css/style.css`, ~53 lines + `login.css`, `signup.css`)
- Minimal custom CSS — mostly relies on Bootstrap defaults
- Login page: centered flex layout with gradient background (`linear-gradient(bottom, #0250c5, #05c3dd)`) and background image overlay
- Signup page: multi-step form wizard with animated progress bar bullets
- Footer: solid blue (`#0096ff`) with centered copyright text

### Attendance Module (`attendance/assets/css/style.css`, ~1063 lines)
- Nearly identical to admin dashboard styles (copy-pasted with minor differences)
- Omits some admin-specific sections (fines card, unreturned card)
- Uses same sidebar/header architecture

## Third-Party UI Libraries

| Library | Purpose | Files |
|---------|---------|-------|
| SweetAlert2 | Modal dialogs, success/error toasts | `sweetalert2.min.css`, `sweetalert2.all.min.js` |
| Alertify | Alternative notification system | `alertify.min.css`, `alertify.bootstraptheme.min.css` |
| DataTables | Sortable/filterable tables | `dataTables.bootstrap5.min.css`, multiple JS modules |
| AOS (Animate On Scroll) | Scroll-triggered animations | `aos.css`, `aos.js` |
| Bootstrap Datepicker | Date input widgets | `bootstrap-datepicker.min.css/.js` |
| Cropper.js | Image cropping (profile photos) | CDN-loaded CSS/JS |
| jsPDF + autoTable | PDF report generation | `jspdf.umd.min.js`, `jspdf.plugin.autotable.min.js` |
| Chart.js | Dashboard statistics charts | `chart.min.js` |

## Responsive Strategy

- Single breakpoint at **1199px/1200px** for sidebar toggle behavior
- Below 1200px: sidebar positioned `left: -300px` (off-canvas); toggled via `.toggle-sidebar` body class
- Search bar becomes fixed-position overlay on small screens
- No mobile-first media query strategy; desktop styles are default with downward overrides

## Rules Developers Should Follow

1. **Module isolation**: Each module (`/`, `/admin`, `/attendance`) maintains separate asset copies. Changes to one do not propagate to others — updates must be applied manually to all three.

2. **Custom CSS goes in `style.css`**: Module-specific overrides belong in each module's `assets/css/style.css`. Do not modify minified Bootstrap or third-party library files.

3. **Color consistency**: Use the established palette (`#05c3dd` cyan, `#012970` navy, `#4154f1` blue) rather than introducing new colors. Hardcoded hex values are the norm — there is no central token file.

4. **Layout via PHP includes**: Never duplicate header/footer markup in individual pages. Use `include('./includes/header.php')` and `include('./includes/script.php')` patterns.

5. **Icon usage**: Prefer Bootstrap Icons (`bi-*` classes) for consistency. Boxicons and Remixicon are available in admin but used sparingly.

6. **No build step**: All CSS is served as static files. There is no Sass compilation, PostCSS, or bundler. Direct editing of `.css` files is expected.

7. **Notification pattern**: Server-side status messages use PHP session variables (`$_SESSION['status']`, `$_SESSION['status_code']`) rendered into SweetAlert2 calls in `script.php`.

## Limitations & Technical Debt

- **Asset duplication**: Three near-identical copies of Bootstrap, icons, and custom styles create maintenance burden
- **No CSS custom properties**: Colors and spacing are magic numbers scattered across files
- **Mixed CDN and local assets**: Some libraries loaded from CDN (jQuery UI, SweetAlert2, DataTables), others vendored locally — inconsistent caching and versioning
- **Duplicate script includes**: jQuery loaded twice in public portal (`jquery-3.6.0.min.js` locally + CDN); SweetAlert2 also duplicated
- **No dark mode or theming infrastructure**: Single light theme hardcoded throughout