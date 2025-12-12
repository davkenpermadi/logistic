<?php
// create_courier.php
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Models/User.php';
require_once __DIR__ . '/app/Models/Courier.php';

use App\Core\Database;
use App\Models\User;
use App\Models\Courier;

$db = Database::getInstance();
$userModel = new User();
$courierModel = new Courier();

echo "<h1>Create Courier Account</h1>";

// Create courier user
$userData = [
    'username' => 'kurir1',
    'email' => 'kurir1@logistic.davken.my.id',
    'password' => 'kurir123',
    'role' => 'courier'
];

try {
    // Create user
    $stmt = $db->prepare("
        INSERT INTO users (username, email, password, role, status) 
        VALUES (:username, :email, :password, :role, 'active')
    ");
    
    $hashedPassword = password_hash($userData['password'], PASSWORD_BCRYPT, ['cost' => 12]);
    
    $stmt->execute([
        ':username' => $userData['username'],
        ':email' => $userData['email'],
        ':password' => $hashedPassword,
        ':role' => $userData['role']
    ]);
    
    $userId = $db->lastInsertId();
    
    // Create courier profile
    $courierData = [
        'vehicle_type' => 'Motorcycle',
        'license_plate' => 'B 1234 XYZ',
        'phone' => '081234567890'
    ];
    
    $courierModel->create($userId, $courierData);
    
    echo "<p style='color:green;'>✅ Courier account created successfully!</p>";
    echo "<p><strong>Username:</strong> {$userData['username']}</p>";
    echo "<p><strong>Email:</strong> {$userData['email']}</p>";
    echo "<p><strong>Password:</strong> {$userData['password']}</p>";
    echo "<p><strong>Vehicle:</strong> {$courierData['vehicle_type']}</p>";
    echo "<p><strong>License Plate:</strong> {$courierData['license_plate']}</p>";
    echo "<p><a href='/login'>Go to Login</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
?>