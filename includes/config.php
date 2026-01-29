<?php
/**
 * Database Configuration File
 * Class Management System
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'class_management_system');

// Site Configuration
define('SITE_URL', 'http://localhost/CMS');
define('SITE_NAME', 'Class Management System');

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Create Database Connection
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
        return $conn;
        
    } catch (Exception $e) {
        die("Database connection error: " . $e->getMessage());
    }
}

// Initialize database connection
$conn = getDBConnection();

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
