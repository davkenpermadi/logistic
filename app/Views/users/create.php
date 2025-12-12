<?php
// app/Views/users/create.php
?>
<div class="max-w-4xl mx-auto">
    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Create New User</h1>
                    <p class="text-gray-600">Add a new user to the system</p>
                </div>
                <a href="/users" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Users
                </a>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error mb-6">
                    <h4 class="font-bold mb-2">Please fix the following errors:</h4>
                    <ul class="list-disc pl-5 space-y-1">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3 text-green-500 text-xl"></i>
                        <div>
                            <h4 class="font-bold">User Created Successfully!</h4>
                            <p>The new user has been added to the system.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-8">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <!-- Basic Information -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">
                        <i class="fas fa-id-card mr-2"></i>Basic Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   value="<?php echo htmlspecialchars($formData['username'] ?? ''); ?>"
                                   class="form-control"
                                   placeholder="johndoe"
                                   required>
                            <p class="mt-1 text-xs text-gray-500">3-50 characters, letters, numbers, underscores and hyphens only</p>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>"
                                   class="form-control"
                                   placeholder="john@example.com"
                                   required>
                        </div>
                        
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                First Name
                            </label>
                            <input type="text" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="<?php echo htmlspecialchars($formData['first_name'] ?? ''); ?>"
                                   class="form-control"
                                   placeholder="John">
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Last Name
                            </label>
                            <input type="text" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="<?php echo htmlspecialchars($formData['last_name'] ?? ''); ?>"
                                   class="form-control"
                                   placeholder="Doe">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Phone Number
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>"
                                   class="form-control"
                                   placeholder="+62 812-3456-7890">
                        </div>
                    </div>
                </div>
                
                <!-- Security & Permissions -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">
                        <i class="fas fa-lock mr-2"></i>Security & Permissions
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="form-control pr-10"
                                       placeholder="••••••••"
                                       required>
                                <button type="button" 
                                        onclick="togglePassword('password')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-eye text-gray-400"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Minimum 6 characters</p>
                        </div>
                        
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       class="form-control pr-10"
                                       placeholder="••••••••"
                                       required>
                                <button type="button" 
                                        onclick="togglePassword('confirm_password')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-eye text-gray-400"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="">Select Role</option>
                                <option value="admin" <?php echo ($formData['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                                <option value="manager" <?php echo ($formData['role'] ?? '') === 'manager' ? 'selected' : ''; ?>>Manager</option>
                                <option value="staff" <?php echo ($formData['role'] ?? '') === 'staff' ? 'selected' : ''; ?>>Staff</option>
                                <option value="courier" <?php echo ($formData['role'] ?? '') === 'courier' ? 'selected' : ''; ?>>Courier</option>
                                <option value="customer" <?php echo ($formData['role'] ?? '') === 'customer' ? 'selected' : ''; ?>>Customer</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-1">
                                Department
                            </label>
                            <input type="text" 
                                   id="department" 
                                   name="department" 
                                   value="<?php echo htmlspecialchars($formData['department'] ?? ''); ?>"
                                   class="form-control"
                                   placeholder="Logistics, Sales, etc.">
                        </div>
                    </div>
                </div>
                
                <!-- Additional Information -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>Additional Information
                    </h3>
                    
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                            Address
                        </label>
                        <textarea id="address" 
                                  name="address" 
                                  rows="3"
                                  class="form-control"
                                  placeholder="Full address..."><?php echo htmlspecialchars($formData['address'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="flex justify-between space-x-4 pt-6 border-t border-gray-200">
                    <button type="reset" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                        <i class="fas fa-redo mr-2"></i>Reset Form
                    </button>
                    
                    <div class="space-x-4">
                        <button type="submit" name="save_and_new" value="1" 
                                class="btn bg-blue-600 hover:bg-blue-700 text-white">
                            <i class="fas fa-save mr-2"></i>Save & Add Another
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check-circle mr-2"></i>Save User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'fas fa-eye-slash text-gray-400';
    } else {
        field.type = 'password';
        icon.className = 'fas fa-eye text-gray-400';
    }
}

// Password strength indicator
document.getElementById('password').addEventListener('input', function(e) {
    const password = e.target.value;
    const strength = checkPasswordStrength(password);
    const indicator = document.getElementById('password-strength');
    
    if (!indicator) {
        const indicatorDiv = document.createElement('div');
        indicatorDiv.id = 'password-strength';
        indicatorDiv.className = 'mt-2';
        e.target.parentNode.appendChild(indicatorDiv);
    }
    
    updatePasswordStrength(strength);
});

function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
}

function updatePasswordStrength(strength) {
    const indicator = document.getElementById('password-strength');
    const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'];
    const texts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    
    indicator.innerHTML = `
        <div class="flex items-center space-x-2">
            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full ${colors[strength - 1] || 'bg-gray-400'} transition-all duration-300" 
                     style="width: ${(strength / 5) * 100}%"></div>
            </div>
            <span class="text-xs font-medium ${strength >= 4 ? 'text-green-600' : strength >= 3 ? 'text-blue-600' : 'text-red-600'}">
                ${texts[strength - 1] || 'Very Weak'}
            </span>
        </div>
    `;
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        document.getElementById('confirm_password').focus();
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('Password must be at least 6 characters long!');
        document.getElementById('password').focus();
    }
});
</script>