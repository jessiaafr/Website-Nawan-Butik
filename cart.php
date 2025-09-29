<?php
session_start();
include 'koneksi.php';

$session_id = session_id();

if (isset($_POST['update_quantity'])) {
    $cart_id = $_POST['cart_id'];
    $new_quantity = max(1, (int)$_POST['quantity']);

    // Ambil product_id dari cart
    $cart_result = mysqli_query($conn, "SELECT product_id FROM cart WHERE cart_id = $cart_id AND session_id = '$session_id'");
    if ($cart_row = mysqli_fetch_assoc($cart_result)) {
        $product_id = $cart_row['product_id'];

        // Ambil stok produk dari tabel product
        $stock_result = mysqli_query($conn, "SELECT product_stock FROM product WHERE product_id = $product_id");
        if ($stock_row = mysqli_fetch_assoc($stock_result)) {
            $available_stock = $stock_row['product_stock'];

            // Batasi quantity tidak melebihi stok
            $final_quantity = min($new_quantity, $available_stock);

            // Update ke database
            mysqli_query($conn, "UPDATE cart SET quantity = $final_quantity WHERE cart_id = $cart_id AND session_id = '$session_id'");
        }
    }
}

// Handle REMOVE item
if (isset($_POST['remove_item'])) {
    $cart_id = $_POST['cart_id'];
    mysqli_query($conn, "DELETE FROM cart WHERE cart_id = $cart_id AND session_id = '$session_id'");
}

// Ambil data cart
$query = "SELECT c.*, p.product_name, p.product_price, p.product_discount_price, p.product_image 
          FROM cart c 
          JOIN product p ON c.product_id = p.product_id 
          WHERE c.session_id = '$session_id'";
$result = mysqli_query($conn, $query);

