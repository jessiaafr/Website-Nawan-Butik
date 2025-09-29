<?php
// Include koneksi database
include '../koneksi.php';
session_start();

// Cek jika user sudah login (misalnya dengan cek session)
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Arahkan ke halaman login jika belum login
    exit;
}

// Ambil data admin dari database
$admin_id = $_SESSION['admin_id']; // Asumsi admin_id disimpan di session
$query = "SELECT * FROM admin WHERE admin_id = '$admin_id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $admin = mysqli_fetch_assoc($result);
} else {
    echo "Data admin tidak ditemukan!";
    exit;
}

// Variabel untuk menyimpan pesan error dan success
$error = '';
$success = '';

// Proses jika form di-submit untuk mengganti password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verifikasi password lama
    if (password_verify($current_password, $admin['password'])) {
        // Cek apakah password baru dan konfirmasi password cocok
        if ($new_password == $confirm_password) {
            // Enkripsi password baru
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password di database
            $update_query = "UPDATE admin SET password = '$hashed_password' WHERE admin_id = '$admin_id'";

            if (mysqli_query($conn, $update_query)) {
                $success = "Password berhasil diperbarui.";
            } else {
                $error = "Gagal memperbarui password.";
            }
        } else {
            $error = "Konfirmasi password tidak cocok.";
        }
    } else {
        $error = "Password lama salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../images/logonawan.jpg" type="image/jpg"> 
    <style>
        body {
            background-color: #6f4f37; /* Coklat */
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-label {
            font-weight: bold;
        }
        .alert {
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        .profile-card {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 10px;
        }
        .row {
            margin-top: 20px;
        }
        .text-primary {
            color: #6f4f37;
        }
        .icon-container {
            font-size: 20px;
        }
        .icon-container i {
            margin-right: 10px;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <h2 class="text-center text-primary"><i class="fas fa-user-circle"></i> Profil Admin</h2>

        <?php
        if ($error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
        if ($success) {
            echo "<div class='alert alert-success'>$success</div>";
        }
        ?>

        <div class="row">
            <div class="col-md-6">
                <div class="profile-card">
                    <!-- Tampilkan data admin -->
                    <div class="mb-3">
                        <label for="admin_name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="admin_name" value="<?php echo $admin['admin_name']; ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" value="<?php echo $admin['username']; ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="admin_telp" class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control" id="admin_telp" value="<?php echo $admin['admin_telp']; ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="admin_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="admin_email" value="<?php echo $admin['admin_email']; ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="admin_address" class="form-label">Alamat</label>
                        <textarea class="form-control" id="admin_address" disabled><?php echo $admin['admin_addres']; ?></textarea>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <h3 class="text-primary"><i class="fas fa-lock"></i> Ubah Password</h3>
                <form action="profile.php" method="POST">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Lama</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="change_password"><i class="fas fa-sync-alt"></i> Ubah Password</button>
                </form>
            </div>
        </div>

        <a href="index.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Kembali</a>
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
