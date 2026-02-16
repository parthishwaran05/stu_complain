# Student Complaint Portal - Windows Setup Guide

## 📋 Prerequisites
- Windows 10/11
- At least 2GB free disk space
- Admin rights on your computer

---

## 🚀 Step-by-Step Installation

### **Step 1: Download and Install XAMPP**

1. **Download XAMPP for Windows**
   - Visit: https://www.apachefriends.org/download.html
   - Download the latest version (PHP 8.2 recommended)
   - File size: ~150MB

2. **Run the Installer**
   - Right-click the downloaded file → **Run as Administrator**
   - If prompted by antivirus, click **Allow**
   - Click **Next** through the setup wizard
   - **Installation Location**: Choose `C:\xampp` (default)
   - **Components to Install**: Select:
     - ✅ Apache
     - ✅ MySQL
     - ✅ PHP
     - ✅ phpMyAdmin
   - Click **Next** → **Install**

3. **Launch XAMPP Control Panel**
   - After installation, check **"Do you want to start the Control Panel now?"**
   - Or manually open: `C:\xampp\xampp-control.exe`

---

### **Step 2: Start Apache and MySQL**

1. **Open XAMPP Control Panel** (as Administrator)
2. Click **Start** button for:
   - ✅ **Apache** (should show green "Running")
   - ✅ **MySQL** (should show green "Running")
3. If port 80 is blocked:
   - Click **Config** → **Apache (httpd.conf)**
   - Find `Listen 80` → Change to `Listen 8080`
   - Save and restart Apache
   - Access site via: `http://localhost:8080/`

---

### **Step 3: Copy Project Files**

1. **Navigate to XAMPP htdocs folder**:
   ```
   C:\xampp\htdocs\
   ```

2. **Create project folder**:
   - Right-click in `htdocs` → **New** → **Folder**
   - Name it: `vallarasu`

3. **Copy all project files** into `C:\xampp\htdocs\vallarasu\`
   - Copy these files and folders:
     ```
     vallarasu/
     ├── assets/
     ├── includes/
     ├── modules/
     ├── uploads/
     ├── database.sql
     ├── index.php
     ├── login.php
     ├── register.php
     ├── dashboard.php
     └── track.php
     ```

---

### **Step 4: Create Upload Directories**

1. **Open** `C:\xampp\htdocs\vallarasu\uploads\`
2. **Create two folders**:
   - `avatars` (for profile pictures)
   - `complaints` (for complaint attachments)

**Final structure**:
```
C:\xampp\htdocs\vallarasu\uploads\
├── avatars\
└── complaints\
```

---

### **Step 5: Configure BASE_URL**

1. **Open** `C:\xampp\htdocs\vallarasu\includes\config.php`
2. **Verify the content**:
   ```php
   <?php
   // BASE_URL should match your directory structure
   $BASE_URL = '/vallarasu';
   ```
3. **Save the file**

**Note**: If you place files directly in `htdocs` (not in `vallarasu` folder):
- Change to: `$BASE_URL = '';`

---

### **Step 6: Create the Database**

#### **Option A: Using phpMyAdmin (Recommended)**

1. **Open your browser** and go to:
   ```
   http://localhost/phpmyadmin
   ```

2. **Create Database**:
   - Click **New** (left sidebar)
   - Database name: `student_portal`
   - Collation: `utf8mb4_general_ci`
   - Click **Create**

3. **Import SQL File**:
   - Select `student_portal` database (left sidebar)
   - Click **Import** tab (top menu)
   - Click **Choose File**
   - Navigate to: `C:\xampp\htdocs\vallarasu\database.sql`
   - Click **Go** (bottom)
   - Wait for success message: ✅ **Import has been successfully finished**

#### **Option B: Using Command Line**

1. **Open Command Prompt** (as Administrator)
2. **Navigate to MySQL bin**:
   ```cmd
   cd C:\xampp\mysql\bin
   ```

3. **Run MySQL**:
   ```cmd
   mysql -u root -p
   ```
   (Press Enter when asked for password - default is empty)

4. **Create database and import**:
   ```sql
   CREATE DATABASE student_portal;
   USE student_portal;
   source C:/xampp/htdocs/vallarasu/database.sql;
   exit;
   ```

---

### **Step 7: Verify Database Connection**

1. **Open** `C:\xampp\htdocs\vallarasu\includes\db.php`
2. **Check database credentials**:
   ```php
   <?php
   $DB_HOST = 'localhost';
   $DB_NAME = 'student_portal';
   $DB_USER = 'root';
   $DB_PASS = '';  // Empty by default on XAMPP
   ```
3. These should work by default - **no changes needed**

---

### **Step 8: Access the Application**

1. **Open your browser**
2. **Go to**:
   ```
   http://localhost/vallarasu/
   ```

3. **You should see**:
   - ✅ Student Portal homepage
   - ✅ "Get Started" and "Member Login" buttons
   - ✅ Statistics cards (Total Cases, Resolved, In Progress)

---

## 🔐 Default Admin Login

Use these credentials to login as administrator:

**URL**: `http://localhost/vallarasu/login.php`

