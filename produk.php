<?php
session_start();
include('koneksi.php');

$is_logged_in = isset($_SESSION['pelanggan_id']);
$session_id = session_id();
$pelanggan_name = $is_logged_in ? $_SESSION['pelanggan_name'] : 'Guest';

// Mendapatkan query pencarian dari parameter GET
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination setup
$limit = 12;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total produk untuk pagination
$count_query = "SELECT COUNT(*) AS total FROM product";
if ($search_query) {
    $escaped_search = mysqli_real_escape_string($conn, $search_query);
    $count_query .= " WHERE product_name LIKE '%$escaped_search%'";
}
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_products = $count_row['total'];
$total_pages = ceil($total_products / $limit);

// Modifikasi query untuk menambahkan filter pencarian
$query = "SELECT * FROM product";
if ($search_query) {
    // Menambahkan kondisi pencarian jika ada
    $search_query = mysqli_real_escape_string($conn, $search_query); // Menghindari SQL injection
    $query .= " WHERE product_name LIKE '%$search_query%'";
}
$query .= " LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);
$products = mysqli_num_rows($result) > 0 ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
// Query kategori
$category_query = "SELECT * FROM category";
$category_result = mysqli_query($conn, $category_query);
$categories = mysqli_num_rows($category_result) > 0 ? mysqli_fetch_all($category_result, MYSQLI_ASSOC) : [];

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

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
    $redirect_page = 'produk.php?page=' . ($_POST['current_page'] ?? 1);
    if (!empty($_POST['current_search'])) {
        $redirect_page .= '&search=' . urlencode($_POST['current_search']);
    }
    header("Location: $redirect_page");
    exit;
}

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
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nawan Butik Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="images/logonawan.jpg" type="image/jpg">
    <style>
        body {
            background-color: #f7f9fb;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .navbar {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .navbar .navbar-brand img {
            width: 40px;
            margin-right: 10px;
        }

        .navbar-nav .nav-item {
            padding-left: 15px;
        }

        .card {
            background-color: #fff;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-body {
            padding: 20px;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            max-height: 250px;
            object-fit: cover;
        }

        .menu-category {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .menu-category h5 {
            font-size: 18px;
            font-weight: 600;
        }

        .menu-category ul {
            padding-left: 0;
            list-style: none;
        }

        .menu-category ul li {
            margin-bottom: 10px;
            font-size: 16px;
            color: #555;
        }

        .menu-category ul li:hover {
            color: #007bff;
        }

        .menu-category button,
        .menu-category a {
            width: 100%;
            font-size: 14px;
            margin-top: 15px;
        }

        footer {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 20px 0;
        }

        .btn-custom {
            background-color: #17a2b8;
            color: white;
            border-radius: 25px;
            padding: 10px;
        }

        .btn-custom:hover {
            background-color: #138496;
            transition: background-color 0.3s ease;
        }

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

        @media (max-width: 767px) {
            .navbar-nav {
                text-align: center;
            }

            .menu-category {
                margin-bottom: 30px;
            }

            .card-img-top {
                max-height: 200px;
            }
        }

        .menu-title {
            margin-bottom: 30px;
            /* Menambah jarak bawah pada judul "Menu" */
        }

        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            background-color: #f7f9fb;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        footer {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
            /* Membuat footer selalu berada di bawah */
        }

        #mainNavbar {
            transition: top 0.3s;
            z-index: 1030;
            /* pastikan navbar tetap di atas elemen lain */
        }

        body {
            padding-top: 80px;
            /* Disesuaikan dengan tinggi navbar */
        }

        .menu-category .list-group {
            max-height: 200px;
            overflow-y: hidden;
            transition: max-height 0.3s ease;
        }

        .menu-category.expanded .list-group {
            max-height: 300px;
            /* batas tampilan scroll */
            overflow-y: auto;
        }

        .menu-category .list-group::-webkit-scrollbar {
            width: 6px;
        }

        .menu-category .list-group::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 4px;
        }

        .show-more-btn {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
            margin-top: 10px;
            text-align: center;
            width: 100%;
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

        #suggestions {
            position: absolute;
            top: 100%;
            /* Menempatkan list suggestion di bawah input */
            left: 0;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            background-color: white;
            border: 1px solid #ddd;
            border-top: none;
            z-index: 1000;
            /* Pastikan list berada di atas elemen lain */
        }

        #suggestions .list-group-item {
            padding: 10px;
            cursor: pointer;
        }

        #suggestions .list-group-item:hover {
            background-color: #f8f9fa;
        }

        @media (min-width: 768px) {
            #floatingCartBtn {
                font-size: 18px;
                padding: 16px 28px;
            }
        }

        @media (min-width: 768px) {
            .toast-body {
                font-size: 1.1rem;
                /* memperbesar teks notifikasi */
                padding: 18px 24px;
            }

            .toast {
                min-width: 300px;
            }
        }

        .btn-primary {
            background-color: #28a745;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #218838;
        }

        /* Chatbot Button */
        .btn-chatbot {
            border: none;
            color: white;
            padding: 12px;
            font-size: 1.05rem;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(245, 127, 23, 0.5);
            transition: background 0.3s ease, box-shadow 0.3s ease;
            font-weight: 600;
            width: 100%;
        }

        .btn-chatbot:hover{
            background: linear-gradient(45deg, #f57f17, #fbc02d);
            box-shadow: 0 6px 14px rgba(251, 192, 45, 0.7);
        }
        /* Chat Container */
        .chat-container {
            height: 400px;
            display: flex;
            flex-direction: column;
        }

        .chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        /* Message Styling */
        .bot-message,
        .user-message {
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 18px;
            max-width: 80%;
            word-wrap: break-word;
        }

        .bot-message {
            background-color: #e9ecef;
            color: #212529;
            margin-right: auto;
            border-bottom-left-radius: 5px;
        }

        .user-message {
            background-color: #0d6efd;
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 5px;
        }

        /* Product Card in Chat */
        .chat-box .card-chatbot {
            transition: transform 0.2s;
        }

        .chat-box .card-chatbot:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .chat-box .card-img-top {
            height: 150px;
            object-fit: contain;
            padding: 10px;
            background-color: #f8f9fa;
        }

        .product-image {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            width: 100%;
            object-fit: contain;
        }

        /* Fallback untuk gambar error */
        .card-img-top[src*="placeholder.com"] {
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            color: #6c757d;
        }
        .card-header.bg-warning {
    background: linear-gradient(45deg, #f9a825, #f57f17);
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
}

.btn-warning {
    background: linear-gradient(45deg, #f9a825, #f57f17);
    border: none;
    transition: background 0.3s ease;
}

.btn-warning:hover {
    background: linear-gradient(45deg, #f57f17, #fbc02d);
    box-shadow: 0 4px 8px rgba(251, 192, 45, 0.6);
}

.chatbot-header {
    background: linear-gradient(135deg, #00c9ff, #a1f6a1, #92fe9d);
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
}
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav id="mainNavbar" class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="images/logonawan.jpg" alt="Logo" class="me-2" style="width: 20px; height: auto;">
                Nawan Butik
            </a>

            <!-- Kolom Pencarian di samping Logo -->
            <form class="d-flex ms-3" action="produk.php" method="GET" id="searchForm">
                <div class="position-relative">
                    <input class="form-control me-2" type="search" placeholder="Cari produk..." aria-label="Search" name="search" id="searchInput" autocomplete="off">
                    <div id="suggestions" class="list-group position-absolute z-3 w-100" style="max-height: 200px; overflow-y: auto;"></div>
                </div>
                <button class="btn btn-outline-success" type="submit">Cari</button>
            </form>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link" href="produk.php">Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Kontak</a></li>
                    <!-- Keranjang (DESKTOP ONLY) -->
        <li class="nav-item d-none d-lg-block">
          <a class="nav-link position-relative" href="cart.php">
            🛒 Keranjang
            <?php if (!empty($total_item) && $total_item > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo $total_item; ?>
              </span>
            <?php endif; ?>
          </a>
        </li>
                </ul>
            </div>
        </div>
    </nav>

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



    <!-- Main Content -->
    <div class="container mt-1">
        <div class="row">
            <div class="col-md-3">

                <!-- Tombol Floating Cart -->
                <?php if ($total_item > 0): ?>
                    <a href="cart.php" class="btn btn-danger" id="floatingCartBtn">
                        🛒 Lihat Keranjang (<?php echo $total_item; ?>)
                    </a>
                <?php endif; ?>


                <!-- Modal Chatbot -->
                <div class="modal fade" id="chatbotModal" tabindex="-1" aria-labelledby="chatbotModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header chatbot-header text-white">
                                <h5 class="modal-title" id="chatbotModalLabel">
                                    <i class="fas fa-robot me-2"></i>Chatbot Rekomendasi Produk
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="chat-container">
                                    <div class="chat-box" id="chatBox">
                                        <div class="bot-message">
                                        Halo, Selamat datang di Nawan Butik! Kami di sini untuk membantu kamu menemukan pakaian yang sesuai dengan gaya dan kebutuhanmu.<br> Cukup ketik deskripsi atau keinginanmu, misalnya:<br>
                                        - Dress untuk pesta malam<br>
                                        - Outfit casual buat hangout<br>
                                        - Pakaian formal yang stylish.<br>
                                        Silakan sampaikan gaya atau acara yang kamu inginkan, dan kami akan rekomendasikan produk terbaik untukmu!<br>
                                        </div>
                                    </div>
                                    <div class="input-group mt-3">
                                        <input type="text" class="form-control" id="userInput" placeholder="Ketik kebutuhanmu Anda...">
                                        <button class="btn btn-primary" id="sendBtn">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 shadow-sm mt-5" style="background: linear-gradient(135deg, #00c9ff, #92fe9d); border: none;">
    <div class="card-header text-white fw-bold" style="background: transparent; border-bottom: none;">
        <i class="fas fa-robot me-2"></i> Layanan Terbaik
    </div>
    <div class="card-body text-center">
        <button type="button" class="btn text-white w-100 shadow-lg" data-bs-toggle="modal" data-bs-target="#chatbotModal"
            style="
                background-color: rgba(255,255,255,0.15);
                border-radius: 12px;
                font-weight: 600;
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            "
            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.3)'"
            onmouseout="this.style.transform='none'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.25)'"
        >
            <i class="fas fa-comments me-2"></i> Chat Rekomendasi Produk
        </button>
    </div>
</div>
               
                <div class="menu-category p-3 bg-white shadow-sm rounded" id="categoryBox">
                    <h5 class="mb-3">Produk Kategori</h5>
                    <div class="list-group">
                        <?php foreach ($categories as $category): ?>
                            <a href="produk_category.php?id=<?php echo $category['category_id']; ?>" class="list-group-item list-group-item-action">
                                <?php echo $category['category_name']; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <button class="show-more-btn" id="toggleCategoryBtn">Lihat Semua Kategori</button>
                    <a href="https://wa.me/6281234567890" class="btn btn-success mt-3 w-100 d-flex align-items-center justify-content-center gap-2" target="_blank">
                        <i class='bx bxl-whatsapp bx-sm'></i>
                        <span>Pesan via WhatsApp</span>
                    </a>
                </div>
            </div>
            <div class="col-md-9">
                <h3 class="animate__animated animate__fadeIn menu-title">Produk</h3>
                <?php if (!empty($search_query)): ?>
                    <div class="mb-3">
                        <a href="produk.php" class="btn" style="background-color: #6c757d; color: #fff;">← Kembali ke Semua Produk</a>
                    </div>
                <?php endif; ?>

                <!-- Pesan jika tidak ada produk yang ditemukan -->
                <?php if (empty($products)): ?>
                    <p class="alert alert-info">Tidak ada produk yang cocok dengan pencarian Anda.</p>
                <?php endif; ?>
                <div class="row" id="productContainer">
                    <?php foreach ($products as $product): ?>
                        <div class="col-6 col-md-4 mb-4 animate__animated animate__fadeIn">
                            <div class="card">
                                <img src="uploads/<?php echo $product['product_image']; ?>" class="card-img-top" alt="<?php echo $product['product_name']; ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $product['product_name']; ?></h5>
                                    <?php if (!empty($product['product_discount_price']) && $product['product_discount_price'] > 0): ?>
                                        <p class="card-text">
                                            <span class="text-muted text-decoration-line-through">Rp <?php echo number_format($product['product_price'], 0, ',', '.'); ?></span><br>
                                            <strong class="text-danger">Rp <?php echo number_format($product['product_discount_price'], 0, ',', '.'); ?></strong>
                                        </p>
                                    <?php else: ?>
                                        <p class="card-text">
                                            <strong>Rp <?php echo number_format($product['product_price'], 0, ',', '.'); ?></strong>
                                        </p>
                                    <?php endif; ?>
                                    <p class="card-text">Stock: <?php echo $product['product_stock']; ?></p>

                                    <?php if ($product['product_stock'] > 0): ?>
                                        <form action="produk.php?page=<?php echo $page; ?><?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>" method="POST">
                                            <input type="hidden" name="current_page" value="<?php echo $page; ?>">
                                            <input type="hidden" name="current_search" value="<?php echo htmlspecialchars($search_query); ?>">
                                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['product_stock']; ?>" class="form-control mb-2">
                                            <button type="submit" name="add_to_cart" class="btn btn-primary btn-block w-100"><i class="bi bi-cart-plus"></i> Tambahkan ke Keranjang</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-danger w-100" disabled>Stok Habis</button>
                                    <?php endif; ?>

                                    <a href="detail_produk.php?id=<?php echo $product['product_id']; ?>" class="btn btn-info mt-2 w-100">Detail</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center mt-4">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Nawan Butik | All rights reserved.</p>
    </footer>

    <script src="script.js"></script>

    <script>
        const toastEl = document.getElementById('cartToast');
        if (toastEl) {
            const toast = new bootstrap.Toast(toastEl, {
                delay: 3000, // 3 detik
                autohide: true
            });
            toast.show();
        }
    </script>

    <script>
        // Simpan scroll position ke localStorage saat sebelum pindah halaman
        window.addEventListener("beforeunload", () => {
            localStorage.setItem("scrollY", window.scrollY);
        });

        // Setelah halaman dimuat, scroll kembali ke posisi sebelumnya
        window.addEventListener("load", () => {
            const scrollY = localStorage.getItem("scrollY");
            if (scrollY !== null) {
                window.scrollTo(0, parseInt(scrollY));
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const productItems = document.querySelectorAll('.product-item');

            searchInput.addEventListener('input', function() {
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
        });
    </script>
    <script>
        let lastScrollTop = 0;
        const navbar = document.getElementById("mainNavbar");

        window.addEventListener("scroll", function() {
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

            if (currentScroll > lastScrollTop) {
                // Scroll ke bawah → sembunyikan navbar
                navbar.style.top = "-100px";
            } else {
                // Scroll ke atas → tampilkan navbar
                navbar.style.top = "0";
            }
            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // mencegah scroll negatif
        });
    </script>
    <script>
        document.getElementById('toggleCategoryBtn').addEventListener('click', function() {
            const categoryBox = document.getElementById('categoryBox');
            categoryBox.classList.toggle('expanded');

            this.textContent = categoryBox.classList.contains('expanded') ?
                'Tutup Kategori' :
                'Lihat Semua Kategori';
        });
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const suggestionsBox = document.getElementById('suggestions');

            searchInput.addEventListener('input', function() {
                const query = this.value;

                if (query.length < 2) {
                    suggestionsBox.innerHTML = '';
                    suggestionsBox.style.display = 'none';
                    return;
                }

                fetch(`search_suggestion.php?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestionsBox.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                const suggestionItem = document.createElement('a');
                                suggestionItem.classList.add('list-group-item', 'list-group-item-action');
                                suggestionItem.href = `detail_produk.php?id=${item.product_id}`;
                                suggestionItem.textContent = item.product_name;
                                suggestionsBox.appendChild(suggestionItem);
                            });
                            suggestionsBox.style.display = 'block';
                        } else {
                            suggestionsBox.style.display = 'none';
                        }
                    });
            });

            // Sembunyikan suggestion saat klik di luar
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                    suggestionsBox.style.display = 'none';
                }
            });
        });
    </script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatBox = document.getElementById('chatBox');
        const userInput = document.getElementById('userInput');
        const sendBtn = document.getElementById('sendBtn');

        function scrollToBottom() {
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function appendMessage(sender, message) {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add(sender + '-message');
            messageDiv.innerHTML = message;
            chatBox.appendChild(messageDiv);
            scrollToBottom();
        }

        function appendProductCards(products) {
            const productsContainer = document.createElement('div');
            productsContainer.classList.add('row', 'g-2', 'mt-2');
            
            products.forEach(product => {
                const formattedPrice = product.formatted_price;
                const link = `detail_produk.php?id=${product.product_id}`;

                const cardHtml = `
                    <div class="col-12">
                        <div class="card card-chatbot shadow-sm">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center">
                                    <img src="${product.full_image_path}" class="product-image rounded me-3" style="width: 120px; height: 120px;" onerror="this.onerror=null;this.src='https://via.placeholder.com/80?text=No+Image';">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">${product.product_name}</h6>
                                        <p class="card-text small text-muted">${product.product_description.substring(0, 50)}...</p>
                                        <p class="card-text mb-1">${formattedPrice}</p>
                                        <p class="card-text text-muted small mb-1">${product.stock_status}</p>
                                        
                                        <form action="produk.php" method="POST">
                                            <input type="hidden" name="add_to_cart" value="1">
                                            <input type="hidden" name="product_id" value="${product.product_id}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-success btn-sm w-100 mt-1">
                                                <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                            </button>
                                        </form>

                                        <a href="${link}" class="btn btn-info btn-sm w-100 mt-1">
                                            Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                productsContainer.innerHTML += cardHtml;
            });
            
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('bot-message', 'p-2');
            messageDiv.appendChild(productsContainer);
            chatBox.appendChild(messageDiv);
            scrollToBottom();
        }

        function sendMessage() {
            const message = userInput.value.trim();
            if (message === '') return;

            appendMessage('user', message);
            userInput.value = '';

            const loadingMessage = '<div class="bot-message" id="loadingMessage"><i class="fas fa-spinner fa-spin me-2"></i>Mencari produk...</div>';
            chatBox.innerHTML += loadingMessage;
            scrollToBottom();

            $.ajax({
                url: 'chatbot.php',
                method: 'POST',
                data: { message: message },
                dataType: 'json',
                success: function(response) {
                    $('#loadingMessage').remove();
                    if (response.products && response.products.length > 0) {
                        appendMessage('bot', response.text);
                        appendProductCards(response.products);
                    } else {
                        appendMessage('bot', response.text);
                    }
                },
                error: function(xhr, status, error) {
                    $('#loadingMessage').remove();
                    appendMessage('bot', 'Maaf, terjadi kesalahan. Silakan coba lagi.');
                    console.error("Error:", error);
                }
            });
        }

        sendBtn.addEventListener('click', sendMessage);

        userInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendMessage();
            }
        });
    });
</script>
</body>

</html>