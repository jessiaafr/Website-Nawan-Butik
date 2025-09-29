<?php
session_start();

// Include the database connection
include '../koneksi.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the 'id' parameter is set
if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    // Sanitize the input
    $category_id = mysqli_real_escape_string($conn, $category_id);

    // Prepare the delete query
    $query = "DELETE FROM category WHERE category_id = '$category_id'";

    // Execute the query
    if (mysqli_query($conn, $query)) {
        // Redirect to the category management page with a success message
        header("Location: kategori.php?message=Kategori berhasil dihapus");
        exit();
    } else {
        // Redirect with an error message if the query fails
        header("Location: kategori.php?message=Gagal menghapus kategori");
        exit();
    }
} else {
    // Redirect if 'id' parameter is not set
    header("Location: kategori.php");
    exit();
}
?>
