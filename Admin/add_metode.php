<?php
// Include koneksi database
include '../koneksi.php';

// Variabel untuk menyimpan pesan error dan success
$error = '';
$success = '';

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $payment_method = $_POST['payment_method'];
    $payment_details = $_POST['payment_details'];

    // Proses upload logo pembayaran
    if (isset($_FILES['payment_logo']) && $_FILES['payment_logo']['error'] == 0) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["payment_logo"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Cek apakah file gambar adalah gambar yang valid
        if (getimagesize($_FILES["payment_logo"]["tmp_name"])) {
            $uploadOk = 1;
        } else {
            $error = "File yang diunggah bukan gambar.";
            $uploadOk = 0;
        }

        // Cek apakah file sudah ada
        if (file_exists($target_file)) {
            $error = "Maaf, file sudah ada.";
            $uploadOk = 0;
        }

        // Batasi ukuran file maksimal
        if ($_FILES["payment_logo"]["size"] > 500000) {
            $error = "Maaf, file terlalu besar.";
            $uploadOk = 0;
        }

        // Hanya izinkan format file tertentu
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $error = "Maaf, hanya file gambar JPG, JPEG, PNG, dan GIF yang diperbolehkan.";
            $uploadOk = 0;
        }

        // Periksa apakah $uploadOk diset ke 0 karena kesalahan
        if ($uploadOk == 0) {
            $error = "Maaf, file Anda tidak dapat diunggah.";
        } else {
            if (move_uploaded_file($_FILES["payment_logo"]["tmp_name"], $target_file)) {
                $payment_logo = basename($_FILES["payment_logo"]["name"]);
            } else {
                $error = "Maaf, terjadi kesalahan dalam mengunggah file.";
            }
        }
    } else {
        $payment_logo = ''; // Jika tidak ada file logo
    }

    // Jika tidak ada error, simpan data ke database
    if ($error == '') {
        $query = "INSERT INTO metode_pembayaran (payment_method, payment_details, payment_logo) 
                  VALUES ('$payment_method', '$payment_details', '$payment_logo')";

        if (mysqli_query($conn, $query)) {
            $success = "Metode pembayaran berhasil ditambahkan.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Metode Pembayaran</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../images/logonawan.jpg" type="image/jpg"> 
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
        }

        h2 {
            color: #333;
            text-align: center;
            font-weight: 600;
        }

        .alert {
            margin-top: 20px;
        }

        .form-control, .btn {
            border-radius: 25px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            color: #6c757d;
        }

        .btn-back {
            margin-top: 15px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Tambah Metode Pembayaran</h2>

        <!-- Tampilkan pesan error dan success -->
        <?php
        if ($error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
        if ($success) {
            echo "<div class='alert alert-success'>$success</div>";
        }
        ?>

        <form action="add_metode.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="payment_method" class="form-label">Metode Pembayaran</label>
                <input type="text" class="form-control" id="payment_method" name="payment_method" required>
            </div>

            <div class="mb-3">
                <label for="payment_details" class="form-label">Detail Pembayaran</label>
                <textarea class="form-control" id="payment_details" name="payment_details" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="payment_logo" class="form-label">Logo Pembayaran</label>
                <input type="file" class="form-control" id="payment_logo" name="payment_logo" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Tambah Metode Pembayaran</button>
            <a href="manage_payments.php" class="btn btn-secondary btn-block btn-back">Kembali</a>
        </form>
    </div>

    <div class="footer">
        <p>&copy; 2024 Semua Hak Cipta Dilindungi</p>
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
