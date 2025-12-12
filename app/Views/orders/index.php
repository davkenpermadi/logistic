<?php
// app/Views/orders/index.php
?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manage Orders</h1>
            <p class="mt-1 text-sm text-gray-600">View and manage all delivery orders</p>
        </div>
        <div class="mt-4 flex items-center space-x-3 md:mt-0">
            <a href="/orders/create" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>New Order
            </a>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-grow">
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" <?php echo ($filters['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo ($filters['status'] ?? '') === 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="in_transit" <?php echo ($filters['status'] ?? '') === 'in_transit' ? 'selected' : ''; ?>>In Transit</option>
                    <option value="delivered" <?php echo ($filters['status'] ?? '') === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="cancelled" <?php echo ($filters['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <button type="submit" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </form>
    </div>
    
    <!-- Orders Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tracking #
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Customer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Package
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="text-4xl text-gray-300 mb-3">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <p>No orders found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 font-mono">
                                    <?php echo htmlspecialchars($order['tracking_number']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($order['customer_name']); ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo htmlspecialchars($order['customer_email']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?php echo ucfirst(htmlspecialchars($order['package_type'])); ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo htmlspecialchars($order['package_weight']); ?> kg
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo $order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : ''; ?>
                                    <?php echo $order['status'] === 'processing' ? 'bg-blue-100 text-blue-800' : ''; ?>
                                    <?php echo $order['status'] === 'in_transit' ? 'bg-indigo-100 text-indigo-800' : ''; ?>
                                    <?php echo $order['status'] === 'delivered' ? 'bg-green-100 text-green-800' : ''; ?>
                                    <?php echo $order['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : ''; ?>">
                                    <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="/orders/track?tracking=<?php echo urlencode($order['tracking_number']); ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-3" title="Track">
                                    <i class="fas fa-search-location"></i>
                                </a>
                                <a href="/orders/edit/<?php echo $order['id']; ?>" 
                                   class="text-indigo-600 hover:text-indigo-900 mr-3" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="confirmDelete(<?php echo $order['id']; ?>)" 
                                        class="text-red-600 hover:text-red-900" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(orderId) {
    if (confirm('Are you sure you want to delete this order?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/orders/delete/${orderId}`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>