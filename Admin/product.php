<?php
session_start();

// Include the database connection
include '../koneksi.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Fetch products with their categories
$search = mysqli_real_escape_string($conn, $_GET['search']??'');
$query = "
    SELECT p.product_id, p.product_name, p.product_price, p.product_description, 
           p.product_image, p.product_stock, c.category_name
    FROM product p
    LEFT JOIN category c ON p.category_id = c.category_id
    " . ($search ? " WHERE p.product_name LIKE '%$search%'":"")."
";
$result = mysqli_query($conn, $query);
$total_products = mysqli_num_rows($result);

// Check if there is a message to display
$message = '';
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="../images/logonawan.jpg" type="image/jpg"> 
    <style>
        body {
            background: linear-gradient(to right, #f8f3eb, #efe4d8);
            font-family: 'Poppins', sans-serif;
            padding-top: 70px;
        }

        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 999;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 1rem;
        }

        .navbar-brand img {
            max-width: 25px;
        }

        .nav-link {
            color: #6f4e37;
            font-weight: 600;
        }

        .nav-link:hover {
            color: #543626;
        }

        .container {
            margin-top: 90px;
        }

        /* Add Product Button */
        .btn-add {
            background-color: #ff6347; /* Tomato color */
            color: white;
            font-weight: bold;
            border-radius: 30px;
            padding: 10px 20px;
            transition: all 0.3s ease;
            position: absolute;
            top: 90px; /* Adjusted to avoid overlapping with navbar */
            right: 20px;
        }

        .btn-add:hover {
            background-color: #e53e32;
            transform: scale(1.1);
        }

        .btn-custom {
            background-color: #3498db;
            color: white;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }

        .product-card {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            background: #fff7f0;
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .product-img {
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .product-img:hover {
            transform: scale(1.1);
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }

        /* Adjust navbar for smaller screens */
        @media (max-width: 768px) {
            .navbar-nav {
                text-align: center;
            }

            .btn-custom {
                width: 100%;
                padding: 15px;
            }

            .product-img {
                max-height: 150px;
            }
        }

    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="../images/logonawan.jpg" alt="Nawan Logo" style="width: 20px; height: auto; margin-right: 10px; vertical-align: middle;"> Nawan Butik
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="kategori.php">Kategori</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Keluar</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="px-3 mt-3">
    <a href="index.php" class="btn btn-back" style="background: #6f4e37; color: white; font-weight: bold; border-radius: 30px; padding: 10px 20px;">
        <i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama
    </a>
</div>

    <!-- Add Product Button -->
    <div class="btn-add-container">
        <a href="add_product.php" class="btn btn-add">
            <i class="fas fa-plus-circle"></i> Tambah Produk
        </a>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h1 class="text-center my-4">Kelola Produk</h1>
        <p class="text-center fw-semibold">Total Produk: <?php echo $total_products;?></p>
<!-- Search Form -->
            <form method="GET" class="mb-4 d-flex justify-content-center">
                <input type="text" name="search" class="form-control w-50" placeholder="Cari nama produk..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button type="submit" class="btn btn-primary ms-2">Cari</button>
            </form>
        <!-- Product List -->
        <div class="row g-3">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="product-card">
                        <img src="../uploads/<?php echo htmlspecialchars($row['product_image']); ?>" class="product-img w-100 mb-3" alt="Gambar Produk">
                        <h5 class="text-center"><?php echo htmlspecialchars($row['product_name']); ?></h5>
                        <p class="text-muted text-center"><?php echo htmlspecialchars($row['category_name']); ?></p>
                        <p class="text-muted text-center">Stock :<?php echo htmlspecialchars($row['product_stock']); ?></p>
                        <p class="text-center">Rp <?php echo number_format($row['product_price'], 0, ',', '.'); ?></p>
                        <div class="text-center">
                            <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-danger btn-sm">Hapus</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Toast Notification -->
    <?php if ($message): ?>
        <div class="toast-container">
            <div class="toast align-items-center text-white bg-info border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss Toast after 3 seconds
        setTimeout(function () {
            var toast = document.querySelector('.toast');
            if (toast) {
                var bootstrapToast = new bootstrap.Toast(toast);
                bootstrapToast.hide();
            }
        }, 3000);
    </script>
</body>
</html>
