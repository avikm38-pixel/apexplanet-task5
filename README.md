# ApexPlanet Final Project - Blog Management Web Application

A full-stack, secure Blog Management Application built with PHP, MySQL, and Bootstrap 5 as part of the ApexPlanet Web Development Internship.

## Features
- **User Authentication:** Registration and login system with secure password hashing (`password_hash`).
- **Role-Based Access Control (RBAC):** Distinct roles for `Admin` (full CRUD permissions) and `Editor` (read-only access).
- **CRUD Operations:** Create, Read, Update, and Delete blog posts.
- **Search Functionality:** Filter posts by matching keyword in title or content.
- **Pagination:** Clean page navigation dividing posts into manageable chunks.
- **Security Enhancements:** Prepared statements with parameter binding to prevent SQL Injection, client-side & server-side validation.
- **Responsive UI:** Styled cleanly with Bootstrap 5.

## Tech Stack
- Frontend: HTML5, CSS3, Bootstrap 5
- Backend: PHP (Procedural/OOP)
- Database: MySQL
- Server Environment: Apache (XAMPP)

## Setup Instructions
1. Clone the repository into `C:/xampp/htdocs/`.
2. Start Apache and MySQL from XAMPP Control Panel.
3. Import the database schema or configure `db.php` with your local database credentials.
4. Navigate to `http://localhost/apexplanet-task5/login.php` in your browser.