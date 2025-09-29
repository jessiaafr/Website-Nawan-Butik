<?php
include('koneksi.php');

$order = null;
$produk_dipesan = [];
$error = '';

if (isset($_POST['track'])) {
    $pesanan_id = mysqli_real_escape_string($conn, $_POST['pesanan_id']);
    $pelanggan_nohp = mysqli_real_escape_string($conn, $_POST['pelanggan_nohp']);

    $query = "SELECT * FROM pesanan 
          WHERE pesanan_id = '$pesanan_id' 
            AND pelanggan_nohp = '$pelanggan_nohp'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);

        // Ambil detail produk yang dipesan
        $produk_query = "SELECT dp.*, p.product_name 
                         FROM detail_pesanan dp
                         JOIN product p ON dp.product_id = p.product_id
                         WHERE dp.pesanan_id = '$pesanan_id'";
        $produk_result = mysqli_query($conn, $produk_query);
        while ($row = mysqli_fetch_assoc($produk_result)) {
            $produk_dipesan[] = $row;
        }
    } else {
        $error = "Pesanan tidak ditemukan. Pastikan nomor pesanan dan nomor HP sesuai.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Ini penting buat mobile -->
    <title>Lacak Pesanan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <link rel="icon" href="images/logonawan.jpg" type="image/jpg">
    <style>
        body {
      font-size: 0.95rem;
    }

    .container {
      max-width: 600px;
    }

    @media (max-width: 576px) {
      h2 {
        font-size: 1.4rem;
      }

      .card-body p {
        font-size: 0.9rem;
        margin-bottom: 0.4rem;
      }

      .table-responsive {
        font-size: 0.9rem;
      }
    }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4 text-center">Lacak Pesanan Anda</h2>

    <form method="POST" class="bg-white p-4 rounded shadow-sm no-print">
        <div class="form-group">
            <label for="pesanan_id">Nomor Pesanan</label>
            <input type="text" name="pesanan_id" id="pesanan_id" class="form-control" required placeholder="Contoh: 123">
        </div>
        <div class="form-group">
            <label for="pelanggan_nohp">Nomor Handphone</label>
            <input type="text" name="pelanggan_nohp" id="pelanggan_nohp" class="form-control" required placeholder="Contoh: 08123456789">
        </div>
        <button type="submit" name="track" class="btn btn-primary btn-block">Lacak</button>
        <a href="index.php" class="btn btn-outline-secondary btn-block mt-2">Kembali</a>

    </form>

    <?php if ($error): ?>
        <div class="alert alert-danger mt-4"><?= $error ?></div>
        <?php elseif ($order): ?>
    <div class="card mt-4">
        <div class="card-header bg-success text-white">Detail Pesanan</div>
        <div class="card-body">
            <p><strong>Nomor Pesanan:</strong> <?= $order['pesanan_id'] ?></p>
            <p><strong>Nama:</strong> <?= $order['pelanggan_name'] ?></p>
            <p><strong>Status Pesanan:</strong> <?= $order['status_pesanan'] ?></p>
            <p><strong>Total Harga:</strong> Rp <?= number_format($order['total_price'], 0, ',', '.') ?></p>
            <p><strong>Tanggal Pesan:</strong> <?= $order['order_date'] ?></p>
            <p><strong>Metode Pembayaran:</strong> <?= $order['payment_method'] ?></p>
            <p><strong>Status Pembayaran:</strong> 
    <?php 
    if ($order['payment_method'] == 'COD') {
        echo '<span class="text-danger font-weight-bold">BELUM LUNAS</span>';
    } else {
        echo $order['payment_status'] == 'LUNAS' 
            ? '<span class="text-success font-weight-bold">LUNAS</span>' 
            : '<span class="text-danger font-weight-bold">BELUM LUNAS</span>';
    }
    ?>
</p>


            <?php if (!empty($produk_dipesan)): ?>
                <hr>
                <h5>Produk Dipesan:</h5>
                <div class="table-responsive">
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($produk_dipesan as $produk): ?>
                        <tr>
                            <td><?= $produk['product_name'] ?></td>
                            <td><?= $produk['quantity'] ?></td>
                            <td>Rp <?= number_format($produk['price'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            <?php endif; ?>

            <button class="btn btn-secondary mt-3 no-print" onclick="window.print()">Cetak Struk</button>
        </div>
    </div>
<?php endif; ?>
</div>
</body>
</html>
