<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Models\Order;
use App\Models\User;

class CourierController extends Controller {
    private $orderModel;
    private $courierModel;
    
    public function __construct() {
        parent::__construct();
        
        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // Check if user is courier
        if ($_SESSION['user_role'] !== 'courier') {
            header('Location: /dashboard');
            exit;
        }
        
        $this->orderModel = new Order();
        $this->courierModel = new User(); // Gunakan User model
    }
    
    public function dashboard() {
        $courierId = $_SESSION['user_id'];
        
        // Get courier data - langsung dari database
        $courier = $this->getCourierData($courierId);
        
        $assignedOrders = $this->orderModel->getOrdersForCourier($courierId, 'assigned');
        $pickedUpOrders = $this->orderModel->getOrdersForCourier($courierId, 'picked_up');
        $availablePickups = $this->orderModel->getAvailablePickups(5);
        
        $data = [
            'title' => 'Courier Dashboard',
            'courier' => $courier,
            'assignedOrders' => $assignedOrders,
            'pickedUpOrders' => $pickedUpOrders,
            'availablePickups' => $availablePickups,
            'activeTab' => 'dashboard',
            'csrfToken' => $_SESSION['csrf_token'] ?? ''
        ];
        
        $this->view('courier/dashboard', $data);
    }
    
    private function getCourierData($userId) {
        // Get courier data from database
        $sql = "SELECT 
                    u.id as user_id,
                    u.username,
                    u.email,
                    u.first_name,
                    u.last_name,
                    u.phone,
                    c.status,
                    c.vehicle_type,
                    c.license_plate,
                    c.current_location,
                    c.current_lat,
                    c.current_lng,
                    c.total_deliveries,
                    c.rating,
                    c.total_earnings
                FROM users u
                LEFT JOIN couriers c ON u.id = c.user_id
                WHERE u.id = :user_id";
        
        $stmt = $this->db->executeQuery($sql, [':user_id' => $userId]);
        
        if ($stmt && $courier = $stmt->fetch()) {
            return $courier;
        }
        
        // If no courier record exists, return default data
        $userSql = "SELECT id as user_id, username, email, first_name, last_name, phone FROM users WHERE id = :user_id";
        $userStmt = $this->db->executeQuery($userSql, [':user_id' => $userId]);
        $userData = $userStmt ? $userStmt->fetch() : [];
        
        return array_merge($userData, [
            'status' => 'offline',
            'vehicle_type' => 'Not Set',
            'license_plate' => '',
            'current_location' => '',
            'current_lat' => 0,
            'current_lng' => 0,
            'total_deliveries' => 0,
            'rating' => 0,
            'total_earnings' => 0
        ]);
    }
    
    public function toggleStatus() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $courierId = $_SESSION['user_id'];
        
        // Get current status
        $currentStatus = $this->getCourierStatus($courierId);
        $newStatus = ($currentStatus === 'available') ? 'offline' : 'available';
        
        // Update status in database
        $updated = $this->updateCourierStatus($courierId, $newStatus);
        
        if ($updated) {
            echo json_encode(['success' => true, 'status' => $newStatus]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
        exit;
    }
    
    private function getCourierStatus($userId) {
        $sql = "SELECT status FROM couriers WHERE user_id = :user_id";
        $stmt = $this->db->executeQuery($sql, [':user_id' => $userId]);
        
        if ($stmt && $result = $stmt->fetch()) {
            return $result['status'] ?? 'offline';
        }
        
        return 'offline';
    }
    
    private function updateCourierStatus($userId, $status) {
        // Check if courier record exists
        $checkSql = "SELECT COUNT(*) as count FROM couriers WHERE user_id = :user_id";
        $checkStmt = $this->db->executeQuery($checkSql, [':user_id' => $userId]);
        $checkResult = $checkStmt ? $checkStmt->fetch() : ['count' => 0];
        
        if ($checkResult['count'] > 0) {
            // Update existing record
            $sql = "UPDATE couriers SET 
                    status = :status,
                    last_active = NOW(),
                    updated_at = NOW()
                    WHERE user_id = :user_id";
        } else {
            // Insert new record
            $sql = "INSERT INTO couriers (user_id, status, is_active, created_at, updated_at) 
                    VALUES (:user_id, :status, 1, NOW(), NOW())";
        }
        
        return $this->db->executeQuery($sql, [
            ':status' => $status,
            ':user_id' => $userId
        ]);
    }
    
    public function updateLocation() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        // Get POST data
        $postData = json_decode(file_get_contents('php://input'), true);
        
        if (empty($postData)) {
            // Try form data
            $postData = $_POST;
        }
        
        $courierId = $_SESSION['user_id'];
        
        if (isset($postData['latitude']) && isset($postData['longitude'])) {
            $location = $postData['location'] ?? '';
            
            // Update location in database
            $updated = $this->updateCourierLocation(
                $courierId,
                $postData['latitude'],
                $postData['longitude'],
                $location
            );
            
            if ($updated) {
                echo json_encode([
                    'success' => true,
                    'latitude' => $postData['latitude'],
                    'longitude' => $postData['longitude'],
                    'location' => $location
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update location']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid coordinates']);
        }
        exit;
    }
    
    private function updateCourierLocation($userId, $lat, $lng, $location = null) {
        // Check if courier record exists
        $checkSql = "SELECT COUNT(*) as count FROM couriers WHERE user_id = :user_id";
        $checkStmt = $this->db->executeQuery($checkSql, [':user_id' => $userId]);
        $checkResult = $checkStmt ? $checkStmt->fetch() : ['count' => 0];
        
        if ($checkResult['count'] > 0) {
            // Update existing record
            $sql = "UPDATE couriers SET 
                    current_lat = :lat,
                    current_lng = :lng,
                    current_location = :location,
                    last_location_update = NOW(),
                    updated_at = NOW()
                    WHERE user_id = :user_id";
        } else {
            // Insert new record
            $sql = "INSERT INTO couriers (user_id, current_lat, current_lng, current_location, status, is_active, created_at, updated_at) 
                    VALUES (:user_id, :lat, :lng, :location, 'offline', 1, NOW(), NOW())";
        }
        
        return $this->db->executeQuery($sql, [
            ':lat' => $lat,
            ':lng' => $lng,
            ':location' => $location,
            ':user_id' => $userId
        ]);
    }
    
    public function acceptPickup($orderId) {
        // Check if POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Invalid security token';
                header('Location: /courier');
                exit;
            }
            
            $courierId = $_SESSION['user_id'];
            
            // Check if order is available
            $order = $this->orderModel->getById($orderId);
            
            if ($order && ($order['pickup_status'] === 'pending' || $order['pickup_status'] === null)) {
                // Assign order to courier
                $sql = "UPDATE orders SET 
                        courier_id = :courier_id,
                        pickup_status = 'assigned',
                        updated_at = NOW()
                        WHERE id = :order_id";
                
                $this->db->executeQuery($sql, [
                    ':courier_id' => $courierId,
                    ':order_id' => $orderId
                ]);
                
                // Update courier status to busy
                $this->updateCourierStatus($courierId, 'busy');
                
                $_SESSION['success'] = 'Pickup accepted successfully!';
                header('Location: /courier/pickup/' . $orderId);
                exit;
            } else {
                $_SESSION['error'] = 'Order not available for pickup';
            }
        }
        
