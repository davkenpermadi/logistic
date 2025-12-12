<?php
namespace App\Core;

class App {
    private $controller = 'BookingController';
    private $method = 'index';
    private $params = [];

    public function __construct() {
        $url = $this->parseUrl();
        
        // Set default route to booking
        if (empty($url[0])) {
            $this->controller = 'OrderController';
            $this->method = 'booking';
        } else {
            // Map URL to controller
            switch ($url[0]) {
                case 'dashboard':
                    $this->controller = 'DashboardController';
                    unset($url[0]);
                    break;
                case 'login':
                case 'logout':
                    $this->controller = 'AuthController';
                    $this->method = $url[0];
                    unset($url[0]);
                    break;
                case 'orders':
                    $this->controller = 'OrderController';
                    unset($url[0]);
                    if (isset($url[1])) {
                        $this->method = $url[1];
                        unset($url[1]);
                    }
                    break;
                case 'api':
                    $this->controller = 'ApiController';
                    unset($url[0]);
                    if (isset($url[1])) {
                        $this->method = $url[1];
                        unset($url[1]);
                    }
                    break;
                default:
                    // Default to booking page
                    $this->controller = 'OrderController';
                    $this->method = 'booking';
                    break;
            }
        }

        // Instantiate controller
        $controllerClass = "App\\Controllers\\{$this->controller}";
        
        if (class_exists($controllerClass)) {
            $this->controller = new $controllerClass();
            
            // Check if method exists
            if (isset($url[1]) && method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
            
            // Get parameters
            $this->params = $url ? array_values($url) : [];
            
            // Call controller method
            call_user_func_array([$this->controller, $this->method], $this->params);
        } else {
            $this->error404();
        }
    }

    private function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }

    private function error404() {
        header("HTTP/1.0 404 Not Found");
        echo "404 - Page not found";
        exit;
    }
}
?>