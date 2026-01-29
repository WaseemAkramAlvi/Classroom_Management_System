# 🎓 Class Management System (CMS)

A comprehensive web-based Class Management System built with PHP, MySQL, HTML, CSS, and JavaScript. This system provides role-based access control for Admins, Teachers, and Students to manage admissions through online MCQ-based tests.

---

## 🌟 Features

### 🛡️ Admin Panel
- **Secure Admin Login**
- **Dashboard with Statistics**
  - Total teachers
  - Active/Inactive teachers
  - Total students
  - Pending/Approved/Rejected admissions
- **Teacher Management**
  - Register new teachers
  - Activate/Deactivate teacher accounts
  - Delete teachers
- **Student Management**
  - View all registered students
  - Track test status and admission status
  - Monitor student performance

### 👨‍🏫 Teacher Panel
- **Secure Teacher Login**
- **Dashboard with Statistics**
  - Total tests created
  - Published tests
  - Students tested
  - Pending approvals
- **Test Management**
  - Create admission tests
  - Add MCQ questions with 4 options (A, B, C, D)
  - Set correct answers
  - Define test duration and passing score
  - Publish tests
- **Student Evaluation**
  - View test results
  - Review student performance
  - Approve/Reject student admissions
  - Add review notes

### 🎓 Student Panel
- **Student Registration**
- **Secure Student Login**
- **Dashboard with Status Cards**
  - Admission status
  - Test status
  - Score and result
- **Take Admission Test**
  - Online MCQ test with timer
  - Multiple choice questions
  - Auto-submit on time expiry
- **View Results**
  - Test score and percentage
  - Pass/Fail status
  - Admission approval status
- **Profile Management**
  - Update personal information
  - Change password

---

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Server**: Apache (XAMPP)

---

## 📁 Project Structure

```
CMS/
├── admin/
│   ├── dashboard.php
│   ├── teachers.php
│   └── students.php
├── teacher/
│   ├── dashboard.php
│   ├── tests.php
│   ├── add_questions.php
│   ├── results.php
│   └── approvals.php
├── student/
│   ├── dashboard.php
│   ├── take_test.php
│   ├── results.php
│   └── profile.php
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
├── includes/
│   ├── config.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
├── database/
│   └── cms_database.sql
└── index.php
```

---

## 🚀 Installation Guide

### Prerequisites
- XAMPP (or any Apache + MySQL + PHP stack)
- Web browser
- Text editor (optional)

### Step 1: Setup XAMPP
1. Download and install XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)
2. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Setup Project
1. Copy the `CMS` folder to `C:\xampp\htdocs\`
2. Your project path should be: `C:\xampp\htdocs\CMS`

### Step 3: Create Database
1. Open your web browser and go to: `http://localhost/phpmyadmin`
2. Click on "Import" tab
3. Click "Choose File" and select: `C:\xampp\htdocs\CMS\database\cms_database.sql`
4. Click "Go" to import the database

**OR**

1. Go to: `http://localhost/phpmyadmin`
2. Click "New" to create a new database
3. Name it: `class_management_system`
4. Click on SQL tab
5. Copy and paste the contents of `cms_database.sql`
6. Click "Go"

### Step 4: Configure Database Connection
1. Open `C:\xampp\htdocs\CMS\includes\config.php`
2. Verify the database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'class_management_system');
```

### Step 5: Access the System
1. Open your web browser
2. Go to: `http://localhost/CMS`
3. You should see the homepage

---

## 👥 Default Login Credentials

### Admin
- **Username**: `admin`
- **Password**: `admin123`

### Teacher (Sample)
- **Username**: `teacher1`
- **Password**: `teacher123`

### Student
- Register a new account through the registration page

> ⚠️ **Important**: Change the default admin password after first login!

---

## 📊 Database Schema

### Tables
1. **admins** - Admin user accounts
2. **teachers** - Teacher accounts with status
3. **students** - Student accounts with admission status
4. **tests** - Admission tests created by teachers
5. **questions** - MCQ questions for each test
6. **student_tests** - Student test attempts and results
7. **student_answers** - Individual answers submitted by students
8. **admissions** - Admission approval records