// Hitung total & kumpulkan item
$total_price = 0;
$cart_items = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $price = ($row['product_discount_price'] > 0) ? $row['product_discount_price'] : $row['product_price'];
        $row['effective_price'] = $price;
        $row['total_item_price'] = $price * $row['quantity'];
        $total_price += $row['total_item_price'];        
        $cart_items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AkiNini Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="icon" href="images/logonawan.jpg" type="image/jpg">
    <style>
        body { background-color: #f7f9fb; font-family: 'Arial', sans-serif; margin: 0; padding: 0; }
        .navbar { box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); background-color: #fff; }
        .navbar .navbar-brand img { width: 40px; margin-right: 10px; }
        .navbar-nav .nav-item { padding-left: 15px; }
        .card { border: none; transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card:hover { transform: translateY(-10px); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .card-img-top { max-height: 250px; object-fit: cover; }
        .menu-category { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .menu-category h5 { font-size: 18px; font-weight: 600; }
        .menu-category ul { padding-left: 0; list-style: none; }
        .menu-category ul li { margin-bottom: 10px; font-size: 16px; color: #555; }
        .menu-category ul li:hover { color: #007bff; }
        .menu-category button, .menu-category a { width: 100%; font-size: 14px; margin-top: 15px; }
        footer { background-color: #343a40; color: #fff; text-align: center; padding: 20px 0; }
        .btn-custom { background-color: #17a2b8; color: white; border-radius: 25px; padding: 10px; }
        .btn-custom:hover { background-color: #138496; transition: background-color 0.3s ease; }
        .notification { position: fixed; top: 20px; right: 20px; background-color: #28a745; color: white; padding: 10px 20px; border-radius: 5px; display: none; z-index: 1000; }
        @media (max-width: 767px) {
            .navbar-nav { text-align: center; }
            .menu-category { margin-bottom: 30px; }
            .card-img-top { max-height: 200px; }
        }
        .quantity-input {
    max-width: 45px;
    min-width: 35px;
    text-align: center;
    font-size: 0.8rem;
    padding: 2px 4px;
}


        .quantity-form {
            display: flex;
            align-items: center;
            gap: 5px;
            justify-content: center;
            margin: 0;
        }
        /* Hilangkan spinner di input number (Chrome, Edge, Safari) */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Hilangkan spinner di Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
        @media (max-width: 768px) {
    table thead {
        display: none;
    }

    table tbody tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 10px;
        background: #fff;
    }

    table tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 10px;
        border: none;
    }

    table tbody td::before {
        content: attr(data-label);
        font-weight: bold;
        flex: 1;
        color: #555;
    }

    .quantity-form {
        flex-direction: row;
        justify-content: flex-end;
    }

    .btn-icon {
        background: none;
        border: none;
        color: #dc3545;
    }
    
}
@media (max-width: 800px) {
    .product-name {
    display: inline-block;
    max-width: 160px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: bold;
}
}
.original-price {
    text-decoration: line-through;
    color: #888;
    display: block;
    font-size: 0.85rem;
}
.discount-price {
    color: #dc3545;
    font-weight: bold;
    display: block;
    font-size: 1rem;
}

@media (min-width: 769px) {
    .original-price {
        display: inline;
        margin-right: 5px;
    }
    .discount-price {
        display: inline;
    }
}

    </style>
</head>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="images/logonawan.jpg" alt="Logo" class="me-2">
            Nawan Butik
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">Tentang Kami</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Kontak</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center mb-4">Keranjang Belanja Anda</h2>

    <?php if (!empty($cart_items)): ?>
        <form method="post">
        <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>No</th>
                <th>Produk</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
                <th>Total Harga</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
<?php foreach ($cart_items as $index => $item): ?>
    <tr>
        <td data-label="No"><?= $index + 1 ?></td>
        <td data-label="Produk">
    <div class="product-name"><?= htmlspecialchars($item['product_name']) ?></div>
        </td>
        <td data-label="Harga Satuan" class="price-cell">
            <?php if ($item['product_discount_price'] > 0): ?>
                <div class="price-wrapper">
                    <span class="original-price">Rp<?= number_format($item['product_price'], 0, ',', '.') ?></span>
                    <span class="discount-price">Rp<?= number_format($item['product_discount_price'], 0, ',', '.') ?></span>
                </div>
            <?php else: ?>
                Rp<?= number_format($item['product_price'], 0, ',', '.') ?>
            <?php endif; ?>
        </td>

        <td data-label="Jumlah" class="<?= $index === 0 ? 'first-row-quantity' : '' ?>">
        <div class="quantity-wrapper <?= $index === 0 ? 'first-row-center' : '' ?>">
            <form method="post" class="quantity-form">
                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                <div class="input-group input-group-sm flex-nowrap">
                    <button type="submit" name="update_quantity" value="1" class="btn btn-outline-secondary"
                        onclick="this.form.quantity.value=Math.max(1,parseInt(this.form.quantity.value)-1)">−</button>
                        <input type="number" name="quantity" class="form-control text-center quantity-input" value="<?= $item['quantity'] ?>" min="1" readonly>
                    <button type="submit" name="update_quantity" value="1" class="btn btn-outline-secondary"
                        onclick="this.form.quantity.value=parseInt(this.form.quantity.value)+1">+</button>
                </div>
            </form>
        </div>
        </td>
        <td data-label="Total Harga">Rp<?= number_format($item['total_item_price'], 0, ',', '.') ?></td>
        <td data-label="Hapus">
            <form method="post">
                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                <button type="submit" name="remove_item" class="btn btn-sm btn-outline-danger" title="Hapus item">
    <i class="fas fa-trash-alt"></i>
</button>
            </form>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>

        </table>
            </div>
        </form>

        <div class="row text-center mt-2 pt-1 mb-4">
    <div class="col-12 mb-3">
        <h4 class="mb-0">Total: Rp<?= number_format($total_price, 0, ',', '.') ?></h4>
    </div>
    <div class="col-12 col-md-6 mb-2">
        <a href="produk.php" class="btn btn-secondary w-100"><i class="fas fa-arrow-left"></i> Kembali ke Menu</a>
    </div>
    <div class="col-12 col-md-6">
        <a href="checkout.php" class="btn btn-success w-100">Checkout</a>
    </div>
</div>
    <?php else: ?>
        <div class="alert alert-warning text-center" role="alert">
            Keranjang belanja Anda kosong.
        </div>
        <div class="text-center mt-3">
            <a href="produk.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Menu
            </a>
        </div>
    <?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Simpan posisi scroll sebelum submit
    document.querySelectorAll("form").forEach(form => {
        form.addEventListener("submit", function() {
            localStorage.setItem("scrollPosition", window.scrollY);
        });
    });

    // Setelah reload, kembalikan posisi scroll
    window.addEventListener("load", function() {
        const scrollY = localStorage.getItem("scrollPosition");
        if (scrollY !== null) {
            window.scrollTo(0, parseInt(scrollY));
            localStorage.removeItem("scrollPosition");
        }
    });
</script>

</body>
</html>
