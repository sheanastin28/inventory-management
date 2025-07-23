<?php
include '../lib/db/db.php';

// ADD USER
if (isset($_POST['add_user'])) {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (user_id, fullname, username, password, role) 
            VALUES (CONCAT('SR-', LPAD(FLOOR(RAND() * 1000), 3, '0')), ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $fullname, $username, $password, $role);
    $stmt->execute();

    header("Location: user_management.php?status=success");;
    exit();
}

// EDIT USER
if (isset($_POST['edit_user'])) {
    $id = $_POST['user_id'];
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET fullname=?, username=?, role=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $fullname, $username, $role, $id);
    $stmt->execute();

    header("Location: user_management.php?status=success");
    exit();
}

// ARCHIVE USER
if (isset($_POST['archive_user'])) {
    $id = $_POST['user_id'];

    // Move user to archive table
    $sql = "INSERT INTO archive_users (user_id, username, fullname, password, role, last_login)
            SELECT user_id, username, fullname, password, role, last_login FROM users WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();

    // Delete from users table
    $sql = "DELETE FROM users WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();

    header("Location: user_management.php?status=success");
    exit();
}
?>
