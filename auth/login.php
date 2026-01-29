<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$page_title = 'Login';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    $role = $_SESSION['user_role'];
    redirect(SITE_URL . '/' . $role . '/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $role = sanitize($_POST['role']);
    
    // Validate inputs
    if (empty($username) || empty($password) || empty($role)) {
        $error = 'All fields are required';
    } else {
        // Determine table based on role
        $table = '';
        $id_field = '';
        
        switch($role) {
            case 'admin':
                $table = 'admins';
                $id_field = 'admin_id';
                break;
            case 'teacher':
                $table = 'teachers';
                $id_field = 'teacher_id';
                break;
            case 'student':
                $table = 'students';
                $id_field = 'student_id';
                break;
            default:
                $error = 'Invalid role selected';
        }
        
        if (empty($error)) {
            $username = $conn->real_escape_string($username);
            $query = "SELECT * FROM $table WHERE username = '$username'";
            $result = $conn->query($query);
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                // Check if teacher is active
                if ($role == 'teacher' && $user['status'] != 'active') {
                    $error = 'Your account is not active. Please contact admin.';
                } else if (verifyPassword($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user[$id_field];
                    $_SESSION['user_role'] = $role;
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    
                    setFlashMessage('success', 'Welcome back, ' . $user['full_name'] . '!');
                    redirect(SITE_URL . '/' . $role . '/dashboard.php');
                } else {
                    $error = 'Invalid username or password';
                }
            } else {
                $error = 'Invalid username or password';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <h1>Login</h1>
            <p>Welcome back! Please login to your account</p>
        </div>
        
        <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <?php echo escape($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="auth-form" id="loginForm">
            <div class="form-group">
                <label for="role">Login As</label>
                <select name="role" id="role" required>
                    <option value="">Select Role</option>
                    <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="teacher" <?php echo (isset($_POST['role']) && $_POST['role'] == 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                    <option value="student" <?php echo (isset($_POST['role']) && $_POST['role'] == 'student') ? 'selected' : ''; ?>>Student</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required 
                       value="<?php echo isset($_POST['username']) ? escape($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        
        <div class="auth-footer">
            <p>Don't have an account? <a href="register.php">Register as Student</a></p>
            <p><a href="<?php echo SITE_URL; ?>">Back to Home</a></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
