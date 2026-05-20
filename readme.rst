###################
Record Management System 
###################
# RMS — Record Management System

A web-based Record Management System built with CodeIgniter 3, Bootstrap 4, and MySQL.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | CodeIgniter 3 |
| Frontend | Bootstrap 4 + jQuery |
| Database | MySQL (via XAMPP) |
| Server | Apache (XAMPP) |
| PHP | 8.2+ |

---

## Requirements

- XAMPP (Apache + MySQL)
- PHP 8.2 or higher
- CodeIgniter 3
- Web browser

---

## Local Setup

**1. Clone the repository**
```bash
**3. Create the database**

Open phpMyAdmin and run:
```sql
CREATE DATABASE rms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**4. Import the users table**
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

**5. Insert a test admin user**
```sql
INSERT INTO `users` (`firstname`, `lastname`, `birthday`, `age`, `address`, `contactno`, `email`, `password`)
VALUES (
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

> Default password: `password`

**6. Configure database connection**

Open `application/config/database.php` and set:
```php
'hostname' => 'localhost',
'username' => 'root',
'password' => '',
'database' => 'rms',
```

**7. Start Apache and MySQL in XAMPP**

**8. Open in browser**
