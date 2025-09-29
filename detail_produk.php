<?php
include('koneksi.php');
session_start();

// Cek login status dan identitas pengguna
$is_logged_in = isset($_SESSION['pelanggan_id']);
$pelanggan_id = $is_logged_in ? $_SESSION['pelanggan_id'] : null;
$session_id = session_id(); // untuk guest

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Ambil stok terbaru dari database
    $stock_query = "SELECT product_stock FROM product WHERE product_id = $product_id";
    $stock_result = mysqli_query($conn, $stock_query);
    $stock_data = mysqli_fetch_assoc($stock_result);
    $current_stock = $stock_data['product_stock'];

    if ($current_stock == 0) {
        $_SESSION['cart_notification'] = "Maaf, produk ini sedang habis!";
    } elseif ($quantity > $current_stock) {
        $_SESSION['cart_notification'] = "Stok tidak mencukupi. Maksimum bisa pesan: $current_stock.";
    } else {
        // Cek apakah produk sudah ada di keranjang
        if ($is_logged_in) {
            $check_query = "SELECT * FROM cart WHERE pelanggan_id = $pelanggan_id AND product_id = $product_id";
        } else {
            $check_query = "SELECT * FROM cart WHERE session_id = '$session_id' AND product_id = $product_id";
        }

        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            // Update quantity jika sudah ada di keranjang
            if ($is_logged_in) {
                $update_query = "UPDATE cart SET quantity = quantity + $quantity WHERE pelanggan_id = $pelanggan_id AND product_id = $product_id";
            } else {
                $update_query = "UPDATE cart SET quantity = quantity + $quantity WHERE session_id = '$session_id' AND product_id = $product_id";
            }
            mysqli_query($conn, $update_query);
        } else {
            // Tambahkan ke keranjang
            if ($is_logged_in) {
                $insert_query = "INSERT INTO cart (pelanggan_id, product_id, quantity) VALUES ($pelanggan_id, $product_id, $quantity)";
            } else {
                $insert_query = "INSERT INTO cart (session_id, product_id, quantity) VALUES ('$session_id', $product_id, $quantity)";
            }
            mysqli_query($conn, $insert_query);
        }

        $_SESSION['cart_notification'] = "Produk berhasil ditambahkan ke keranjang!";
    }

    // Redirect kembali ke halaman menu (menyertakan parameter halaman dan pencarian jika ada)
    $redirect_page = 'detail_produk.php?id=' . ($_POST['product_id'] ?? 1);
    if (!empty($_POST['current_search'])) {
        $redirect_page .= '&search=' . urlencode($_POST['current_search']);
    }
    header("Location: $redirect_page");
    exit;
}

// Ambil detail produk jika diperlukan
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = "SELECT * FROM product WHERE product_id = $product_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $product = mysqli_fetch_assoc($result);
} else {
    die("Produk tidak ditemukan");
}

