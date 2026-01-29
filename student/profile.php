<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin('student');

$page_title = 'My Profile';

$student_id = $_SESSION['user_id'];
$student = getUserById($conn, $student_id, 'student');

$success = '';
$errors = [];

// Handle profile update
if (isset($_POST['update_profile'])) {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    
    // Validation
    if (empty($full_name)) {
        $errors[] = 'Full name is required';
    }
    
    if (empty($email) || !validateEmail($email)) {
        $errors[] = 'Valid email is required';
    }
    
    // Check if email exists for another user
    $email = $conn->real_escape_string($email);
    $check_query = "SELECT * FROM students WHERE email = '$email' AND student_id != $student_id";
    if ($conn->query($check_query)->num_rows > 0) {
        $errors[] = 'Email already exists';
    }
    
    if (empty($errors)) {
        $full_name = $conn->real_escape_string($full_name);
        $phone = $conn->real_escape_string($phone);
        $address = $conn->real_escape_string($address);
        
        $query = "UPDATE students SET full_name = '$full_name', email = '$email', 
                  phone = '$phone', address = '$address' WHERE student_id = $student_id";
        
        if ($conn->query($query)) {
            $_SESSION['full_name'] = $full_name;
            setFlashMessage('success', 'Profile updated successfully');
            redirect('profile.php');
        } else {
            $errors[] = 'Failed to update profile';
        }
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (!verifyPassword($current_password, $student['password'])) {
        $errors[] = 'Current password is incorrect';
    }
    
    if (strlen($new_password) < 6) {
        $errors[] = 'New password must be at least 6 characters';
    }
    
    if ($new_password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($errors)) {
        $hashed_password = hashPassword($new_password);
        $conn->query("UPDATE students SET password = '$hashed_password' WHERE student_id = $student_id");
        setFlashMessage('success', 'Password changed successfully');
        redirect('profile.php');
    }
}

include '../includes/header.php';
?>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Student Panel</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item">
                <span class="icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="take_test.php" class="nav-item">
                <span class="icon">📝</span>
                <span>Take Test</span>
            </a>
            <a href="results.php" class="nav-item">
                <span class="icon">📈</span>
                <span>My Results</span>
            </a>
            <a href="profile.php" class="nav-item active">
                <span class="icon">👤</span>
                <span>Profile</span>
            </a>
            <a href="../auth/logout.php" class="nav-item">
                <span class="icon">🚪</span>
                <span>Logout</span>
            </a>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="content-header">
            <h1>My Profile</h1>
        </div>
        
        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach($errors as $error): ?>
                <li><?php echo escape($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <!-- Profile Information -->
        <div class="section">
            <h2>Profile Information</h2>
            
            <form method="POST" action="" class="profile-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" value="<?php echo escape($student['username']); ?>" disabled>
                        <small>Username cannot be changed</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <input type="text" name="full_name" id="full_name" required 
                               value="<?php echo escape($student['full_name']); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" name="email" id="email" required 
                               value="<?php echo escape($student['email']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" name="phone" id="phone" 
                               value="<?php echo escape($student['phone']); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" id="date_of_birth" 
                               value="<?php echo $student['date_of_birth']; ?>" disabled>
                        <small>Date of birth cannot be changed</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Member Since</label>
                        <input type="text" value="<?php echo formatDate($student['created_at']); ?>" disabled>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" rows="3"><?php echo escape($student['address']); ?></textarea>
                </div>
                
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
        
        <!-- Change Password -->
        <div class="section">
            <h2>Change Password</h2>
            
            <form method="POST" action="" class="profile-form">
                <div class="form-group">
                    <label for="current_password">Current Password *</label>
                    <input type="password" name="current_password" id="current_password" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="new_password">New Password *</label>
                        <input type="password" name="new_password" id="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" name="confirm_password" id="confirm_password" required>
                    </div>
                </div>
                
                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
