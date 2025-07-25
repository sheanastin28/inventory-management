<?php
session_start();
include '../lib/db/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Session values
$user = [
    'id'    => $_SESSION['user_id'],
    'name'  => $_SESSION['fullname'] ?? 'John Doe',
    'email' => $_SESSION['username'] ?? 'johndoe@example.com'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="../lib/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f6fa;
            margin: 0;
        }
        .main-content {
            padding: 15px;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 1);
            padding: 18px;
            max-width: 450px;
            margin: 30px auto;
            transition: 0.3s;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0, 0, 0, 1);
        }
        .card h1 {
            margin: 0 0 12px;
            font-size: 25px;
            color: #333;
            text-align: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        .profile-details {
            text-align: center;
            font-size: 14px;
        }
        .profile-details h2 {
            margin: 8px 0 4px;
            font-size: 20px;
            color: #333;
        }
        .profile-details p {
            margin: 4px 0;
            color: #555;
            font-size: 20px;
        }
        .btn-link {
            display: inline-block;
            margin-top: 12px;
            padding: 8px 14px;
            background: #007bff;
            color: #fff;
            font-size: 13px;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.3s;
            box-shadow: 0 5px 12px rgba(0, 0, 0, 1);
        }
        .btn-link:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <?php include 'header.php'; ?>
        <div class="card">
            <h1>Profile Settings</h1>
            <div class="profile-details">
                <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                <a href="update_profile.php" class="btn-link"><i class="fa fa-edit"></i> Edit Profile</a>
            </div>
        </div>
    </div>
<script src="../lib/js/main.js"></script>
</body>
</html>
