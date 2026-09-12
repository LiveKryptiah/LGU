# Government Workflow OS

**Workplace Management System for Philippine Government Offices**

A clean, modular web application built for Local Government Units (LGUs) and Provincial Government offices, adhering strictly to modern government design principles adapted from `DESIGN.md`.

---

## Technology Stack

* **Backend / SSR**: PHP (PHP 7.4+, 8.0+) with PDO
* **Database**: MySQL 5.7+ / 8.0+ / MariaDB
* **Structure**: Semantic HTML5
* **Styling**: Modern Custom CSS (Pill actions, 16px cards, 8px inputs, 4px/8px rhythm spacing system, professional government blue palette)
* **Client Behavior**: Vanilla JavaScript (Collapsible sidebar, mobile drawer, dropdowns, modal controllers)

---

## Directory Structure

```text
/LGU-OS
│
├── /assets
│   ├── /css
│   │   ├── design-system.css   (Typography, 4px/8px rhythm, color tokens, variables)
│   │   ├── layout.css          (Collapsible sidebar, sticky header, mobile drawer)
│   │   └── components.css      (Pills, 16px cards, 8px inputs, badges, empty states)
│   ├── /js
│   │   └── app.js              (Sidebar toggle, mobile drawer, dropdowns, shortcuts)
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
│   ├── dashboard.php           (Good morning greeting, 4 summary cards, quick actions)
│   ├── tasks.php               (My Tasks placeholder with empty state)
│   ├── office-tasks.php        (Office Tasks placeholder with empty state)
│   ├── offices.php             (Provincial Offices directory placeholder)
│   ├── calendar.php            (Calendar & events placeholder with empty state)
│   ├── employees.php           (Personnel directory placeholder with active staff)
│   ├── activity.php            (Audit log & event stream placeholder)
│   ├── notifications.php       (Notification center placeholder with empty state)
│   ├── reports.php             (Administrative reports placeholder with empty state)
│   ├── settings.php            (Account preferences and system environment)
│   └── help.php                (Help desk & user manual placeholder)
│
├── /api
│   └── index.php               (API gateway entry point)
│
├── /database
│   ├── schema.sql              (Organizations, offices, employees table DDL)
│   └── seed.sql                (Provincial Government & Juan Dela Cruz seed data)
│
├── index.php                   (Route dispatcher: auth -> dashboard, guest -> login)
├── login.php                   (Sign in portal with autofill demo button)
├── logout.php                  (Session termination and redirect)
├── install.php                 (One-click web database installer wizard)
└── start-server.bat            (Convenience local server launcher)
```

---

## Database Architecture

The application requires database **`government_workflow_os`** with three foundational tables:

### 1. `organizations`
* `id` (INT UNSIGNED AUTO_INCREMENT PRIMARY KEY)
* `name` (VARCHAR)
* `organization_type` (VARCHAR)
* `address` (TEXT)
* `logo` (VARCHAR)
* `created_at`, `updated_at` (TIMESTAMP)

### 2. `offices`
* `id` (INT UNSIGNED AUTO_INCREMENT PRIMARY KEY)
* `organization_id` (FOREIGN KEY -> `organizations.id`)
* `name` (VARCHAR)
* `description` (TEXT)
* `status` (ENUM 'active', 'inactive')
* `created_at`, `updated_at` (TIMESTAMP)

### 3. `employees`
* `id` (INT UNSIGNED AUTO_INCREMENT PRIMARY KEY)
* `organization_id` (FOREIGN KEY -> `organizations.id`)
* `office_id` (FOREIGN KEY -> `offices.id`)
* `first_name` (VARCHAR)
* `last_name` (VARCHAR)
* `position` (VARCHAR)
* `email` (VARCHAR UNIQUE)
* `password` (VARCHAR, hashed with `password_hash()`)
* `role` (ENUM 'admin', 'supervisor', 'staff')
* `status` (ENUM 'active', 'inactive', 'on_leave')
* `created_at`, `updated_at` (TIMESTAMP)

---

## Demo Organization & Seed Credentials

* **Organization**: Provincial Government
* **Mandated Offices**:
  1. Provincial Assessor's Office
  2. Provincial Treasurer's Office
  3. Provincial Engineering Office
  4. Human Resource Management Office
  5. General Services Office
  6. Planning and Development Office
* **Demo Account**:
  * **Name**: Juan Dela Cruz
  * **Position**: Administrative Officer
  * **Office**: Provincial Assessor's Office
  * **Email**: `juan.delacruz@pgov.ph`
  * **Password**: `Password123!`

---

## Installation & Running

### Option 1: Browser-Based Installer (Recommended)
1. Ensure your local web server (e.g., XAMPP, Laragon, or Apache) has MySQL started.
2. Open your web browser to `http://localhost/LGU-OS/install.php` (or `http://localhost:8000/install.php`).
3. Click **"Initialize Database & Seed Data"**.
4. Click **"Go to Sign In Portal"** and log in with the demo credentials.

### Option 2: Command Line Import
```bash
mysql -u root -p < database/schema.sql
mysql -u root -p government_workflow_os < database/seed.sql
```

### Option 3: Quick Start via Batch Launcher
Double click `start-server.bat` in the project root.
