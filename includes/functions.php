<?php
/**
 * Common Functions File
 * Class Management System
 */

// Sanitize input data
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

// Check if admin is logged in
function isAdmin() {
    return isLoggedIn() && $_SESSION['user_role'] === 'admin';
}

// Check if teacher is logged in
function isTeacher() {
    return isLoggedIn() && $_SESSION['user_role'] === 'teacher';
}

// Check if student is logged in
function isStudent() {
    return isLoggedIn() && $_SESSION['user_role'] === 'student';
}

// Redirect function
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Set flash message
function setFlashMessage($type, $message) {
    $_SESSION['flash_type'] = $type; // success, error, warning, info
    $_SESSION['flash_message'] = $message;
}

// Get and clear flash message
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'];
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        return ['type' => $type, 'message' => $message];
    }
    return null;
}

// Require login
function requireLogin($role = null) {
    if (!isLoggedIn()) {
        setFlashMessage('error', 'Please login to access this page');
        redirect(SITE_URL . '/auth/login.php');
    }
    
    if ($role && $_SESSION['user_role'] !== $role) {
        setFlashMessage('error', 'Unauthorized access');
        redirect(SITE_URL . '/auth/login.php');
    }
}

// Format date
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Format datetime
function formatDateTime($datetime) {
    return date('M d, Y h:i A', strtotime($datetime));
}

// Generate random string
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

// Escape output
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Get time ago
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;
    
    if ($difference < 60) {
        return $difference . ' seconds ago';
    } elseif ($difference < 3600) {
        return floor($difference / 60) . ' minutes ago';
    } elseif ($difference < 86400) {
        return floor($difference / 3600) . ' hours ago';
    } elseif ($difference < 604800) {
        return floor($difference / 86400) . ' days ago';
    } else {
        return formatDate($datetime);
    }
}

// Calculate percentage
function calculatePercentage($obtained, $total) {
    if ($total == 0) return 0;
    return round(($obtained / $total) * 100, 2);
}

// Check if username exists
function usernameExists($conn, $username, $table) {
    $username = $conn->real_escape_string($username);
    $query = "SELECT * FROM $table WHERE username = '$username'";
    $result = $conn->query($query);
    return $result->num_rows > 0;
}

// Check if email exists
function emailExists($conn, $email, $table) {
    $email = $conn->real_escape_string($email);
    $query = "SELECT * FROM $table WHERE email = '$email'";
    $result = $conn->query($query);
    return $result->num_rows > 0;
}

// Get user by ID
function getUserById($conn, $user_id, $role) {
    $user_id = (int)$user_id;
    
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
            return null;
    }
    
    $query = "SELECT * FROM $table WHERE $id_field = $user_id";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Logout function
function logout() {
    session_unset();
    session_destroy();
    redirect(SITE_URL . '/auth/login.php');
}
?>
