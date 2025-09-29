<?php
session_start();

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_GET['order_id'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Terima Kasih - Nawan Butik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/logonawan.jpg" type="image/jpg">
    <style>
        body {
    background-color: #f7f9fb;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    text-align: center;
    font-family: Arial, sans-serif;
}

.thank-you-box {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}

.thank-you-box h1 {
    font-size: 3rem; /* Memperbesar ukuran font judul */
    color: #28a745;
}

.thank-you-box p {
    font-size: 1.5rem; /* Memperbesar ukuran font paragraf */
}

.btn-home {
    font-size: 1.2rem; /* Memperbesar ukuran font tombol */
    padding: 12px 25px; /* Menambah padding pada tombol */
    margin-top: 40px; /* Menambahkan jarak lebih banyak agar tombol lebih ke bawah */
}

@media (max-width: 800px) {
    .thank-you-box {
        padding: 40px 30px; /* Padding lebih besar di mobile */
    }

    .thank-you-box h1 {
        font-size: 3rem; /* Menyesuaikan ukuran font di mobile */
    }

    .thank-you-box p {
        font-size: 1.3rem; /* Menyesuaikan ukuran font di mobile */
    }

    .btn-home {
        font-size: 1.5rem; /* Menyesuaikan ukuran font tombol */
        padding: 12px 30px; /* Menambah padding pada tombol */
    }
}


    </style>
</head>
<body>
    <div class="thank-you-box">
        <h1>🎉 Terima Kasih!</h1>
        <p>Pesanan Anda telah diterima dengan Nomor Pemesanan: <strong><?php echo htmlspecialchars($order_id); ?></strong></p>
        <p>Silakan cek <a href="lacak_orderan.php">Klik Lacak Orderan</a> Anda untuk melihat statusnya.</p>
        <a href="index.php" class="btn btn-success btn-home">Kembali ke Beranda</a>
    </div>
</body>
</html>
