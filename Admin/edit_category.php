<?php
session_start();

// Include the database connection
include '../koneksi.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Check if category id is provided
if (!isset($_GET['id'])) {
    header("Location: edit_category.php?message=ID kategori tidak ditemukan");
    exit();
}

$category_id = $_GET['id'];

// Fetch the category data based on the id
$query = "SELECT * FROM category WHERE category_id = '$category_id'";
$result = mysqli_query($conn, $query);
$category = mysqli_fetch_assoc($result);

if (!$category) {
    header("Location: edit_category.php?message=Kategori tidak ditemukan");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);

    // Update category in the database
    $update_query = "UPDATE category SET category_name = '$category_name' WHERE category_id = '$category_id'";
    if (mysqli_query($conn, $update_query)) {
        header("Location: edit_category.php?id=$category_id&success=1");
        exit();
    } else {
        $message = "Terjadi kesalahan saat memperbarui kategori. Coba lagi.";
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori</title>
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
        .container {
            max-width: 600px;
            padding: 40px 20px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
        }
        .btn-custom {
            background-color: #6f4e37;
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #543626;
        }
        footer {
            text-align: center;
            margin-top: 30px;
            color: #6f4e37;
        }
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        #alertMessage {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    width: auto;
    max-width: 100%;
    padding: 15px 20px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    border-radius: 8px;
    white-space: nowrap;  /* Agar teks tidak terputus menjadi dua baris */
    overflow: hidden;     /* Menghindari overflow jika teks terlalu panjang */
    text-overflow: ellipsis;  /* Menambahkan "..." jika teks terlalu panjang */
}


    </style>
</head>
<body>
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success text-center" id="alertMessage">
        <i class="fas fa-check-circle"></i> Berhasil Simpan Perubahan
    </div>
<?php endif; ?>

    <div class="container">
        <h1><i class="fas fa-edit"></i> Edit Kategori</h1>

        <!-- Display success or error message -->
        <?php if (isset($message)): ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Edit Category Form -->
        <form method="POST">
            <div class="mb-3">
                <label for="category_name" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" id="category_name" name="category_name" value="<?php echo htmlspecialchars($category['category_name']); ?>" required>
            </div>

            <button type="submit" class="btn btn-custom w-100">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </form>

        <div class="mt-3 text-center">
            <a href="kategori.php" class="btn btn-secondary">Kembali ke Daftar Kategori</a>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p class="text-muted">&copy; <?php echo date("Y"); ?> Admin Panel - Semua hak dilindungi</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Sembunyikan alert setelah 3 detik
    window.onload = function () {
        const alertBox = document.getElementById('alertMessage');
        if (alertBox) {
            setTimeout(() => {
                alertBox.style.display = 'none';
            }, 3000);
        }
    };
</script>
</body>
</html>
