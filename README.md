# Government Workflow OS

**Workplace Management System for Philippine Government Offices**

A clean, modular web application built for Local Government Units (LGUs) and Provincial Government offices, adhering strictly to modern government design principles adapted from `DESIGN.md`.

---

## Technology Stack

* **Server-Side Edition**: PHP 7.4+ / 8.0+ with PDO and MySQL
* **Standalone Browser Edition**: HTML5 + CSS3 + Vanilla JavaScript (Zero dependencies, runs directly in any browser)
* **Design System**: 4px base / 8px rhythm spacing system, 16px cards, 8px inputs, full-radius stadium pills, Inter typography, professional government blue palette.

---

## Quick Launch (3 Ways to Run)

### Option 1: Double-Click Launcher (Recommended)
Double-click **`start-server.bat`** in the project folder. It will automatically detect your environment (PHP, Python, or PowerShell) and launch the app in your default browser.

### Option 2: Zero-Dependency Python Server
```bash
python serve.py
```
This starts the local web server at `http://localhost:8000/` and opens your browser automatically.

### Option 3: Direct Browser Launch
Double-click **`index.html`** or **`login.html`** to open the interface directly in Chrome, Edge, or Firefox.

### Option 4: Full PHP + MySQL Stack (XAMPP / Laragon)
1. Start Apache and MySQL in your XAMPP Control Panel.
2. Ensure this folder is inside your `htdocs` (e.g. `C:\xampp\htdocs\LGU-OS`).
3. Open `http://localhost/LGU-OS/install.php` to initialize the database and seed data.
4. Open `http://localhost/LGU-OS/login.php` to sign in.

---

## Demo Credentials

* **Email:** `juan.delacruz@pgov.ph`
* **Password:** `Password123!`
* **Officer:** Juan Dela Cruz (Administrative Officer)
* **Office:** Provincial Assessor's Office &bull; Provincial Government

---

## Directory Structure

```text
/LGU-OS
│
├── /assets
│   ├── /css
│   │   ├── design-system.css   (Typography, 4px/8px rhythm, color tokens)
│   │   ├── layout.css          (Collapsible sidebar, sticky header, mobile drawer)
│   │   └── components.css      (Pills, 16px cards, 8px inputs, badges, empty states)
│   ├── /js
│   │   ├── app.js              (Sidebar toggle, mobile drawer, dropdowns, shortcuts)
│   │   └── auth.js             (Client-side session management & demo auth)
│   └── /images
│       └── logo.svg            (Official government crest emblem)
│
├── /config
│   └── database.php            (Centralized PDO database configuration & helpers)
│
├── /includes
│   ├── auth.php                (Session management, auth guards, CSRF, escaping)
│   ├── header.php              (Top bar with search, notifications, profile menu)
│   ├── sidebar.php             (Collapsible left navigation with active states)
│   └── footer.php              (Official footer and script mounting)
│
├── /pages
│   ├── dashboard.php / .html   (Good morning greeting, 4 summary cards, quick actions)
│   ├── tasks.php / .html       (My Tasks placeholder with empty state)
│   ├── office-tasks.php/.html  (Office Tasks placeholder with empty state)
│   ├── offices.php / .html     (Provincial Offices directory placeholder)
│   ├── calendar.php / .html    (Calendar & events placeholder with empty state)
│   ├── employees.php / .html   (Personnel directory placeholder with active staff)
│   ├── activity.php / .html    (Audit log & event stream placeholder)
│   ├── notifications.php/.html (Notification center placeholder with empty state)
│   ├── reports.php / .html     (Administrative reports placeholder with empty state)
│   ├── settings.php / .html    (Account preferences and system environment)
│   └── help.php / .html        (Help desk & user manual placeholder)
│
├── /api
│   └── index.php               (API gateway entry point)
│
├── /database
│   ├── schema.sql              (Organizations, offices, employees table DDL)
│   └── seed.sql                (Provincial Government & Juan Dela Cruz seed data)
│
├── index.html / index.php      (Application entrypoints with automatic routing)
├── login.html / login.php      (Sign in portal with autofill demo button)
├── logout.php                  (Session termination and redirect)
├── install.php                 (One-click web database installer wizard)
├── serve.py                    (Zero-dependency Python local server)
├── serve.ps1                   (Zero-dependency Windows PowerShell local server)
└── start-server.bat            (Automatic environment launcher)
```
