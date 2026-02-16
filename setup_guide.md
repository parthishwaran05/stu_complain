# Student Complaint Portal - XAMPP Setup Guide

This guide will help you run the Student Complaint Portal using **PHP + MySQL** on **XAMPP**.

## Prerequisites
1. **XAMPP** (Apache + MySQL): https://www.apachefriends.org/
2. **VS Code** (optional for editing): https://code.visualstudio.com/

---

## Step 1: Place the Project in XAMPP
1. Copy this project folder into your XAMPP web root:
   - Windows: `C:\xampp\htdocs\vallarasu`
   - Linux: `/opt/lampp/htdocs/vallarasu`

## Step 2: Start Apache & MySQL
1. Open XAMPP Control Panel.
2. Start **Apache** and **MySQL**.

## Step 3: Import the Database
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click **Import** and choose the file:
   - `database.sql`
3. Click **Go** to import.

## Step 4: Update Database Credentials (if needed)
Open `includes/db.php` and update:
- `$DB_HOST`, `$DB_NAME`, `$DB_USER`, `$DB_PASS`

## Step 5: Access the Portal
Open in your browser:
http://localhost/vallarasu/index.php

## Default Admin Login
- **Email**: admin@portal.com
- **Password**: Admin@123

---

## Notes
- Students can register from the Register page.
- Complaint tracking works via Complaint ID.
- Attachments are stored in `/uploads/complaints`.

## Troubleshooting
- **Database Connection Error**: Verify MySQL is running and credentials in `includes/db.php`.
- **Permission Issues**: Ensure the `uploads/` folder is writable.
