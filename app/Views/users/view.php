<?php
// Views/view.php
$title = $data['title'] ?? 'User Profile';
$user = $data['user'] ?? [];
$activeTab = $data['activeTab'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <!-- Tambahkan CSS dan JS yang diperlukan -->
</head>
<body>
    <?php include 'layout/header.php'; ?>
    
    <div class="container">
        <h1>User Profile</h1>
        
        <div class="profile-card">
            <h2><?php echo htmlspecialchars($user['username'] ?? ''); ?></h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role'] ?? ''); ?></p>
            <p><strong>Status:</strong> 
                <span class="status-badge status-<?php echo $user['status'] ?? 'inactive'; ?>">
                    <?php echo ucfirst($user['status'] ?? 'inactive'); ?>
                </span>
            </p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($user['department'] ?? 'N/A'); ?></p>
            
            <div class="action-buttons">
                <a href="/users/edit/<?php echo $user['id']; ?>" class="btn btn-primary">Edit</a>
                <a href="/users" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
    
    <?php include 'layout/footer.php'; ?>
</body>
</html>