if ($is_logged_in) {
    $pelanggan_id = $_SESSION['pelanggan_id'];
    $query = "SELECT SUM(quantity) AS total_items FROM cart WHERE pelanggan_id = $pelanggan_id";
} else {
    $query = "SELECT SUM(quantity) AS total_items FROM cart WHERE session_id = '$session_id'";
}

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
$total_items = $data['total_items'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - Nawan Butik</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="images/logonawan.jpg" type="image/jpg">
    <style>
        body {
            background-color: #f7f9fb;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }
        /* Navbar */
        .navbar {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand img {
            width: 40px;
            margin-right: 10px;
        }
        /* Product Detail Section */
        .product-detail {
            margin-top: 40px;
        }
        .product-detail img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .product-detail img:hover {
            transform: scale(1.05);
        }
        .product-description {
            font-size: 1.2rem;
            color: #555;
        }
        .product-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
        }
        .btn-primary {
            background-color: #28a745;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        /* Pesan Sekarang Button */
        .btn-checkout {
            background-color: #ff5733;
            color: white;
            border: none;
            transition: background-color 0.3s;
            font-size: 1.1rem;
        }
        .btn-checkout:hover {
            background-color: #c13c20;
        }
        /* Notification */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: none;
            z-index: 1000;
        }
        /* Responsive Design */
        @media (max-width: 767px) {
            .product-detail .row {
                flex-direction: column;
                align-items: center;
            }
            .product-detail img {
                width: 80%;
            }
        }
        /* Footer */
        footer {
            background-color: #007bff;
            color: white;
            padding: 30px 0;
        }
        footer i {
            color: #ff5733;
        }
        .card {
    border-radius: 12px;
    border: none;
}
.card-title {
    font-weight: bold;
    color: #333;
}
.product-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #00695c; /* warna hijau kebiruan untuk kesan kesehatan */
    letter-spacing: 0.5px;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #b2dfdb;
    font-family: 'Segoe UI', 'Helvetica Neue', sans-serif;
}
@media (max-width: 767px) {
    .product-detail img {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 80%;
    }
}
.product-detail .card {
    height: 100%;
}
@media (max-width: 767px) {
    .product-detail .col-md-6:first-child {
        margin-bottom: 20px;
    }
}

.card-body p.card-text {
    font-size: 1rem;
    line-height: 1.8;
    color: #444;
    text-align: justify;
    margin-bottom: 1rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.mobile-cart-btn {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    border-radius: 0;
    z-index: 1050;
    font-size: 1.1rem;
}
#floatingCartBtn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1050;
    padding: 16px 28px;
    font-size: 18px;
    border-radius: 50px;
    background-color: #dc3545; /* merah gelap */
    color: white;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    width: auto;
    display: inline-block;
    white-space: nowrap;
}

/* Media query untuk layar minimal 768px */
@media (min-width: 768px) {
    #floatingCartBtn {
        font-size: 18px;   /* ini sama dengan default, bisa dihilangkan */
        padding: 16px 28px; /* ini juga sama, jadi optional */
    }
}
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="images/logonawan.jpg" alt="Logo" class="me-2" style="width: 25px; height: auto;">
                Nawan Butik
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link" href="produk.php">Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Kontak</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Product Details -->
    <div class="container product-detail">
    <div class="row">
        <!-- KIRI: Nama Produk + Gambar & Form -->
        <div class="col-md-6 animate__animated animate__fadeInLeft">
    <div class="card shadow-sm h-100">
        <div class="card-body text-center">
            <h2 class="product-title"><?php echo $product['product_name']; ?></h2>
            <img src="uploads/<?php echo $product['product_image']; ?>" 
                alt="<?php echo $product['product_name']; ?>" 
                class="img-fluid mb-3 d-block mx-auto">

            <form action="detail_produk.php?id=<?php echo $product['product_id']; ?>" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                <?php if (!empty($product['product_discount_price']) && $product['product_discount_price'] > 0 && $product['product_discount_price'] < $product['product_price']): ?>
                    <p class="product-price">
                        <span class="text-muted text-decoration-line-through">Rp <?php echo number_format($product['product_price'], 0, ',', '.'); ?></span><br>
                        <strong class="text-danger">Rp <?php echo number_format($product['product_discount_price'], 0, ',', '.'); ?></strong>
                    </p>
                <?php else: ?>
                    <p class="product-price">Rp <?php echo number_format($product['product_price'], 0, ',', '.'); ?></p>
                <?php endif; ?>

                <p class="product-description">Stock : <?php echo $product['product_stock']; ?></p>
                <div class="mb-3 text-start">
                    <label for="quantity" class="form-label">Jumlah</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $product['product_stock']; ?>" class="form-control" <?php echo ($product['product_stock'] == 0) ? 'disabled' : ''; ?>>
                </div>

                <?php if ($product['product_stock'] > 0): ?>
                    <button type="submit" name="add_to_cart" class="btn btn-primary btn-block w-100 mb-2">
                        <i class="bi bi-cart-plus"></i> Tambahkan ke Keranjang
                    </button>
                    <a href="produk.php" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left"></i> Kembali ke Produk
                    </a>
                <?php else: ?>
                    <button class="btn w-100 text-white mb-2" style="background-color: #dc3545; cursor: not-allowed;" disabled>Stock Habis</button>
<a href="produk.php" class="btn btn-outline-secondary w-100">
    <i class="bi bi-arrow-left"></i> Kembali ke Menu
</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

        <!-- KANAN: Deskripsi Produk dalam Card -->
        <div class="col-md-6 animate__animated animate__fadeInRight">
    <div class="card shadow-sm h-100">
        <div class="card-body">
            <p class="card-text"><?php echo nl2br($product['product_description']); ?></p>
            <?php if (!empty($product['product_detail'])): ?>
                <hr>
                <p class="card-text"><?php echo nl2br($product['product_detail']); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Pastikan $total_item sudah didefinisikan dan bernilai integer
$total_item = 5; // contoh nilai, bisa ambil dari database atau session

?>

<!-- Tombol Floating Cart -->
<?php if ($total_item > 0): ?>
    <a href="cart.php" class="btn btn-danger" id="floatingCartBtn"> 
        🛒 Lihat Keranjang (<?php echo $total_item; ?>)
    </a>
<?php endif; ?>

    <!-- Notifikasi -->
<?php if (isset($_SESSION['cart_notification'])): ?>
    <div class="position-fixed w-100 d-flex justify-content-center" style="top: 20px; z-index: 1050;">
    <div id="cartToast" class="toast align-items-center text-bg-success border-0 fade" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <?php echo $_SESSION['cart_notification']; ?>
            </div>
        </div>
    </div>
</div>

<?php unset($_SESSION['cart_notification']); ?>
<?php endif; ?>


    <!-- Bootstrap JS and Font Awesome Icons -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

    <script>
    // Auto-hide toast setelah 3 detik
    const toastEl = document.getElementById('cartToast');
    if (toastEl) {
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
    }
</script>

</body>
</html>