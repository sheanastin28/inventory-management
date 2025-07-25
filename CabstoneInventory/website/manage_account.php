<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "inventory_system";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// ✅ Update profile info
if (isset($_POST['update_info'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $cont_num = $_POST['cont_num'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE user SET name = ?, email = ?, cont_num = ?, address = ? WHERE user_id = ?");
    $stmt->bind_param("sssss", $name, $email, $cont_num, $address, $user_id);

    if ($stmt->execute()) {
        $message = "✅ Profile updated successfully.";
    } else {
        $message = "❌ Error updating profile.";
    }
    $stmt->close();
}

// ✅ Change password
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $newpass = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $res = $conn->prepare("SELECT password FROM user WHERE user_id = ?");
    $res->bind_param("s", $user_id);
    $res->execute();
    $res->bind_result($hashed);
    $res->fetch();
    $res->close();

    if (password_verify($current, $hashed)) {
        if ($newpass === $confirm) {
            $new_hashed = password_hash($newpass, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE user SET password = ? WHERE user_id = ?");
            $update->bind_param("ss", $new_hashed, $user_id);
            if ($update->execute()) {
                $message = "Password changed successfully.";
            } else {
                $message = "Failed to change password.";
            }
            $update->close();
        } else {
            $message = "New passwords do not match.";
        }
    } else {
        $message = "Current password is incorrect.";
    }
}

// ✅ Fetch user info (after update so latest data shows)
$stmt = $conn->prepare("SELECT name, email, cont_num, address FROM user WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $email = $row['email'];
    $cont_num = $row['cont_num'];
    $address = $row['address'];
} else {
    $name = $email = $cont_num = $address = '';
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans text-sm">
    <?php include 'header.php'; ?>

    <div class="max-w-5xl mx-auto bg-white p-6 mt-8 shadow-xl rounded-lg">
        <h2 class="text-xl font-bold mb-4 text-gray-700 text-center">Manage Account</h2>

        <?php if (!empty($message)): ?>
            <div class="mb-4 text-center text-white px-3 py-2 rounded text-xs <?php echo (strpos($message, '✅') !== false) ? 'bg-green-500 shadow-md' : 'bg-red-500 shadow-md'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Update Info Form -->
            <div class="bg-white shadow-lg rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-3 text-gray-700">Update Profile Info</h3>
                <form method="POST" class="space-y-3">
                    <div>
                        <label class="block text-gray-600 text-xs mb-1">Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" class="w-full p-2 border rounded text-xs focus:ring-1 focus:ring-blue-400 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs mb-1">Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="w-full p-2 border rounded text-xs focus:ring-1 focus:ring-blue-400 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs mb-1">Contact Number</label>
                        <input type="text" name="cont_num" value="<?php echo htmlspecialchars($cont_num); ?>" class="w-full p-2 border rounded text-xs focus:ring-1 focus:ring-blue-400 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs mb-1">Address</label>
                        <textarea name="address" class="w-full p-2 border rounded text-xs focus:ring-1 focus:ring-blue-400 shadow-sm" required><?php echo htmlspecialchars($address); ?></textarea>
                    </div>
                    <button type="submit" name="update_info" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full text-xs font-semibold shadow-md">Update Info</button>
                </form>
            </div>

            <!-- Change Password Form -->
            <div class="bg-white shadow-lg rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-3 text-gray-700">Change Password</h3>
                <form method="POST" class="space-y-3">
                    <div>
                        <label class="block text-gray-600 text-xs mb-1">Current Password</label>
                        <input type="password" name="current_password" class="w-full p-2 border rounded text-xs focus:ring-1 focus:ring-green-400 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs mb-1">New Password</label>
                        <input type="password" name="new_password" class="w-full p-2 border rounded text-xs focus:ring-1 focus:ring-green-400 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs mb-1">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="w-full p-2 border rounded text-xs focus:ring-1 focus:ring-green-400 shadow-sm" required>
                    </div>
                    <button type="submit" name="change_password" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 w-full text-xs font-semibold shadow-md">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
