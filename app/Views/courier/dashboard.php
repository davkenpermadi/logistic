<?php
// app/Views/courier/dashboard.php
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Courier Dashboard</h1>
            <p class="mt-1 text-sm text-gray-600">
                Welcome back, <?php echo htmlspecialchars($courier['username'] ?? 'Courier'); ?>!
                <span id="statusBadge" class="ml-2 px-2 py-1 text-xs font-semibold rounded-full 
                    <?php echo ($courier['status'] ?? 'offline') === 'available' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                    <?php echo ucfirst($courier['status'] ?? 'offline'); ?>
                </span>
            </p>
        </div>
        <div class="mt-4 flex items-center space-x-3 md:mt-0">
            <button id="toggleStatus" 
                    class="btn <?php echo ($courier['status'] ?? 'offline') === 'available' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'; ?>">
                <i class="fas fa-power-off mr-2"></i>
                <?php echo ($courier['status'] ?? 'offline') === 'available' ? 'Go Offline' : 'Go Online'; ?>
            </button>
            <button onclick="getLocation()" class="btn bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-location-crosshairs mr-2"></i>Update Location
            </button>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-box text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Assigned Pickups</dt>
                            <dd class="text-lg font-semibold text-gray-900"><?php echo count($assignedOrders); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Completed Today</dt>
                            <dd class="text-lg font-semibold text-gray-900"><?php echo count($pickedUpOrders); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Available Pickups</dt>
                            <dd class="text-lg font-semibold text-gray-900"><?php echo count($availablePickups); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-motorcycle text-purple-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Vehicle</dt>
                            <dd class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($courier['vehicle_type'] ?? 'Not Set'); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Assigned Pickups -->
        <div class="card">
            <div class="card-body">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-clipboard-list mr-2"></i>Assigned Pickups
                </h3>
                
                <?php if (empty($assignedOrders)): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-box-open text-3xl mb-3"></i>
                        <p>No assigned pickups</p>
                        <p class="text-sm mt-2">Check available pickups below</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($assignedOrders as $order): ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($order['customer_name']); ?></h4>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($order['pickup_address']); ?></p>
                                    <div class="mt-2 flex items-center space-x-4">
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-phone mr-1"></i><?php echo htmlspecialchars($order['customer_phone']); ?>
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-calendar mr-1"></i><?php echo date('M d', strtotime($order['delivery_date'])); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">
                                        Assigned
                                    </span>
                                    <div class="mt-2">
                                        <a href="/courier/pickup/<?php echo $order['id']; ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-camera mr-1"></i>Pickup
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <div class="mt-4 text-center">
                    <a href="/courier/assignments" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View all assignments <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Available Pickups -->
        <div class="card">
            <div class="card-body">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-boxes mr-2"></i>Available Pickups
                </h3>
                
                <?php if (empty($availablePickups)): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-check-circle text-3xl mb-3"></i>
                        <p>No available pickups</p>
                        <p class="text-sm mt-2">All pickups have been assigned</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($availablePickups as $pickup): ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($pickup['customer_name']); ?></h4>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($pickup['pickup_address']); ?></p>
                                    <div class="mt-2">
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-weight-hanging mr-1"></i><?php echo htmlspecialchars($pickup['package_weight']); ?> kg
                                        </span>
                                        <span class="text-xs text-gray-500 ml-3">
                                            <?php echo ucfirst(htmlspecialchars($pickup['package_type'])); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <form method="POST" action="/courier/accept-pickup/<?php echo $pickup['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                        <button type="submit" class="btn bg-green-600 hover:bg-green-700 btn-sm">
                                            <i class="fas fa-check mr-1"></i>Accept
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <div class="mt-4 text-center">
                    <a href="/courier/pickup" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View all pickups <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Location Map (Optional) -->
    <div class="card">
        <div class="card-body">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-map-marker-alt mr-2"></i>Current Location
            </h3>
            <div id="mapContainer" class="h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                <div class="text-center text-gray-500">
                    <i class="fas fa-map text-3xl mb-3"></i>
                    <p>Click "Update Location" to show your current position</p>
                    <p class="text-sm mt-2" id="locationInfo"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('toggleStatus').addEventListener('click', function() {
    const button = this;
    const originalText = button.innerHTML;
    const csrfToken = '<?php echo $_SESSION["csrf_token"] ?? ""; ?>';
    
    // Show loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    
    fetch('/courier/toggle-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({})
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const badge = document.getElementById('statusBadge');
            
            if (data.status === 'available') {
                badge.className = 'ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800';
                badge.textContent = 'Available';
                button.className = 'btn bg-red-600 hover:bg-red-700';
                button.innerHTML = '<i class="fas fa-power-off mr-2"></i>Go Offline';
            } else {
                badge.className = 'ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800';
                badge.textContent = 'Offline';
                button.className = 'btn bg-green-600 hover:bg-green-700';
                button.innerHTML = '<i class="fas fa-power-off mr-2"></i>Go Online';
            }
        } else {
            alert('Failed to update status: ' + (data.message || 'Unknown error'));
            button.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating status. Please try again.');
        button.innerHTML = originalText;
    })
    .finally(() => {
        button.disabled = false;
    });
});

