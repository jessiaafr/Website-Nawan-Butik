<?php
session_start();
include('koneksi.php');

$guest_session_id = session_id();
$is_logged_in = isset($_SESSION['pelanggan_id']);
$session_id = session_id();

$cart_query = $is_logged_in
    ? "SELECT * FROM cart WHERE pelanggan_id = {$_SESSION['pelanggan_id']}"
    : "SELECT * FROM cart WHERE session_id = '$session_id'";

$query = "SELECT c.*, p.product_name, p.product_price, p.product_discount_price, p.product_image, p.product_stock 
    FROM cart c 
    JOIN product p ON c.product_id = p.product_id 
    WHERE c.session_id = '$guest_session_id'";
$result = mysqli_query($conn, $query);

$total_price = 0;
$cart_items = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $effective_price = $row['product_discount_price'] && $row['product_discount_price'] < $row['product_price']
            ? $row['product_discount_price']
            : $row['product_price'];
    
        $row['effective_price'] = $effective_price;
        $row['total_item_price'] = $effective_price * $row['quantity'];
        $total_price += $row['total_item_price'];
        $cart_items[] = $row;
    }    
}

if (isset($_POST['checkout'])) {
    $payment_method = $_POST['payment_method'];

    // Validasi stok
    $stock_ok = true;
    foreach ($cart_items as $item) {
        if ($item['product_stock'] < $item['quantity']) {
            $stock_ok = false;
            echo "Stok produk <b>{$item['product_name']}</b> tidak mencukupi!<br>";
        }
    }

    if (!$stock_ok) exit();

    // Data pelanggan
    $pelanggan_name     = mysqli_real_escape_string($conn, $_POST['pelanggan_name']);
    $pelanggan_address  = mysqli_real_escape_string($conn, $_POST['pelanggan_address']);
    $pelanggan_kota     = mysqli_real_escape_string($conn, $_POST['pelanggan_kota']);
    $pelanggan_kodepos  = mysqli_real_escape_string($conn, $_POST['pelanggan_kodepos']);
    $pelanggan_nohp     = mysqli_real_escape_string($conn, $_POST['pelanggan_nohp']);
    $payment_method     = mysqli_real_escape_string($conn, $_POST['payment_method']);

    $payment_proof_filename = "";

    if ($payment_method === 'Bank Transfer') {
        // Validasi dan upload bukti pembayaran
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
            $filename = time() . '_' . basename($_FILES["payment_proof"]["name"]);
            $target_dir = "uploads/";
            $target_file = $target_dir . $filename;

            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($imageFileType, $valid_extensions)) {
                echo "Format file tidak valid.";
                exit();
            }

            if ($_FILES["payment_proof"]["size"] > 5000000) {
                echo "Ukuran file terlalu besar.";
                exit();
            }

            if (move_uploaded_file($_FILES["payment_proof"]["tmp_name"], $target_file)) {
                $payment_proof_filename = $filename;
            } else {
                echo "Upload bukti pembayaran gagal!";
                exit();
            }
        } else {
            echo "Bukti pembayaran tidak ditemukan!";
            exit();
        }
    }

    // Insert pesanan
    $pelanggan_id = $is_logged_in ? $_SESSION['pelanggan_id'] : "NULL";
    $insert_order = "INSERT INTO pesanan (
        pelanggan_id, pelanggan_name, pelanggan_address, pelanggan_kota, pelanggan_kodepos, pelanggan_nohp, total_price, status_pesanan,
        order_date, payment_proof, payment_method
    ) VALUES (
        $pelanggan_id, '$pelanggan_name', '$pelanggan_address', '$pelanggan_kota', '$pelanggan_kodepos', '$pelanggan_nohp',
        $total_price, 'Pending', NOW(), '$payment_proof_filename', '$payment_method'
    )";

    if (mysqli_query($conn, $insert_order)) {
        $order_id = mysqli_insert_id($conn);

        foreach ($cart_items as $item) {
            $product_id = $item['product_id'];
            $quantity   = $item['quantity'];
            $price      = $item['total_item_price'];

            mysqli_query($conn, "INSERT INTO detail_pesanan (pesanan_id, product_id, quantity, price)
                                 VALUES ($order_id, $product_id, $quantity, $price)");

            mysqli_query($conn, "UPDATE product SET product_stock = product_stock - $quantity 
                                 WHERE product_id = $product_id");
        }

        mysqli_query($conn, "DELETE FROM cart WHERE session_id = '$guest_session_id'");

        header("Location: thank_you.php?order_id=$order_id");
        exit();
    } else {
        echo "Gagal menyimpan pesanan: " . mysqli_error($conn);
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - AkiNini</title>
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

        .cart-item {
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            transition: transform 0.3s ease;
        }

        .cart-item:hover {
            transform: scale(1.05);
        }

        .cart-item img {
            width: 100px;
            height: auto;
            border-radius: 5px;
        }

        .cart-item .item-details {
            margin-left: 15px;
        }

        .total-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: #333;
        }

        .btn-custom {
            background-color: #28a745;
            color: white;
            transition: background-color 0.3s ease, transform 0.2s ease;
            border-radius: 8px;
        }

        .btn-custom:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        footer {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 20px 0;
        }

        .navbar-brand img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .navbar-nav .nav-link {
            font-size: 1.1rem;
            padding: 8px 15px;
            transition: background-color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .bank-info {
            margin-top: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .bank-info img {
            width: 100px;
            height: auto;
        }

        /* Responsive Design */
        @media (max-width: 767px) {
            .cart-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .cart-item img {
                width: 80px;
            }

            .navbar-nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .bank-info {
                padding: 15px;
            }
        }
        .text-decoration-line-through {
    text-decoration: line-through;
}

    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="images/logonawan.jpg" alt="Logo">
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

<!-- Checkout Form -->
<div class="container mt-5">
    <h3 class="text-center mb-4">Checkout</h3>
    <?php if (count($cart_items) > 0): ?>
        <form id="checkoutForm" action="checkout.php" method="POST" enctype="multipart/form-data">
    <div class="row">

        <!-- Data Pelanggan -->
        <div class="col-lg-8 col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Data Pelanggan</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="pelanggan_name">Nama Lengkap</label>
                        <input type="text" class="form-control" name="pelanggan_name" id="pelanggan_name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="pelanggan_address">Alamat</label>
                        <input type="text" class="form-control" name="pelanggan_address" id="pelanggan_address" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="pelanggan_kota">Kota</label>
                        <input type="text" class="form-control" name="pelanggan_kota" id="pelanggan_kota" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="pelanggan_kodepos">Kode Pos</label>
                        <input type="text" class="form-control" name="pelanggan_kodepos" id="pelanggan_kodepos" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="pelanggan_nohp">No. HP</label>
                        <input type="text" class="form-control" name="pelanggan_nohp" required>
                    </div>
                </div>
            </div>

            <!-- Pesanan Anda -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Pesanan Anda</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item d-flex align-items-center">
                            <img src="uploads/<?php echo $item['product_image']; ?>" alt="<?php echo $item['product_name']; ?>">
                            <div class="item-details">
                                <h5><?php echo $item['product_name']; ?></h5>
                                <p>Jumlah: <?php echo $item['quantity']; ?></p>
                                <p>Harga Satuan: 
                                    <?php if ($item['product_discount_price'] && $item['product_discount_price'] < $item['product_price']): ?>
                                        <span class="text-muted text-decoration-line-through">Rp <?php echo number_format($item['product_price'], 0, ',', '.'); ?></span>
                                        <span class="text-danger">Rp <?php echo number_format($item['product_discount_price'], 0, ',', '.'); ?></span>
                                    <?php else: ?>
                                        Rp <?php echo number_format($item['product_price'], 0, ',', '.'); ?>
                                    <?php endif; ?>
                                </p>
                                <p>Total: Rp <?php echo number_format($item['total_item_price'], 0, ',', '.'); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Metode Pembayaran -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Metode Pembayaran</h5>
                </div>
                <div class="card-body">
                    <p class="total-price">Total: <strong>Rp <?php echo number_format($total_price, 0, ',', '.'); ?></strong></p>

                    <div class="mb-2">
                        <input type="radio" name="payment_method" value="COD" onchange="togglePaymentProof()" required>
                        COD (Cash on Delivery)<br>
                        <input type="radio" name="payment_method" value="Bank Transfer" onchange="togglePaymentProof()">
                        Bank Transfer
                    </div>

                    <div id="paymentProofField" style="display: none;">
                        <label for="payment_proof">Upload Bukti Pembayaran</label><br>
                        <small id="paymentProofError" class="text-danger d-none">* Mohon upload bukti transfer.</small>
                        <input type="file" name="payment_proof" class="form-control mb-1" id="payment_proof">                       
                    </div>

                    <button type="submit" name="checkout" class="btn btn-custom w-100">
                        Buat Pesanan <i class="fas fa-credit-card"></i>
                    </button>
                    <a href="cart.php" class="btn btn-secondary mt-3 w-100">Kembali ke Keranjang</a>
                </div>
            </div>
        </div>

    </div>
</form>

    <?php else: ?>
        <p>Keranjang Anda kosong. Silakan tambahkan produk ke keranjang terlebih dahulu.</p>
    <?php endif; ?>

    <?php
// Koneksi ke database
include('koneksi.php');

// Ambil data metode pembayaran
$query = "SELECT * FROM metode_pembayaran";
$result = mysqli_query($conn, $query);
?>

<!-- Informasi Rekening Admin -->
<div class="bank-info container mt-5">
    <h4 class="mb-4">Informasi Rekening Admin</h4>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="card mb-3 p-3 shadow-sm">
                <div class="row g-3 align-items-center">
                    <div class="col-md-2">
                        <img src="uploads/<?php echo htmlspecialchars($row['payment_logo']); ?>" alt="Logo <?php echo $row['payment_method']; ?>" width="80">
                    </div>
                    <div class="col-md-10">
                        <p><strong>Bank / Metode:</strong> <?php echo htmlspecialchars($row['payment_method']); ?></p>
                        <p><strong>Detail Rekening:</strong> <?php echo nl2br(htmlspecialchars($row['payment_details'])); ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Tidak ada metode pembayaran yang tersedia.</p>
    <?php endif; ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function togglePaymentProof() {
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
    const proofField = document.getElementById('paymentProofField');

    if (selectedMethod === 'Bank Transfer') {
        proofField.style.display = 'block';
    } else {
        proofField.style.display = 'none';
    }
}

// Jalankan sekali saat halaman dimuat
document.addEventListener("DOMContentLoaded", function () {
    const checkedRadio = document.querySelector('input[name="payment_method"]:checked');
    if (checkedRadio) {
        togglePaymentProof();
    }
});
</script>
<script>
function togglePaymentProof() {
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
    const proofField = document.getElementById('paymentProofField');
    const errorText = document.getElementById('paymentProofError');

    if (selectedMethod === 'Bank Transfer') {
        proofField.style.display = 'block';
    } else {
        proofField.style.display = 'none';
        errorText.classList.add('d-none');
    }
}

document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
    const paymentProofInput = document.getElementById('payment_proof');
    const errorText = document.getElementById('paymentProofError');

    if (selectedMethod && selectedMethod.value === 'Bank Transfer') {
        if (!paymentProofInput || paymentProofInput.files.length === 0) {
            e.preventDefault();
            errorText.classList.remove('d-none');
            paymentProofInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            errorText.classList.add('d-none');
        }
    }
});

document.getElementById('payment_proof').addEventListener('change', function () {
    const errorText = document.getElementById('paymentProofError');
    if (this.files.length > 0) {
        errorText.classList.add('d-none');
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const checkedRadio = document.querySelector('input[name="payment_method"]:checked');
    if (checkedRadio) {
        togglePaymentProof();
    }

    const radios = document.querySelectorAll('input[name="payment_method"]');
    radios.forEach(r => r.addEventListener('change', togglePaymentProof));
});
</script>



</body>
</html>
