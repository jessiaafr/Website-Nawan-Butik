<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nawan Butik Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="images/logonawan.jpg" type="image/jpg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f7f9fb;
            font-family: Arial, sans-serif;
        }

        /* Navbar Styling */
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand img {
            height: 30px;
            margin-right: 10px;
        }

        .navbar-nav {
            margin: 0 auto;
        }

        .navbar-nav .nav-link {
            color: #6c4f3d;
            font-weight: bold;
        }

        .navbar-nav .nav-link:hover {
            color: #d79e6e;
        }

        /* Contact Section Styling */
        .contact-header {
            text-align: center;
            margin: 40px 0;
            font-size: 30px;
            font-weight: bold;
            color: #6c4f3d;
            text-transform: uppercase;
            animation: fadeIn 1s ease-out;
        }

        .contact-section {
            margin: 30px 0;
        }

        .contact-section h5 {
            color: #6c4f3d;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .contact-section p {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
        }

        .map-container {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Footer Styling */
        footer {
            background-color: #6c4f3d;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 30px;
            border-top: 2px solid #ddd;
            animation: fadeIn 2s ease-out;
        }

        /* Hover effects and animations */
        .map-container iframe {
            border-radius: 8px;
            transition: transform 0.3s ease-in-out;
        }

        .map-container iframe:hover {
            transform: scale(1.05);
        }

        /* Animation */
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="images/logonawan.jpg" alt="Logo" height="30" class="me-2">
                Nawan Butik
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link" href="produk.php">Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Kontak</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contact Section -->
    <div class="container mt-4">
        <h3 class="contact-header">Hubungi Kami</h3>
        <div class="row contact-section">
            <div class="col-md-6">
                <h5>Informasi Kontak</h5>
                <p>Alamat: Jl. Harapan Jaya, Kota Bekasi</p>
                <p>Email: info@nawan butik.com</p>
                <p>Telepon: +62 812 3456 7890</p>
            </div>
            <div class="col-md-6">
                <h5>Penjelasan</h5>
                <p>Kami siap melayani kebutuhan Anda dengan produk terbaik dan pelayanan ramah. Jangan ragu untuk menghubungi kami melalui informasi di samping atau datang langsung ke lokasi kami.</p>
                <!-- Tambahkan ikon sosial media -->
                 <div class="mt-3">
                 <a href="https://www.instagram.com/nawanbutik?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank" class="me-3 text-dark fs-4">
                <i class="bi bi-instagram"></i>
                </a>
</div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <h5>Lokasi Kami</h5>
                <div class="map-container mb-4">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15865.675183842823!2d106.97999129518118!3d-6.208362518493993!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e698bf6937828cb%3A0x28efdca284a4c754!2sHarapan%20Jaya%2C%20Kec.%20Bekasi%20Utara%2C%20Kota%20Bks%2C%20Jawa%20Barat!5e0!3m2!1sid!2sid!4v1751695264334!5m2!1sid!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2025 Nawan Butik | All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
