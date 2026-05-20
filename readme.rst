Here is the raw README.md file content. Copy everything between the lines exactly as is:

C:\xampp\htdocs\rms\README.md — replace entire file with this:
markdown# 📁 RMS — Record Management System

A web-based Record Management System built with CodeIgniter 3, Bootstrap 4, and MySQL. Designed for managing records efficiently with a clean authentication system and scalable architecture.

---

## 🛠 Tech Stack

| Layer | Technology |
|---|---|
| Framework | CodeIgniter 3 |
| Frontend | Bootstrap 4 + jQuery |
| Database | MySQL (via XAMPP) |
| Server | Apache (XAMPP) |
| PHP | 8.2+ |

---

## ✅ Requirements

- XAMPP (Apache + MySQL)
- PHP 8.2 or higher
- CodeIgniter 3
- Any modern web browser

---

## 🚀 Local Setup

### 1. Clone the repository

```bash
git clone https://github.com/CAjmnz/-RMS-Record-Management-System-.git
```

### 2. Move project to XAMPP

Place the project folder inside:
C:\xampp\htdocs\rms\

### 3. Create the database

Open [phpMyAdmin](http://localhost/phpmyadmin) and run:

```sql
CREATE DATABASE rms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Import the users table

```sql
CREATE TABLE `users` (
    `id`        int(11)      NOT NULL AUTO_INCREMENT,
    `firstname` varchar(50)  NOT NULL,
    `lastname`  varchar(50)  NOT NULL,
    `birthday`  date         NOT NULL,
    `age`       int(11)      NOT NULL,
    `address`   varchar(100) NOT NULL,
    `contactno` varchar(11)  NOT NULL,
    `email`     varchar(100) NOT NULL,
    `password`  varchar(255) NOT NULL,
    `create_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5. Insert a test admin user

```sql
INSERT INTO `users` (
    `firstname`, `lastname`, `birthday`, `age`,
    `address`, `contactno`, `email`, `password`
) VALUES (
    'Admin',
    'User',
    '1990-01-01',
    35,
    'Manila Philippines',
    '09000000000',
    'admin@rms.local',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
);
```

> 🔑 Default login password: `password`

### 6. Configure database connection

Open `application/config/database.php` and update:

```php
'hostname' => 'localhost',
'username' => 'root',
'password' => '',
'database' => 'rms',
```

### 7. Start XAMPP

Start **Apache** and **MySQL** from the XAMPP Control Panel.

### 8. Open in browser
http://localhost/rms/

---

## 📁 Project Structure
rms/
├── .htaccess
├── index.php
└── application/
├── config/
│   ├── autoload.php
│   ├── config.php
│   ├── database.php
│   └── routes.php
├── core/
│   └── MY_Controller.php
├── controllers/
│   ├── Home.php
│   ├── Dashboard.php
│   └── auth/
│       ├── Login.php
│       └── Logout.php
├── models/
│   └── auth/
│       └── Auth_model.php
└── views/
├── auth/
│   └── login.php
└── dashboard/
└── index.php

---

## 🏗 Controller Architecture
MY_Controller
│   Loads: session, database, helpers
│   Pre-fetches: flashdata into $this->data
│
├── Guest_Controller
│       Redirects to dashboard if already logged in
│       └── auth/Login.php
│
└── RMS_Controller
Redirects to login if not authenticated
├── auth/Logout.php
└── Dashboard.php

---

## 🔀 Routes

| URL | Controller | Access |
|---|---|---|
| `http://localhost/rms/` | `Home` → redirects to login | Public |
| `http://localhost/rms/login` | `auth/Login@index` | Guest only |
| `http://localhost/rms/auth/login` | `auth/Login@index` | Guest only |
| `http://localhost/rms/auth/login/submit` | `auth/Login@submit` | Guest only |
| `http://localhost/rms/logout` | `auth/Logout@index` | Authenticated |
| `http://localhost/rms/dashboard` | `Dashboard@index` | Authenticated |

---

## ✨ Features

### ✅ Completed — Phase 1

- [x] CodeIgniter 3 base setup
- [x] PHP 8.2 compatibility fix
- [x] Clean URLs without index.php
- [x] Session management
- [x] Login with email and password
- [x] bcrypt password verification
- [x] Flash messages
- [x] Protected route middleware
- [x] Logout with session destroy
- [x] MY_Controller base architecture

### 🔲 Planned

- [ ] Phase 2 — User registration
- [ ] Phase 2 — Edit profile
- [ ] Phase 3 — Dashboard with real stats
- [ ] Phase 4 — Full user CRUD
- [ ] Phase 5 — Role management (admin, staff, viewer)
- [ ] Phase 6 — Search and pagination
- [ ] Phase 7 — File uploads and document storage
- [ ] Phase 8 — Audit logs (who, what, when, IP)
- [ ] Phase 9 — REST API with token auth
- [ ] Phase 10 — Production deployment

---

## 🔒 Security

| Item | Implementation |
|---|---|
| Password storage | `password_hash()` bcrypt |
| Password verification | `password_verify()` |
| SQL injection | CI3 Query Builder (no raw queries) |
| XSS | `htmlspecialchars()` in all views |
| Session hijacking | Session destroyed fully on logout |
| Route protection | `RMS_Controller` middleware |
| Form validation | CI3 Form Validation library |
| Direct URL access | `defined('BASEPATH') OR exit` in all files |

---

## 🐛 Known Issues Fixed

| Issue | Fix Applied |
|---|---|
| `flashdata() on null` crash | Session pre-loaded in `MY_Controller` |
| Headers already sent killing session | PHP 8.2 fix placed before switch block in `index.php` |
| Old `Auth.php` blocking subdirectory routing | File deleted |
| `sess_save_path = NULL` breaking sessions | Changed to `sys_get_temp_dir()` |
| Duplicate `index_page` in config | Cleaned to single empty string |
| `.htaccess` sending to wrong `index.php` | Fixed with `RewriteBase /rms/` |
| `default_controller` failing with subdirectory | Created `Home.php` as root redirect controller |
| Logout redirecting to dashboard | Moved to separate `Logout.php` extending `RMS_Controller` |

---

## 📝 Developer Notes

- PHP 8.2 dynamic property deprecation warnings from CI3 core are suppressed in `index.php`
- `sess_save_path` uses `sys_get_temp_dir()` for reliable XAMPP Windows compatibility
- All subdirectory controllers require `require_once APPPATH . 'core/MY_Controller.php'` at the top
- CI3 cannot use a subdirectory controller as `default_controller` — `Home.php` handles the root URL redirect

---

## 👤 Default Test Account

| Field | Value |
|---|---|
| Email | `admin@rms.local` |
| Password | `password` |

> ⚠️ Change this password immediately after first login in a production environment.

---

## 📄 License

MIT License — free for personal and commercial use.
