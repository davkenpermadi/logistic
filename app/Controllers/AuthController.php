<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Models\User;

class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }
    
    public function login() {
        // If already logged in, redirect based on role
        if (isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole();
            return;
        }
        
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                $error = "Invalid security token. Please try again.";
            } else {
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                
                // Simple validation
                if (empty($email) || empty($password)) {
                    $error = "Please enter both email and password.";
                } else {
                    // Sanitize input
                    $email = htmlspecialchars($email);
                    
                    // Try to login
                    $user = $this->userModel->login($email, $password);
                    
                    if ($user) {
                        // Regenerate session ID
                        session_regenerate_id(true);
                        
                        // Set session variables
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['username'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_role'] = $user['role'];
                        $_SESSION['logged_in'] = true;
                        $_SESSION['last_activity'] = time();
                        
                        // Initialize courier record if user is courier
                        if ($user['role'] === 'courier') {
                            $this->initializeCourierRecord($user['id']);
                        }
                        
                        // Redirect based on role
                        $this->redirectBasedOnRole();
                        return;
                    } else {
                        $error = "Invalid email or password";
                    }
                }
            }
        }
        
        $this->view('auth/login', [
            'title' => 'Login',
            'error' => $error,
            'csrfToken' => $_SESSION['csrf_token'] ?? ''
        ]);
    }
    
    private function redirectBasedOnRole() {
        $role = $_SESSION['user_role'] ?? 'customer';
        
        switch ($role) {
            case 'admin':
            case 'staff':
                header('Location: /dashboard');
                break;
            case 'courier':
                header('Location: /courier/dashboard');
                break;
            default:
                header('Location: /');
        }
        exit;
    }
    
    private function initializeCourierRecord($userId) {
        // Check if courier record exists, if not create one
        $db = \App\Core\Database::getInstance();
        
        $sql = "SELECT COUNT(*) as count FROM couriers WHERE user_id = :user_id";
        $stmt = $db->executeQuery($sql, [':user_id' => $userId]);
        $result = $stmt ? $stmt->fetch() : ['count' => 0];
        
        if ($result['count'] == 0) {
            // Create courier record
            $sql = "INSERT INTO couriers (user_id, status, is_active, created_at) 
                    VALUES (:user_id, 'offline', 1, NOW())";
            $db->executeQuery($sql, [':user_id' => $userId]);
        }
    }
    
    public function logout() {
        // Clear all session variables
        $_SESSION = [];
        
        // Destroy session
        session_destroy();
        
        // Redirect to login
        header('Location: /login');
        exit;
    }
}
?>