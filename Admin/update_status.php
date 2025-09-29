<?php
session_start();
// Include koneksi database
include '../koneksi.php';

// Ambil ID pesanan dari URL
if (isset($_GET['id'])) {
    $pesanan_id = $_GET['id'];
} else {
    die("ID pesanan tidak ditemukan.");
}

// Query untuk mengambil data pesanan berdasarkan ID
$query = "SELECT * FROM pesanan WHERE pesanan_id = $pesanan_id";
$result = mysqli_query($conn, $query);

// Cek apakah pesanan ditemukan
if (mysqli_num_rows($result) == 0) {
    die("Pesanan tidak ditemukan.");
}

// Ambil data pesanan
$pesanan = mysqli_fetch_assoc($result);

// Mengupdate status pesanan jika form disubmit
if (isset($_POST['update_status'])) {
    $status_pesanan = $_POST['status_pesanan'];
    $payment_status = $_POST['payment_status'];

    // Gabungkan keduanya dalam satu query
    $update_query = "UPDATE pesanan 
                     SET status_pesanan = '$status_pesanan', payment_status = '$payment_status' 
                     WHERE pesanan_id = $pesanan_id";

    if (mysqli_query($conn, $update_query)) {
        // Redirect ke halaman daftar pesanan setelah update
        $_SESSION['status_success'] = "Status pesanan berhasil diperbarui!"; // ✅ Set session notifikasi
        header("Location: manage_orders.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Status Pesanan</title>
    <!-- Link to Bootstrap 4, FontAwesome, and custom CSS -->
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
    </style>
</head>
<body>

<div class="container mt-5">
    <h2><i class="fas fa-box"></i> Ubah Status Pesanan</h2>

    <form action="update_status.php?id=<?php echo $pesanan_id; ?>" method="POST">
        <div class="form-group">
            <label for="status_pesanan">Status Pesanan</label>
            <select id="status_pesanan" name="status_pesanan" class="form-control" required>
                <option value="Pending" <?php echo $pesanan['status_pesanan'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="Proses" <?php echo $pesanan['status_pesanan'] == 'Proses' ? 'selected' : ''; ?>>Proses</option>
                <option value="Selesai" <?php echo $pesanan['status_pesanan'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                <option value="Dikirim" <?php echo $pesanan['status_pesanan'] == 'Dikirim' ? 'selected' : ''; ?>>Dikirim</option>
            </select>
        </div>

        <div class="form-group">
            <label for="payment_status">Status Pembayaran</label>
            <select id="payment_status" name="payment_status" class="form-control" required>
                <option value="Belum Lunas" <?php echo $pesanan['payment_status'] == 'Belum Lunas' ? 'selected' : ''; ?>>Belum Lunas</option>
                <option value="Lunas" <?php echo $pesanan['payment_status'] == 'Lunas' ? 'selected' : ''; ?>>Lunas</option>
            </select>
        </div>

        <button type="submit" name="update_status" class="btn btn-warning mt-3">
            <i class="fas fa-sync"></i> Update Status
        </button>
    </form>

    <!-- Tombol Kembali -->
    <a href="manage_orders.php" class="btn btn-back mt-3">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pesanan
    </a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Tutup koneksi
mysqli_close($conn);
?>
