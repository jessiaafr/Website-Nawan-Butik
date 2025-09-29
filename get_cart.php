<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['pelanggan_id'])) {
    echo json_encode([]);
    exit();
}

$pelanggan_id = $_SESSION['pelanggan_id'];

$query = "SELECT c.quantity, p.product_name AS name, p.product_price AS price
          FROM cart c
          JOIN product p ON c.product_id = p.product_id
          WHERE c.pelanggan_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pelanggan_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

echo json_encode($cart_items);
?>