**Credentials**:
- **Email**: `admin@portal.com`
- **Password**: `Admin@123`

After login, you'll be redirected to the admin dashboard.

---

## 👤 Creating a Student Account

1. Go to: `http://localhost/vallarasu/register.php`
2. Fill in the registration form:
   - Full Name: (your name)
   - Phone: (10-digit number)
   - Email: (use a valid email format)
   - Password: (minimum 8 characters)
   - Confirm Password: (same as password)
   - Profile Picture: (optional)
3. Click **Create Account**
4. Login with your new credentials

---

## ✅ Testing the Application

### **Test 1: Homepage**
- URL: `http://localhost/vallarasu/`
- Should show: Landing page with statistics

### **Test 2: Login**
- URL: `http://localhost/vallarasu/login.php`
- Login as admin: `admin@portal.com` / `Admin@123`
- Should redirect to: Admin Dashboard

### **Test 3: Register**
- URL: `http://localhost/vallarasu/register.php`
- Create a student account
- Should show: "Registration successful" message

### **Test 4: Student Dashboard**
- Login with student account
- Should redirect to: Complaints list page
- Options: Create complaint, view complaints

### **Test 5: File Upload**
- Login as student
- Go to Profile
- Upload a profile picture
- Should save to: `C:\xampp\htdocs\vallarasu\uploads\avatars\`

---

## 🔧 Common Issues & Solutions

### **Issue 1: Apache won't start - Port 80 in use**

**Error**: Port 80 is already in use (Skype, IIS, etc.)

**Solution**:
1. Open XAMPP Control Panel
2. Click **Config** → **Apache (httpd.conf)**
3. Find line: `Listen 80`
4. Change to: `Listen 8080`
5. Save and restart Apache
6. Access via: `http://localhost:8080/vallarasu/`

---

### **Issue 2: MySQL won't start - Port 3306 in use**

**Error**: Port 3306 is already in use (by another MySQL service)

**Solution 1 - Stop other MySQL**:
1. Press `Win + R`
2. Type: `services.msc`
3. Find "MySQL" service
4. Right-click → **Stop**
5. Restart MySQL in XAMPP

**Solution 2 - Change Port**:
1. XAMPP Control → **Config** → **my.ini**
2. Find: `port=3306`
3. Change to: `port=3307`
4. Update `includes/db.php`: `$DB_HOST = 'localhost:3307';`

---

### **Issue 3: Page shows "404 Not Found"**

**Causes**:
- Wrong BASE_URL configuration
- Files not in correct directory

