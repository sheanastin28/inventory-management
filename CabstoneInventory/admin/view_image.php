<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "inventory_system";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (isset($_GET['id'])) {
    $img_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT image FROM product_image WHERE img_id = ?");
    $stmt->bind_param("s", $img_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($image);
    if ($stmt->num_rows > 0 && $stmt->fetch()) {
        header("Content-Type: image/jpeg"); // adjust if PNG/GIF/etc
        echo $image;
    } else {
        echo "Image not found.";
    }
    $stmt->close();
}
if (isset($_GET['prod_id'])) {
    $img_id = $_GET['prod_id'];
    $stmt = $conn->prepare("SELECT image FROM product WHERE image = ?");
    $stmt->bind_param("s", $img_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($image);
    if ($stmt->num_rows > 0 && $stmt->fetch()) {
        header("Content-Type: image/jpeg"); // adjust if PNG/GIF/etc
        echo $image;
    } else {
        echo "Image not found.";
    }
    $stmt->close();
}
?>
