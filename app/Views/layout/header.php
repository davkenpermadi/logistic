<?php
// app/Views/layout/header.php

// Use Security class with namespace
use App\Core\Security;

// Get CSRF token safely
$csrfToken = '';
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Logistic Management System'); ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS with absolute path -->
    <link rel="stylesheet" href="/assets/css/style.css?v=<?php echo time(); ?>">
    
    <!-- CSRF Token Meta -->
    <meta name="csrf-token" content="<?php echo $csrfToken; ?>">
    
    <style>
        /* Inline styles as fallback */
        .btn { 
            display: inline-block; 
            font-weight: 500; 
            text-align: center; 
            white-space: nowrap; 
            vertical-align: middle; 
            user-select: none; 
            border: 1px solid transparent; 
            padding: 0.5rem 1rem; 
            font-size: 0.875rem; 
            line-height: 1.5; 
            border-radius: 0.375rem; 
            transition: all 0.15s ease-in-out; 
        }
        .btn-primary { 
            color: #fff; 
            background-color: #3b82f6; 
            border-color: #3b82f6; 
        }
        .btn-primary:hover { 
            background-color: #2563eb; 
            border-color: #2563eb; 
        }
        .form-control { 
            display: block; 
            width: 100%; 
            padding: 0.5rem 0.75rem; 
            font-size: 0.875rem; 
            line-height: 1.5; 
            color: #374151; 
            background-color: #fff; 
            background-clip: padding-box; 
            border: 1px solid #d1d5db; 
            border-radius: 0.375rem; 
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; 
        }
        .form-control:focus { 
            color: #374151; 
            background-color: #fff; 
            border-color: #3b82f6; 
            outline: 0; 
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25); 
        }
        .alert { 
            position: relative; 
            padding: 1rem; 
            margin-bottom: 1rem; 
            border: 1px solid transparent; 
            border-radius: 0.375rem; 
        }
        .alert-success { 
            color: #065f46; 
            background-color: #d1fae5; 
            border-color: #10b981; 
        }
        .alert-error { 
            color: #991b1b; 
            background-color: #fee2e2; 
            border-color: #ef4444; 
        }
        .card { 
            position: relative; 
            display: flex; 
            flex-direction: column; 
            min-width: 0; 
            word-wrap: break-word; 
            background-color: #fff; 
            background-clip: border-box; 
            border: 1px solid #e5e7eb; 
            border-radius: 0.5rem; 
        }
        .card-body { 
            flex: 1 1 auto; 
            padding: 1.5rem; 
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-blue-600">
                        <i class="fas fa-shipping-fast mr-2"></i>DavKenLog
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium <?php echo ($title ?? '') === 'Book Delivery' ? 'bg-blue-50 text-blue-700' : ''; ?>">
                        <i class="fas fa-calendar-plus mr-1"></i>Book Delivery
                    </a>
                    <a href="/orders/track" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-search-location mr-1"></i>Track Order
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/dashboard" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                        </a>
                        <a href="/logout" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </a>
                    <?php else: ?>
                        <a href="/login" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm font-medium">
                            <i class="fas fa-sign-in-alt mr-1"></i>Login
                        </a>
                    <?php endif; ?>
                    <?php if (Security::hasRole('admin')): ?>
                        <a href="/users" 
                        class="<?php echo ($title ?? '') === 'Manage Users' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> 
                                inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-users mr-2"></i>Users
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">