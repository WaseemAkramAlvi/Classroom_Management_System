# 🚀 Quick Reference Guide - Class Management System

## 📍 Important URLs

| Page | URL |
|------|-----|
| Homepage | http://localhost/CMS |
| Login | http://localhost/CMS/auth/login.php |
| Register | http://localhost/CMS/auth/register.php |
| Admin Dashboard | http://localhost/CMS/admin/dashboard.php |
| Teacher Dashboard | http://localhost/CMS/teacher/dashboard.php |
| Student Dashboard | http://localhost/CMS/student/dashboard.php |
| phpMyAdmin | http://localhost/phpmyadmin |

---

## 🔑 Default Login Credentials

### Admin
```
Username: admin
Password: admin123
```

### Teacher (Sample)
```
Username: teacher1
Password: teacher123
```

### Student
```
Register a new account from the registration page
```

---

## 📂 Key Files

| File | Purpose |
|------|---------|
| `includes/config.php` | Database configuration |
| `includes/functions.php` | Common PHP functions |
| `assets/css/style.css` | Main stylesheet |
| `assets/js/main.js` | JavaScript functionality |
| `database/cms_database.sql` | Database schema |

---

## 🗄️ Database Information

| Setting | Value |
|---------|-------|
| Database Name | class_management_system |
| Host | localhost |
| Username | root |
| Password | (empty) |
| Tables | 8 tables |

---

## 👥 User Roles & Permissions

### Admin
- ✅ Manage teachers (add, activate, deactivate, delete)
- ✅ View all students
- ✅ Monitor system statistics
- ✅ View dashboard analytics

### Teacher
- ✅ Create and manage tests
- ✅ Add MCQ questions
- ✅ Publish tests
- ✅ View student results
- ✅ Approve/reject student admissions

### Student
- ✅ Register account
- ✅ Take admission test
- ✅ View test results
- ✅ Check admission status
- ✅ Update profile

---

## 🎯 Common Tasks

### Add a New Teacher (Admin)
1. Login as admin
2. Go to "Manage Teachers"
3. Click "Add New Teacher"
4. Fill in the form
5. Submit

### Create a Test (Teacher)
1. Login as teacher
2. Go to "Manage Tests"
3. Click "Create New Test"
4. Set test name, duration, passing score
5. Add questions
6. Publish test

### Take a Test (Student)
1. Login as student
2. View available tests on dashboard
3. Click "Start Test"
4. Read instructions
5. Answer all questions
6. Submit before time expires

### Approve Student (Teacher)
1. Login as teacher
2. Go to "Student Approvals"
3. Review student performance
4. Add review notes
5. Click "Approve" or "Reject"

---

## 🛠️ Configuration Options

### Change Site URL
Edit `includes/config.php`:
```php
define('SITE_URL', 'http://localhost/CMS');
```

### Change Timezone
Edit `includes/config.php`:
```php
date_default_timezone_set('Asia/Kolkata');
```

### Enable/Disable Error Display
Edit `includes/config.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 in production
```

---

## 🔧 Maintenance Tasks

### Backup Database
```sql
1. Open phpMyAdmin
2. Select class_management_system database
3. Click "Export" tab
4. Click "Go" to download
```

### Reset Admin Password
```sql
UPDATE admins SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE username = 'admin';
-- This resets password to: admin123
```

### Clear All Test Data
```sql
TRUNCATE student_answers;
TRUNCATE student_tests;
TRUNCATE admissions;
```

---

## 📊 Database Tables Quick Reference

| Table | Purpose | Key Fields |
|-------|---------|------------|
| admins | Admin accounts | admin_id, username, password |
| teachers | Teacher accounts | teacher_id, username, status |
| students | Student accounts | student_id, username, admission_status |
| tests | Admission tests | test_id, teacher_id, status |
| questions | MCQ questions | question_id, test_id, correct_answer |
| student_tests | Test attempts | student_test_id, score, result |
| student_answers | Individual answers | answer_id, selected_answer, is_correct |
| admissions | Admission records | admission_id, status, reviewed_by |

---

## 🎨 Customization Tips

### Change Primary Color
Edit `assets/css/style.css`:
```css
:root {
    --primary-color: #4CAF50; /* Change this */
}
```

### Change Login Background
Edit `assets/css/style.css`:
```css
.auth-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

### Modify Test Duration
When creating a test, set duration in minutes (default: 30)

### Change Passing Score
When creating a test, set passing percentage (default: 50%)

---

## 🐛 Quick Fixes

### Problem: Blank Page
```
Solution: Enable error display in config.php
Check Apache error logs
```

### Problem: Login Failed
```
Solution: Check database connection
Verify credentials
Clear browser cache
```

### Problem: Test Not Submitting
```
Solution: Check JavaScript console
Enable JavaScript
Clear browser cookies
```

### Problem: Database Error
```
Solution: Restart MySQL service
Check database exists
Import SQL file again
```

---

## 📱 Testing Checklist

- [ ] Admin can login
- [ ] Admin can add teacher
- [ ] Admin can view students
- [ ] Teacher can login
- [ ] Teacher can create test
- [ ] Teacher can add questions
- [ ] Teacher can publish test
- [ ] Student can register
- [ ] Student can login
- [ ] Student can take test
- [ ] Timer works correctly
- [ ] Test submits properly
- [ ] Results display correctly
- [ ] Teacher can approve/reject
- [ ] Profile update works

---

## 🌐 Production Deployment

### Before Going Live
1. ✅ Change all default passwords
2. ✅ Update SITE_URL in config.php
3. ✅ Disable error display
4. ✅ Set proper file permissions
5. ✅ Backup database
6. ✅ Test all functionality
7. ✅ Enable HTTPS if available

### Recommended Hosting Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite
- 100MB disk space (minimum)
- SSL certificate (recommended)

---

## 📞 Support Contacts

| Issue Type | Action |
|------------|--------|
| Installation | Check INSTALLATION.txt |
| Features | Read README.md |
| Database | Check phpMyAdmin |
| Errors | View browser console |
| Configuration | Review config.php |

---

## 💡 Tips & Best Practices

1. **Security**
   - Change default passwords immediately
   - Use strong passwords (8+ characters, mixed case, numbers)
   - Keep PHP and MySQL updated
   - Regular database backups

2. **Performance**
   - Clear old test records periodically
   - Optimize database tables monthly
   - Use browser caching
   - Compress images

3. **Maintenance**
   - Monitor disk space
   - Check error logs regularly
   - Test backups monthly
   - Update documentation

4. **User Management**
   - Deactivate inactive teachers
   - Archive old student records
   - Regular password changes
   - Monitor login attempts

---

## 📈 System Statistics

The dashboard shows:
- Total teachers (with active/inactive breakdown)
- Total students registered
- Pending admission approvals
- Approved/rejected students
- Recent activities
- Test results summary

---

## ✨ Feature Highlights

### Auto-submit Test
Tests automatically submit when time expires

### Real-time Timer
Countdown timer shows remaining time

### Password Security
All passwords hashed with bcrypt

### Responsive Design
Works on all devices and screen sizes

### Flash Messages
User-friendly feedback notifications

### Role-based Access
Strict access control per user role

---

**Quick Reference Guide v1.0**  
*For Class Management System*  
*Updated: January 2026*
