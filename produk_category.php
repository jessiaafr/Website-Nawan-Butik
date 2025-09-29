<?php
include('koneksi.php');
session_start();

$is_logged_in = isset($_SESSION['pelanggan_id']);
$session_id = session_id();
$pelanggan_name = 
// Hitung total item di keranjang
$cart_query = $is_logged_in ?
    "SELECT SUM(quantity) AS total FROM cart WHERE pelanggan_id = {$_SESSION['pelanggan_id']}" :
    "SELECT SUM(quantity) AS total FROM cart WHERE session_id = '$session_id'";

$cart_result = mysqli_query($conn, $cart_query);
$cart_data = mysqli_fetch_assoc($cart_result);
$cart_total = $cart_data['total'] ?? 0;$is_logged_in ? $_SESSION['pelanggan_name'] : 'Guest';

// Ambil category_id dari URL
$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil nama kategori
$category_result = mysqli_query($conn, "SELECT category_name FROM category WHERE category_id = $category_id");
$category_data = mysqli_fetch_assoc($category_result);
$category_name = $category_data ? $category_data['category_name'] : 'Kategori Tidak Ditemukan';

// Ambil produk berdasarkan kategori
$product_result = mysqli_query($conn, "SELECT * FROM product WHERE category_id = $category_id");
$products = mysqli_fetch_all($product_result, MYSQLI_ASSOC);

// Hitung total item dari cart (bukan dari $_SESSION['keranjang'])
$total_item = 0;

if ($is_logged_in) {
    $cart_query = "SELECT SUM(quantity) AS total FROM cart WHERE pelanggan_id = {$_SESSION['pelanggan_id']}";
} else {
    $cart_query = "SELECT SUM(quantity) AS total FROM cart WHERE session_id = '$session_id'";
}

$cart_result = mysqli_query($conn, $cart_query);
if ($cart_row = mysqli_fetch_assoc($cart_result)) {
    $total_item = $cart_row['total'] ?? 0;
}

// Proses add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = max(1, intval($_POST['quantity']));

    // Cek stok
    $stok_result = mysqli_query($conn, "SELECT product_stock FROM product WHERE product_id = $product_id");
    $stok_data = mysqli_fetch_assoc($stok_result);
    $stok_tersedia = $stok_data['product_stock'];

    if ($quantity > $stok_tersedia) {
        $_SESSION['cart_notification'] = "Jumlah melebihi stok tersedia!";
        header("Location: produk_category.php?id=$category_id");
        exit;
    }

    if ($is_logged_in) {
        $check_query = "SELECT * FROM cart WHERE pelanggan_id = {$_SESSION['pelanggan_id']} AND product_id = $product_id";
    } else {
        $check_query = "SELECT * FROM cart WHERE session_id = '$session_id' AND product_id = $product_id";
    }

    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $update_query = $is_logged_in ?
            "UPDATE cart SET quantity = quantity + $quantity WHERE pelanggan_id = {$_SESSION['pelanggan_id']} AND product_id = $product_id" :
            "UPDATE cart SET quantity = quantity + $quantity WHERE session_id = '$session_id' AND product_id = $product_id";
        mysqli_query($conn, $update_query);
    } else {
        $insert_query = $is_logged_in ?
            "INSERT INTO cart (pelanggan_id, product_id, quantity) VALUES ({$_SESSION['pelanggan_id']}, $product_id, $quantity)" :
            "INSERT INTO cart (session_id, product_id, quantity) VALUES ('$session_id', $product_id, $quantity)";
        mysqli_query($conn, $insert_query);
    }

    $_SESSION['cart_notification'] = "Pesanan berhasil ditambahkan ke keranjang!";
    header("Location: produk_category.php?id=$category_id");
    exit;
}

