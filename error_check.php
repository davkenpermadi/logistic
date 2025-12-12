<?php
// error_check.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Error Test</h1>";

// Test 1: Include config
echo "<h2>Test 1: Config File</h2>";
require_once __DIR__ . '/app/config.php';
echo "✅ Config loaded<br>";

// Test 2: Create database instance
echo "<h2>Test 2: Database</h2>";
require_once __DIR__ . '/app/Core/Database.php';
$db = App\Core\Database::getInstance();
echo "✅ Database connected<br>";

// Test 3: Create controller
echo "<h2>Test 3: Controller</h2>";
require_once __DIR__ . '/app/Core/Controller.php';
require_once __DIR__ . '/app/Controllers/OrderController.php';

$controller = new App\Controllers\OrderController();
echo "✅ Controller created<br>";

// Test 4: Call booking method
echo "<h2>Test 4: Booking Method</h2>";
ob_start();
$controller->booking();
$output = ob_get_clean();

echo "✅ Booking method executed<br>";
echo "<h3>Output:</h3>";
echo "<textarea style='width:100%;height:300px;'>" . htmlspecialchars($output) . "</textarea>";

echo "<h2 style='color:green;'>✅ All tests passed!</h2>";
?>