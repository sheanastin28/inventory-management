<?php
session_start();
include '../lib/db/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Use proper session values
$user = [
    'id'    => $_SESSION['user_id'],
    'name'  => $_SESSION['fullname'] ?? 'John Doe',
    'email' => $_SESSION['username'] ?? 'johndoe@example.com',
    // Use default profile pic if you don’t store a photo in session
    'profile_pic' => $_SESSION['profile_pic'] ?? 'default-avatar.png'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="../lib/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">

    <?php include 'header.php';?>
    <div class="card" style="max-width: 600px; margin: 40px auto;">
        <h2 style="margin-top: 0;">Profile Settings</h2>
        <div style="display: flex; align-items: center; gap: 20px;">
            <div>
                <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                <a href="edit_profile.php" class="btn-link">Edit Profile</a>
            </div>
        </div>
    </div>
</div>
<script src="../lib/js/main.js"></script>
</body>
</html>
