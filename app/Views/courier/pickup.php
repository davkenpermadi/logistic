<?php
// app/Views/courier/pickup.php
?>
<div class="max-w-2xl mx-auto">
    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Pickup Package</h1>
                    <p class="text-gray-600">Scan or take photo of the package</p>
                </div>
                <a href="/courier/dashboard" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
            
            <!-- Order Info -->
            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <h3 class="text-lg font-medium text-blue-700 mb-3">Order Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Tracking Number</p>
                        <p class="font-mono font-bold text-lg"><?php echo htmlspecialchars($order['tracking_number']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Customer</p>
                        <p class="font-medium"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($order['customer_phone']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Pickup Address</p>
                        <p class="font-medium"><?php echo nl2br(htmlspecialchars($order['pickup_address'])); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Package</p>
                        <p class="font-medium">
                            <?php echo ucfirst(htmlspecialchars($order['package_type'])); ?> • 
                            <?php echo htmlspecialchars($order['package_weight']); ?> kg
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Pickup Form -->
            <form method="POST" action="/courier/complete-pickup/<?php echo $order['id']; ?>" 
                  enctype="multipart/form-data" class="space-y-6" id="pickupForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                
                <!-- Photo Capture -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">
                        <i class="fas fa-camera mr-2"></i>Package Photo
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Camera Preview -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                            <div id="cameraPreview" class="mb-4">
                                <div class="text-gray-500">
                                    <i class="fas fa-camera text-4xl mb-3"></i>
                                    <p>No photo taken yet</p>
                                </div>
                            </div>
                            
                            <!-- Camera Controls -->
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <button type="button" onclick="startCamera()" 
                                        class="btn bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-video mr-2"></i>Open Camera
                                </button>
                                
                                <button type="button" onclick="capturePhoto()" 
                                        id="captureBtn" class="btn bg-green-600 hover:bg-green-700" disabled>
                                    <i class="fas fa-camera mr-2"></i>Take Photo
                                </button>
                                
                                <label class="btn bg-purple-600 hover:bg-purple-700 cursor-pointer">
                                    <i class="fas fa-upload mr-2"></i>Upload Photo
                                    <input type="file" name="pickup_photo" accept="image/*" 
                                           capture="environment" class="hidden" 
                                           onchange="previewUploadedFile(this)">
                                </label>
                            </div>
                        </div>
                        
                        <!-- Hidden canvas for photo capture -->
                        <canvas id="photoCanvas" class="hidden"></canvas>
                        
                        <!-- Photo Preview -->
                        <div id="photoPreview" class="hidden">
                            <h4 class="font-medium text-gray-700 mb-2">Photo Preview:</h4>
                            <div class="border border-gray-200 rounded-lg p-2">
                                <img id="previewImage" class="max-w-full h-auto rounded" 
                                     alt="Package photo preview">
                            </div>
                            <button type="button" onclick="retakePhoto()" 
                                    class="mt-2 text-red-600 hover:text-red-800">
                                <i class="fas fa-redo mr-1"></i>Retake Photo
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Location Information -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">
                        <i class="fas fa-map-marker-alt mr-2"></i>Pickup Location
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="pickup_location" class="block text-sm font-medium text-gray-700 mb-1">
                                Location Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="pickup_location" 
                                   name="pickup_location" 
                                   class="form-control"
                                   placeholder="Enter pickup location or use current location"
                                   required>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                                <input type="text" 
                                       id="latDisplay" 
                                       class="form-control bg-gray-100" 
                                       readonly
                                       placeholder="Click 'Get Location'">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                                <input type="text" 
                                       id="lngDisplay" 
                                       class="form-control bg-gray-100" 
                                       readonly
                                       placeholder="Click 'Get Location'">
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <button type="button" onclick="getCurrentLocation()" 
                                    class="btn bg-blue-600 hover:bg-blue-700">
                                <i class="fas fa-location-crosshairs mr-2"></i>Get Current Location
                            </button>
                            
                            <div id="locationStatus" class="text-sm">
                                <!-- Status will appear here -->
                            </div>
                        </div>
                        
                        <!-- Map Preview -->
                        <div id="mapPreview" class="hidden mt-4">
                            <h4 class="font-medium text-gray-700 mb-2">Location Map:</h4>
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-100 h-48 flex items-center justify-center">
                                <div id="staticMap" class="text-center text-gray-600">
                                    <!-- Google Static Map or similar would go here -->
                                    <i class="fas fa-map text-3xl mb-2"></i>
                                    <p>Location coordinates captured</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Notes -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">
                        <i class="fas fa-sticky-note mr-2"></i>Additional Notes
                    </h3>
                    <div>
                        <label for="pickup_notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Notes (Optional)
                        </label>
                        <textarea id="pickup_notes" 
                                  name="pickup_notes" 
                                  rows="3"
                                  class="form-control"
                                  placeholder="Any special instructions or observations..."></textarea>
                    </div>
                </div>
                
                <!-- Form Validation -->
                <div id="formValidation" class="hidden">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h4 class="font-medium text-yellow-800 mb-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Missing Information
                        </h4>
                        <ul id="validationErrors" class="text-yellow-700 text-sm space-y-1"></ul>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="/courier/dashboard" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" 
                            id="submitBtn"
                            class="btn btn-primary">
                        <i class="fas fa-check-circle mr-2"></i>Complete Pickup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Camera Modal -->
<div id="cameraModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg overflow-hidden max-w-2xl w-full mx-4">
        <div class="p-4 bg-gray-800 text-white flex justify-between items-center">
            <h3 class="text-lg font-medium">Camera</h3>
            <button onclick="closeCamera()" class="text-white hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-4">
            <video id="cameraVideo" autoplay playsinline class="w-full h-auto rounded"></video>
            <div class="mt-4 flex justify-center">
                <button onclick="captureFromCamera()" 
                        class="btn bg-red-600 hover:bg-red-700 text-white">
                    <i class="fas fa-camera mr-2"></i>Capture Photo
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let stream = null;
let photoData = null;
let currentLocation = null;

// Camera Functions
function startCamera() {
    const modal = document.getElementById('cameraModal');
    const video = document.getElementById('cameraVideo');
    
    modal.classList.remove('hidden');
    
    navigator.mediaDevices.getUserMedia({ 
        video: { 
            facingMode: 'environment', // Use rear camera
            width: { ideal: 1280 },
            height: { ideal: 720 }
        } 
    })
    .then(function(mediaStream) {
        stream = mediaStream;
        video.srcObject = stream;
        document.getElementById('captureBtn').disabled = false;
    })
    .catch(function(err) {
        console.error("Camera error: ", err);
        alert('Unable to access camera. Please check permissions.');
        closeCamera();
    });
}

function closeCamera() {
    const modal = document.getElementById('cameraModal');
    modal.classList.add('hidden');
    
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
}

function captureFromCamera() {
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('photoCanvas');
    const context = canvas.getContext('2d');
    
    // Set canvas size to video size
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw video frame to canvas
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Convert to data URL
    photoData = canvas.toDataURL('image/jpeg', 0.8);
    
    // Preview the photo
    previewCapturedPhoto();
    
    // Close camera
    closeCamera();
}

function previewCapturedPhoto() {
    const preview = document.getElementById('photoPreview');
    const image = document.getElementById('previewImage');
    
    image.src = photoData;
    preview.classList.remove('hidden');
    
    // Create a blob from data URL for form submission
    const byteString = atob(photoData.split(',')[1]);
    const mimeString = photoData.split(',')[0].split(':')[1].split(';')[0];
    const ab = new ArrayBuffer(byteString.length);
    const ia = new Uint8Array(ab);
    
    for (let i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }
    
    const blob = new Blob([ab], { type: mimeString });
    
    // Create a file from blob
    const file = new File([blob], 'package_photo.jpg', { type: 'image/jpeg' });
    
    // Create a new DataTransfer object and add the file
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    
    // Assign the files to the file input
    document.querySelector('input[name="pickup_photo"]').files = dataTransfer.files;
}

function previewUploadedFile(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            photoData = e.target.result;
            const preview = document.getElementById('photoPreview');
            const image = document.getElementById('previewImage');
            
            image.src = photoData;
            preview.classList.remove('hidden');
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function retakePhoto() {
    photoData = null;
    document.getElementById('photoPreview').classList.add('hidden');
    document.querySelector('input[name="pickup_photo"]').value = '';
}

// Location Functions
function getCurrentLocation() {
    const statusDiv = document.getElementById('locationStatus');
    statusDiv.innerHTML = '<span class="text-blue-600"><i class="fas fa-spinner fa-spin mr-1"></i>Getting location...</span>';
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const accuracy = position.coords.accuracy;
                
                // Update hidden inputs
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                
                // Update display
                document.getElementById('latDisplay').value = lat.toFixed(6);
                document.getElementById('lngDisplay').value = lng.toFixed(6);
                
                // Update map preview
                updateMapPreview(lat, lng);
                
                // Get address from coordinates (reverse geocoding)
                getAddressFromCoordinates(lat, lng);
                
                // Update status
                statusDiv.innerHTML = `<span class="text-green-600">
                    <i class="fas fa-check-circle mr-1"></i>
                    Location captured (Accuracy: ${Math.round(accuracy)}m)
                </span>`;
                
                currentLocation = { lat, lng };
            },
            function(error) {
                let errorMessage = 'Unable to get location: ';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Permission denied';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Position unavailable';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Request timeout';
                        break;
                    default:
                        errorMessage += 'Unknown error';
                }
                
                statusDiv.innerHTML = `<span class="text-red-600">
                    <i class="fas fa-exclamation-circle mr-1"></i>${errorMessage}
                </span>`;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        statusDiv.innerHTML = '<span class="text-red-600">Geolocation not supported</span>';
    }
}

function getAddressFromCoordinates(lat, lng) {
    // Using OpenStreetMap Nominatim (free, no API key required)
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(response => response.json())
        .then(data => {
            if (data && data.display_name) {
                document.getElementById('pickup_location').value = data.display_name;
            }
        })
        .catch(error => {
            console.error('Geocoding error:', error);
        });
}

function updateMapPreview(lat, lng) {
    const mapPreview = document.getElementById('mapPreview');
    const staticMap = document.getElementById('staticMap');
    
    mapPreview.classList.remove('hidden');
    
    // Create a simple map preview using Google Static Maps (requires API key)
    // Or use OpenStreetMap
    staticMap.innerHTML = `
        <div class="space-y-2">
            <i class="fas fa-map-marked-alt text-blue-500 text-3xl"></i>
            <p class="font-mono text-sm">Lat: ${lat.toFixed(6)}</p>
            <p class="font-mono text-sm">Lng: ${lng.toFixed(6)}</p>
            <a href="https://www.openstreetmap.org/?mlat=${lat}&mlon=${lng}&zoom=16" 
               target="_blank" class="text-blue-600 hover:underline text-sm">
                <i class="fas fa-external-link-alt mr-1"></i>View on OpenStreetMap
            </a>
        </div>
    `;
}

// Form Validation
document.getElementById('pickupForm').addEventListener('submit', function(e) {
    const errors = [];
    
    // Check photo
    if (!photoData && !document.querySelector('input[name="pickup_photo"]').files[0]) {
        errors.push('Please take or upload a photo of the package');
    }
    
    // Check location
    if (!document.getElementById('latitude').value || !document.getElementById('longitude').value) {
        errors.push('Please capture your current location');
    }
    
    // Check location address
    if (!document.getElementById('pickup_location').value.trim()) {
        errors.push('Please enter or confirm the pickup location');
    }
    
    if (errors.length > 0) {
        e.preventDefault();
        
        const validationDiv = document.getElementById('formValidation');
        const errorsList = document.getElementById('validationErrors');
        
        errorsList.innerHTML = '';
        errors.forEach(error => {
            errorsList.innerHTML += `<li>• ${error}</li>`;
        });
        
        validationDiv.classList.remove('hidden');
        
        // Scroll to validation message
        validationDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Try to get location automatically
    setTimeout(getCurrentLocation, 1000);
});
</script>

<style>
#cameraVideo {
    max-height: 70vh;
    background: #000;
}

#previewImage {
    max-height: 300px;
    object-fit: contain;
}

@media (max-width: 640px) {
    .btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
    }
}
</style>