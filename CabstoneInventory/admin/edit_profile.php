<?php
session_start();
include '../lib/db/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user data from database
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../lib/css/style.css">
</head>
<style>
    form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    }

    input, button {
        padding: 10px;
        font-size: 16px;
    }

    button {
        background-color: #3f51b5;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 5px;
    }
</style>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">

    <?php include 'header.php';?>
    <div class="card" style="max-width: 600px; margin: 40px auto;">
        <h2>Edit Profile</h2>
        <form action="update_profile.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">

            <label>Full Name</label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>

            <label>Username (email)</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label>New Password</label>
            <input type="password" name="password" placeholder="Leave blank to keep current password">

            <button type="submit">Update Profile</button>
        </form>
    </div>
</div>

</body>
</html>
