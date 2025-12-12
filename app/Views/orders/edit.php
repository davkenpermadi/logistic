<?php
// app/Views/orders/edit.php
?>
<div class="max-w-4xl mx-auto">
    <div class="card">
        <div class="card-body">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Order</h1>
            
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-error mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <!-- Order Info -->
                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-blue-700 mb-2">Order Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Tracking Number</p>
                            <p class="font-mono font-bold text-lg"><?php echo htmlspecialchars($order['tracking_number']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Created Date</p>
                            <p class="font-medium"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Customer Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">
                        <i class="fas fa-user-circle mr-2"></i>Customer Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="customer_name" 
                                   name="customer_name" 
                                   value="<?php echo htmlspecialchars($order['customer_name']); ?>"
                                   class="form-control <?php echo isset($errors['customer_name']) ? 'border-red-500' : ''; ?>"
                                   required>
                        </div>
                        
                        <div>
                            <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="customer_email" 
                                   name="customer_email" 
                                   value="<?php echo htmlspecialchars($order['customer_email']); ?>"
                                   class="form-control <?php echo isset($errors['customer_email']) ? 'border-red-500' : ''; ?>"
                                   required>
                        </div>
                        
                        <div>
                            <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" 
                                   id="customer_phone" 
                                   name="customer_phone" 
                                   value="<?php echo htmlspecialchars($order['customer_phone']); ?>"
                                   class="form-control <?php echo isset($errors['customer_phone']) ? 'border-red-500' : ''; ?>"
                                   required>
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
                                      required><?php echo htmlspecialchars($order['pickup_address']); ?></textarea>
                        </div>
                        
                        <div>
                            <label for="delivery_address" class="block text-sm font-medium text-gray-700 mb-1">
                                Delivery Address <span class="text-red-500">*</span>
                            </label>
                            <textarea id="delivery_address" 
                                      name="delivery_address" 
                                      rows="3"
                                      class="form-control <?php echo isset($errors['delivery_address']) ? 'border-red-500' : ''; ?>"
                                      required><?php echo htmlspecialchars($order['delivery_address']); ?></textarea>
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
                                <option value="document" <?php echo $order['package_type'] === 'document' ? 'selected' : ''; ?>>Document</option>
                                <option value="parcel" <?php echo $order['package_type'] === 'parcel' ? 'selected' : ''; ?>>Parcel</option>
                                <option value="electronics" <?php echo $order['package_type'] === 'electronics' ? 'selected' : ''; ?>>Electronics</option>
                                <option value="fragile" <?php echo $order['package_type'] === 'fragile' ? 'selected' : ''; ?>>Fragile</option>
                                <option value="food" <?php echo $order['package_type'] === 'food' ? 'selected' : ''; ?>>Food</option>
                                <option value="other" <?php echo $order['package_type'] === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
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
                                   value="<?php echo htmlspecialchars($order['package_weight']); ?>"
                                   class="form-control <?php echo isset($errors['package_weight']) ? 'border-red-500' : ''; ?>"
                                   required>
                        </div>
                        
                        <div>
                            <label for="package_dimensions" class="block text-sm font-medium text-gray-700 mb-1">Dimensions (LxWxH cm)</label>
                            <input type="text" 
                                   id="package_dimensions" 
                                   name="package_dimensions" 
                                   value="<?php echo htmlspecialchars($order['package_dimensions'] ?? ''); ?>"
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
                                Delivery Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="delivery_date" 
                                   name="delivery_date" 
                                   value="<?php echo htmlspecialchars($order['delivery_date']); ?>"
                                   class="form-control <?php echo isset($errors['delivery_date']) ? 'border-red-500' : ''; ?>"
                                   required>
                        </div>
                        
                        <div>
                            <label for="delivery_time" class="block text-sm font-medium text-gray-700 mb-1">Preferred Time</label>
                            <select id="delivery_time" 
                                    name="delivery_time" 
                                    class="form-control">
                                <option value="anytime" <?php echo ($order['delivery_time'] ?? 'anytime') === 'anytime' ? 'selected' : ''; ?>>Anytime</option>
                                <option value="morning" <?php echo ($order['delivery_time'] ?? '') === 'morning' ? 'selected' : ''; ?>>Morning (9AM-12PM)</option>
                                <option value="afternoon" <?php echo ($order['delivery_time'] ?? '') === 'afternoon' ? 'selected' : ''; ?>>Afternoon (1PM-5PM)</option>
                                <option value="evening" <?php echo ($order['delivery_time'] ?? '') === 'evening' ? 'selected' : ''; ?>>Evening (6PM-9PM)</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Status -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>Order Status
                    </h3>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" 
                                name="status" 
                                class="form-control"
                                required>
                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="in_transit" <?php echo $order['status'] === 'in_transit' ? 'selected' : ''; ?>>In Transit</option>
                            <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                </div>
                
                <!-- Submit Buttons -->
                <div class="flex justify-between pt-6 border-t border-gray-200">
                    <a href="/orders" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                    </a>
                    <div class="space-x-4">
                        <button type="reset" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                            <i class="fas fa-redo mr-2"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Update Order
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>