// Get Current Location - SIMPLIFIED VERSION
function getLocation() {
    const button = document.querySelector('button[onclick="getLocation()"]');
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Getting location...';
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                // Show location immediately
                const locationInfo = document.getElementById('locationInfo');
                locationInfo.innerHTML = 
                    `<i class="fas fa-spinner fa-spin text-blue-500 mr-1"></i>
                    Getting address...`;
                
                // Get address from coordinates (simplified)
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                    .then(response => response.json())
                    .then(addressData => {
                        const address = addressData.display_name || 'Location updated';
                        
                        // Update location on server
                        return fetch('/courier/update-location', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-Token': '<?php echo $_SESSION["csrf_token"] ?? ""; ?>'
                            },
                            body: JSON.stringify({
                                latitude: lat,
                                longitude: lng,
                                location: address
                            })
                        });
                    })
                    .then(response => response.json())
                    .then(data => {
                        const locationInfo = document.getElementById('locationInfo');
                        
                        if (data.success) {
                            locationInfo.innerHTML = 
                                `<i class="fas fa-check text-green-500 mr-1"></i>
                                Location updated: ${lat.toFixed(4)}, ${lng.toFixed(4)}<br>
                                <small class="text-gray-600">${data.location || ''}</small>`;
                            
                            showSimpleMap(lat, lng);
                        } else {
                            locationInfo.innerHTML = 
                                `<i class="fas fa-times text-red-500 mr-1"></i>
                                Failed to save location`;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const locationInfo = document.getElementById('locationInfo');
                        locationInfo.innerHTML = 
                            `<i class="fas fa-times text-red-500 mr-1"></i>
                            Error: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                    })
                    .finally(() => {
                        button.disabled = false;
                        button.innerHTML = originalText;
                    });
            },
            function(error) {
                console.error('Geolocation error:', error);
                alert('Error getting location. Please enable location services or try again.');
                button.disabled = false;
                button.innerHTML = originalText;
                
                // Fallback: Use default coordinates
                const locationInfo = document.getElementById('locationInfo');
                locationInfo.innerHTML = 
                    `<i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i>
                    Location access denied`;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000, // 10 seconds
                maximumAge: 0
            }
        );
    } else {
        alert('Geolocation is not supported by this browser.');
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

function showSimpleMap(lat, lng) {
    const mapContainer = document.getElementById('mapContainer');
    mapContainer.innerHTML = `
        <div class="w-full h-full flex flex-col items-center justify-center bg-blue-50 rounded-lg">
            <i class="fas fa-map-marker-alt text-blue-500 text-3xl mb-2"></i>
            <p class="font-mono text-sm">Latitude: ${lat.toFixed(6)}</p>
            <p class="font-mono text-sm">Longitude: ${lng.toFixed(6)}</p>
            <div class="mt-4">
                <a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank" 
                   class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                    <i class="fas fa-external-link-alt mr-1"></i> Open in Google Maps
                </a>
            </div>
        </div>
    `;
}

// Fix form submissions
document.addEventListener('DOMContentLoaded', function() {
    // Add CSRF token to all forms
    const forms = document.querySelectorAll('form');
    const csrfToken = '<?php echo $_SESSION["csrf_token"] ?? ""; ?>';
    
    forms.forEach(form => {
        if (!form.querySelector('input[name="csrf_token"]')) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }
        
        // Handle form submission
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
            }
        });
    });
});
</script>