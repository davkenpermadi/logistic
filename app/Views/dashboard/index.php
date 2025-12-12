<div class="space-y-6">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-600">Welcome back, <?php echo $_SESSION['user_name']; ?>! Here's what's happening with your logistics.</p>
        </div>
        <div class="mt-4 flex items-center space-x-3 md:mt-0">
            <a href="/orders/create" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>New Order
            </a>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-filter mr-2"></i>Filter
                    <i class="fas fa-chevron-down ml-2 text-xs"></i>
                </button>
                
                <div x-show="open" 
                     @click.away="open = false"
                     class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-1" role="menu">
                        <a href="/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">All Orders</a>
                        <a href="/dashboard?status=pending" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pending Only</a>
                        <a href="/dashboard?status=processing" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Processing Only</a>
                        <a href="/dashboard?status=delivered" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Delivered Only</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-boxes text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                            <dd class="text-lg font-semibold text-gray-900"><?php echo $totalOrders; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                            <dd class="text-lg font-semibold text-gray-900">
                                <?php 
                                $pending = array_filter($orderStats, fn($stat) => $stat['status'] === 'pending');
                                echo $pending ? reset($pending)['count'] : 0;
                                ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Delivered</dt>
                            <dd class="text-lg font-semibold text-gray-900">
                                <?php 
                                $delivered = array_filter($orderStats, fn($stat) => $stat['status'] === 'delivered');
                                echo $delivered ? reset($delivered)['count'] : 0;
                                ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-purple-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                            <dd class="text-lg font-semibold text-gray-900"><?php echo $totalUsers; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Statistics</h3>
            <div class="relative h-64">
                <canvas id="orderChart"></canvas>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Status Distribution</h3>
            <div class="relative h-64">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Recent Orders -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Orders</h3>
            <p class="mt-1 text-sm text-gray-600">List of recently created delivery orders.</p>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tracking #
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Package
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo $order['tracking_number']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo $order['customer_name']; ?></div>
                                <div class="text-sm text-gray-500"><?php echo $order['customer_email']; ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo ucfirst($order['package_type']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo $order['package_weight']; ?> kg</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo $order['status'] === 'pending' ? 'status-pending' : ''; ?>
                                    <?php echo $order['status'] === 'processing' ? 'status-processing' : ''; ?>
                                    <?php echo $order['status'] === 'delivered' ? 'status-delivered' : ''; ?>
                                    <?php echo $order['status'] === 'cancelled' ? 'status-cancelled' : ''; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="/orders/edit/<?php echo $order['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" 
                                   onclick="confirmDelete(<?php echo $order['id']; ?>)" 
                                   class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-end">
                <a href="/orders" class="text-blue-600 hover:text-blue-900 font-medium">
                    View all orders <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<div class="mt-8 card">
    <div class="card-body">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <a href="/users/create" class="btn bg-blue-600 hover:bg-blue-700 text-white">
                <i class="fas fa-user-plus mr-2"></i>Add User
            </a>
            <a href="/users" class="btn bg-green-600 hover:bg-green-700 text-white">
                <i class="fas fa-users mr-2"></i>Manage Users
            </a>
            <a href="/orders/create" class="btn bg-purple-600 hover:bg-purple-700 text-white">
                <i class="fas fa-plus-circle mr-2"></i>Create Order
            </a>
            <a href="/courier" class="btn bg-orange-600 hover:bg-orange-700 text-white">
                <i class="fas fa-motorcycle mr-2"></i>Courier Dashboard
            </a>
        </div>
    </div>
</div>
<script>
// Pass PHP data to JavaScript
const chartData = <?php echo json_encode($chartData); ?>;
const orderStats = <?php echo json_encode($orderStats); ?>;

// Delete confirmation
function confirmDelete(orderId) {
    if (confirm('Are you sure you want to delete this order?')) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/orders/delete/${orderId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({ csrf_token: csrfToken })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to delete order');
            }
        });
    }
}
</script>