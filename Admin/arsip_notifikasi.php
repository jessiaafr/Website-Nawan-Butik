<?php
include '../koneksi.php';

// Update notifikasi agar tidak muncul lagi
$query = "UPDATE pesanan SET notif_dismissed = 1 WHERE status_pesanan = 'PENDING'";
mysqli_query($conn, $query);

// Redirect ke halaman sebelumnya
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>
