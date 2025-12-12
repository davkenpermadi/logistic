<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Models\User;

class UserController extends Controller {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        Security::requireRole('admin');
        $this->userModel = new User();
    }
    
    public function index() {
        $filters = [];
        
        if (isset($_GET['role'])) {
            $filters['role'] = Security::sanitize($_GET['role']);
        }
        
        if (isset($_GET['status'])) {
            $filters['status'] = Security::sanitize($_GET['status']);
        }
        
        if (isset($_GET['search'])) {
            $filters['search'] = Security::sanitize($_GET['search']);
        }
        
        $users = $this->userModel->getAllUsers($filters);
        $userStats = $this->userModel->getUserStats();
        $departments = $this->userModel->getDepartments();
        
        $data = [
            'title' => 'Manage Users',
            'users' => $users,
            'userStats' => $userStats,
            'departments' => $departments,
            'filters' => $filters,
            'activeTab' => 'users'
        ];
        
        $this->view('users/index', $data);
    }
    
    public function create() {
        $errors = [];
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $errors[] = "Invalid security token";
            } else {
                // Validate input
                $validationErrors = $this->validateUserData($_POST);
                
                if (empty($validationErrors)) {
                    // Check if username exists
                    if ($this->userModel->usernameExists($_POST['username'])) {
                        $errors[] = "Username already exists";
                    }
                    
                    // Check if email exists
                    if ($this->userModel->emailExists($_POST['email'])) {
                        $errors[] = "Email already exists";
                    }
                    
                    if (empty($errors)) {
                        $userId = $this->userModel->createUser($_POST);
                        
                        if ($userId) {
                            $success = true;
                            $_SESSION['flash_message'] = 'User created successfully!';
                            
                            // Redirect to user list or show success
                            if (isset($_POST['save_and_new'])) {
                                // Stay on create page
                                unset($_POST);
                            } else {
                                $this->redirect('/users');
                            }
                        } else {
                            $errors[] = "Failed to create user. Please try again.";
                        }
                    }
                } else {
                    $errors = array_merge($errors, $validationErrors);
                }
            }
        }
        
        $data = [
            'title' => 'Create New User',
            'errors' => $errors,
            'success' => $success,
            'formData' => $_POST ?? [],
            'activeTab' => 'users'
        ];
        
        $this->view('users/create', $data);
    }
    
    public function edit($id = null) {
        if (!$id) {
            $this->redirect('/users');
        }
        
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            $_SESSION['flash_message'] = 'User not found';
            $this->redirect('/users');
        }
        
        $errors = [];
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $errors[] = "Invalid security token";
            } else {
                // Validate input
                $validationErrors = $this->validateUserData($_POST, $id);
                
                if (empty($validationErrors)) {
                    // Check if username exists (excluding current user)
                    if ($this->userModel->usernameExists($_POST['username'], $id)) {
                        $errors[] = "Username already exists";
                    }
                    
                    // Check if email exists (excluding current user)
                    if ($this->userModel->emailExists($_POST['email'], $id)) {
                        $errors[] = "Email already exists";
                    }
                    
                    if (empty($errors)) {
                        $success = $this->userModel->updateUser($id, $_POST);
                        
                        if ($success) {
                            $_SESSION['flash_message'] = 'User updated successfully!';
                            $this->redirect('/users');
                        } else {
                            $errors[] = "Failed to update user. Please try again.";
                        }
                    }
                } else {
                    $errors = array_merge($errors, $validationErrors);
                }
            }
        }
        
        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'errors' => $errors,
            'success' => $success,
            'activeTab' => 'users'
        ];
        
        $this->view('users/edit', $data);
    }
    
    public function delete($id = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid security token']);
                exit;
            }
            
            // Prevent admin from deleting themselves
            if ($id == $_SESSION['user_id']) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete your own account']);
                exit;
            }
            
            $success = $this->userModel->deleteUser($id);
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
            }
            exit;
        }
        
        $this->redirect('/users');
    }
    
    public function viewProfile($id = null) {
        if (!$id) {
            $this->redirect('/users');
        }
        
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            $_SESSION['flash_message'] = 'User not found';
            $this->redirect('/users');
        }
        
        $data = [
            'title' => 'User Profile - ' . $user['username'],
            'user' => $user,
            'activeTab' => 'users'
        ];
        
        $this->view('view', $data);
    }
    
    public function toggleStatus($id = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid security token']);
                exit;
            }
            
            $user = $this->userModel->getUserById($id);
            
            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'User not found']);
                exit;
            }
            
            // Prevent admin from deactivating themselves
            if ($id == $_SESSION['user_id'] && $user['role'] === 'admin') {
                echo json_encode(['success' => false, 'message' => 'Cannot deactivate your own admin account']);
                exit;
            }
            
            $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
            $data = [
                'username' => $user['username'],
                'email' => $user['email'],
                'first_name' => $user['first_name'] ?? '',
                'last_name' => $user['last_name'] ?? '',
                'phone' => $user['phone'] ?? '',
                'address' => $user['address'] ?? '',
                'role' => $user['role'],
                'department' => $user['department'] ?? '',
                'status' => $newStatus
            ];
            
            $success = $this->userModel->updateUser($id, $data);
            
            if ($success) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'User status updated',
                    'newStatus' => $newStatus,
                    'statusText' => ucfirst($newStatus)
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update status']);
            }
            exit;
        }
        
        $this->redirect('/users');
    }
    
    private function validateUserData($data, $excludeId = null) {
        $errors = [];
        
        $required = ['username', 'email', 'role'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required";
            }
        }
        
        // Validate email format
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address";
        }
        
        // Validate username format (alphanumeric, underscore, hyphen)
        if (!empty($data['username']) && !preg_match('/^[a-zA-Z0-9_-]{3,50}$/', $data['username'])) {
            $errors[] = "Username must be 3-50 characters and can only contain letters, numbers, underscores and hyphens";
        }
        
        // Validate password on create or when changing
        if (empty($excludeId) || !empty($data['password'])) {
            if (empty($data['password'])) {
                $errors[] = "Password is required";
            } elseif (strlen($data['password']) < 6) {
                $errors[] = "Password must be at least 6 characters";
            } elseif ($data['password'] !== ($data['confirm_password'] ?? '')) {
                $errors[] = "Passwords do not match";
            }
        }
        
        // Validate phone if provided
        if (!empty($data['phone']) && !preg_match('/^[0-9+\-\s()]{10,20}$/', $data['phone'])) {
            $errors[] = "Invalid phone number format";
        }
        
        return $errors;
    }
}
?>