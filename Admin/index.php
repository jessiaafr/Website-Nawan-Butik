<?php
session_start();

// Include the database connection
include '../koneksi.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Admin info
$admin_name = $_SESSION['admin_name'];

// Ambil jumlah notifikasi (lebih efisien pakai COUNT)
$query_notif = "SELECT COUNT(*) AS total FROM pesanan WHERE status_pesanan = 'PENDING' AND notif_dismissed = 0";
$result_notif = mysqli_query($conn, $query_notif);
$row_notif = mysqli_fetch_assoc($result_notif);
$jumlah_notifikasi = $row_notif['total'];

// Ambil daftar pesanan baru (LIMIT 5)
$query_list = "SELECT * FROM pesanan WHERE status_pesanan = 'PENDING' AND notif_dismissed = 0 ORDER BY order_date DESC LIMIT 5";
$result_list = mysqli_query($conn, $query_list);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Nawan Butik</title>
    <!-- Link to Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts (Poppins and Roboto) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../images/logonawan.jpg" type="image/jpg"> 
    
    <style>
        body {
            background-color: #f0f3f7;
            font-family: 'Poppins', sans-serif;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #fff; /* White background */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.8rem;
            color: #5D4037;
            display: flex;
            align-items: center;
        }
        .navbar-nav .nav-item .nav-link {
            color: #5D4037;
            font-weight: 500;
        }
        .navbar-nav .nav-item .nav-link:hover {
            color: #ff5722;
        }
        .navbar-toggler-icon {
            background-color: #5D4037;
        }

        /* Welcome Text */
        .welcome-text {
            font-size: 2.5rem;
            font-weight: 600;
            color: #5D4037;
            margin-top: 90px;
            text-align: center;
            animation: slideIn 2s ease-in-out;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
        }
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
            background: #ffeb3b;
        }
        .card-title {
            font-size: 1.6rem;
            font-weight: 600;
            color: #5D4037;
        }
        .card-body {
            text-align: center;
        }
        .btn-custom {
            background-color: #ff5722;
            color: white;
            border-radius: 20px;
            transition: background-color 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #e64a19;
        }

        /* Icon Style */
        .nav-link i {
            margin-right: 10px;
        }

        /* Animation */
        @keyframes slideIn {
            0% { transform: translateY(-50px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        /* Mobile Styling */
        .logo {
            width: 55px;
            height: auto;
            margin-right: 10px;
        }

        .nav-item {
            margin-right: 30px;
        }

        footer {
    border-top: 1px solid #ddd;
}

body {
    padding-top: 80px; /* Supaya konten gak ketimpa navbar fixed-top */
}

.btn-custom {
    background-color: #0d6efd;
    color: white;
    border: none;
    transition: 0.3s;
}
.btn-custom:hover {
    background-color: #0b5ed7;
    color: white;
}


    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="../images/logonawan.jpg" alt="Nawan Logo" style="width: 20px; height: auto; margin-right: 10px; vertical-align: middle;"> Nawan Butik
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- Notifikasi -->
                <li class="nav-item dropdown me-3">
                    <a class="nav-link dropdown-toggle position-relative" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <?php if ($jumlah_notifikasi > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $jumlah_notifikasi ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown" style="width: 300px;">
                        <li class="dropdown-header">Pesanan Baru</li>
                        <?php if ($jumlah_notifikasi > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result_list)): ?>
                                <li>
                                    <a class="dropdown-item" href="struk.php?pesanan_id=<?= $row['pesanan_id'] ?>">
                                        <strong><?= $row['pelanggan_name'] ?></strong><br>
                                        <small>ID: <?= $row['pesanan_id'] ?> - <?= date('d/m/Y H:i', strtotime($row['order_date'])) ?></small>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endwhile; ?>
                            <?php else: ?>
    <li><span class="dropdown-item text-muted">Tidak ada pesanan baru</span></li>
<?php endif; ?>

<?php if ($jumlah_notifikasi > 0): ?>
    <li><hr class="dropdown-divider"></li>
    <li>
    <form action="arsip_notifikasi.php" method="post" class="text-center">
    <button type="submit" class="dropdown-item text-muted small" onclick="return confirm('Arsipkan semua notifikasi?')">
        Arsipkan Semua Notifikasi
    </button>
</form>

        </form>
    </li>
<?php endif; ?>

                <!-- Link Menu -->
                <li class="nav-item me-3">
                    <a class="nav-link" href="profile.php"><i class="fas fa-user-circle"></i> Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


    <!-- Main Content -->
    <div class="container mt-5">
        <h2 class="welcome-text">Selamat datang, <?php echo htmlspecialchars($admin_name); ?> di website Nawan Butik!</h2>
        
        <!-- Admin Dashboard Cards -->
<div class="container">
    <div class="row mt-4">
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Kelola Kategori</h5>
                    <a href="kategori.php" class="btn btn-custom w-100">Kelola</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Kelola Produk</h5>
                    <a href="product.php" class="btn btn-custom w-100">Kelola</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Kelola Pesanan</h5>
                    <a href="manage_orders.php" class="btn btn-custom w-100">Kelola</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Manajemen Pembayaran</h5>
                    <a href="manage_payments.php" class="btn btn-custom w-100">Kelola</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Laporan Transaksi</h5>
                    <a href="laporan_transaksi.php" class="btn btn-custom w-100">Kelola</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS & Custom JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
