<?php
session_start();
include '../koneksi.php';

// Query untuk mengambil data pesanan
$query = "SELECT 
    p.pesanan_id,
    p.total_price,
    p.status_pesanan,
    p.payment_status,
    p.order_date,
    p.pelanggan_name,
    p.pelanggan_nohp,
    p.pelanggan_address,
    p.payment_proof,
    p.payment_method
  FROM pesanan p
  LEFT JOIN pelanggan pl ON p.pelanggan_id = pl.pelanggan_id
  ORDER BY p.order_date DESC";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../images/logonawan.jpg" type="image/jpg"> 
    <style>
        .payment-proof-img { max-width: 100%; height: auto; }
        body { font-family: 'Arial', sans-serif; background-color: #f4f4f4; }
        .container {
            background-color: white; padding: 30px;
            border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 { color: #4e342e; font-size: 28px; margin-bottom: 20px; }
        .table th, .table td { vertical-align: middle; }
        .table th { background-color: #6d4c41; color: white; }
        .table td { font-size: 14px; }
        .btn-warning { color: #fff; background-color: #8d6e63; border-color: #8d6e63; }
        .btn-warning:hover { background-color: #795548; border-color: #795548; }
        .btn-back { color: white; background-color: #8d6e63; border-color: #8d6e63; margin-top: 20px; }
        .btn-back:hover { background-color: #795548; border-color: #795548; }
        .status-pending { color: #f39c12; }
        .status-processing { color: #f1c40f; }
        .status-completed { color: #2ecc71; }
        .status-shipped { color: #3498db; }
        .status-belumlunas { color:rgb(0, 0, 0); }
        .status-lunas { color: #2ecc71; }
        @media (max-width: 768px) {
            h2 { font-size: 24px; }
            .table th, .table td { font-size: 12px; }
            .btn-warning, .btn-back { font-size: 12px; padding: 5px 10px; }
        }
        #statusAlert {
            transition: opacity 0.5s ease-out;
        }
        table {
    table-layout: auto !important;
    word-wrap: break-word;
}
td, th {
    white-space: normal !important;
}
.container {
    width: 95%;
    max-width: 100%;
    margin: 0 auto;
    padding: 30px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}
.table td, .table th {
    padding: 8px;
    
}
.table th {
    background-color: #6d4c41;
    color: white;
    text-align: center; /* Tambahkan ini */
}
.table td {
    font-size: 14px;
    text-align: center; /* Optional: tambahkan ini kalau mau semuanya center */
}

    </style>
</head>
<body>

<div class="container mt-5">
    <h2><i class="fas fa-box"></i> Daftar Pesanan</h2>
    <?php
    if (isset($_SESSION['status_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" id="statusAlert">
            <?= $_SESSION['status_success']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['status_success']); ?>
    <?php endif; ?>

    <div class="table-responsive">
    <table class="table table-striped table-bordered w-100">
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Pesanan</th>
                <th>Nama Pelanggan</th>
                <th>Alamat</th>
                <th>Nomor Handphone</th>
                <th>Total Harga</th>
                <th>Tanggal Pesanan</th>
                <th>Status Pesanan</th>
                <th>Status Pembayaran</th>
                <th>Jenis Pembayaran</th>
                <th>Struk</th>
                <th>Bukti Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                switch ($row['status_pesanan']) {
                    case 'Pending':
                        $status_class = 'status-pending';
                        break;
                    case 'Proses':
                        $status_class = 'status-processing';
                        break;
                    case 'Selesai':
                        $status_class = 'status-completed';
                        break;
                    case 'Dikirim':
                        $status_class = 'status-shipped';
                        break;
                    default:
                        $status_class = '';
                        break;
                }

                $pembayaran_class = $row['payment_status'] === 'Lunas' ? 'status-lunas' : 'status-belumlunas';

                $payment_proof = $row['payment_proof']
                    ? "<button class='btn btn-info btn-sm' data-toggle='modal' data-target='#paymentProofModal{$row['pesanan_id']}'>Lihat</button>"
                    : 'Tidak Ada';

                echo "<tr>
                    <td>{$no}</td>
                    <td>".htmlspecialchars($row['pesanan_id'])."</td>
                    <td>".htmlspecialchars($row['pelanggan_name'])."</td>
                    <td>".htmlspecialchars($row['pelanggan_address'])."</td>
                    <td>".htmlspecialchars($row['pelanggan_nohp'])."</td>
                    <td>Rp " . number_format($row['total_price'], 0, ',', '.') . "</td>
                    <td>{$row['order_date']}</td>
                    <td class='{$status_class}'><strong>{$row['status_pesanan']}</strong></td>
                    <td class='{$pembayaran_class}'><strong>{$row['payment_status']}</strong></td>
                   <td>".htmlspecialchars($row['payment_method'])."</td>
                    <td><a href='struk.php?pesanan_id={$row['pesanan_id']}' class='btn btn-primary btn-sm'>Lihat</a></td>
                    <td>{$payment_proof}</td>
                    <td><a href='update_status.php?id={$row['pesanan_id']}' class='btn btn-warning btn-sm'><i class='fas fa-sync'></i></a></td>
                </tr>";

                if ($row['payment_proof']) {
                    echo "
                    <div class='modal fade' id='paymentProofModal{$row['pesanan_id']}' tabindex='-1' role='dialog' aria-labelledby='modalLabel{$row['pesanan_id']}' aria-hidden='true'>
                        <div class='modal-dialog' role='document'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <h5 class='modal-title' id='modalLabel{$row['pesanan_id']}'>Bukti Pembayaran</h5>
                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                        <span aria-hidden='true'>&times;</span>
                                    </button>
                                </div>
                                <div class='modal-body'>
                                    <img src='../uploads/".htmlspecialchars($row['payment_proof'])."' alt='Bukti Pembayaran' class='payment-proof-img'>
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-dismiss='modal'>Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>";
                }

                $no++;
            }
            ?>
        </tbody>
        </div>
    </table>
    </div>
    <a href="index.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama</a>
</div>
 
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Hilangkan notifikasi sukses setelah 3 detik
    setTimeout(function () {
        const alertBox = document.getElementById('statusAlert');
        if (alertBox) {
            alertBox.classList.remove('show');
            alertBox.classList.add('fade');
            alertBox.style.opacity = '0';
            setTimeout(() => alertBox.remove(), 500); // Hapus elemen setelah fade out
        }
    }, 3000);
</script>
</body>
</html>

<?php mysqli_close($conn); ?>
