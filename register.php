<?php
session_start();
include 'koneksi.php';

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $telp = $_POST['telp'];
    $address = $_POST['address'];

    // Validasi password dan konfirmasi password
    if ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Cek apakah email sudah terdaftar
        $query = "SELECT * FROM pelanggan WHERE pelanggan_email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            // Query untuk menyimpan data pelanggan baru
            $query = "INSERT INTO pelanggan (pelanggan_name, pelanggan_email, pelanggan_password, pelanggan_telp, pelanggan_address) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('sssss', $name, $email, $hashed_password, $telp, $address);

            if ($stmt->execute()) {
                $_SESSION['pelanggan_name'] = $name;
                $_SESSION['pelanggan_email'] = $email;
                header("Location: login.php"); // Redirect ke halaman login setelah registrasi berhasil
                exit;
            } else {
                $error = "Terjadi kesalahan saat mendaftar!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Dulu Yuk</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"> <!-- Poppins Font -->
    <style>
        body {
            background: linear-gradient(to right, rgba(255, 204, 153, 0.7), rgba(255, 204, 153, 0.9)), url('https://source.unsplash.com/1600x900/?nature,background') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
            color: white;
            margin: 0;
            padding: 0;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.8); /* Transparan putih */
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background-color: #f4a261;
            color: white;
            border-radius: 15px 15px 0 0;
            font-weight: bold;
            font-size: 24px;
        }

        .btn-primary {
            background-color: #f4a261;
            border: none;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-primary:hover {
            background-color: #e07b3c;
        }

        .form-control, .btn {
            border-radius: 10px;
        }

        .alert {
            border-radius: 10px;
        }

        .container {
            padding: 50px 0;
        }

        .form-label {
            font-weight: bold;
        }

        .icon {
            font-size: 40px;
            margin-bottom: 20px;
        }

        .text-center a {
            color: #f4a261;
            text-decoration: none;
        }

        .text-center a:hover {
            color: #e07b3c;
        }

        /* Responsif - memastikan elemen menyesuaikan di perangkat mobile */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .card {
                padding: 20px;
            }

            .card-header {
                font-size: 20px;
            }

            .form-control, .btn {
                font-size: 14px;
            }

            .icon {
                font-size: 30px;
            }

            .btn-primary {
                font-size: 14px;
                padding: 10px;
            }

            .mb-3 {
                margin-bottom: 1rem !important;
            }

            .form-label {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-header text-center">
                        <i class="bi bi-person-add icon"></i> Daftar Dulu Yuk
                    </div>
                    <div class="card-body">
                        <form action="register.php" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama:</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password:</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="telp" class="form-label">Nomor Telepon:</label>
                                <input type="text" id="telp" name="telp" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat:</label>
                                <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary w-100">Daftar</button>
                        </form>

                        <?php if (isset($error)) { echo "<div class='alert alert-danger mt-3'>$error</div>"; } ?>

                        <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Login disini</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS & Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
