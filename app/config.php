<?php
// app/config.php - UPDATE dengan credentials cPanel Anda

// Database configuration for cPanel
define('DB_HOST', 'localhost');
define('DB_NAME', 'davf1826_logistic_db'); // Ganti dengan nama database Anda
define('DB_USER', 'davf1826_logistic_db'); // Ganti dengan username database
define('DB_PASS', 'Davken@1506'); // Ganti dengan password database

// Application configuration
define('APP_NAME', 'Logistic Management System');
define('APP_URL', 'https://logistic.davken.my.id');
define('TIMEZONE', 'Asia/Jakarta');
define('APP_DEBUG', true); // Set false di production

// Security configuration
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_TIMEOUT', 3600);

// Set timezone
date_default_timezone_set(TIMEZONE);

// Error reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>