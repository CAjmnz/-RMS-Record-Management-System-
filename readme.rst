📌 Record Management System (RMS) CREATE USER ARC

A **CodeIgniter 3-based Record Management System** with full CRUD functionality for user management, built with a clean UI and AJAX-powered interactions.

---

🚀 Project Overview

This system allows administrators to:

- Create users
- View user list
- Edit user information
- Delete users
- Manage user roles and status
- Track basic user details

Built using:
- CodeIgniter 3 (PHP MVC Framework)
- MySQL Database
- jQuery AJAX
- Bootstrap UI components
- Custom RMS UI styling

---

📁 Project Structure


application/
│
├── controllers/
│ └── Users.php
│ └── Profile.php
│
├── models/
│ └── User_model.php
│
├── views/
│ ├── users/
│ │ └── index.php
│ │ └── edit.php (optional if used)
│ │
│ ├── profile/
│ │ └── index.php
│ │
│ ├── templates/
│ │ ├── head.php
│ │ ├── sidebar.php
│ │ ├── topbar.php
│ │ └── footer.php
│
├── config/
│ └── routes.php
│ └── database.php


---

⚙️ Features

👤 User Management
- Create user with full profile details
- Edit user (AJAX modal update)
- Soft delete / delete user
- Role-based access control (Admin / User)
- Active / Inactive status toggle

🧾 Fields Supported
- First Name
- Last Name
- Employee ID
- Birthday
- Contact Number
- Address
- Email
- Password
- Role
- Status
- Job Title
- Department

---

🖥️ UI Modules

 📊 User Table
- Displays all users
- Search filter
- Role badge display
- Status indicator
- Action buttons (Edit / Delete)

---

➕ Create User Modal
- Full form layout
- Two-column structured inputs
- Dropdowns for role & status
- Clean RMS-styled modal design

---

✏️ Edit User Modal
- Same structure as Create User
- Pre-filled via AJAX (`users/get/{id}`)
- Updates via `users/update`

---

🔌 API / Controller Endpoints

 Users Controller

| Method | Endpoint | Description |
|------|--------|-------------|
| GET | /users | Load user list |
| GET | /users/get/{id} | Fetch user data |
| POST | /users/store | Create user |
| POST | /users/update | Update user |
| POST | /users/delete/{id} | Delete user |

---

🧠 AJAX Flow

Edit User

Click Edit → GET user data → populate modal → submit update → reload


 Create User

Fill form → POST store → reload table


## Delete User

Click delete → confirm → POST delete → reload table


---

🛠️ Setup Instructions

1. Clone project into XAMPP:

htdocs/rms


2. Configure database:

application/config/database.php


3. Run migration / import SQL schema

4. Start server:

http://localhost/rms


---

🔐 Access Control

- Admin users: full CRUD access
- Regular users: read-only access (depending on role checks)

---

📌 Notes

- Uses CodeIgniter `$this->load->view($view, $data)` pattern
- All modals use Bootstrap
- AJAX handled via jQuery
- UI is custom RMS design system (do not overwrite without consistency)

---


 📈 patch notes

🧱 User Management System (CRUD Stabilized)
✔ Create User
Fixed NULL field insertion issues (firstname bug identified for next patch)
Full form-based user creation via AJAX
✔ Read Users
Fully integrated with DataTables
Displays complete user dataset
Soft-deleted users excluded properly
✔ Update User
AJAX-based edit system
Modal-based editing fixed and stabilized
Unified form structure between create and edit
✔ Delete User
Replaced confirm() with SweetAlert2 (SWAL)
Soft delete implemented using deleted_at
AJAX-based delete with success/error feedback

---

 📈 Future Improvements

- Form validation (client + server)
- Toast notifications
- Pagination + server-side filtering
- Audit logs (who edited what)
- Role permission matrix
- Activity tracking system

---

 🧑‍💻 Author

Developed as part of **RMS System Development (CodeIgniter 3)**

---
