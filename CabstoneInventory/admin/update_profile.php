<?php
session_start();
include '../lib/db/db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id        = $_POST['user_id'];
    $fullname       = $_POST['fullname'];
    $username       = $_POST['username'];
    $currentPass    = $_POST['current_password'] ?? '';
    $newPass        = $_POST['new_password'] ?? '';
    $confirmPass    = $_POST['confirm_password'] ?? '';

    // Fetch current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $hashedPassword = $user['password'] ?? '';

    if ($newPass || $confirmPass || $currentPass) {
        if (empty($currentPass) || empty($newPass) || empty($confirmPass)) {
            $error = "All password fields are required.";
        } elseif (!password_verify($currentPass, $hashedPassword)) {
            $error = "Current password is incorrect.";
        } elseif ($newPass !== $confirmPass) {
            $error = "New passwords do not match.";
        } else {
            $newHashed = password_hash($newPass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET fullname = ?, username = ?, password = ? WHERE user_id = ?");
            $stmt->bind_param("ssss", $fullname, $username, $newHashed, $user_id);
        }
    }

    if (!isset($error)) {
        if (empty($newPass)) {
            $stmt = $conn->prepare("UPDATE users SET fullname = ?, username = ? WHERE user_id = ?");
            $stmt->bind_param("sss", $fullname, $username, $user_id);
        }
        if ($stmt->execute()) {
            $_SESSION['fullname'] = $fullname;
            $_SESSION['username'] = $username;
            header("Location: profile.php?status=success");
            exit();
        } else {
            $error = "Failed to update profile.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../lib/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f6fa;
            margin: 0;
        }
        .main-content { padding: 10px; }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 1);
            padding: 15px;
            margin: 20px auto;
            position: relative;
            height: 550px;
            width: 600px;
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 18px;
            color: #777;
            text-decoration: none;
        }
        .close-btn:hover { color: #000; }
        .card h2 {
            margin-bottom: 10px;
            font-size: 18px;
            text-align: center;
            color: #333;
        }
        form { display: flex; flex-direction: column; gap: 8px; }
        label {
            font-size: 13px;
            color: #444;
        }
        input[type="text"], input[type="password"] {
            width: 97%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 13px;
        }
        input:focus { border-color: #007bff; outline: none; }
        .btn-submit {
            padding: 8px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            margin-bottom: 20px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 1);
        }
        .btn-submit:hover { background-color: #0056b3; }
        .info-text {
            font-size: 11px;
            color: #888;
            margin-top: -4px;
        }
        .error { color: red; font-size: 12px; text-align: center; margin-bottom: 8px; }
        h3 {
            font-size: 14px;
            margin: 6px 0;
            color: #333;
        }
        hr { margin: 8px 0; }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <?php include 'header.php'; ?>
    <div class="card">
        <a href="profile.php" class="close-btn"><i class="fa fa-times"></i></a>
        <h2>Edit Profile</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">

            <div>
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($_SESSION['fullname']); ?>" required>
            </div>

            <div>
                <label for="username">Username (Email)</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
            </div>

            <hr>
            <h3>Change Password</h3>
            <p class="info-text">Leave blank if you do not want to change it.</p>

            <div>
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password">
            </div>

            <div>
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password">
            </div>

            <div>
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>

            <button type="submit" class="btn-submit"><i class="fa fa-save"></i> Save</button>
        </form>
    </div>
</div>
</body>
</html>
