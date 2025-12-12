<?php
namespace App\Core;

class Security {
    
    // Generate CSRF token
    public static function generateCSRFToken() {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
    
    // Validate CSRF token
    public static function validateCSRFToken($token) {
        if (!isset($_SESSION[CSRF_TOKEN_NAME]) || !hash_equals($_SESSION[CSRF_TOKEN_NAME], $token)) {
            return false;
        }
        return true;
    }
    
    // Sanitize input
    public static function sanitize($input) {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = self::sanitize($value);
            }
            return $input;
        }
        
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return $input;
    }
    
    // Validate email
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    // Validate phone number (Indonesian format)
    public static function validatePhone($phone) {
        return preg_match('/^(\+62|62|0)8[1-9][0-9]{6,9}$/', $phone);
    }
    
    // Hash password
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    // Verify password
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    // Check if user is authenticated
    public static function isAuthenticated() {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
    }
    
    // Require authentication
    public static function requireAuth() {
        if (!self::isAuthenticated()) {
            header('Location: /login');
            exit;
        }
    }
    
    // Check if user has role
    public static function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }

    public static function requireRole($role) {
        self::requireAuth();
        if (!self::hasRole($role)) {
            if ($role === 'courier') {
                header('Location: /dashboard');
            } else {
                header('Location: /courier/dashboard');
            }
            exit;
        }
    }
}
?>