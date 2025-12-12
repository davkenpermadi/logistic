<?php
// Force disable directory listing
header('Content-Type: text/html; charset=utf-8');

// Path to public folder
$publicIndex = __DIR__ . '/public/index.php';

// If public/index.php exists, include it
if (file_exists($publicIndex)) {
    // Set some headers for security
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('X-Content-Type-Options: nosniff');
    
    // Include the actual application
    require_once $publicIndex;
} else {
    // Show error if public folder doesn't exist
    http_response_code(500);
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Error - Application Not Found</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; margin-top: 100px; }
            .error { color: #d63031; }
            .info { color: #636e72; }
        </style>
    </head>
    <body>
        <h1 class="error">Application Error</h1>
        <p class="info">The application files are missing or corrupted.</p>
        <p><small>public/index.php not found</small></p>
    </body>
    </html>';
}
?>