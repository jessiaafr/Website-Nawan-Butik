<?php
session_start();

// Menghapus session pelanggan
session_unset();
session_destroy();

// Arahkan pengguna ke halaman login setelah logout
header("Location: login.php");
exit;
?>