// Ambil semua kategori untuk sidebar
$category_all_result = mysqli_query($conn, "SELECT * FROM category");
$categories = mysqli_num_rows($category_all_result) > 0 ? mysqli_fetch_all($category_all_result, MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Kategori - Nawan Butik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/logonawan.jpg" type="image/jpg">
    <style>
        body {
            background-color: #f7f9fb;
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .card {
            border: none;
            transition: 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .menu-category {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
        }
        .btn-custom {
            background-color: #17a2b8;
            color: white;
            border-radius: 25px;
        }
        .btn-custom:hover {
            background-color: #138496;
        }
        footer {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 20px 0;
        }
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            z-index: 1000;
            display: none;
        }
        @media (max-width: 768px) {
    .card img {
        height: 150px;
        object-fit: cover;
    }

    .card .card-title {
        font-size: 1rem;
    }

    .card .card-text {
        font-size: 0.9rem;
    }

    .menu-category h5 {
        font-size: 1rem;
    }

    .navbar-brand img {
        width: 30px;
    }
}
@media (max-width: 768px) {
    .card {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .card img {
    max-height: 250px; /* Batasi tinggi gambar */
    width: 100%; /* Biarkan lebar gambar tetap responsif */
    object-fit: cover; /* Menjaga aspek rasio gambar */
}

    .btn {
        font-size: 0.9rem;
        padding: 8px 10px;
    }
}
.wrapper {
    flex: 1;
}
footer {
    background-color: #343a40;
    color: #fff;
    text-align: center;
    padding: 20px 0;
    margin-top: auto;
}

.fixed-bottom {
    z-index: 1050;
}
#floatingCartBtn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    border-radius: 50px;
    padding: 12px 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    z-index: 1050;
}
    </style>
</head>
<body>
<div class="wrapper d-flex flex-column min-vh-100">
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="images/logonawan.jpg" alt="Logo" width="40" class="me-2"> Nawan Butik
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

<!-- Kolom Pencarian Global -->
<div class="container mt-3">
    <input type="text" class="form-control" id="liveSearchInput" placeholder="Cari produk di kategori ini...">
</div>

<!-- Main Content -->
<div class="container-fluid mt-4 px-3">
    <!-- Tombol Floating Cart -->
    <?php if ($total_item > 0): ?>
<a href="cart.php" class="btn btn-danger" id="floatingCartBtn">
    🛒 Lihat Keranjang (<?php echo $total_item; ?>)
</a>
<?php endif; ?>

        <!-- Produk -->
        <div class="col-12">
            <h3>Kategori: <?= htmlspecialchars($category_name); ?></h3>
            <a href="produk.php" class="btn btn-secondary mb-3">← Kembali ke Semua Produk</a>
        </div>

<div class="mb-3">
            <div class="row id="productContainer">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-6 col-md-4 col-lg-2 mb-4 product-item" data-name="<?= strtolower($product['product_name']); ?>">
                            <div class="card">
                            <img src="uploads/<?= $product['product_image']; ?>" class="card-img-top img-fluid" alt="<?= htmlspecialchars($product['product_name']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($product['product_name']); ?></h5>
                                    <p class="card-text"><strong>Rp <?= number_format($product['product_price'], 0, ',', '.'); ?></strong></p>
                                    <p class="card-text">Stock: <?= $product['product_stock']; ?></p>

                                    <?php if ($product['product_stock'] > 0): ?>
                                        <form action="" method="POST">
                                            <input type="hidden" name="product_id" value="<?= $product['product_id']; ?>">
                                            <input type="number" name="quantity" value="1" min="1" max="<?= $product['product_stock']; ?>" class="form-control mb-2">
                                            <button type="submit" name="add_to_cart" class="btn btn-custom w-100">Add to Cart</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-danger w-100" disabled>Stok Habis</button>
                                    <?php endif; ?>

                                    <a href="detail_produk.php?id=<?= $product['product_id']; ?>" class="btn btn-info mt-2 w-100">Detail</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Produk tidak tersedia dalam kategori ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div> 
<!-- Footer -->
<footer>
    <p>&copy; 2025 Nawan Butik | All Rights Reserved</p>
</footer>

<!-- Notifikasi -->
<?php if (isset($_SESSION['cart_notification'])): ?>
    <div class="position-fixed" style="top: 20px; right: 20px; z-index: 1050;">
    <div id="cartToast" class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <?php echo $_SESSION['cart_notification']; ?>
            </div>
        </div>
    </div>
</div>
<?php unset($_SESSION['cart_notification']); ?>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const notification = document.querySelector('.notification');
    if (notification) {
        notification.style.display = 'block';
        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const liveSearchInput = document.getElementById('liveSearchInput');
    const productItems = document.querySelectorAll('.product-item');

    liveSearchInput.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();

        productItems.forEach(item => {
            const name = item.getAttribute('data-name');
            if (name.includes(keyword)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Menampilkan notifikasi jika ada
    const cartToast = document.querySelector('.toast');
    if (cartToast) {
        setTimeout(() => {
            cartToast.classList.remove('show');
        }, 3000); // Hapus class 'show' setelah 3 detik
    }
});

</script>


</body>
</html>
