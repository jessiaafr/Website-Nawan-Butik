<?php
session_start();
include '../koneksi.php';

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
$admin_name = $_SESSION['admin_name'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pelanggan - AkiNini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../images/logonawan.jpg" type="image/jpg"> 
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #4e342e;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .table th {
            background-color: #6d4c41;
            color: white;
        }

        .table td {
            font-size: 14px;
        }

        .btn-back {
            color: white;
            background-color: #8d6e63;
            border-color: #8d6e63;
            margin-top: 20px;
        }

        .btn-back:hover {
            background-color: #795548;
            border-color: #795548;
        }

        @media (max-width: 768px) {
            h2 {
                font-size: 24px;
            }

            .table th, .table td {
                font-size: 12px;
            }

            .btn-back {
                font-size: 12px;
                padding: 5px 10px;
            }
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2><i class="fas fa-users"></i> Daftar Pelanggan</h2>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM pelanggan";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$no}</td>
                        <td>" . htmlspecialchars($row['pelanggan_name']) . "</td>
                        <td>" . htmlspecialchars($row['pelanggan_email']) . "</td>
                        <td>" . htmlspecialchars($row['pelanggan_telp']) . "</td>
                        <td>" . htmlspecialchars($row['pelanggan_address']) . "</td>
                    </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>Tidak ada data pelanggan</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="index.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama</a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
mysqli_close($conn);
?>
