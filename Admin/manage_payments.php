<?php
// Include koneksi database
include '../koneksi.php';

// Ambil data metode pembayaran dari database
$query = "SELECT * FROM metode_pembayaran";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Metode Pembayaran</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../images/logonawan.jpg" type="image/jpg"> 
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        h2 {
            color: #333;
            margin-top: 30px;
            text-align: center;
            font-weight: 600;
        }

        .table {
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        th {
            background-color: #007bff;
            color: black;
        }

        td img {
            border-radius: 5px;
            transition: transform 0.3s ease;
        }

        td img:hover {
            transform: scale(1.1);
        }

        .btn-warning {
            background-color: #ff9900;
            border-color: #ff9900;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 25px;
        }

        .btn-warning:hover {
            background-color: #e68900;
            border-color: #e68900;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 25px;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #c82333;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .table-container {
            padding: 40px;
            margin-top: 50px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .container {
            max-width: 1200px;
        }

        .table-container .btn {
            margin-right: 5px;
        }

        .btn-back {
            background-color: #6c757d;
            border-color: #6c757d;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 25px;
        }

        .btn-back:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            color: #6c757d;
        }

        /* Responsive Tabel */
        .table-responsive {
            overflow-x: auto;
        }

        .btn-tambah {
            background-color: #28a745;
            border-color: #28a745;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 25px;
            margin-bottom: 20px;
        }

        .btn-tambah:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="table-container">
            <!-- Tombol Tambah Metode Pembayaran -->
            <a href="add_metode.php" class="btn btn-tambah mb-3">
                <i class="fas fa-plus-circle"></i> Tambah Pembayaran
            </a>

            <?php
            if ($result) {
                echo "<br><h2>Daftar Metode Pembayaran</h2></br>";
                echo "<div class='table-responsive'>
                        <table class='table table-bordered'>
                            <thead>
                                <tr>
                                    <th>ID Pembayaran</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Detail Pembayaran</th>
                                    <th>Logo Pembayaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>";

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . $row['payment_id'] . "</td>
                            <td>" . $row['payment_method'] . "</td>
                            <td>" . $row['payment_details'] . "</td>
                            <td><img src='../uploads/" . $row['payment_logo'] . "' alt='Logo Pembayaran' width='50'></td>
                            <td>
                                <a href='edit_metode.php?id=" . $row['payment_id'] . "' class='btn btn-warning btn-sm'>
                                    <i class='fas fa-edit'></i> Edit
                                </a>
                                <a href='delete_metode.php?id=" . $row['payment_id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Anda yakin ingin menghapus metode pembayaran ini?\")'>
                                    <i class='fas fa-trash-alt'></i> Hapus
                                </a>
                            </td>
                          </tr>";
                }

                echo "</tbody></table></div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
            }

            // Tutup koneksi
            mysqli_close($conn);
            ?>
        </div>
        <a href="index.php" class="btn btn-back btn-block mt-3">Kembali</a>
    </div>

    <div class="footer">
        <p>&copy; 2024 Semua Hak Cipta Dilindungi</p>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>
