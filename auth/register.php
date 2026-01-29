<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$page_title = 'Student Registration';

// If already logged in, redirect
if (isLoggedIn()) {
    $role = $_SESSION['user_role'];
    redirect(SITE_URL . '/' . $role . '/dashboard.php');
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $full_name = sanitize($_POST['full_name']);
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = sanitize($_POST['phone']);
    $date_of_birth = sanitize($_POST['date_of_birth']);
    $address = sanitize($_POST['address']);
    
    // Validation
    if (empty($full_name)) {
        $errors[] = 'Full name is required';
    }
    
    if (empty($username) || strlen($username) < 4) {
        $errors[] = 'Username must be at least 4 characters';
    }
    
    if (empty($email) || !validateEmail($email)) {
        $errors[] = 'Valid email is required';
    }
    
    if (empty($password) || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($phone)) {
        $errors[] = 'Phone number is required';
    }
    
    if (empty($date_of_birth)) {
        $errors[] = 'Date of birth is required';
    }
    
    // Check if username already exists
    if (usernameExists($conn, $username, 'students')) {
        $errors[] = 'Username already exists';
    }
    
    // Check if email already exists
    if (emailExists($conn, $email, 'students')) {
        $errors[] = 'Email already exists';
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        $hashed_password = hashPassword($password);
        $full_name = $conn->real_escape_string($full_name);
        $username = $conn->real_escape_string($username);
        $email = $conn->real_escape_string($email);
        $phone = $conn->real_escape_string($phone);
        $date_of_birth = $conn->real_escape_string($date_of_birth);
        $address = $conn->real_escape_string($address);
        
        $query = "INSERT INTO students (username, email, password, full_name, phone, date_of_birth, address) 
                  VALUES ('$username', '$email', '$hashed_password', '$full_name', '$phone', '$date_of_birth', '$address')";
        
        if ($conn->query($query)) {
            setFlashMessage('success', 'Registration successful! Please login.');
            redirect(SITE_URL . '/auth/login.php');
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}

include '../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <h1>Student Registration</h1>
            <p>Create your account to apply for admission</p>
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
        
        <form method="POST" action="" class="auth-form" id="registerForm">
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" name="full_name" id="full_name" required 
                       value="<?php echo isset($_POST['full_name']) ? escape($_POST['full_name']) : ''; ?>">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" name="username" id="username" required 
                           value="<?php echo isset($_POST['username']) ? escape($_POST['username']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" required 
                           value="<?php echo isset($_POST['email']) ? escape($_POST['email']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" name="password" id="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone *</label>
                    <input type="tel" name="phone" id="phone" required 
                           value="<?php echo isset($_POST['phone']) ? escape($_POST['phone']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth *</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" required 
                           value="<?php echo isset($_POST['date_of_birth']) ? escape($_POST['date_of_birth']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" rows="3"><?php echo isset($_POST['address']) ? escape($_POST['address']) : ''; ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>
        
        <div class="auth-footer">
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
