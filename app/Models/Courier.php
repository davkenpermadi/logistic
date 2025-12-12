<?php
namespace App\Models;

use App\Core\Database;

class Courier {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($userId, $data = []) {
        $sql = "INSERT INTO couriers (
                user_id, vehicle_type, license_plate, phone,
                status, created_at
                ) VALUES (
                :user_id, :vehicle_type, :license_plate, :phone,
                'available', NOW()
                )";
        
        $params = [
            ':user_id' => $userId,
            ':vehicle_type' => $data['vehicle_type'] ?? null,
            ':license_plate' => $data['license_plate'] ?? null,
            ':phone' => $data['phone'] ?? null
        ];
        
        return $this->db->executeQuery($sql, $params);
    }
    
    public function getByUserId($userId) {
        $sql = "SELECT c.*, u.username, u.email 
                FROM couriers c
                JOIN users u ON c.user_id = u.id
                WHERE c.user_id = :user_id";
        
        $stmt = $this->db->executeQuery($sql, [':user_id' => $userId]);
        return $stmt ? $stmt->fetch() : false;
    }
    
    public function getAll($status = null) {
        $sql = "SELECT c.*, u.username, u.email, 
                COUNT(o.id) as active_deliveries
                FROM couriers c
                JOIN users u ON c.user_id = u.id
                LEFT JOIN orders o ON c.user_id = o.courier_id 
                    AND o.pickup_status IN ('assigned', 'picked_up')
                WHERE 1=1";
        
        $params = [];
        
        if ($status) {
            $sql .= " AND c.status = :status";
            $params[':status'] = $status;
        }
        
        $sql .= " GROUP BY c.id ORDER BY c.status, c.last_active DESC";
        
        $stmt = $this->db->executeQuery($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    public function updateStatus($userId, $status) {
        $sql = "UPDATE couriers SET 
                status = :status,
                last_active = NOW()
                WHERE user_id = :user_id";
        
        return $this->db->executeQuery($sql, [
            ':status' => $status,
            ':user_id' => $userId
        ]);
    }
    
    public function getAvailableCouriers($limit = 5) {
        $sql = "SELECT c.*, u.username,
                (6371 * acos(cos(radians(:lat)) * cos(radians(c.current_lat)) 
                * cos(radians(c.current_lng) - radians(:lng)) 
                + sin(radians(:lat)) * sin(radians(c.current_lat)))) as distance
                FROM couriers c
                JOIN users u ON c.user_id = u.id
                WHERE c.status = 'available'
                AND c.current_lat IS NOT NULL
                AND c.current_lng IS NOT NULL
                ORDER BY distance ASC
                LIMIT :limit";
        
        // This would be called with latitude/longitude parameters
        return $sql;
    }
}
?>