<?php
// app/Views/auth/login.php
?>
<div class="max-w-md mx-auto">
    <div class="card">
        <div class="card-body">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Login to Dashboard</h1>
            
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-error mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           class="form-control"
                           placeholder="admin@logistic.davken.my.id"
                           required>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control"
                           placeholder="Enter your password"
                           required>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="text-sm">
                        <a href="#" class="text-blue-600 hover:text-blue-500">
                            Forgot your password?
                        </a>
                    </div>
                </div>
                
                <button type="submit" class="w-full btn btn-primary">
                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                </button>
            </form>
        </div>
    </div>
</div>