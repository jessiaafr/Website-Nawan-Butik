<?php
// Menghubungkan ke database
include('koneksi.php');
session_start();

// Verifikasi apakah pelanggan sudah login
$is_logged_in = isset($_SESSION['pelanggan_id']);
if (!$is_logged_in) {
    header("Location: login.php");
    exit();
}

// Mendapatkan data pesanan untuk pelanggan yang sedang login
$pelanggan_id = $_SESSION['pelanggan_id'];
$query = "SELECT p.*, d.quantity, d.price, pr.product_name, pr.product_image
          FROM pesanan p
          JOIN detail_pesanan d ON p.pesanan_id = d.pesanan_id
          JOIN product pr ON d.product_id = pr.product_id
          WHERE p.pelanggan_id = $pelanggan_id
          ORDER BY p.order_date DESC";
$result = mysqli_query($conn, $query);


// Periksa apakah query berhasil
if (!$result) {
    echo "Error: " . mysqli_error($conn);
    exit(); // Hentikan eksekusi jika query gagal
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Anda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="images/logonawan.jpg" type="image/jpg">
    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            color: #6f4f1f;
            font-weight: bold;
        }
        .navbar-nav .nav-link {
            color: #6f4f1f;
        }
        .navbar-nav .nav-link:hover {
            color: #FFD700;
        }
        .container {
            max-width: 1200px;
            margin-top: 50px;
        }
        .order-card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .order-card:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .order-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }
        .status-text {
            font-weight: bold;
            font-size: 16px;
        }
        .status-pending { color: orange; }
        .status-processing { color: blue; }
        .status-completed { color: green; }
        .status-cancelled { color: red; }
        .order-heading {
            color: #6f4f1f;
            font-size: 30px;
            text-align: center;
            margin-bottom: 40px;
        }
        .order-btn {
            background-color: #6f4f1f;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-size: 16px;
            display: inline-block;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .order-btn:hover {
            background-color: #5e3f1c;
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .order-btn-container {
            text-align: center;
            margin-top: 30px;
        }
        @media (max-width: 767px) {
            .order-image {
                width: 60px;
                height: 60px;
            }
            .order-heading {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="#">Nawan Butik</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">Tentang Kami</a></li>
                <li class="nav-item"><a class="nav-link" href="produk.php">Produk</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Kontak</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Keluar</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container">
    <h2 class="order-heading">Ini adalah Pesanan Anda</h2>

    <?php
    include('koneksi.php');

    $pelanggan_id = $_SESSION['pelanggan_id'];
    $query = "SELECT
    p.*,
    d.quantity,
    d.price,
    pr.product_name,
    pr.product_image,
    p.pelanggan_name,
    p.pelanggan_address,
    p.pelanggan_kota,
    p.pelanggan_provinsi,
    p.pelanggan_kodepos,
    p.pelanggan_negara,
    p.pelanggan_nohp
FROM pesanan p
JOIN detail_pesanan d ON p.pesanan_id = d.pesanan_id
JOIN product pr ON d.product_id = pr.product_id
JOIN pelanggan pl ON p.pelanggan_id = pl.pelanggan_id
WHERE p.pelanggan_id = $pelanggan_id
ORDER BY p.order_date DESC
";
    $result = mysqli_query($conn, $query);

    $current_order_id = null;
    $total_harga_pesanan = 0;

    while ($row = mysqli_fetch_assoc($result)):
        if ($current_order_id != $row['pesanan_id']):
            if ($current_order_id !== null):
    ?>
                <p><strong>Total Belanja:</strong> Rp <?php echo number_format($total_harga_pesanan, 0, ',', '.'); ?></p>
                <a href="struk.php?pesanan_id=<?php echo $current_order_id; ?>" class="order-btn mt-2">Lihat Struk</a>
            </div>
    <?php
            endif;

            $current_order_id = $row['pesanan_id'];
            $total_harga_pesanan = 0;
    ?>
            <div class="order-card">
            <h5>Nomor Order: <?php echo $row['pesanan_id']; ?></h5>
                <h5>Nama Pemesan: <?php echo $row['pelanggan_name']; ?></h5>
                <div class="d-flex align-items-start mb-2">
    <i class="fas fa-map-marker-alt me-3 mt-1 text-secondary"></i>
    <div>
        <strong>Alamat:</strong><br>
        <?php echo $row['pelanggan_address']; ?><br>
        <?php echo $row['pelanggan_kota']; ?> – <?php echo $row['pelanggan_provinsi']; ?> - <?php echo $row['pelanggan_kodepos']; ?> - <?php echo $row['pelanggan_negara']; ?>
        <br>
        <?php echo $row['pelanggan_nohp']; ?>
    </div>
</div>

                <p><i class="fas fa-calendar-alt"></i> <?php echo date("d M Y", strtotime($row['order_date'])); ?></p>
                <p><strong>Status:</strong> <?php echo $row['status_pesanan']; ?></p>
    <?php
        endif;

        $subtotal = $row['price'] * $row['quantity'];
        $total_harga_pesanan += $subtotal;
    ?>
                <div class="item d-flex mb-2">
                    <img src="uploads/<?php echo $row['product_image']; ?>" alt="Gambar Produk" class="order-image me-3">
                    <div>
                        <p class="mb-1"><strong><?php echo $row['product_name']; ?></strong> (x<?php echo $row['quantity']; ?>)</p>
                        <p class="mb-0">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></p>
                    </div>
                </div>
    <?php endwhile; ?>

    <?php if ($current_order_id !== null): ?>
        <p><strong>Total Belanja:</strong> Rp <?php echo number_format($total_harga_pesanan, 0, ',', '.'); ?></p>
        <a href="struk.php?pesanan_id=<?php echo $current_order_id; ?>" class="order-btn mt-2">Lihat Struk</a>
        </div>
    <?php endif; ?>

    <div class="order-btn-container">
        <a href="index.php" class="order-btn">Kembali ke Beranda</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
