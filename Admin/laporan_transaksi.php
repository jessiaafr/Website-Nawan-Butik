<?php
session_start();

// Include the database connection
include '../koneksi.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

if (isset($_POST['submit'])) {
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];

    // Awal query dan parameter dasar
    $where = "WHERE MONTH(order_date) = ? AND YEAR(order_date) = ?";
    $params = [$bulan, $tahun];
    $types = "ii";

    // Tambahan filter jika ada
    if (!empty($_POST['payment_method'])) {
        $where .= " AND payment_method = ?";
        $params[] = $_POST['payment_method'];
        $types .= "s";
    }

    if (!empty($_POST['payment_status'])) {
        $where .= " AND payment_status = ?";
        $params[] = $_POST['payment_status'];
        $types .= "s";
    }

    $sql = "SELECT
    p.pesanan_id,
    p.pelanggan_name,
    p.order_date,
    p.payment_method,
    p.payment_status,
    p.total_price,
  GROUP_CONCAT(CONCAT(pr.product_name, ' (', dp.quantity, ')') SEPARATOR '\n') AS product_dibeli
FROM pesanan p
JOIN detail_pesanan dp ON p.pesanan_id = dp.pesanan_id
JOIN product pr ON dp.product_id = pr.product_id
$where
GROUP BY p.pesanan_id
ORDER BY p.order_date ASC";


    // Prepare dan eksekusi query
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi</title>
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

        .card {
    background: #fff7f0;
    border: none;
    border-radius: 16px;
}

.table thead {
    background-color: #f5e9dd;
    color: #6f4e37;
    font-weight: bold;
}

.table td, .table th {
    vertical-align: middle;
}

.btn-custom {
    background-color: #3498db;
    color: white;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-custom:hover {
    background-color: #2980b9;
    transform: scale(1.05);
}

td ul {
    display: block;
    white-space: pre-wrap; /* Agar line breaks bekerja */
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

    <div class="container mt-4">
    <div class="card shadow-sm p-4">
        <h3 class="mb-3">Laporan Transaksi Bulanan</h3>
        <form method="POST" action="laporan_transaksi.php" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="bulan" class="form-label">Bulan</label>
                <select name="bulan" id="bulan" class="form-select">
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="tahun" class="form-label">Tahun</label>
                <select name="tahun" id="tahun" class="form-select">
                    <?php
                        for ($i = 2025; $i <= date('Y'); $i++) {
                            echo "<option value='$i'>$i</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" name="submit" class="btn btn-custom w-100">Tampilkan Laporan</button>
            </div>

            <label for="payment_method">Metode Pembayaran:</label>
<select name="payment_method" id="payment_method">
    <option value="">Semua</option>
    <option value="COD">COD</option>
    <option value="Bank Transfer">Bank Transfer</option>
</select>

<label for="payment_status">Status Pembayaran:</label>
<select name="payment_status" id="payment_status">
    <option value="">Semua</option>
    <option value="LUNAS">LUNAS</option>
    <option value="BELUM LUNAS">BELUM LUNAS</option>
</select>

        </form>
    </div>

    <?php if (isset($result) && $result->num_rows > 0): ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Tombol Kembali -->
    <a href="index.php" class="btn btn-back" style="background: linear-gradient(45deg, #1E865C, #4CAF83); color: white; font-weight: bold; border-radius: 30px; padding: 10px 20px;">
        <i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama
    </a>
    <div class="d-flex justify-content-end gap-2 mb-3">
        <button id="download-pdf" class="btn btn-danger">Download PDF</button>
        <button id="print-page" class="btn btn-secondary">Cetak Halaman</button>
        <button id="download-excel" class="btn btn-success">Download Excel</button>
    </div>
    </div>
<?php endif; ?>

<div id="laporan">

<?php
if (isset($result) && $result->num_rows > 0) {
    echo "<h2 class='mt-4 mb-3 text-center'>Detail Transaksi untuk $bulan/$tahun</h2>";
    echo "<div class='table-responsive'><table class='table table-bordered'>
            <thead class='table-light'>
                <tr>
                    <th>No</th>
                    <th>ID Pesanan</th>
                    <th>Nama Pelanggan</th>
                    <th>Tanggal Pesanan</th>
                    <th>Metode Pembayaran</th>
                    <th>Status Pembayaran</th>
                    <th>Produk Dibeli</th>
                    <th>Total Harga</th>
                </tr>
            </thead>
            <tbody>";

    $no = 1;
    $grandTotal = 0;

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$no}</td>
            <td>{$row['pesanan_id']}</td>
            <td>{$row['pelanggan_name']}</td>
            <td>" . date('d-m-Y', strtotime($row['order_date'])) . "</td>
            <td>{$row['payment_method']}</td>
            <td>{$row['payment_status']}</td>
            <td>"; // Mulai kolom Produk Dibeli

            // Pisahkan produk dengan menggunakan newline
            $produkList = explode("\n", $row['product_dibeli']);
            echo '<ul style="padding-left: 20px; margin-bottom: 0;">'; // Gaya untuk list produk
            foreach ($produkList as $item) {
                echo '<li style="margin-bottom: 5px;">' . htmlspecialchars($item) . '</li>'; // Menambahkan jarak antar produk
            }
            echo '</ul>'; // Tutup daftar produk

        echo "</td>
            <td>Rp " . number_format($row['total_price'], 0, ',', '.') . "</td>
        </tr>";

        $grandTotal += $row['total_price'];
        $no++;
    }

    // Total keseluruhan di luar loop
    echo "<tr class='fw-bold table-secondary'>
            <td colspan='7' class='text-end'>Total Keseluruhan:</td>
            <td>Rp " . number_format($grandTotal, 0, ',', '.') . "</td>
        </tr>";

    echo "</tbody></table></div>";
} elseif (isset($result)) {
    echo "<div class='alert alert-warning mt-4'>Tidak ada transaksi untuk bulan $bulan tahun $tahun.</div>";
}
?>


</div> <!-- Penutup div laporan -->

<?php
// Tutup koneksi jika sudah digunakan
if (isset($conn)) {
    $conn->close();
}
?>


<!-- Include jsPDF dan html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    document.getElementById('download-pdf')?.addEventListener('click', function () {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4');

        const strukElement = document.getElementById('laporan');
        const downloadBtn = document.getElementById('download-pdf');

        downloadBtn.style.display = 'none'; // hide while generating

        html2canvas(strukElement, { scale: 2 }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');

            const pageWidth = doc.internal.pageSize.getWidth();
            const imgProps = doc.getImageProperties(imgData);
            const imgHeight = (imgProps.height * pageWidth) / imgProps.width;

            doc.addImage(imgData, 'PNG', 0, 0, pageWidth, imgHeight);
            doc.save('Laporan_Transaksi_<?php echo "$bulan-$tahun"; ?>.pdf');

            downloadBtn.style.display = 'inline-block'; // show again
        });
    });

    // CETAK HALAMAN
    document.getElementById('print-page')?.addEventListener('click', function () {
        window.print();
    });

    // DOWNLOAD EXCEL
    document.getElementById('download-excel')?.addEventListener('click', function () {
        const table = document.querySelector("#laporan table");
        if (!table) return;

        const wb = XLSX.utils.table_to_book(table, { sheet: "Laporan Transaksi" });
        XLSX.writeFile(wb, "Laporan_Transaksi_<?php echo "$bulan-$tahun"; ?>.xlsx");
    });

</script>