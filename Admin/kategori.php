<?php
session_start();

// Include the database connection
include '../koneksi.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Fetch categories
$query = "SELECT * FROM category";
$result = mysqli_query($conn, $query);

// Check if there is a message to display
$message = '';
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="icon" href="../images/logonawan.jpg" type="image/jpg"> 
    <style>
        body {
            background: linear-gradient(to right, #f3f4f6, #dcdde1);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 950px;
            padding: 40px 20px;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
        }
        .header-icon {
            color: #8e7d6d;
        }
        h1 {
            font-size: 2.8rem;
            color: #8e7d6d;
            text-align: center;
            font-weight: 600;
        }
        .btn-custom {
            background-color: #8e7d6d;
            color: white;
            border: none;
            border-radius: 25px;
            transition: background-color 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #6f5c44;
        }
        .btn-warning {
            background-color: #c5a56d !important;
            border: none;
        }
        .btn-warning:hover {
            background-color: #9e7b50 !important;
        }
        .btn-sm {
            padding: 8px 14px;
        }
        .table-container {
            background: #fafafa;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            margin-top: 30px;
        }
        .table-dark {
            background-color: #8e7d6d;
            color: white;
        }
        footer {
            text-align: center;
            margin-top: 30px;
            color: #8e7d6d;
            font-size: 14px;
        }
        .alert {
            margin-top: 15px;
            margin-bottom: 15px;
            font-size: 15px;
            padding: 8px 16px;
            border-radius: 6px;
            display: none;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        .alert.show {
            display: block;
            opacity: 1;
        }
        .alert-message {
            font-size: 14px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="../images/logonawan.jpg" alt="Nawan Butik Logo" class="logo" style="width: 20px;"> Nawan Butik
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i> Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php"><i class="fas fa-user-circle"></i> Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1><i class=></i> Kelola Kategori</h1>

        <!-- Display success or error message -->
        <?php if ($message): ?>
            <div class="alert alert-info text-center" id="alertMessage">
                <span class="alert-message"><?php echo $message; ?></span>
            </div>
        <?php endif; ?>

        <!-- Add Category Button -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <a href="index.php" class="btn btn-back" style="background: #6f5c44; color: white; border: none;
             border-radius: 25px; padding: 10px 20px;">
                <i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama
            </a>
            <a href="add_category.php" class="btn btn-custom">
                <i class="fas fa-plus-circle"></i> Tambah Kategori
            </a>
        </div>

        <!-- Category Table -->
        <div class="table-container p-3">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama Kategori</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['category_id']; ?></td>
                                <td><?php echo $row['category_name']; ?></td>
                                <td class="text-center">
                                    <a href="edit_category.php?id=<?php echo $row['category_id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="delete_category.php?id=<?php echo $row['category_id']; ?>" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">Belum ada kategori yang ditambahkan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p class="text-muted">&copy; <?php echo date("Y"); ?> Admin Panel - Semua hak dilindungi</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Menampilkan alert secara perlahan
        window.onload = function() {
            const alertMessage = document.getElementById('alertMessage');
            if (alertMessage) {
                alertMessage.classList.add('show');  // Tampilkan alert dengan transisi
                setTimeout(function() {
                    alertMessage.classList.remove('show');  // Sembunyikan alert setelah 3 detik
                }, 3000);
            }
        };
    </script>
</body>
</html>