### Key Relationships
- Teachers create Tests (1:N)
- Tests contain Questions (1:N)
- Students take Tests creating StudentTests (N:M)
- StudentTests have StudentAnswers (1:N)
- StudentTests lead to Admissions (1:1)

---

## 🎯 How to Use

### For Admin
1. Login with admin credentials
2. View dashboard statistics
3. Add teachers from "Manage Teachers"
4. Activate teacher accounts
5. Monitor students from "Manage Students"

### For Teacher
1. Login with teacher credentials
2. Create a new test from "Manage Tests"
3. Add MCQ questions to the test
4. Publish the test
5. View student results from "View Results"
6. Approve/Reject students from "Student Approvals"

### For Student
1. Register a new account
2. Login with your credentials
3. View available tests from dashboard
4. Click "Start Test" to begin
5. Answer all questions within time limit
6. Submit the test
7. View your result and admission status

---

## 🔒 Security Features

- **Password Hashing**: bcrypt algorithm
- **SQL Injection Prevention**: Prepared statements and input sanitization
- **Session Management**: Secure session handling
- **Role-Based Access Control**: Strict role verification
- **Input Validation**: Client-side and server-side validation
- **XSS Protection**: HTML entity encoding

---

## 🎨 UI/UX Features

- **Responsive Design**: Works on desktop, tablet, and mobile
- **Modern Interface**: Clean and attractive UI
- **Dashboard Cards**: Visual statistics display
- **Flash Messages**: User feedback notifications
- **Modal Dialogs**: Smooth popup forms
- **Timer Display**: Real-time test countdown
- **Form Validation**: Real-time input validation
- **Smooth Animations**: CSS transitions and effects

---

## 📱 Browser Compatibility

- ✅ Chrome (Recommended)
- ✅ Firefox
- ✅ Edge
- ✅ Safari
- ✅ Opera

---

## 🐛 Troubleshooting

### Database Connection Error
- Check if MySQL service is running in XAMPP
- Verify database credentials in `config.php`
- Ensure database `class_management_system` exists

### Page Not Found
- Check if Apache service is running
- Verify the project is in `C:\xampp\htdocs\CMS`
- Clear browser cache

### Cannot Login
- Ensure database is imported correctly
- Check if default admin account exists
- Verify password hashing is working

### Test Timer Not Working
- Enable JavaScript in your browser
- Check browser console for errors
- Refresh the page

---

## 📝 Future Enhancements

- Email notifications
- PDF report generation
- Advanced analytics and charts
- Multiple test attempts
- Question bank management
- Bulk student import
- Mobile app version

---

## 🤝 Contributing

This is a complete project ready for deployment. You can customize it according to your needs:

1. Modify colors in `assets/css/style.css`
2. Add new features in respective role folders
3. Extend database schema in `database/cms_database.sql`
4. Update validation rules in `includes/functions.php`

---

## 📄 License

This project is open-source and available for educational purposes.

---

## 👨‍💻 Developer

Developed by: **Senior Full-Stack Web Developer**  
Technologies: PHP, MySQL, HTML5, CSS3, JavaScript  
Date: January 2026

---

## 📞 Support

For any issues or questions:
1. Check the troubleshooting section
2. Review the code comments
3. Check database connection settings
4. Verify file permissions

---

## ✅ Testing Checklist

- [ ] Database imported successfully
- [ ] Admin login working
- [ ] Teacher can create tests
- [ ] Teacher can add questions
- [ ] Teacher can publish tests
- [ ] Student can register
- [ ] Student can take test
- [ ] Test timer working
- [ ] Test auto-submit working
- [ ] Results calculated correctly
- [ ] Teacher can approve/reject students
- [ ] All pages responsive

---

## 🎉 Deployment Ready

This system is fully functional and ready for deployment on:
- XAMPP (Local)
- WAMP (Local)
- Live hosting with PHP/MySQL support

---

**Thank you for using Class Management System!** 🎓
