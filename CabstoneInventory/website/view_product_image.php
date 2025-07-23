<?php
include '../lib/db/db.php';

$id = $_GET['id'];
$res = $conn->query("SELECT image FROM product WHERE product_id = '$id'");
if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    header("Content-Type: image/jpeg");
    echo $row['image'];
}
?>
