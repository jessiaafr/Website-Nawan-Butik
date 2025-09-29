<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_id = session_id();

    // Validasi product_id
    if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
        die("Produk tidak valid.");
    }

    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Cek apakah produk sudah ada di cart
    $cek = mysqli_query($conn, "SELECT * FROM cart WHERE session_id = '$session_id' AND product_id = $product_id");
    if (mysqli_num_rows($cek) > 0) {
        // Jika sudah ada, update quantity
        mysqli_query($conn, "UPDATE cart SET quantity = quantity + $quantity WHERE session_id = '$session_id' AND product_id = $product_id");
    } else {
        // Jika belum ada, tambahkan ke cart
        mysqli_query($conn, "INSERT INTO cart (session_id, product_id, quantity) VALUES ('$session_id', $product_id, $quantity)");
    }

    // Redirect kembali ke halaman sebelumnya atau ke cart
    header("Location: cart.php");
    exit;
}
?>
