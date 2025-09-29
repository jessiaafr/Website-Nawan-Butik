<?php
session_start();
include '../koneksi.php';

ini_set('upload_max_filesize', '2M');
ini_set('post_max_size', '8M');
ini_set('max_execution_time', '120');

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = mysqli_real_escape_string($conn, $_POST['product_name']);
    $productPrice = (float) str_replace('.', '', $_POST['product_price']);
    $productDescription = mysqli_real_escape_string($conn, $_POST['product_description']);
    $productDetail = mysqli_real_escape_string($conn, $_POST['product_detail']);
    $categoryId = (int) $_POST['category_id'];
    $productStock = (int) $_POST['product_stock'];
    $imageFile = $_FILES['product_image'];

    // Debug error code
    var_dump($_FILES['product_image']['error']);

    if ($imageFile['error'] === UPLOAD_ERR_OK) {
        $imagePath = '../uploads/' . basename($imageFile['name']);

        if (move_uploaded_file($imageFile['tmp_name'], $imagePath)) {
            $query = "
                INSERT INTO product (product_name, product_price, product_description, 
                                     product_image, product_stock, category_id, product_detail) 
                VALUES ('$productName', $productPrice, '$productDescription', 
                        '" . basename($imageFile['name']) . "', $productStock, $categoryId)
            ";
            if (mysqli_query($conn, $query)) {
                $message = "Produk berhasil ditambahkan.";
            } else {
                $message = "Terjadi kesalahan saat menyimpan ke database: " . mysqli_error($conn);
            }
        } else {
            $message = "Gagal memindahkan file gambar ke folder tujuan.";
        }
    } else {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE   => 'File melebihi ukuran maksimum yang diizinkan oleh server.',
            UPLOAD_ERR_FORM_SIZE  => 'File melebihi ukuran maksimum dari form.',
            UPLOAD_ERR_PARTIAL    => 'File hanya terupload sebagian.',
            UPLOAD_ERR_NO_FILE    => 'Tidak ada file yang diupload.',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder sementara tidak tersedia.',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menyimpan file ke disk.',
            UPLOAD_ERR_EXTENSION  => 'Upload dihentikan oleh ekstensi PHP.',
        ];
        $errorCode = $imageFile['error'];
        $message = $uploadErrors[$errorCode] ?? 'Terjadi kesalahan saat upload.';
    }
}

// Ambil kategori dari database
$categoryQuery = "SELECT category_id, category_name FROM category";
$categories = mysqli_query($conn, $categoryQuery);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="../images/logonawan.jpg" type="image/jpg"> 
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        body {
            background: linear-gradient(135deg, #f4e9dd, #d4a97b);
            font-family: 'Poppins', sans-serif;
            animation: fadeIn 1s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            margin-top: 50px;
            max-width: 700px;
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            animation: slideIn 1s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        h1 {
            color: #6f4e37;
            font-weight: 600;
            text-align: center;
            animation: fadeIn 1.2s ease-in-out;
        }

        .btn-custom {
            background-color: #6f4e37;
            color: #fff;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #543626;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-back {
            background-color: #868e96;
            color: #fff;
            border-radius: 10px;
            padding: 10px 20px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-back:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }

        label {
            font-weight: 500;
            color: #6c757d;
        }

        .form-control:focus, .form-select:focus {
            border-color: #6f4e37;
            box-shadow: 0 0 5px rgba(111, 66, 55, 0.5);
        }

        .form-control, .form-select {
            border-radius: 10px;
        }

        @media (max-width: 576px) {
            .container {
                margin-top: 20px;
                padding: 15px;
            }

            h1 {
                font-size: 1.5rem;
            }

            label {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="bi bi-plus-circle icon-wrapper"></i> Tambah Produk</h1>
        <?php if ($message): ?>
            <div class="alert alert-info text-center mt-3">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="product_name" class="form-label"><i class="bi bi-box-seam"></i> Nama Produk</label>
                <input type="text" id="product_name" name="product_name" class="form-control" placeholder="Masukkan nama produk" required>
            </div>
            <div class="mb-3">
                <label for="product_price" class="form-label"><i class="bi bi-currency-dollar"></i> Harga Produk</label>
                <input type="number" id="product_price" name="product_price" class="form-control" placeholder="Masukkan harga produk" required onkeyup="formatRupiah(this)">
            </div>
            <div class="mb-3">
                <label for="product_description" class="form-label"><i class="bi bi-textarea-resize"></i> Deskripsi Produk</label>
                <textarea id="product_description" name="product_description" class="form-control" rows="4" placeholder="Tulis deskripsi produk" required></textarea>
            </div>
<div class="mb-3">
                <label for="product_detail" class="form-label"><i class="bi bi-file-earmark-text"></i> Detail Produk</label>
                <textarea id="product_detail" name="product_detail" class="form-control" rows="6" placeholder="Tulis detail tambahan seperti komposisi, cara pakai, dll."></textarea>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label"><i class="bi bi-tags"></i> Kategori</label>
                <select id="category_id" name="category_id" class="form-select" required>
                    <option value="">Pilih Kategori</option>
                    <?php while ($row = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?php echo $row['category_id']; ?>">
                            <?php echo htmlspecialchars($row['category_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="product_stock" class="form-label"><i class="bi bi-check-circle"></i> Stok Produk</label>
                <input type="number" id="product_stock" name="product_stock" class="form-control" placeholder="Masukkan jumlah stok" required min="0">
            </div>
            <div class="mb-3">
                <label for="product_image" class="form-label"><i class="bi bi-image"></i> Gambar Produk</label>
                <input type="file" id="product_image" name="product_image" class="form-control" accept="image/*" required>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-custom"><i class="bi bi-save"></i> Tambah Produk</button>
            </div>
        </form>
        <div class="mt-3 text-center">
            <a href="product.php" class="btn btn-back"><i class="bi bi-arrow-left-circle"></i> Kembali</a>
        </div>
    </div>
</body>
</html>
