<?php
session_start();
include '../lib/db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id  = $_POST['user_id'];
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET fullname = ?, username = ?, password = ? WHERE user_id = ?");
        $stmt->bind_param("ssss", $fullname, $username, $hashedPassword, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET fullname = ?, username = ? WHERE user_id = ?");
        $stmt->bind_param("sss", $fullname, $username, $user_id);
    }

    if ($stmt->execute()) {
        // Update session data too
        $_SESSION['fullname'] = $fullname;
        $_SESSION['username'] = $username;

        header("Location: profile.php?status=success");
    } else {
        echo "Failed to update profile.";
    }
}
?>
