<?php
// app/Views/orders/booking.php
?>
<div class="max-w-4xl mx-auto">
    <div class="card">
        <div class="card-body">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Book a Delivery</h1>
            <p class="text-gray-600 mb-6">Fill out the form below to schedule your delivery</p>
            
            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3 text-green-500"></i>
                        <div>
                            <h4 class="font-bold">Booking Successful!</h4>
                            <p class="mt-1">Your delivery has been scheduled successfully.</p>
                            <p class="mt-2 font-semibold">Tracking Number: <span class="text-lg text-blue-600"><?php echo htmlspecialchars($trackingNumber ?? ''); ?></span></p>
                            <div class="mt-3 flex space-x-3">
                                <a href="/orders/track?tracking=<?php echo urlencode($trackingNumber ?? ''); ?>" 
                                   class="btn btn-primary">
                                    <i class="fas fa-search-location mr-2"></i>Track This Order
                                </a>
                                <a href="/" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                                    <i class="fas fa-plus mr-2"></i>New Booking
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-error mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!isset($success) || !$success): ?>
            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <!-- Customer Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">
                        <i class="fas fa-user-circle mr-2"></i>Customer Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="customer_name" 
                                   name="customer_name" 
                                   value="<?php echo htmlspecialchars($_POST['customer_name'] ?? ''); ?>"
                                   class="form-control <?php echo isset($errors['customer_name']) ? 'border-red-500' : ''; ?>"
                                   required>
                            <?php if (isset($errors['customer_name'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['customer_name']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="customer_email" 
                                   name="customer_email" 
                                   value="<?php echo htmlspecialchars($_POST['customer_email'] ?? ''); ?>"
                                   class="form-control <?php echo isset($errors['customer_email']) ? 'border-red-500' : ''; ?>"
                                   required>
                            <?php if (isset($errors['customer_email'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['customer_email']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" 
                                   id="customer_phone" 
                                   name="customer_phone" 
                                   value="<?php echo htmlspecialchars($_POST['customer_phone'] ?? ''); ?>"
                                   class="form-control <?php echo isset($errors['customer_phone']) ? 'border-red-500' : ''; ?>"
                                   placeholder="081234567890"
                                   required>
                            <?php if (isset($errors['customer_phone'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['customer_phone']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Delivery Details -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">
                        <i class="fas fa-map-marker-alt mr-2"></i>Delivery Details
                    </h3>
                    <div class="space-y-6">
                        <div>
                            <label for="pickup_address" class="block text-sm font-medium text-gray-700 mb-1">
                                Pickup Address <span class="text-red-500">*</span>
                            </label>
                            <textarea id="pickup_address" 
                                      name="pickup_address" 
                                      rows="3"
                                      class="form-control <?php echo isset($errors['pickup_address']) ? 'border-red-500' : ''; ?>"
                                      required><?php echo htmlspecialchars($_POST['pickup_address'] ?? ''); ?></textarea>
                            <?php if (isset($errors['pickup_address'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['pickup_address']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label for="delivery_address" class="block text-sm font-medium text-gray-700 mb-1">
                                Delivery Address <span class="text-red-500">*</span>
                            </label>
                            <textarea id="delivery_address" 
                                      name="delivery_address" 
                                      rows="3"
                                      class="form-control <?php echo isset($errors['delivery_address']) ? 'border-red-500' : ''; ?>"
                                      required><?php echo htmlspecialchars($_POST['delivery_address'] ?? ''); ?></textarea>
                            <?php if (isset($errors['delivery_address'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['delivery_address']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Package Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">
                        <i class="fas fa-box mr-2"></i>Package Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="package_type" class="block text-sm font-medium text-gray-700 mb-1">
                                Package Type <span class="text-red-500">*</span>
                            </label>
                            <select id="package_type" 
                                    name="package_type" 
                                    class="form-control <?php echo isset($errors['package_type']) ? 'border-red-500' : ''; ?>"
                                    required>
                                <option value="">Select Type</option>
                                <option value="document" <?php echo ($_POST['package_type'] ?? '') === 'document' ? 'selected' : ''; ?>>Document</option>
                                <option value="parcel" <?php echo ($_POST['package_type'] ?? '') === 'parcel' ? 'selected' : ''; ?>>Parcel</option>
                                <option value="electronics" <?php echo ($_POST['package_type'] ?? '') === 'electronics' ? 'selected' : ''; ?>>Electronics</option>
                                <option value="fragile" <?php echo ($_POST['package_type'] ?? '') === 'fragile' ? 'selected' : ''; ?>>Fragile</option>
                                <option value="food" <?php echo ($_POST['package_type'] ?? '') === 'food' ? 'selected' : ''; ?>>Food</option>
                                <option value="other" <?php echo ($_POST['package_type'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                            <?php if (isset($errors['package_type'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['package_type']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label for="package_weight" class="block text-sm font-medium text-gray-700 mb-1">
                                Weight (kg) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="package_weight" 
                                   name="package_weight" 
                                   min="0.1"
                                   step="0.1"
                                   value="<?php echo htmlspecialchars($_POST['package_weight'] ?? ''); ?>"
                                   class="form-control <?php echo isset($errors['package_weight']) ? 'border-red-500' : ''; ?>"
                                   required>
                            <?php if (isset($errors['package_weight'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['package_weight']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label for="package_dimensions" class="block text-sm font-medium text-gray-700 mb-1">Dimensions (LxWxH cm)</label>
                            <input type="text" 
                                   id="package_dimensions" 
                                   name="package_dimensions" 
                                   value="<?php echo htmlspecialchars($_POST['package_dimensions'] ?? ''); ?>"
                                   class="form-control"
                                   placeholder="30x20x15">
                        </div>
                    </div>
                </div>
                
                <!-- Delivery Schedule -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">
                        <i class="fas fa-calendar-alt mr-2"></i>Delivery Schedule
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Preferred Delivery Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="delivery_date" 
                                   name="delivery_date" 
                                   min="<?php echo date('Y-m-d'); ?>"
                                   value="<?php echo htmlspecialchars($_POST['delivery_date'] ?? ''); ?>"
                                   class="form-control <?php echo isset($errors['delivery_date']) ? 'border-red-500' : ''; ?>"
                                   required>
                            <?php if (isset($errors['delivery_date'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['delivery_date']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label for="delivery_time" class="block text-sm font-medium text-gray-700 mb-1">Preferred Time</label>
                            <select id="delivery_time" 
                                    name="delivery_time" 
                                    class="form-control">
                                <option value="anytime">Anytime</option>
                                <option value="morning" <?php echo ($_POST['delivery_time'] ?? '') === 'morning' ? 'selected' : ''; ?>>Morning (9AM-12PM)</option>
                                <option value="afternoon" <?php echo ($_POST['delivery_time'] ?? '') === 'afternoon' ? 'selected' : ''; ?>>Afternoon (1PM-5PM)</option>
                                <option value="evening" <?php echo ($_POST['delivery_time'] ?? '') === 'evening' ? 'selected' : ''; ?>>Evening (6PM-9PM)</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button type="reset" 
                            class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                        <i class="fas fa-redo mr-2"></i>Reset Form
                    </button>
                    <button type="submit" 
                            class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Booking
                    </button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
