📁 RMS — Record Management System

A secure Role-Based Record Management System (RMS) built using CodeIgniter 3, Bootstrap 4, jQuery, and MySQL.
Designed with a clean MVC architecture and strict role-based access control.

🧰 Tech Stack










🚀 Features
🔐 Authentication
Login / Logout system
Session-based authentication
Secure password hashing (bcrypt)
Guest vs protected routes
👤 User Management (RBAC)
Admin-only user creation
Admin-only edit and delete
Users can only view data (read-only)
Soft delete implementation
📊 Dashboard
User welcome panel
Session-based display
Modular UI cards (Records, Users, Reports)
🔒 Security
Query Builder (SQL injection protection)
XSS protection (htmlspecialchars)
Role-based access control
Soft delete support (deleted_at)
🏗️ Project Structure
application/
├── controllers/
│   ├── auth/
│   ├── Dashboard.php
│   └── Users.php
│
├── models/
│   ├── auth/
│   └── User_model.php
│
├── views/
│   ├── auth/
│   ├── dashboard/
│   └── users/
│
└── core/
    └── MY_Controller.php
👥 User Roles
Role	Permissions
Admin	Full access (Create, Edit, Delete, View Users)
User	Read-only access (Dashboard + Users table)
🗄️ Database Schema
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    birthday DATE NOT NULL,
    address VARCHAR(100) NOT NULL,
    contactno VARCHAR(11) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    is_active TINYINT(1) DEFAULT 1,
    deleted_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
);
⚙️ Installation
git clone https://github.com/your-username/rms.git
Import database into MySQL (rms)
Configure:
application/config/database.php
application/config/config.php
Run project:
http://localhost/rms/
🔑 Default Login
Email: admin@rms.local
Password: password
📌 Project Status
Module	Status
Authentication	✅ Complete
Dashboard	✅ Complete
User Management	⚙️ In Progress
Reports	⏳ Pending
📜 License

For educational and portfolio use only.

👨‍💻 Author

Built using CodeIgniter 3 MVC architecture for learning and system design practice.
