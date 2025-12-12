<?php
// setup_database.php - Run this to setup database

echo "<h1>Database Setup for Logistic System</h1>";

// Database configuration - SESUAIKAN DENGAN cPanel ANDA
$db_config = [
    'host' => 'localhost',
    'name' => 'davf1826_logistic', // Ganti dengan nama database Anda
    'user' => 'davf1826_logistic_user', // Ganti dengan username
    'pass' => 'PasswordAnda123!' // Ganti dengan password
];

try {
    // Connect to MySQL server
    $pdo = new PDO("mysql:host={$db_config['host']};charset=utf8mb4", 
                   $db_config['user'], 
                   $db_config['pass'], 
                   [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "<p style='color:green;'>✅ Connected to MySQL server</p>";
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_config['name']}` 
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    echo "<p style='color:green;'>✅ Database created or already exists</p>";
    
    // Use the database
    $pdo->exec("USE `{$db_config['name']}`");
    
    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'user', 'driver') DEFAULT 'user',
            status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "<p style='color:green;'>✅ Users table created</p>";
    
    // Create orders table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INT PRIMARY KEY AUTO_INCREMENT,
            tracking_number VARCHAR(20) NOT NULL UNIQUE,
            customer_name VARCHAR(100) NOT NULL,
            customer_email VARCHAR(100) NOT NULL,
            customer_phone VARCHAR(20) NOT NULL,
            pickup_address TEXT NOT NULL,
            delivery_address TEXT NOT NULL,
            package_type ENUM('document', 'parcel', 'electronics', 'fragile', 'food', 'other') DEFAULT 'parcel',
            package_weight DECIMAL(10,2) NOT NULL,
            package_dimensions VARCHAR(50),
            delivery_date DATE NOT NULL,
            delivery_time ENUM('morning', 'afternoon', 'evening', 'anytime') DEFAULT 'anytime',
            status ENUM('pending', 'processing', 'in_transit', 'delivered', 'cancelled') DEFAULT 'pending',
            estimated_cost DECIMAL(10,2),
            actual_cost DECIMAL(10,2),
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_tracking (tracking_number),
            INDEX idx_status (status),
            FULLTEXT idx_search (customer_name, customer_email, pickup_address, delivery_address)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "<p style='color:green;'>✅ Orders table created</p>";
    
    // Create admin user (password: admin123)
    $hashed_password = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
    
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO users (username, email, password, role) 
        VALUES (:username, :email, :password, 'admin')
    ");
    
    $stmt->execute([
        ':username' => 'admin',
        ':email' => 'admin@logistic.davken.my.id',
        ':password' => $hashed_password
    ]);
    
    echo "<p style='color:green;'>✅ Admin user created</p>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p style='color:orange;'>⚠️ Please change the password after login!</p>";
    
    // Create sample order
    $tracking = 'TRK' . date('Ymd') . strtoupper(uniqid());
    
    $stmt = $pdo->prepare("
        INSERT INTO orders (
            tracking_number, customer_name, customer_email, customer_phone,
            pickup_address, delivery_address, package_type, package_weight,
            delivery_date, status
        ) VALUES (
            :tracking, :name, :email, :phone,
            :pickup, :delivery, :type, :weight,
            :date, 'pending'
        )
    ");
    
    $stmt->execute([
        ':tracking' => $tracking,
        ':name' => 'John Doe',
        ':email' => 'john@example.com',
        ':phone' => '081234567890',
        ':pickup' => 'Jl. Sudirman No. 123, Jakarta',
        ':delivery' => 'Jl. Asia Afrika No. 456, Bandung',
        ':type' => 'parcel',
        ':weight' => 2.5,
        ':date' => date('Y-m-d', strtotime('+3 days'))
    ]);
    
    echo "<p style='color:green;'>✅ Sample order created</p>";
    echo "<p><strong>Tracking Number:</strong> $tracking</p>";
    
    echo "<h2 style='color:green;'>🎉 Database setup completed successfully!</h2>";
    echo "<p><a href='/' target='_blank'>Go to Application</a></p>";
    echo "<p><a href='/login' target='_blank'>Go to Login Page</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color:red;'>Database Setup Failed</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Configuration used:</strong></p>";
    echo "<pre>" . print_r($db_config, true) . "</pre>";
    echo "<p>Please check:</p>";
    echo "<ol>
        <li>Username and password are correct</li>
        <li>Database user has proper privileges</li>
        <li>MySQL server is running</li>
    </ol>";
}
?>