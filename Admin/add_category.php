<?php
session_start();

// Include the database connection
include '../koneksi.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = htmlspecialchars($_POST['category_name']);
    if (!empty($category_name)) {
        $query = "INSERT INTO category (category_name) VALUES ('$category_name')";
        if (mysqli_query($conn, $query)) {
            $success_message = "Kategori berhasil ditambahkan!";
        } else {
            $error_message = "Gagal menambahkan kategori. Silakan coba lagi.";
        }
    } else {
        $error_message = "Nama kategori tidak boleh kosong.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
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
            margin-top: 60px;
        }
        .form-container {
            background: #fff7f0;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }
        .btn-custom {
            background: #6f4e37;
            color: white;
            border: none;
            border-radius: 50px;
            transition: background 0.3s ease;
        }
        .btn-custom:hover {
            background: #543626;
        }
        .btn-secondary {
            background: #d4a373;
            border: none;
            border-radius: 50px;
        }
        .btn-secondary:hover {
            background: #b07c4f;
        }
        .alert {
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 20px;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .alert.show {
            opacity: 1;
        }
        footer {
            color: #6f4e37;
            margin-top: 40px;
        }
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        @media (max-width: 576px) {
            .container {
                margin-top: 30px;
            }
            .form-container {
                padding: 20px;
            }
        }
        #alertMessage {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    width: auto;
    min-width: 250px;
    max-width: 300px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

/* Untuk HP: pindah ke tengah atas */
@media (max-width: 576px) {
    #alertMessage {
        left: 50%;
        transform: translateX(-50%);
        right: auto;
    }
}

    </style>
</head>
<body>

    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4 animate__animated animate__fadeInDown">
                <i class="fas fa-plus-circle"></i> Tambah Kategori
            </h2>

            <!-- Success and Error Messages -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success show" id="alertMessage">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger show" id="alertMessage">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Add Category Form -->
            <form method="POST">
                <div class="mb-3">
                    <label for="category_name" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Masukkan nama kategori" required>
                </div>
                <button type="submit" class="btn btn-custom w-100">Tambah</button>
            </form>

            <!-- Back Button -->
            <div class="text-center mt-3">
                <a href="kategori.php" class="btn btn-secondary w-100">Kembali</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-5">
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
