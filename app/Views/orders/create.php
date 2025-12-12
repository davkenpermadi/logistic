<?php
// app/Views/orders/create.php
?>
<div class="max-w-4xl mx-auto">
    <div class="card">
        <div class="card-body">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Create New Order</h1>
            
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-error mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <!-- Same form as booking.php but for admin -->
                <?php include __DIR__ . '/booking.php'; ?>
            </form>
        </div>
    </div>
</div>