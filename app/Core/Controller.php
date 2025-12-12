<?php
namespace App\Core;

class Controller {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // UBAH DARI protected MENJADI public
    public function view($view, $data = []) {
        // Extract data to variables
        extract($data);
        
        // Get CSRF token safely
        if (class_exists('App\Core\Security')) {
            $csrfToken = Security::generateCSRFToken();
        } else {
            // Fallback
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            $csrfToken = $_SESSION['csrf_token'];
        }
        
        // Check if view files exist
        $headerFile = APP_PATH . "/Views/layout/header.php";
        $viewFile = APP_PATH . "/Views/{$view}.php";
        $footerFile = APP_PATH . "/Views/layout/footer.php";
        
        // Include header
        if (file_exists($headerFile)) {
            require_once $headerFile;
        } else {
            echo "<!-- Header file not found: $headerFile -->";
            echo "<!DOCTYPE html><html><head><title>" . ($title ?? 'Logistic System') . "</title></head><body>";
        }
        
        // Include main view
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "<div style='padding: 20px; border: 2px solid red;'>
                    <h2>View Not Found</h2>
                    <p>View file not found: $viewFile</p>
                    <p>Current data: " . htmlspecialchars(print_r($data, true)) . "</p>
                  </div>";
        }
        
        // Include footer
        if (file_exists($footerFile)) {
            require_once $footerFile;
        } else {
            echo "</body></html>";
        }
    }
    
    // Tetap protected karena hanya untuk internal AJAX
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    // Jadikan public untuk bisa dipanggil dari manapun
    public function redirect($url) {
        header("Location: {$url}");
        exit;
    }
    
    protected function validateRequired($data, $fields) {
        $errors = [];
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = "Field {$field} is required";
            }
        }
        return $errors;
    }
}
?>