<?php
// test_connection.php - Test database connection

echo "<h1>Test Database Connection</h1>";

// Try different configurations
$configs = [
    // cPanel default pattern
    [
        'host' => 'localhost',
        'user' => 'davf1826', // Username cPanel tanpa prefix
        'pass' => '', // Password cPanel
        'db'   => 'davf1826_logistic'
    ],
    // Database user pattern
    [
        'host' => 'localhost',
        'user' => 'davf1826_logistic_user',
        'pass' => '', // Password database
        'db'   => 'davf1826_logistic'
    ]
];

foreach ($configs as $config) {
    echo "<h3>Testing: {$config['user']}@{$config['host']}</h3>";
    
    try {
        // Try to connect without database first
        $dsn = "mysql:host={$config['host']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        echo "<p style='color:green;'>✅ MySQL connection successful</p>";
        
        // Try to select database
        if (!empty($config['db'])) {
            try {
                $pdo->exec("USE `{$config['db']}`");
                echo "<p style='color:green;'>✅ Database '{$config['db']}' selected</p>";
                
                // Show tables
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                if (count($tables) > 0) {
                    echo "<p>Tables found: " . implode(', ', $tables) . "</p>";
                } else {
                    echo "<p style='color:orange;'>⚠️ No tables found in database</p>";
                }
                
            } catch (PDOException $e) {
                echo "<p style='color:orange;'>⚠️ Cannot use database '{$config['db']}': " . $e->getMessage() . "</p>";
            }
        }
        
    } catch (PDOException $e) {
        echo "<p style='color:red;'>❌ Connection failed: " . $e->getMessage() . "</p>";
    }
}

// Show current PHP info
echo "<h3>PHP Information:</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>PDO Available: " . (extension_loaded('pdo_mysql') ? 'Yes' : 'No') . "</p>";
?>