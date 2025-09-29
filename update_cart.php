<?php
session_start();

// Update cart from AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartData = json_decode(file_get_contents('php://input'), true);
    $_SESSION['cart'] = $cartData['cart'];
    echo json_encode(['status' => 'success']);
}
?>
