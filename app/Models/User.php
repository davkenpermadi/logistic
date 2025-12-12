<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Security;

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function login($email, $password) {
        $sql = "SELECT id, username, email, password, role, status, first_name, last_name FROM users WHERE email = :email";
        $stmt = $this->db->executeQuery($sql, [':email' => $email]);
        
        if ($stmt && $user = $stmt->fetch()) {
            if (Security::verifyPassword($password, $user['password'])) {
                // Update last login
                $this->updateLastLogin($user['id']);
                
                unset($user['password']);
                return $user;
            }
        }
        
        // Fallback for testing: admin123 - HANYA untuk development!
        // PERINGATAN: Hapus bagian ini di production!
        if ($email === 'admin@logistic.davken.my.id' && $password === 'admin123') {
            return [
                'id' => 1,
                'username' => 'admin',
                'email' => 'admin@logistic.davken.my.id',
                'role' => 'admin',
                'status' => 'active',
                'first_name' => 'Admin',
                'last_name' => 'System'
            ];
        }
        
        return false;
    }
    
    // CREATE USER - Method baru
    public function createUser($data) {
        $sql = "INSERT INTO users (
            username, email, password, 
            first_name, last_name, phone, address,
            role, department, status, 
            created_at, updated_at
        ) VALUES (
            :username, :email, :password,
            :first_name, :last_name, :phone, :address,
            :role, :department, 'active',
            NOW(), NOW()
        )";
        
        $params = [
            ':username' => Security::sanitize($data['username']),
            ':email' => Security::sanitize($data['email']),
            ':password' => Security::hashPassword($data['password']),
            ':first_name' => Security::sanitize($data['first_name'] ?? ''),
            ':last_name' => Security::sanitize($data['last_name'] ?? ''),
            ':phone' => Security::sanitize($data['phone'] ?? ''),
            ':address' => Security::sanitize($data['address'] ?? ''),
            ':role' => Security::sanitize($data['role'] ?? 'customer'),
            ':department' => Security::sanitize($data['department'] ?? '')
        ];
        
        if ($this->db->executeQuery($sql, $params)) {
            $userId = $this->db->lastInsertId();
            
            // Jika role adalah courier, buat record di tabel couriers
            if (($data['role'] ?? 'customer') === 'courier') {
                $this->createCourierRecord($userId, $data);
            }
            
            return $userId;
        }
        
        return false;
    }

    private function createCourierRecord($userId, $data) {
        $sql = "INSERT INTO couriers (user_id, username, email, first_name, last_name, phone, status, created_at)
                VALUES (:user_id, :username, :email, :first_name, :last_name, :phone, 'offline', NOW())";
        
        $params = [
            ':user_id' => $userId,
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':first_name' => $data['first_name'] ?? '',
            ':last_name' => $data['last_name'] ?? '',
            ':phone' => $data['phone'] ?? ''
        ];
        
        return $this->db->executeQuery($sql, $params);
    }
    
    // UPDATE USER - Method yang diperbaiki
    public function updateUser($id, $data) {
        // Mulai dengan SQL dasar tanpa password
        $sql = "UPDATE users SET 
            username = :username,
            email = :email,
            first_name = :first_name,
            last_name = :last_name,
            phone = :phone,
            address = :address,
            role = :role,
            department = :department,
            status = :status,
            updated_at = NOW()
            WHERE id = :id";
        
        $params = [
            ':username' => Security::sanitize($data['username']),
            ':email' => Security::sanitize($data['email']),
            ':first_name' => Security::sanitize($data['first_name'] ?? ''),
            ':last_name' => Security::sanitize($data['last_name'] ?? ''),
            ':phone' => Security::sanitize($data['phone'] ?? ''),
            ':address' => Security::sanitize($data['address'] ?? ''),
            ':role' => Security::sanitize($data['role']),
            ':department' => Security::sanitize($data['department'] ?? ''),
            ':status' => Security::sanitize($data['status']),
            ':id' => $id
        ];
        
        // Jika ada password baru
        if (!empty($data['password'])) {
            $sql = "UPDATE users SET 
                username = :username,
                email = :email,
                first_name = :first_name,
                last_name = :last_name,
                phone = :phone,
                address = :address,
                role = :role,
                department = :department,
                status = :status,
                password = :password,
                updated_at = NOW()
                WHERE id = :id";
            
            $params[':password'] = Security::hashPassword($data['password']);
        }
        
        return $this->db->executeQuery($sql, $params);
    }
    
    // DELETE USER (soft delete)
    public function deleteUser($id) {
        $sql = "UPDATE users SET status = 'inactive', updated_at = NOW() WHERE id = :id";
        return $this->db->executeQuery($sql, [':id' => $id]);
    }
    
    // GET USER BY ID - Method yang diperbaiki
    public function getUserById($id) {
        $sql = "SELECT id, username, email, first_name, last_name, 
                       phone, address, role, department, status,
                       profile_picture, created_at, updated_at, last_login
                FROM users WHERE id = :id";
        $stmt = $this->db->executeQuery($sql, [':id' => $id]);
        return $stmt ? $stmt->fetch() : false;
    }
    
    // GET ALL USERS - Method yang diperbaiki
    public function getAllUsers($filters = []) {
        $sql = "SELECT id, username, email, first_name, last_name, 
                       phone, role, department, status,
                       created_at, updated_at
                FROM users WHERE 1=1";
        $params = [];
        
        if (!empty($filters['role'])) {
            $sql .= " AND role = :role";
            $params[':role'] = $filters['role'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['department'])) {
            $sql .= " AND department = :department";
            $params[':department'] = $filters['department'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (username LIKE :search OR email LIKE :search OR 
                     first_name LIKE :search OR last_name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->executeQuery($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    // GET USER STATISTICS
    public function getUserStats() {
        $sql = "SELECT 
            COUNT(*) as total_users,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users,
            SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_users,
            SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_count,
            SUM(CASE WHEN role = 'courier' THEN 1 ELSE 0 END) as courier_count,
            SUM(CASE WHEN role = 'staff' THEN 1 ELSE 0 END) as staff_count,
            SUM(CASE WHEN role = 'customer' THEN 1 ELSE 0 END) as customer_count
        FROM users";
        
        $stmt = $this->db->executeQuery($sql);
        return $stmt ? $stmt->fetch() : [];
    }
    
    // CHECK IF USERNAME EXISTS
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = :username";
        $params = [':username' => $username];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->executeQuery($sql, $params);
        $result = $stmt ? $stmt->fetch() : ['count' => 0];
        
        return $result['count'] > 0;
    }
    
    // CHECK IF EMAIL EXISTS
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
        $params = [':email' => $email];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->executeQuery($sql, $params);
        $result = $stmt ? $stmt->fetch() : ['count' => 0];
        
        return $result['count'] > 0;
    }
    
    // UPDATE LAST LOGIN
    public function updateLastLogin($userId) {
        $sql = "UPDATE users SET last_login = NOW() WHERE id = :id";
        return $this->db->executeQuery($sql, [':id' => $userId]);
    }
    
    // GET DEPARTMENTS
    public function getDepartments() {
        $sql = "SELECT DISTINCT department FROM users WHERE department IS NOT NULL AND department != '' ORDER BY department";
        $stmt = $this->db->executeQuery($sql);
        return $stmt ? $stmt->fetchAll(\PDO::FETCH_COLUMN) : [];
    }
    
    // Method sederhana untuk kompatibilitas
    public function getById($id) {
        return $this->getUserById($id);
    }
    
    public function getAll() {
        return $this->getAllUsers();
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) as count FROM users WHERE status = 'active'";
        $stmt = $this->db->executeQuery($sql);
        return $stmt ? $stmt->fetch()['count'] : 0;
    }
}
?>