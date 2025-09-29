<?php
session_start();

// Include the database connection
include '../koneksi.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Check if product ID is provided
if (isset($_GET['id'])) {
    $productId = (int) $_GET['id'];

    // Delete product from the database
    $query = "DELETE FROM product WHERE product_id = $productId";
    if (mysqli_query($conn, $query)) {
        // Redirect back to product page with success message
        header("Location: product.php?message=Produk berhasil dihapus");
    } else {
        // Redirect back to product page with error message
        header("Location: product.php?message=Gagal menghapus produk");
    }
} else {
    // If no product ID is provided, redirect back to product page
    header("Location: product.php");
}
exit();
?>
