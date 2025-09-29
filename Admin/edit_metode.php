<?php
// Include koneksi database
include '../koneksi.php';

// Cek apakah ada parameter 'id' pada URL
if (isset($_GET['id'])) {
    $payment_id = $_GET['id'];

    // Ambil data metode pembayaran berdasarkan ID
    $query = "SELECT * FROM metode_pembayaran WHERE payment_id = '$payment_id'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        // Ambil data dari hasil query
        $row = mysqli_fetch_assoc($result);
        $payment_method = $row['payment_method'];
        $payment_details = $row['payment_details'];
        $payment_logo = $row['payment_logo'];
    } else {
        echo "<div class='alert alert-danger'>Metode pembayaran tidak ditemukan.</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-danger'>ID Pembayaran tidak ditemukan.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data yang diinputkan user
    $payment_method = $_POST['payment_method'];
    $payment_details = $_POST['payment_details'];

    // Cek apakah ada logo yang di-upload
    if ($_FILES['payment_logo']['name']) {
        $logo_tmp = $_FILES['payment_logo']['tmp_name'];
        $logo_name = $_FILES['payment_logo']['name'];
        $logo_path = '../uploads/' . $logo_name;

        // Pindahkan file logo ke folder uploads
        move_uploaded_file($logo_tmp, $logo_path);

        // Update logo di database
        $query = "UPDATE metode_pembayaran SET payment_method = '$payment_method', payment_details = '$payment_details', payment_logo = '$logo_name' WHERE payment_id = '$payment_id'";
    } else {
        // Jika tidak ada logo baru, update tanpa mengganti logo
        $query = "UPDATE metode_pembayaran SET payment_method = '$payment_method', payment_details = '$payment_details' WHERE payment_id = '$payment_id'";
    }

    if (mysqli_query($conn, $query)) {
        echo "<div class='alert alert-success'>Metode pembayaran berhasil diperbarui.</div>";
        echo "<a href='manage_payments.php' class='btn btn-success btn-block'>Kembali ke Manage Payments</a>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Metode Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../images/logonawan.jpg" type="image/jpg"> 
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #495057;
        }

        .container {
            max-width: 900px;
            margin-top: 60px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px;
            font-size: 16px;
        }

        .btn-primary,
        .btn-success,
        .btn-back {
            font-weight: bold;
            border-radius: 30px;
            padding: 12px 30px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #218838;
        }

        .btn-back {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-back:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }

        .alert {
            margin-top: 30px;
            border-radius: 10px;
        }

        h2 {
            font-size: 28px;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 40px;
            text-align: center;
        }

        .card {
            border-radius: 10px;
            padding: 30px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-text {
            font-size: 14px;
            color: #6c757d;
        }

        .file-input {
            border-radius: 10px;
        }

        .container-fluid {
            background-color: #ffffff;
        }

        .logo-preview {
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card">
            <h2>Edit Metode Pembayaran</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="payment_method">Metode Pembayaran</label>
                    <input type="text" name="payment_method" id="payment_method" class="form-control" value="<?php echo $payment_method; ?>" required>
                </div>

                <div class="form-group">
                    <label for="payment_details">Detail Pembayaran</label>
                    <textarea name="payment_details" id="payment_details" class="form-control" rows="4" required><?php echo $payment_details; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="payment_logo">Logo Pembayaran (Opsional)</label>
                    <input type="file" name="payment_logo" id="payment_logo" class="form-control-file file-input">
                    <small class="form-text text-muted">Jika Anda tidak ingin mengganti logo, biarkan kosong.</small>
                </div>

                <?php if ($payment_logo) : ?>
                    <div class="logo-preview">
                        <h5>Logo Saat Ini</h5>
                        <img src="../uploads/<?php echo $payment_logo; ?>" alt="Logo Pembayaran" width="100">
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary btn-block mt-4">Simpan Perubahan</button>
                <a href="manage_payments.php" class="btn btn-back btn-block mt-2">Kembali</a>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>

<?php
// Tutup koneksi
mysqli_close($conn);
?>
