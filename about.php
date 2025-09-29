<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Nawan Butik</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/logonawan.jpg" type="image/jpg">
    <style>
        /* Custom CSS */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
        }

        /* Navbar Styling */
        .navbar {
            background-color: #ffffff; /* White background for navbar */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand img {
            height: 40px;
        }

        .navbar-nav {
            margin: auto;
        }

        .navbar-nav .nav-link {
            color: #6c4f3d; /* Dark Brown for text */
            font-weight: bold;
        }

        .navbar-nav .nav-link:hover {
            color: #d79e6e; /* Lighter brown for hover effect */
        }

        /* Header Image Section */
        .image-header {
            background-image: url('images/about.jpg');
            background-size: cover;
            background-position: center;
            height: 300px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #ffffff;
            font-size: 2rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            animation: fadeIn 2s ease-in-out;
            margin-bottom: 30px; /* Add bottom margin */
        }

        /* Animations */
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        .image-header {
    background-image: url('images/1.jpg');  /* Gambar baru */
    background-size: cover;
    background-position: center;
    height: 300px;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #ffffff;
    font-size: 2rem;
    font-weight: bold;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    animation: fadeIn 2s ease-in-out;
    margin-bottom: 30px;
}
        /* Content Section Styling */
        .content-section {
            padding: 40px;
            background-color: #f8f9fa; /* Light beige */
            margin-bottom: 40px; /* Increase bottom margin */
            border: 1px solid #dee2e6;
            border-radius: 10px;
            text-align: left;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            opacity: 0;
            animation: slideUp 1s forwards;
        }

        @keyframes slideUp {
            0% { transform: translateY(30px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        .content-section:nth-child(odd) {
            background-color: #f1e2c9; /* Light chocolate color */
        }

        .content-section h2 {
            color: #6c4f3d; /* Dark brown title */
            font-weight: bold;
            font-size: 1.75rem;
            margin-bottom: 20px;
        }

        .content-section p {
            font-size: 1rem;
            line-height: 1.6;
            color: #555;
            margin-bottom: 20px; /* Add bottom margin to paragraphs */
        }

        /* Image Styling for Responsiveness */
        .content-section img {
            max-height: 250px;
            object-fit: cover;
            width: 100%;
            border-radius: 10px;
            margin-bottom: 30px; /* Increase bottom margin */
            transition: transform 0.3s ease-in-out;
        }

        .content-section img:hover {
            transform: scale(1.05); /* Zoom effect on image hover */
        }

        footer {
            background-color: #6c4f3d;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 30px;
            border-top: 2px solid #ddd;
            animation: fadeIn 2s ease-out;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            .image-header {
                font-size: 1.5rem; /* Smaller font size for mobile */
                height: 200px;
            }

            .content-section h2 {
                font-size: 1.5rem;
            }

            .content-section {
                padding: 30px;
            }

            .navbar-nav .nav-link {
                font-size: 0.9rem; /* Adjust font size for smaller screens */
            }

            .content-section img {
                max-height: 200px; /* Smaller images on mobile */
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#"><img src="images/logonawan.jpg" alt="Logo"> Nawan Butik</a>
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

    <!-- Header Image Section -->
    <div class="image-header">
        <p>Tentang Nawan Butik</p>
    </div>

    <div class="container">
        <!-- Section 1: Pendirian Catering -->
        <div class="content-section">
            <h2>Selamat Datang di Nawan Butik</h2>
            <p>
                Nawan Butik adalah toko fashion yang bergerak di bidang penjualan pakaian wanita melalui platform e-commerce. Kami berkomitmen untuk menyediakan produk-produk fashion yang stylish, nyaman, dan terjangkau, yang dapat dipesan secara online dan dikirim langsung ke rumah pelanggan. Tidak hanya menjual pakaian wanita, Nawan Butik juga menyediakan berbagai pilihan hijab dan aksesori untuk melengkapi penampilan pelanggan. Mulai dari busana kasual hingga busana formal, koleksi kami dirancang untuk memenuhi beragam kebutuhan dan gaya wanita modern. Dengan pelayanan yang terus berkembang, kami berupaya memberikan pengalaman belanja online yang mudah, aman, dan menyenangkan bagi setiap pelanggan.
            </p>
            <p>
                Dengan mengutamakan bahan-bahan yang berkualitas, Nawan Butik tidak hanya menghadirkan pakaian , tetapi juga kepercayaan. Kami percaya bahwa fashion adalah cara untuk mengekspresikan, dan setiap koleksi kami dirancang dengan cinta dan perhatian terhadap detail.
            </p>
        </div>

        <!-- Section 2 -->
        <div class="content-section">
            <img src="images/tentangkami.jpg" alt="Nawan Butik 2024">
            <p>
                Nawan Butik telah berdiri sejak tahun 2024, dengan komitmen untuk menyediakan produk fashion berkualitas tinggi yang memenuhi kebutuhan gaya wanita Indonesia. Sejak awal, kami fokus pada penggunaan bahan-bahan yang nyaman dan berkualitas, yang telah menarik perhatian banyak pelanggan setia dari berbagai kalangan.
            </p>
            <p>
                Kami terus berkembang dengan mengedepankan inovasi dalam setiap koleksi yang kami hadirkan, dari pakaian kasual hingga busana semi-formal yang elegan. Setiap produk kami dirancang dengan perhatian terhadap detail dan tren terkini, untuk memastikan pelanggan mendapatkan pengalaman berbelanja yang tidak hanya memuaskan, tetapi juga melebihi ekspektasi.
            </p>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2025 Nawan Butik | All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
