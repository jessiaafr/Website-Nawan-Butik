<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AkiNini Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f9fb; /* Light gray background */
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Navbar Styling */
        .navbar {
            background-color: #ffffff; /* White background */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .navbar-brand img {
            height: 30px;
            margin-right: 10px;
        }

        .navbar-nav {
            margin: 0 auto;
        }

        .navbar-nav .nav-link {
            color: #6c4f3d; /* Dark Brown */
            font-weight: bold;
        }

        .navbar-nav .nav-link:hover {
            color: #d79e6e; /* Lighter Brown */
            transition: color 0.3s ease;
        }

        /* Gallery Header */
        .gallery-header {
            text-align: center;
            margin: 40px 0;
            font-size: 30px;
            font-weight: bold;
            color: #6c4f3d; /* Dark brown */
            animation: fadeIn 1s ease-in-out;
        }

        /* Image Box Styling */
        .image-box {
            border: 1px solid #ddd;
            background-color: #ececec;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 200px;
            text-align: center;
            font-size: 16px;
            color: #333;
            padding: 10px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .image-box:hover {
            transform: scale(1.05);
        }

        .image-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .image-box img:hover {
            transform: scale(1.1);
        }

        /* Footer Styling */
        footer {
            background-color: #6c4f3d; /* Dark brown footer */
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            border-top: 2px solid #ddd;
        }

        /* Button Styling */
        .btn-success {
            background-color: #6c4f3d; /* Dark brown button */
            border: none;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .btn-success:hover {
            background-color: #d79e6e; /* Lighter brown on hover */
            transform: scale(1.05);
            transition: all 0.3s ease;
        }

        .btn-dark {
            background-color: #333; /* Dark button */
            border: none;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .btn-dark:hover {
            background-color: #555; /* Slightly lighter dark on hover */
            transform: scale(1.05);
            transition: all 0.3s ease;
        }

        /* Animation for fading in */
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="images/logo_catering.jpg" alt="Logo" height="30" class="me-2">
                AkiNini
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="produk.php">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="gallery.php">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Gallery Section -->
    <div class="container mt-5">
        <h3 class="gallery-header">Foto Menu Makan</h3>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="image-box">
                    <img src="images/paket_ayam1.jpg" alt="Food Image 1">
                </div>
            </div>
            <div class="col-md-4">
                <div class="image-box">
                    <img src="images/paket_ayam.jpg" alt="Food Image 2">
                </div>
            </div>
            <div class="col-md-4">
                <div class="image-box">
                    <img src="images/paketnasi.jpg" alt="Food Image 3">
                </div>
            </div>
            <div class="col-md-4">
                <div class="image-box">
                    <img src="images/paketikan.jpg" alt="Food Image 4">
                </div>
            </div>
            <div class="col-md-4">
                <div class="image-box">
                    <img src="images/gambar1.jpg" alt="Food Image 5">
                </div>
            </div>
            <div class="col-md-4">
                <div class="image-box">
                    <img src="images/gambar2.jpg" alt="Food Image 6">
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-4">
            <a href="produk.php" class="btn btn-success me-2">Pesan Sekarang</a>
           
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 AkiNini. All Rights Reserved. | Designed with Love</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
