<?php
// public/index.php - IMPROVED ROUTING VERSION

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Define paths
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', __DIR__);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load config
require_once APP_PATH . '/config.php';

// Load core files
require_once APP_PATH . '/Core/Database.php';
require_once APP_PATH . '/Core/Controller.php';
require_once APP_PATH . '/Core/Security.php';

// Simple autoloader
spl_autoload_register(function ($className) {
    $className = str_replace('App\\', '', $className);
    $file = APP_PATH . '/' . str_replace('\\', '/', $className) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log("Autoload failed for: $className, File: $file");
    }
    
    return false;
});

// Get URL
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlSegments = explode('/', $url);

// Debug
if (defined('APP_DEBUG') && APP_DEBUG) {
    error_log("URL: $url");
    error_log("Segments: " . print_r($urlSegments, true));
}

// Default route
$controllerName = 'OrderController';
$method = 'booking';
$params = [];

// Determine route based on URL segments
if (!empty($urlSegments[0])) {
    $firstSegment = $urlSegments[0];
    
    // Check if it's a numeric ID (like tracking number or order ID)
    if (is_numeric($firstSegment)) {
        // Handle numeric URLs (tracking numbers, IDs)
        $controllerName = 'OrderController';
        $method = 'track';
        $params = [$firstSegment];
    } 
    // Check for known routes
    elseif ($firstSegment === 'dashboard') {
        $controllerName = 'DashboardController';
        $method = 'index';
    }
    elseif ($firstSegment === 'login' || $firstSegment === 'logout') {
        $controllerName = 'AuthController';
        $method = $firstSegment;
    }
    elseif ($firstSegment === 'users') {
        $controllerName = 'UserController';
        $method = isset($urlSegments[1]) ? $urlSegments[1] : 'index';
        if (isset($urlSegments[2])) $params[] = $urlSegments[2];
    }
    elseif ($firstSegment === 'orders') {
        $controllerName = 'OrderController';
        $method = isset($urlSegments[1]) ? $urlSegments[1] : 'index';
        if (isset($urlSegments[2])) $params[] = $urlSegments[2];
    }
    elseif ($firstSegment === 'courier') {
        $controllerName = 'CourierController';
        $method = isset($urlSegments[1]) ? $urlSegments[1] : 'dashboard';
        if (isset($urlSegments[2])) $params[] = $urlSegments[2];
    }
    elseif ($firstSegment === 'track' || $firstSegment === 'tracking') {
        $controllerName = 'OrderController';
        $method = 'track';
        if (isset($urlSegments[1])) $params[] = $urlSegments[1];
    }
    else {
        // Unknown route - check if it's a valid controller
        $potentialController = ucfirst($firstSegment) . 'Controller';
        $controllerFile = APP_PATH . "/Controllers/{$potentialController}.php";
        
        if (file_exists($controllerFile)) {
            $controllerName = $potentialController;
            $method = isset($urlSegments[1]) ? $urlSegments[1] : 'index';
            if (isset($urlSegments[2])) $params[] = $urlSegments[2];
        } else {
            // Treat as tracking number
            $controllerName = 'OrderController';
            $method = 'track';
            $params = [$firstSegment];
        }
    }
}

// Load controller
$controllerFile = APP_PATH . "/Controllers/{$controllerName}.php";

if (!file_exists($controllerFile)) {
    // Fallback to OrderController
    $controllerName = 'OrderController';
    $controllerFile = APP_PATH . "/Controllers/{$controllerName}.php";
    $method = 'booking';
    $params = [];
}

require_once $controllerFile;

$className = "App\\Controllers\\{$controllerName}";

if (!class_exists($className)) {
    die("Class not found: $className");
}

try {
    $controller = new $className();
    
    // Check if method exists
    if (!method_exists($controller, $method)) {
        // Try default methods
        if (method_exists($controller, 'index')) {
            $method = 'index';
            $params = [];
        } elseif (method_exists($controller, 'booking')) {
            $method = 'booking';
            $params = [];
        } else {
            throw new Exception("Method $method not found in $controllerName");
        }
    }
    
    // Call controller method
    call_user_func_array([$controller, $method], $params);
    
} catch (Exception $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        die("<h1>Application Error</h1>
            <p><strong>Error:</strong> " . $e->getMessage() . "</p>
            <p><strong>File:</strong> " . $e->getFile() . "</p>
            <p><strong>Line:</strong> " . $e->getLine() . "</p>");
    } else {
        die("Application error. Please try again later.");
    }
}
?>