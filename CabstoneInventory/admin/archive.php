<?php
include '../lib/db/db.php'; // Database connection
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Archived Users</title>
    <link rel="stylesheet" href="../lib/css/style.css"> <!-- Assuming shared styles -->
    <link rel="stylesheet" href="../lib/css/modal.css"> <!-- Optional -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <?php include 'header.php'; ?>

    <h1>Archive</h1>

    <div class="user-table-box">
        <h3>Archived Users<i class="fas fa-archive"></i></h3>

        <?php
        $query = "SELECT username, fullname, role FROM archive_users";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0): ?>
            <table>
                <tr>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Role</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No archived users found.</p>
        <?php endif; ?>
    </div>
</div>

<script src="../lib/js/modal.js"></script>
</body>
</html>
