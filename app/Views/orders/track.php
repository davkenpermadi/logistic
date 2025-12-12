<?php
// app/Views/orders/track.php
?>
<div class="max-w-4xl mx-auto">
    <div class="card">
        <div class="card-body">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Track Your Order</h1>
            <p class="text-gray-600 mb-6">Enter your tracking number to check the status of your delivery</p>
            
            <!-- Tracking Form -->
            <form method="GET" action="/orders/track" class="mb-8">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-grow">
                        <input type="text" 
                               name="tracking" 
                               value="<?php echo htmlspecialchars($trackingNumber ?? ''); ?>"
                               class="form-control"
                               placeholder="Enter tracking number (e.g., TRK202512110001)"
                               required>
                    </div>
                    <button type="submit" class="btn btn-primary whitespace-nowrap">
                        <i class="fas fa-search mr-2"></i>Track Order
                    </button>
                </div>
            </form>
            
            <?php if (isset($trackingNumber) && $trackingNumber): ?>
                <?php if ($order): ?>
                    <!-- Order Found -->
                    <div class="border border-gray-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Order Details</h2>
                                <p class="text-gray-600">Tracking: <span class="font-mono font-bold"><?php echo htmlspecialchars($order['tracking_number']); ?></span></p>
                            </div>
                            <div class="px-4 py-2 rounded-full 
                                <?php echo $order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : ''; ?>
                                <?php echo $order['status'] === 'processing' ? 'bg-blue-100 text-blue-800' : ''; ?>
                                <?php echo $order['status'] === 'in_transit' ? 'bg-indigo-100 text-indigo-800' : ''; ?>
                                <?php echo $order['status'] === 'delivered' ? 'bg-green-100 text-green-800' : ''; ?>
                                <?php echo $order['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : ''; ?>">
                                <span class="font-semibold"><?php echo ucfirst($order['status']); ?></span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <h3 class="font-medium text-gray-700 mb-2">Customer Information</h3>
                                <div class="space-y-2">
                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="font-medium text-gray-700 mb-2">Delivery Information</h3>
                                <div class="space-y-2">
                                    <p><strong>Package Type:</strong> <?php echo ucfirst($order['package_type']); ?></p>
                                    <p><strong>Weight:</strong> <?php echo htmlspecialchars($order['package_weight']); ?> kg</p>
                                    <p><strong>Delivery Date:</strong> <?php echo date('M d, Y', strtotime($order['delivery_date'])); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="font-medium text-gray-700 mb-2">Pickup Address</h3>
                                <div class="bg-gray-50 p-3 rounded">
                                    <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($order['pickup_address'])); ?></p>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="font-medium text-gray-700 mb-2">Delivery Address</h3>
                                <div class="bg-gray-50 p-3 rounded">
                                    <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Order Timeline -->
                        <div class="mt-8">
                            <h3 class="font-medium text-gray-700 mb-4">Order Timeline</h3>
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                        <i class="fas fa-check text-green-600 text-sm"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium">Order Created</p>
                                        <p class="text-sm text-gray-500"><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
                                    </div>
                                </div>
                                
                                <?php if ($order['status'] === 'processing' || $order['status'] === 'in_transit' || $order['status'] === 'delivered'): ?>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-box text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium">Processing</p>
                                        <p class="text-sm text-gray-500">Package is being prepared for shipment</p>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($order['status'] === 'in_transit' || $order['status'] === 'delivered'): ?>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <i class="fas fa-shipping-fast text-indigo-600 text-sm"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium">In Transit</p>
                                        <p class="text-sm text-gray-500">Package is on the way to destination</p>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($order['status'] === 'delivered'): ?>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                        <i class="fas fa-home text-green-600 text-sm"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium">Delivered</p>
                                        <p class="text-sm text-gray-500">Package has been delivered successfully</p>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex justify-center space-x-4">
                        <a href="/" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                            <i class="fas fa-calendar-plus mr-2"></i>Book New Delivery
                        </a>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="fas fa-print mr-2"></i>Print Details
                        </button>
                    </div>
                    
                <?php else: ?>
                    <!-- Order Not Found -->
                    <div class="text-center py-12">
                        <div class="text-6xl text-gray-300 mb-4">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Order Not Found</h3>
                        <p class="text-gray-600 mb-6">We couldn't find an order with tracking number: <span class="font-mono font-bold"><?php echo htmlspecialchars($trackingNumber); ?></span></p>
                        <div class="space-y-3">
                            <p class="text-gray-500">Please check:</p>
                            <ul class="text-gray-500 list-disc pl-5 text-left inline-block">
                                <li>The tracking number is correct</li>
                                <li>The order has been created successfully</li>
                                <li>You're using the correct format</li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- No Tracking Number Entered -->
                <div class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
                    <div class="text-6xl text-gray-300 mb-4">
                        <i class="fas fa-search-location"></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-700 mb-2">Enter Tracking Number</h3>
                    <p class="text-gray-600">Please enter your tracking number above to check your order status.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Help Section -->
    <div class="mt-8 card">
        <div class="card-body">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Need Help?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl text-blue-600 mb-3">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h4 class="font-medium text-gray-700 mb-2">Where to find tracking number?</h4>
                    <p class="text-gray-600 text-sm">You'll receive the tracking number in your email after booking.</p>
                </div>
                
                <div class="text-center">
                    <div class="text-3xl text-blue-600 mb-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h4 class="font-medium text-gray-700 mb-2">How long does tracking update?</h4>
                    <p class="text-gray-600 text-sm">Tracking updates every few hours during business days.</p>
                </div>
                
                <div class="text-center">
                    <div class="text-3xl text-blue-600 mb-3">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4 class="font-medium text-gray-700 mb-2">Need more help?</h4>
                    <p class="text-gray-600 text-sm">Contact our support team for assistance.</p>
                    <a href="mailto:support@logistic.davken.my.id" class="text-blue-600 hover:underline text-sm">support@logistic.davken.my.id</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-focus on tracking input
document.addEventListener('DOMContentLoaded', function() {
    const trackingInput = document.querySelector('input[name="tracking"]');
    if (trackingInput && !trackingInput.value) {
        trackingInput.focus();
    }
    
    // Print functionality
    window.printDetails = function() {
        const printContent = document.querySelector('.border.border-gray-200.rounded-lg');
        if (printContent) {
            const originalContent = document.body.innerHTML;
            document.body.innerHTML = printContent.innerHTML;
            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
        }
    };
});
</script>