<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Models\Order;

class ApiController extends Controller {
    private $orderModel;
    
    public function __construct() {
        parent::__construct();
        $this->orderModel = new Order();
    }
    
    public function orders() {
        header('Content-Type: application/json');
        
        // API Key authentication (simple version)
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        if ($apiKey !== 'YOUR_API_KEY_HERE') {
            $this->json(['error' => 'Unauthorized'], 401);
        }
        
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch ($method) {
            case 'GET':
                $this->getOrders();
                break;
            case 'POST':
                $this->createOrder();
                break;
            case 'PUT':
                $this->updateOrder();
                break;
            case 'DELETE':
                $this->deleteOrder();
                break;
            default:
                $this->json(['error' => 'Method not allowed'], 405);
        }
    }
    
    private function getOrders() {
        $id = $_GET['id'] ?? null;
        $trackingNumber = $_GET['tracking'] ?? null;
        
        if ($id) {
            $order = $this->orderModel->getById($id);
            if ($order) {
                $this->json(['success' => true, 'data' => $order]);
            } else {
                $this->json(['error' => 'Order not found'], 404);
            }
        } elseif ($trackingNumber) {
            $order = $this->orderModel->getByTrackingNumber($trackingNumber);
            if ($order) {
                $this->json(['success' => true, 'data' => $order]);
            } else {
                $this->json(['error' => 'Order not found'], 404);
            }
        } else {
            $filters = $_GET;
            $orders = $this->orderModel->getAll($filters);
            $this->json(['success' => true, 'data' => $orders, 'count' => count($orders)]);
        }
    }
    
    private function createOrder() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $this->json(['error' => 'Invalid JSON data'], 400);
        }
        
        $result = $this->orderModel->create($data);
        
        if ($result) {
            $this->json([
                'success' => true, 
                'message' => 'Order created successfully',
                'data' => [
                    'id' => $result['id'],
                    'tracking_number' => $result['tracking_number']
                ]
            ], 201);
        } else {
            $this->json(['error' => 'Failed to create order'], 500);
        }
    }
    
    private function updateOrder() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['id'])) {
            $this->json(['error' => 'Invalid data or missing ID'], 400);
        }
        
        $id = $data['id'];
        unset($data['id']);
        
        $success = $this->orderModel->update($id, $data);
        
        if ($success) {
            $this->json(['success' => true, 'message' => 'Order updated successfully']);
        } else {
            $this->json(['error' => 'Failed to update order'], 500);
        }
    }
    
    private function deleteOrder() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['id'])) {
            $this->json(['error' => 'Missing ID'], 400);
        }
        
        $success = $this->orderModel->delete($data['id']);
        
        if ($success) {
            $this->json(['success' => true, 'message' => 'Order deleted successfully']);
        } else {
            $this->json(['error' => 'Failed to delete order'], 500);
        }
    }
}
?>