**Solutions**:
1. **Verify file location**:
   - Files should be in: `C:\xampp\htdocs\vallarasu\`
2. **Check config.php**:
   ```php
   $BASE_URL = '/vallarasu';  // If files in vallarasu folder
   $BASE_URL = '';            // If files directly in htdocs
   ```
3. **Check .htaccess** (if any) - not needed for this project

---

### **Issue 4: "HTTP ERROR 500" - Internal Server Error**

**Causes**:
- PHP syntax errors
- Missing functions
- Headers already sent

**Solutions**:
1. **Enable error display**:
   - Open `C:\xampp\htdocs\vallarasu\includes\db.php`
   - Add at top:
     ```php
     <?php
     ini_set('display_errors', 1);
     error_reporting(E_ALL);
     ```
2. **Check PHP error log**:
   - Location: `C:\xampp\php\logs\php_error_log`
   - View last errors with Notepad

---

### **Issue 5: Upload fails - "Permission denied"**

**Cause**: Upload directories need write permissions

**Solution** (Windows 11):
1. Right-click `uploads` folder
2. Properties → **Security** tab
3. Click **Edit**
4. Select **Users**
5. Check **Full Control**
6. Click **Apply** → **OK**

**Or recreate folders**:
1. Delete `uploads\avatars` and `uploads\complaints`
2. Create them again (they'll have proper permissions)

---

### **Issue 6: "Cannot modify header information - headers already sent"**

**Cause**: PHP trying to redirect after HTML output

**Already Fixed** in current code, but if it occurs:
1. Check that `header.php` is included AFTER login processing
2. Ensure no spaces or blank lines before `<?php` tags
3. Save files with UTF-8 encoding (no BOM)

---

### **Issue 7: Database import fails**

**Error**: "Table already exists" or import errors

**Solution**:
1. **Drop existing database**:
   - phpMyAdmin → Select `student_portal`
   - Click **Drop** → Confirm
2. **Create fresh database**:
   - Click **New** → Name: `student_portal`
   - Import `database.sql` again

---

### **Issue 8: Blank page after login**

**Causes**:
- Session issues
- Redirect problems

**Solutions**:
1. **Clear browser cache**: `Ctrl + Shift + Delete`
2. **Try different browser**: Chrome, Firefox, Edge
3. **Check session path**:
   - Open `C:\xampp\php\php.ini`
   - Find: `session.save_path`
   - Ensure folder exists: `C:\xampp\tmp`

---

## 🛠️ Optional Configurations

### **Change Database Password**

1. **Open phpMyAdmin**: `http://localhost/phpmyadmin`
2. Click **User accounts** tab
3. Find user **root** → Click **Edit privileges**
4. Click **Change password**
5. Enter new password → **Go**
6. **Update** `includes/db.php`:
   ```php
   $DB_PASS = 'your_new_password';
   ```

---

### **Enable Pretty URLs** (Optional)

1. **Enable mod_rewrite** in Apache:
   - XAMPP Control → **Config** → **httpd.conf**
   - Find: `#LoadModule rewrite_module modules/mod_rewrite.so`
   - Remove `#` to uncomment
   - Save and restart Apache

2. **Create** `.htaccess` in project root:
   ```apache
   RewriteEngine On
   RewriteBase /vallarasu/
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
   ```

---

## 📱 Access from Mobile/Other Devices

**On same WiFi network**:

1. **Find your PC's IP address**:
   - Press `Win + R`
   - Type: `cmd` → Enter
   - Type: `ipconfig`
   - Look for: **IPv4 Address** (e.g., 192.168.1.100)

2. **Allow through firewall**:
   - Windows Security → **Firewall & network protection**
   - **Allow an app** → Find **Apache HTTP Server**
   - Check both **Private** and **Public** networks

3. **Access from mobile**:
   ```
   http://192.168.1.100/vallarasu/
   ```
   (Replace with your actual IP)

---

## 🔒 Security Recommendations

### **For Production Deployment**:

1. **Change default passwords**:
   - Database root password
   - Admin portal password

2. **Update** `includes/db.php`:
   ```php
   $DB_PASS = 'strong_password_here';
   ```

3. **Hide phpMyAdmin**:
   - Rename: `C:\xampp\phpMyAdmin` to `C:\xampp\myadmin`
   - Access via: `http://localhost/myadmin`

4. **Disable directory listing**:
   - Add to `.htaccess`:
     ```apache
     Options -Indexes
     ```

5. **Use HTTPS** (advanced):
   - Enable SSL in XAMPP
   - Configure SSL certificate
   - Force HTTPS redirects

---