        header('Location: /courier');
        exit;
    }
    
    // Method lainnya tetap sama seperti sebelumnya
    public function assignments() {
        $courierId = $_SESSION['user_id'];
        $orders = $this->orderModel->getOrdersForCourier($courierId);
        
        $data = [
            'title' => 'My Assignments',
            'orders' => $orders,
            'activeTab' => 'assignments',
            'csrfToken' => $_SESSION['csrf_token'] ?? ''
        ];
        
        $this->view('courier/assignments', $data);
    }
    
    public function pickup($orderId = null) {
        if ($orderId) {
            $order = $this->orderModel->getById($orderId);
            
            if (!$order || $order['courier_id'] != $_SESSION['user_id']) {
                header('Location: /courier');
                exit;
            }
            
            $data = [
                'title' => 'Pickup Package',
                'order' => $order,
                'activeTab' => 'pickup',
                'csrfToken' => $_SESSION['csrf_token'] ?? ''
            ];
            
            $this->view('courier/pickup', $data);
        } else {
            $availablePickups = $this->orderModel->getAvailablePickups();
            
            $data = [
                'title' => 'Available Pickups',
                'pickups' => $availablePickups,
                'activeTab' => 'pickup',
                'csrfToken' => $_SESSION['csrf_token'] ?? ''
            ];
            
            $this->view('courier/pickup_list', $data);
        }
    }
    
    public function completePickup($orderId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courierId = $_SESSION['user_id'];
            
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Invalid security token';
                header('Location: /courier/pickup/' . $orderId);
                exit;
            }
            
            $order = $this->orderModel->getById($orderId);
            
            if (!$order || $order['courier_id'] != $courierId) {
                $_SESSION['error'] = 'Order not assigned to you';
                header('Location: /courier');
                exit;
            }
            
            // Process pickup completion
            $pickupData = [
                'location' => $_POST['pickup_location'] ?? '',
                'latitude' => $_POST['latitude'] ?? 0,
                'longitude' => $_POST['longitude'] ?? 0,
                'photo_filename' => '',
                'notes' => $_POST['pickup_notes'] ?? ''
            ];
            
            // Handle photo upload
            if (isset($_FILES['pickup_photo']) && $_FILES['pickup_photo']['error'] === UPLOAD_ERR_OK) {
                $pickupData['photo_filename'] = $this->uploadPickupPhoto($_FILES['pickup_photo'], $orderId);
            }
            
            // Update order status
            $this->orderModel->markAsPickedUp($orderId, $pickupData);
            
            // Update courier location
            $this->updateCourierLocation(
                $courierId,
                $pickupData['latitude'],
                $pickupData['longitude'],
                $pickupData['location']
            );
            
            $_SESSION['success'] = 'Package picked up successfully!';
            header('Location: /courier');
            exit;
        }
        
        header('Location: /courier');
        exit;
    }
    
    private function uploadPickupPhoto($file, $orderId) {
        $uploadDir = __DIR__ . '/../../public/uploads/pickups/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024;
        
        if (!in_array($file['type'], $allowedTypes) || $file['size'] > $maxSize) {
            return '';
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'pickup_' . $orderId . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }
        
        return '';
    }
}
?>