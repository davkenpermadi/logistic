<?php
$files = [
    'app/config.php',
    'app/Core/Controller.php',
    'app/Core/Database.php',
    'app/Core/Security.php',
    'app/Controllers/OrderController.php',
    'public/index.php'
];

echo "<h1>File Check</h1>";
foreach ($files as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    echo $file . ': ' . ($exists ? '✅ EXISTS' : '❌ MISSING') . '<br>';
}
?>