<?php
// Script to create admin user

require_once __DIR__ . '/../app/config.php';

use App\Core\Database;
use App\Core\Security;
use App\Models\User;

// Connect to database
$db = Database::getInstance();
$userModel = new User();

echo "Creating admin user...\n";

$adminData = [
    'username' => 'superadmin',
    'email' => 'superadmin@logistic.davken.my.id',
    'password' => 'Admin@123!',
    'role' => 'admin'
];

try {
    $result = $userModel->create($adminData);
    
    if ($result) {
        echo "✅ Admin user created successfully!\n";
        echo "Username: superadmin\n";
        echo "Email: superadmin@logistic.davken.my.id\n";
        echo "Password: Admin@123!\n";
        echo "\n⚠️  Please change the password after first login!\n";
    } else {
        echo "❌ Failed to create admin user\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>