## 📊 Project Structure

```
C:\xampp\htdocs\vallarasu\
│
├── assets/
│   ├── css/
│   │   └── style.css          # All styling
│   └── js/
│       ├── main.js            # Theme toggle
│       ├── student.js         # AJAX complaints
│       └── admin.js           # Chart animations
│
├── includes/
│   ├── config.php             # BASE_URL configuration
│   ├── db.php                 # Database connection
│   ├── auth.php               # Session & auth functions
│   ├── csrf.php               # CSRF protection
│   ├── functions.php          # Helper functions
│   ├── header.php             # Header & navigation
│   ├── footer.php             # Footer & scripts
│   └── forbidden.php          # 403 error page
│
├── modules/
│   ├── auth/
│   │   ├── profile.php        # User profile
│   │   ├── change_password.php
│   │   ├── notifications.php
│   │   └── logout.php
│   │
│   ├── complaints/
│   │   ├── create.php         # Submit complaint
│   │   ├── list.php           # Student complaints list
│   │   ├── view.php           # Single complaint view
│   │   └── ajax_list.php      # AJAX API endpoint
│   │
│   └── admin/
│       ├── dashboard.php      # Admin analytics
│       ├── complaints.php     # Manage complaints
│       └── report.php         # PDF report generation
│
├── uploads/
│   ├── avatars/               # Profile pictures
│   └── complaints/            # Complaint attachments
│
├── database.sql               # Database schema
├── index.php                  # Landing page
├── login.php                  # Login page
├── register.php               # Registration page
├── dashboard.php              # Dashboard router
└── track.php                  # Track complaint by ID
```

---

## 🎯 Features Overview

### **For Students**:
✅ Register account with profile picture  
✅ Submit complaints with categories and priorities  
✅ Attach files (images, PDFs, etc.)  
✅ Track complaint status in real-time  
✅ View complaint timeline/logs  
✅ Receive notifications on status changes  
✅ Provide feedback when resolved  

### **For Admins/Staff**:
✅ View all complaints dashboard  
✅ Assign complaints to staff  
✅ Update complaint status  
✅ Add remarks/comments  
✅ Filter and search complaints  
✅ Generate PDF reports  
✅ View analytics and statistics  
✅ Audit log for all actions  

### **Security Features**:
🔒 Password hashing (bcrypt)  
🔒 CSRF protection on all forms  
🔒 SQL injection prevention (PDO prepared statements)  
🔒 XSS protection (output escaping)  
🔒 Role-based access control  
🔒 Session timeout (30 minutes)  
🔒 File upload validation  

---

## 📞 Support & Troubleshooting

### **Still having issues?**

1. **Check XAMPP logs**:
   - Apache: `C:\xampp\apache\logs\error.log`
   - PHP: `C:\xampp\php\logs\php_error_log`
   - MySQL: `C:\xampp\mysql\data\mysql_error.log`

2. **Test PHP installation**:
   - Create file: `C:\xampp\htdocs\test.php`
   - Content: `<?php phpinfo(); ?>`
   - Visit: `http://localhost/test.php`
   - Should show PHP configuration page

3. **Restart XAMPP**:
   - Stop all services
   - Close XAMPP Control Panel
   - Reopen as Administrator
   - Start Apache and MySQL

4. **Browser Console**:
   - Press `F12` in browser
   - Check **Console** tab for JavaScript errors
   - Check **Network** tab for failed requests

---

## ✅ Installation Complete!

Your Student Complaint Portal is now ready to use on Windows!

**Default Access URLs**:
- **Homepage**: http://localhost/vallarasu/
- **Login**: http://localhost/vallarasu/login.php
- **Register**: http://localhost/vallarasu/register.php
- **Admin Login**: admin@portal.com / Admin@123

**Next Steps**:
1. Change admin password after first login
2. Create a test student account
3. Submit a test complaint
4. Test the admin dashboard features
5. Customize as needed for your institution

---

## 🎓 Made for Educational Purposes

This Student Complaint Portal is designed to help educational institutions manage and resolve student concerns efficiently.

**Happy Deployment! 🚀**
