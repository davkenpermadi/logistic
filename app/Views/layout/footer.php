<?php
// app/Views/layout/footer.php
?>
    </div> <!-- Close main content div -->
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="mb-2">
                    <i class="fas fa-shipping-fast text-xl mr-2"></i>
                    <span class="text-xl font-bold">LogisticPro</span>
                </p>
                <p class="text-gray-300 mb-4">Professional Logistics Management System</p>
                <div class="flex justify-center space-x-6 mb-4">
                    <a href="/" class="text-gray-300 hover:text-white">Home</a>
                    <a href="/orders/track" class="text-gray-300 hover:text-white">Track Order</a>
                    <a href="/login" class="text-gray-300 hover:text-white">Login</a>
                </div>
                <p class="text-gray-400">&copy; <?php echo date('Y'); ?> Logistic Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        // Simple form validation
        document.addEventListener('DOMContentLoaded', function() {
            // Add CSRF token to all forms
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                if (!form.querySelector('input[name="csrf_token"]')) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = 'csrf_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
                    form.appendChild(csrfInput);
                }
                
                // Form submission handling
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                    }
                });
            });
            
            // Show alerts for 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>