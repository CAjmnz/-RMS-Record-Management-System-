<div align="center">

# 🗂️ RMS — Record Management System

### Role-Based System built with CodeIgniter 3

![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)
![CI](https://img.shields.io/badge/CodeIgniter-3-red)
![Bootstrap](https://img.shields.io/badge/Bootstrap-4-purple)
![MySQL](https://img.shields.io/badge/MySQL-Database-orange)

---

A secure **MVC-based Record Management System** with authentication, role-based access control, and full user management.

</div>

---

## 🚀 Project Overview

RMS is designed for **internal management systems** where:
- Admin controls all users
- Users have limited access
- Authentication is secure and session-based

---

## ⚙️ Tech Stack

- PHP (CodeIgniter 3)
- MySQL
- Bootstrap 4
- jQuery
- Apache (XAMPP)

---

## 🔐 Authentication System

- Login / Logout
- Session-based authentication
- Password hashing (bcrypt)
- Protected routes (Guest vs RMS controllers)

---

## 👤 User Management (RBAC)

### Admin
✔ Create users  
✔ Edit users  
✔ Delete users  
✔ View all users  

### Regular User
✔ View dashboard  
✔ View users (read-only)  

---

## 📊 Dashboard Features

- Welcome panel
- Logged-in user info
- Modular system cards:
  - Records
  - Users
  - Reports

---

## 🗄️ Database Structure

```sql
users
├── id
├── firstname
├── lastname
├── birthday
├── address
├── contactno
├── email
├── password
├── role (admin/user)
├── is_active
├── deleted_at
├── created_at
└── updated_at
