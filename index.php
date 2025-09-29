<?php
session_start();
include('koneksi.php');

// Mengecek apakah ada session pelanggan yang aktif
$is_logged_in = isset($_SESSION['pelanggan_id']);
$pelanggan_name = $is_logged_in ? $_SESSION['pelanggan_name'] : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nawan Butik</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts for Custom Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="images/logonawan.jpg" type="image/jpg">
    <style>
        body {
    font-family: 'Montserrat', sans-serif;
}
            background-color: #f4f4f9; /* Warna latar belakang */
            color: #333; /* Warna teks */
            overflow-x: hidden;
        }

        .navbar {
            background-color: #fff; /* Putih */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand img {
            font-size: 17px;
            height: 40px;
            transition: transform 0.3s;
        }

        .navbar, .navbar-nav .nav-link, .navbar-brand {
    font-family: 'montserrat', sans-serif;
}

.navbar-nav .nav-link {
    font-size: 17px;
    font-weight: bold;
}
        .navbar-nav .nav-link:hover {
            color: #b08a72; /* Cokelat medium */
        }

        .welcome-banner {
            height: 300px;
            background-size: cover;
            background-position: center;
            color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            font-size: 2.5rem;
            font-family: 'Segoe UI', sans-serif;
            font-weight: bold;
            background-color: #4a3c31; /* Cokelat tua */
            margin-bottom: 40px;
            animation: fadeIn 2s ease-in-out;
        }

        .welcome-banner h1 {
            animation: slideIn 1.5s ease-out;
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        @keyframes slideIn {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(0); }
        }

        .section-title {
    font-family: 'Playfair Display', cursive;
    font-size: 2,8rem;
    color: #4a3c31;
    text-align: center;
    margin-bottom: 20px;
    animation: fadeIn 1s ease-in-out;
}

        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background-color: #b08a72;
            margin: 10px auto;
            border-radius: 2px;
        }

        .content-section {
            padding: 40px;
            margin-bottom: 40px;
            background-color: #fff;
            border: 1px solid #e5d3c7;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            font-size: 1.1rem;
        }

        .content-section:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-center {
            display: block;
            margin: 20px auto;
            background-color: #b08a72;
            color: #fff;
            padding: 12px 40px;
            border-radius: 30px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-center:hover {
            background-color: #8a6b56;
            transform: scale(1.1);
        }

        footer {
            background-color: #4a3c31;
            color: #f4f4f9;
            text-align: center;
            padding: 20px 0;
            font-size: 1rem;
            letter-spacing: 1px;
        }

        .carousel-inner img {
            border-radius: 10px;
        }

        /* Smooth scroll effect */
        html {
            scroll-behavior: smooth;
        }
        
        .material-symbols-outlined {
        font-variation-settings:
        'FILL' 0,
        'wght' 400,
        'GRAD' 0,
        'opsz' 24
        }
        </style>
    
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="index.php"><img src="images/logonawan.jpg" alt="Logo"> Nawan Butik</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">Tentang Kami</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Kontak</a></li>
            </ul>

            <ul class="navbar-nav ms-auto"> <!-- ms-auto untuk push ke kanan -->
                    <li class="nav-item">
                        <a class="nav-link" href="produk.php"><i class="fas fa-bars"></i> Produk</a>
                    </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Tambahkan ini di <head> jika kamu pakai file HTML lengkap -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">

<div class="welcome-banner" style="
    background-color: #E2CEB1;
    height: 300px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: #5A4631;
    padding: 20px;
    font-family: 'Great Vibes', serif;
">
    <h1 style="margin: 0; font-size: 35px; font-weight: bold;">Gaya Kamu, Pilihan Kami!</h1>
    <h2 style="margin: 10px 0 0; font-size: 30px;">Temukan outfit yang tepat untuk harimu bersama Nawan Butik</h2>
</div>

<div class="container">
    <div class="content-section">
        <h2 class="section-title">Apa sih spesialnya Nawan Butik?</h2>
        <p class="text-center">Karena kamu pantas dapat yang terbaik dari kualitas hingga pelayanan, semua kami siapkan dengan hati.</p>
    </div>

    
    <div class="content-section">
        <h2 class="section-title">Eksplor Koleksi Kami</h2>
        <div class="row align-items-center">
            <div class="col-md-6">
                <p>Nawan Butik hadir melengkapi gaya anda dari kasual hingga elegan.</p>
                <button class="btn btn-center" onclick="location.href='produk.php'"><i class="fas fa-shopping-cart"></i> Pesan Sekarang</button>
            </div>
            <div class="col-md-6">
                <div id="carouselPrasmanan" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="images/nawan.jpg" style="width: 90%; height: auto;" class="d-block mx-auto" alt="Prasmanan 1">
                        </div>
                        <div class="carousel-item">
                            <img src="images/nawan2.jpg" style="width: 85%; height: auto;" class="d-block mx-auto" alt="Prasmanan 2">
                        </div>
                        <div class="carousel-item">
                            <img src="images/nawan3.jpg" style="width: 60%; height: auto;" class="d-block mx-auto" alt="Prasmanan ">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselPrasmanan" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselPrasmanan" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="content-section">
        <h2 class="section-title">Lacak Pesanan Anda</h2>
        <div class="row align-items-center">
            <div class="col-md-6">
                <p></p>
                <button class="btn btn-center d-flex align-items-center justify-content-center gap-2" onclick="location.href='lacak_orderan.php'">
    <span class="material-symbols-outlined" style="font-size: 1.5rem;">location_on</span>Lacak Sekarang</button>
            </div>
            <div class="col-md-6">
                <img src="images/hijab.jpg" alt="Catering Nasi Box" style="width: 90%; height: auto;" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

<footer>
    <p style="font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 500;">© 2025 Nawan Butik | All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
