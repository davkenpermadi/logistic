<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Security;

class Order {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // ============ CRUD METHODS ============
    
    public function create($data) {
        $sql = "INSERT INTO orders (
            customer_name, customer_email, customer_phone, 
            pickup_address, delivery_address, package_type, 
            package_weight, package_dimensions, delivery_date,
            status, tracking_number, created_at
        ) VALUES (
            :customer_name, :customer_email, :customer_phone,
            :pickup_address, :delivery_address, :package_type,
            :package_weight, :package_dimensions, :delivery_date,
            'pending', :tracking_number, NOW()
        )";
        
        $trackingNumber = 'TRK' . date('Ymd') . strtoupper(uniqid());
        
        $params = [
            ':customer_name' => Security::sanitize($data['customer_name']),
            ':customer_email' => Security::sanitize($data['customer_email']),
            ':customer_phone' => Security::sanitize($data['customer_phone']),
            ':pickup_address' => Security::sanitize($data['pickup_address']),
            ':delivery_address' => Security::sanitize($data['delivery_address']),
            ':package_type' => Security::sanitize($data['package_type']),
            ':package_weight' => Security::sanitize($data['package_weight']),
            ':package_dimensions' => Security::sanitize($data['package_dimensions']),
            ':delivery_date' => Security::sanitize($data['delivery_date']),
            ':tracking_number' => $trackingNumber
        ];
        
        if ($this->db->executeQuery($sql, $params)) {
            return [
                'id' => $this->db->lastInsertId(),
                'tracking_number' => $trackingNumber
            ];
        }
        
        return false;
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM orders WHERE id = :id";
        $stmt = $this->db->executeQuery($sql, [':id' => $id]);
        return $stmt ? $stmt->fetch() : false;
    }
    
    public function update($id, $data) {
        $sql = "UPDATE orders SET 
            customer_name = :customer_name,
            customer_email = :customer_email,
            customer_phone = :customer_phone,
            pickup_address = :pickup_address,
            delivery_address = :delivery_address,
            package_type = :package_type,
            package_weight = :package_weight,
            package_dimensions = :package_dimensions,
            delivery_date = :delivery_date,
            status = :status,
            updated_at = NOW()
        WHERE id = :id";
        
        $params = [
            ':customer_name' => Security::sanitize($data['customer_name']),
            ':customer_email' => Security::sanitize($data['customer_email']),
            ':customer_phone' => Security::sanitize($data['customer_phone']),
            ':pickup_address' => Security::sanitize($data['pickup_address']),
            ':delivery_address' => Security::sanitize($data['delivery_address']),
            ':package_type' => Security::sanitize($data['package_type']),
            ':package_weight' => Security::sanitize($data['package_weight']),
            ':package_dimensions' => Security::sanitize($data['package_dimensions']),
            ':delivery_date' => Security::sanitize($data['delivery_date']),
            ':status' => Security::sanitize($data['status']),
            ':id' => $id
        ];
        
        return $this->db->executeQuery($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM orders WHERE id = :id";
        return $this->db->executeQuery($sql, [':id' => $id]);
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT * FROM orders WHERE 1=1";
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(created_at) >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(created_at) <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->executeQuery($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    public function getByTrackingNumber($trackingNumber) {
        $sql = "SELECT * FROM orders WHERE tracking_number = :tracking_number";
        $stmt = $this->db->executeQuery($sql, [':tracking_number' => $trackingNumber]);
        return $stmt ? $stmt->fetch() : false;
    }
    
    // ============ STATISTICS METHODS ============
    
    public function countByStatus($status = null) {
        if ($status) {
            $sql = "SELECT COUNT(*) as count FROM orders WHERE status = :status";
            $stmt = $this->db->executeQuery($sql, [':status' => $status]);
        } else {
            $sql = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
            $stmt = $this->db->executeQuery($sql);
        }
        
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    public function getMonthlyStats($year = null) {
        $year = $year ?: date('Y');
        $sql = "SELECT 
            MONTH(created_at) as month,
            COUNT(*) as total_orders,
            SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered
        FROM orders 
        WHERE YEAR(created_at) = :year
        GROUP BY MONTH(created_at)";
        
        $stmt = $this->db->executeQuery($sql, [':year' => $year]);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    // ============ COURIER METHODS ============
    
    public function assignToCourier($orderId, $courierId) {
        $sql = "UPDATE orders SET 
                courier_id = :courier_id,
                pickup_status = 'assigned',
                updated_at = NOW()
                WHERE id = :order_id";
        
        return $this->db->executeQuery($sql, [
            ':courier_id' => $courierId,
            ':order_id' => $orderId
        ]);
    }
    
    public function markAsPickedUp($orderId, $data) {
        $sql = "UPDATE orders SET 
                pickup_status = 'picked_up',
                pickup_time = NOW(),
                pickup_location = :location,
                pickup_lat = :lat,
                pickup_lng = :lng,
                pickup_photo = :photo,
                pickup_notes = :notes,
                status = 'in_transit',
                updated_at = NOW()
                WHERE id = :order_id";
        
        return $this->db->executeQuery($sql, [
            ':location' => Security::sanitize($data['location']),
            ':lat' => $data['latitude'],
            ':lng' => $data['longitude'],
            ':photo' => $data['photo_filename'],
            ':notes' => Security::sanitize($data['notes'] ?? ''),
            ':order_id' => $orderId
        ]);
    }
    
    public function getOrdersForCourier($courierId, $status = null) {
        $sql = "SELECT o.*, 
                CONCAT(u.first_name, ' ', u.last_name) as courier_name
                FROM orders o
                LEFT JOIN users u ON o.courier_id = u.id
                WHERE o.courier_id = :courier_id";
        
        $params = [':courier_id' => $courierId];
        
        if ($status) {
            $sql .= " AND o.pickup_status = :status";
            $params[':status'] = $status;
        }
        
        $sql .= " ORDER BY o.delivery_date ASC, o.created_at DESC";
        
        $stmt = $this->db->executeQuery($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    public function getAvailablePickups($limit = 10) {
        $sql = "SELECT * FROM orders 
                WHERE pickup_status = 'pending'
                AND status = 'processing'
                ORDER BY delivery_date ASC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function updateCourierLocation($courierId, $lat, $lng, $location = null) {
        $sql = "UPDATE couriers SET 
                current_lat = :lat,
                current_lng = :lng,
                current_location = :location,
                last_active = NOW()
                WHERE user_id = :courier_id";
        
        return $this->db->executeQuery($sql, [
            ':lat' => $lat,
            ':lng' => $lng,
            ':location' => $location,
            ':courier_id' => $courierId
        ]);
    }
}
?>