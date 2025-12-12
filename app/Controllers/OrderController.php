<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security; // TAMBAHKAN INI
use App\Models\Order;

class OrderController extends Controller {
    private $orderModel;
    
    public function __construct() {
        parent::__construct();
        $this->orderModel = new Order();
    }
    
    public function booking() {
        $success = false;
        $trackingNumber = null;
        $error = null;
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token - GUNAKAN namespace yang benar
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $error = "Invalid security token. Please try again.";
            } else {
                // Validate input
                $errors = $this->validateBookingData($_POST);
                
                if (empty($errors)) {
                    $result = $this->orderModel->create($_POST);
                    
                    if ($result) {
                        $success = true;
                        $trackingNumber = $result['trackingNumber'] ?? $result['tracking_number'] ?? null;
                    } else {
                        $error = "Failed to create booking. Please try again.";
                    }
                }
            }
        }
        
        $data = [
            'title' => 'Book Delivery',
            'success' => $success,
            'trackingNumber' => $trackingNumber,
            'error' => $error,
            'errors' => $errors
        ];
        
        $this->view('orders/booking', $data);
    }
    
    // Order management (admin)
    public function index() {
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $this->redirect('/dashboard');
        }
        
        $filters = [];
        if (isset($_GET['status'])) {
            $filters['status'] = Security::sanitize($_GET['status']);
        }
        
        $orders = $this->orderModel->getAll($filters);
        
        $data = [
            'title' => 'Manage Orders',
            'orders' => $orders,
            'filters' => $filters
        ];
        
        $this->view('orders/index', $data);
    }
    
    public function create() {
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $this->redirect('/dashboard');
        }
        
        $error = null;
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $error = "Invalid security token. Please try again.";
            } else {
                $errors = $this->validateBookingData($_POST);
                
                if (empty($errors)) {
                    $result = $this->orderModel->create($_POST);
                    
                    if ($result) {
                        $this->redirect('/orders');
                    } else {
                        $error = "Failed to create order.";
                    }
                }
            }
        }
        
        $data = [
            'title' => 'Create Order',
            'error' => $error,
            'errors' => $errors
        ];
        
        $this->view('orders/create', $data);
    }
    
    public function edit($id) {
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $this->redirect('/dashboard');
        }
        
        $order = $this->orderModel->getById($id);
        
        if (!$order) {
            $this->redirect('/orders');
        }
        
        $error = null;
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $error = "Invalid security token. Please try again.";
            } else {
                $errors = $this->validateBookingData($_POST);
                
                if (empty($errors)) {
                    $success = $this->orderModel->update($id, $_POST);
                    
                    if ($success) {
                        $this->redirect('/orders');
                    } else {
                        $error = "Failed to update order.";
                    }
                }
            }
        }
        
        $data = [
            'title' => 'Edit Order',
            'order' => $order,
            'error' => $error,
            'errors' => $errors
        ];
        
        $this->view('orders/edit', $data);
    }
    
    public function delete($id) {
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                $this->json(['success' => false, 'message' => 'Invalid token']);
            }
            
            $success = $this->orderModel->delete($id);
            
            if ($success) {
                $this->json(['success' => true]);
            } else {
                $this->json(['success' => false, 'message' => 'Delete failed']);
            }
        }
        
        $this->redirect('/orders');
    }
    
    public function track($trackingNumber = null) {
        $order = null;
        $error = null;
        
        // If tracking number provided in URL
        if ($trackingNumber) {
            $order = $this->orderModel->getByTrackingNumber($trackingNumber);
            if (!$order) {
                $order = $this->orderModel->getById($trackingNumber);
            }
        }
        
        // If form submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tracking_number'])) {
            $trackingNumber = $_POST['tracking_number'];
            $order = $this->orderModel->getByTrackingNumber($trackingNumber);
            
            if (!$order) {
                $error = "No order found with tracking number: " . htmlspecialchars($trackingNumber);
            }
        }
        
        $data = [
            'title' => 'Track Order',
            'order' => $order,
            'trackingNumber' => $trackingNumber,
            'error' => $error
        ];
        
        $this->view('orders/track', $data);
    }
    
    public function updateStatus($id) {
        // Check if user is admin or staff
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'staff'])) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['status'])) {
                $order = $this->orderModel->getById($id);
                if ($order) {
                    $order['status'] = Security::sanitize($data['status']);
                    $success = $this->orderModel->update($id, $order);
                    
                    if ($success) {
                        $this->json(['success' => true]);
                    } else {
                        $this->json(['success' => false, 'message' => 'Update failed']);
                    }
                } else {
                    $this->json(['success' => false, 'message' => 'Order not found']);
                }
            } else {
                $this->json(['success' => false, 'message' => 'Status required']);
            }
        }
    }
    
    private function validateBookingData($data) {
        $errors = [];
        
        $required = [
            'customer_name', 'customer_email', 'customer_phone',
            'pickup_address', 'delivery_address', 'package_type',
            'package_weight', 'delivery_date'
        ];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[$field] = "This field is required";
            }
        }
        
        if (!empty($data['customer_email']) && !Security::validateEmail($data['customer_email'])) {
            $errors['customer_email'] = "Invalid email address";
        }
        
        if (!empty($data['customer_phone']) && !Security::validatePhone($data['customer_phone'])) {
            $errors['customer_phone'] = "Invalid phone number format";
        }
        
        if (!empty($data['delivery_date']) && strtotime($data['delivery_date']) < strtotime('today')) {
            $errors['delivery_date'] = "Delivery date must be in the future";
        }
        
        if (!empty($data['package_weight']) && (!is_numeric($data['package_weight']) || $data['package_weight'] <= 0)) {
            $errors['package_weight'] = "Package weight must be a positive number";
        }
        
        return $errors;
    }
}
?>