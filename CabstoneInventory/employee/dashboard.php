<?php
session_start();
include '../lib/db/db.php';

$sql = "SELECT COUNT(*) AS total FROM users";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$sql1 = "SELECT COUNT(*) AS total FROM inquire";
$result1 = $conn->query($sql1);
$row1 = $result1->fetch_assoc();

// Total Category
$sql2 = "SELECT COUNT(*) AS total FROM category";
$result2 = $conn->query($sql2);
$row2 = $result2->fetch_assoc();

?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../lib/css/style.css">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">

    <?php include 'header.php';?>

        <h1>Dashboard</h1>

        <div class="stats">
            <div class="stat-box red"><i class="fas fa-user"></i><h2><?php echo $row['total']; ?></h2><p>Users</p></div>
            <div class="stat-box purple"><i class="fas fa-user-friends"></i><h2><?php echo $row1['total']; ?></h2><p>Inquires</p></div>
            <div class="stat-box blue"><i class="fas fa-th-large"></i><h2><?php echo $row2['total']; ?></h2><p>Category</p></div>
            <div class="stat-box yellow"><i class="fas fa-coins"></i><h2>₱120,000</h2><p>Sales</p></div>
        </div>

        <div class="tables">
            <div class="table-box">
                <h3><i class="fas fa-table-cells-large"></i> Latest Sales</h3>
                <table>
                    <tr><th>Product Name</th><th>Date</th><th>Total Sales</th></tr>
                    <tr><td>Product Name</td><td>6-18-25</td><td>5000</td></tr>
                    <tr><td>Product Name</td><td>6-18-25</td><td>5000</td></tr>
                    <tr><td>Product Name</td><td>6-18-25</td><td>5000</td></tr>
                    <tr><td>Product Name</td><td>6-18-25</td><td>5000</td></tr>
                </table>
            </div>

            <div class="table-box">
                <h3><i class="fas fa-table-cells-large"></i> Recently Added Products</h3>
                <div class="product">
                    <img src="https://via.placeholder.com/60">
                    <div><strong>Product Name</strong><br><small>Price • Category</small></div>
                </div>
                <div class="product">
                    <img src="https://via.placeholder.com/60">
                    <div><strong>Product Name</strong><br><small>Price • Category</small></div>
                </div>
                <div class="product">
                    <img src="https://via.placeholder.com/60">
                    <div><strong>Product Name</strong><br><small>Price • Category</small></div>
                </div>
            </div>
        </div>
    </div>

    <script src="../lib/js/main.js"></script>
</body>
</html>
