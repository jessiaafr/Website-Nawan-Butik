<?php
session_start();

// Include the database connection
include '../koneksi.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the product id is set and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$product_id = intval($_GET['id']);

// Fetch product details
$query = "SELECT * FROM product WHERE product_id = $product_id";
$result = mysqli_query($conn, $query);

// Check if the product exists
if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}

$product = mysqli_fetch_assoc($result);

// Handle form submission to update product details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_stock = mysqli_real_escape_string($conn, $_POST['product_stock']);
    $product_price = isset($_POST['product_price']) ? str_replace('.', '', $_POST['product_price']) : 0;
    $product_discount_price = isset($_POST['product_discount_price']) ? str_replace('.', '', $_POST['product_discount_price']) : 0;
    $product_description = mysqli_real_escape_string($conn, $_POST['product_description']);
    $product_detail = mysqli_real_escape_string($conn, $_POST['product_detail']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);

    // Handle image upload if a new image is provided
    $product_image = $product['product_image']; // Default to current image
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);

        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            $upload_dir = "../uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $upload_file = $upload_dir . basename($_FILES['product_image']['name']);

            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_file)) {
                $product_image = basename($_FILES['product_image']['name']);
            }
        }
    }

    // Update the product details in the database
    $update_query = "
        UPDATE product 
        SET product_name = '$product_name', 
            product_price = '$product_price',
            product_discount_price = '$product_discount_price',  
            product_stock = '$product_stock',
            product_description = '$product_description', 
            product_detail = '$product_detail',
            product_image = '$product_image', 
            category_id = '$category_id' 
        WHERE product_id = $product_id
    ";

    if (mysqli_query($conn, $update_query)) {
        $message = 'Produk berhasil diperbarui!';
        header("Location: product.php?message=" . urlencode($message));
        exit();
    } else {
        $message = 'Gagal memperbarui produk. Silakan coba lagi.';
    }
}

// Format price with dots for displaying
$formatted_price = number_format($product['product_price'], 0, ',', '.');

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../images/logonawan.jpg" type="image/jpg"> 
    <style>
        body {
            background: linear-gradient(to right, #f8f3eb, #efe4d8);
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        .navbar-brand img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .navbar-nav .nav-link {
            font-weight: bold;
            color: #6f4e37;
        }

        .navbar-nav .nav-link:hover {
            color: #543626;
            transition: color 0.3s ease;
        }

        .container {
            margin-top: 80px; /* To prevent navbar overlap */
            padding: 30px;
        }

        .btn-custom {
            background-color: #6f4e37;
            color: white;
            border: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #543626;
            transform: translateY(-5px);
        }

        .form-control, .form-select {
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 8px rgba(110, 140, 255, 0.7);
        }

        .alert {
            transition: opacity 0.5s ease;
        }

        .alert-info {
            background-color: #d9edf7;
            color: #31708f;
        }

        .form-text {
            font-size: 0.9rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .navbar-nav {
                text-align: center;
            }
            .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="../images/logonawan.jpg" alt="Nawan Butik Logo"> Nawan Butik
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="kategori.php"><i class="fas fa-th"></i> Kategori</a></li>
                    <li class="nav-item"><a class="nav-link" href="product.php"><i class="fas fa-box"></i> Kelola Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h1 class="text-center my-4">Edit Produk</h1>

        <!-- Display error or success message -->
        <?php if (isset($message)): ?>
            <div class="alert alert-info text-center">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Edit Product Form -->
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="product_name" class="form-label">Nama Produk</label>
                <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="product_price" class="form-label">Harga Produk</label>
                <input type="text" class="form-control" id="product_price" name="product_price" value="<?php echo htmlspecialchars($formatted_price); ?>" required>
            </div>
            <div class="mb-3">
                <label for="product_discount_price" class="form-label">Harga Diskon (Opsional)</label>
                <input type="text" name="product_discount_price" id="product_discount_price" class="form-control" placeholder="Harga Diskon">
                <small class="form-text text-muted">Kosongkan jika tidak ada diskon.</small>
            </div>

            <div class="mb-3">
                <label for="product_description" class="form-label">Deskripsi Produk</label>
                <textarea class="form-control" id="product_description" name="product_description" rows="4" required><?php echo htmlspecialchars($product['product_description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="product_detail" class="form-label">Detail Produk</label>
                <textarea class="form-control" id="product_detail" name="product_detail" rows="6" placeholder="Tulis detail tambahan seperti komposisi, cara pakai, dll."><?php echo htmlspecialchars($product['product_detail']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="product_stock" class="form-label">Stock Produk</label>
                <textarea class="form-control" id="product_stock" name="product_stock" rows="1" required><?php echo htmlspecialchars($product['product_stock']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Kategori Produk</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <?php
                    // Fetch categories for the dropdown
                    $category_query = "SELECT * FROM category";
                    $category_result = mysqli_query($conn, $category_query);
                    while ($category = mysqli_fetch_assoc($category_result)): ?>
                        <option value="<?php echo $category['category_id']; ?>" <?php echo ($product['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="product_image" class="form-label">Gambar Produk (Opsional)</label>
                <input type="file" class="form-control" id="product_image" name="product_image">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti gambar.</small>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-custom w-100">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript to format price with dots as user types
        document.getElementById('product_price').addEventListener('input', function (e) {
            var value = e.target.value.replace(/\./g, '').replace(/(\d)(\d{3})$/, '$1.$2');
            e.target.value = value;
        });
    </script>
    <script>
    document.getElementById('product_discount_price').addEventListener('input', function (e) {
    var value = e.target.value.replace(/\./g, '');
    if (value !== '') {
        value = parseInt(value).toLocaleString('id-ID');
    }
    e.target.value = value;
});
    </script>
</body>
</html>
