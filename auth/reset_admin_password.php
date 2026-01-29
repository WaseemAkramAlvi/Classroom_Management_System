<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$page_title = 'Reset Admin Password';

// Reset admin password to admin123
$new_password = 'admin123';
$hashed_password = hashPassword($new_password);

$conn->query("UPDATE admins SET password = '$hashed_password' WHERE username = 'admin'");

include '../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <h1>Admin Password Reset</h1>
            <p>The admin password has been reset successfully.</p>
        </div>
        
        <div class="alert alert-success">
            <p><strong>New Password:</strong> admin123</p>
        </div>
        
        <div class="auth-footer">
            <p><a href="login.php">Go to Login</a></p>
            <p><small>Delete this file after use for security.</small></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
