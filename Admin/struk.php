<?php
// Menghubungkan ke database
include('koneksi.php');

// Cek apakah pesanan_id ada di URL
if (isset($_GET['pesanan_id'])) {
    $pesanan_id = $_GET['pesanan_id'];

    // Query menggunakan Prepared Statement untuk keamanan
    $query = "SELECT p.pesanan_id, p.total_price, p.status_pesanan, p.order_date,
                     p.pelanggan_name, p.pelanggan_address, p.pelanggan_kota, p.pelanggan_kodepos, p.pelanggan_nohp
              FROM pesanan p
              LEFT JOIN pelanggan pl ON p.pelanggan_id = pl.pelanggan_id
              WHERE p.pesanan_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pesanan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order_data = $result->fetch_assoc();

    if (!$order_data) {
        die("<p style='color:red; text-align:center;'>Error: Pesanan tidak ditemukan.</p>");
    }
} else {
    die("<p style='color:red; text-align:center;'>Error: ID Pesanan tidak ditemukan.</p>");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        body { background-color: #f4f4f4; }
        .receipt {
            border: 1px solid #ddd;
            padding: 20px;
            max-width: 600px;
            margin: 50px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .receipt h2 { text-align: center; color: #6f4f1f; }
        .details ul { list-style-type: none; padding: 0; }
        .details li { margin-bottom: 10px; font-size: 16px; font-weight: bold; }
        .total {
            font-size: 20px;
            text-align: center;
            color: #333;
            font-weight: bold;
        }
        .btn-download {
            background-color: #6f4f1f;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 50px;
            text-align: center;
            display: block;
            margin: 20px auto;
            cursor: pointer;
            border: none;
        }
        .btn-download:hover { background-color: #5e3f1c; }
        .logo { width: 100px; display: block; margin: 0 auto 10px; }
    </style>
</head>
<body>

<div class="container">
    <div class="receipt p-4 border rounded" id="struk">
        <div class="text-center mb-3">
            <img src="../images/logo_catering.jpg" alt="AkiNiNi Catering Logo" class="logo mb-2">
            <h3 class="mb-1">AkiNiNi Catering</h3>
            <p class="mb-0"><strong>No HP:</strong> 0812xxxxxxxxx</p>
            <p class="mb-0"><strong>Alamat:</strong> Bekasi</p>
        </div>

        <h2>Struk Pesanan</h2>
        <p><strong>Nomor Pesanan:</strong> <?php echo htmlspecialchars($order_data['pesanan_id']); ?></p>
        <p><strong>Tanggal Pesanan:</strong> <?php echo date("d M Y", strtotime($order_data['order_date'])); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($order_data['status_pesanan']); ?></p>

        <p><strong>Nama Pemesan:</strong> <?php echo htmlspecialchars($order_data['pelanggan_name']); ?></p>
        <p><strong>Alamat:</strong><br>
            <?php echo htmlspecialchars($order_data['pelanggan_address']); ?>,<br>
            <?php echo htmlspecialchars($order_data['pelanggan_kota']); ?>,
            <?php echo htmlspecialchars($order_data['pelanggan_kodepos']); ?><br>
        <p><strong>No. HP:</strong>
            <?php echo htmlspecialchars($order_data['pelanggan_nohp']); ?>
        </p>

        <div class="details">
            <h4>Daftar Pesanan:</h4>
            <ul>
                <?php
               $query_detail = "SELECT dp.quantity, dp.price, pr.product_name, pr.product_discount_price
               FROM detail_pesanan dp
               JOIN product pr ON dp.product_id = pr.product_id
               WHERE dp.pesanan_id = ?";
                $stmt_detail = $conn->prepare($query_detail);
                $stmt_detail->bind_param("i", $pesanan_id);
                $stmt_detail->execute();
                $result_detail = $stmt_detail->get_result();

                $total = 0;
                while ($row = $result_detail->fetch_assoc()):
                    $harga = ($row['product_discount_price'] > 0) ? $row['product_discount_price'] : $row['price'];
                    $subtotal = $harga * $row['quantity'];
                    $total += $subtotal;
                ?>
                    <li>
                        <?php echo htmlspecialchars($row['product_name']); ?> (x<?php echo $row['quantity']; ?>)
                        - Rp <?php echo number_format($subtotal, 0, ',', '.'); ?>
                        <?php if ($row['product_discount_price'] > 0): ?>
                            <br><small style="color: green;">(Harga Diskon: Rp <?php echo number_format($row['product_discount_price'], 0, ',', '.'); ?>)</small>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>                
            </ul>
        </div>

        <div class="total">
            <p><strong>Total: Rp <?php echo number_format($total, 0, ',', '.'); ?></strong></p>
        </div>

        <button id="download-pdf" class="btn-download">Unduh PDF</button>
        <a href="manage_orders.php" class="btn-download">Kembali ke Daftar Pesanan</a>
    </div>
</div>

<script>
    document.getElementById('download-pdf').addEventListener('click', function () {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4');

        const strukElement = document.getElementById('struk');
        const downloadBtn = document.getElementById('download-pdf');

        downloadBtn.style.display = 'none';

        html2canvas(strukElement, { scale: 2 }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');

            doc.addImage(imgData, 'PNG', 10, 10, 190, 0);
            doc.save('Struk_Pesanan_<?php echo $order_data["pesanan_id"]; ?>.pdf');

            downloadBtn.style.display = 'inline-block';
        });
    });
</script>

</body>
</html>