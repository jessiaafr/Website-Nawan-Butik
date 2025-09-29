<?php
// Include koneksi database
include '../koneksi.php';

// Cek jika parameter 'id' ada di URL
if (isset($_GET['id'])) {
    // Ambil ID dari URL
    $payment_id = $_GET['id'];

    // Query untuk menghapus data metode pembayaran
    $query = "DELETE FROM metode_pembayaran WHERE payment_id = '$payment_id'";

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        // Jika berhasil, redirect ke halaman daftar metode pembayaran
        header("Location: manage_payments.php");
        exit();
    } else {
        // Jika gagal, tampilkan pesan error
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
} else {
    // Jika parameter 'id' tidak ditemukan
    echo "<div class='alert alert-danger'>ID Pembayaran tidak ditemukan.</div>";
}

// Tutup koneksi
mysqli_close($conn);
?>
