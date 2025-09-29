<?php
session_start();
include 'koneksi.php';

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validasi input
    if (empty($email) || empty($password)) {
        $error = "Email dan Password tidak boleh kosong!";
    } else {
        // Query untuk mencari pelanggan berdasarkan email
        $query = "SELECT * FROM pelanggan WHERE pelanggan_email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $pelanggan = $result->fetch_assoc();
            
            // Verifikasi password
            if (password_verify($password, $pelanggan['pelanggan_password'])) {
                // Set session
                $_SESSION['pelanggan_id'] = $pelanggan['pelanggan_id'];
                $_SESSION['pelanggan_name'] = $pelanggan['pelanggan_name'];
                // Redirect ke halaman index.php
                header("Location: index.php");
                exit;
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Email tidak terdaftar!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts for Custom Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="images/logonawan.jpg" type="image/jpg">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('https://puncatraining.id/wp-content/uploads/2019/10/catering.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            color: #fff;
            margin: 0;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            animation: fadeIn 1s ease-in-out;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
            color: #333;
        }

        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        /* Update label color for better visibility */
        .form-group label {
            color: #333; /* Dark color for contrast */
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 15px 40px 15px 40px;
            margin-bottom: 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
            font-size: 1rem;
            transition: all 0.3s ease-in-out;
        }

        .form-group input:focus {
            border-color: #ff7b00;
            box-shadow: 0 0 8px rgba(255, 123, 0, 0.6);
        }

        .form-group .icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #ff7b00;
            font-size: 18px;
        }

        .btn-login {
            background: linear-gradient(135deg, #ff7b00, #ff9e00);
            color: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            width: 100%;
            font-weight: bold;
            font-size: 1.1rem;
            transition: background 0.3s ease, transform 0.3s;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #ff9e00, #ff7b00);
            transform: scale(1.05);
        }

        .register-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #333;
        }

        .register-link a {
            color: #ff7b00;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 10px;
        }

        .forgot-password a {
            color: #ff7b00;
            font-weight: bold;
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .login-container {
                padding: 30px;
                width: 80%; /* Menyesuaikan dengan lebar layar */
            }
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 20px;
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <i class="fas fa-envelope icon"></i>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <i class="fas fa-lock icon"></i>
            </div>
            <button type="submit" name="submit" class="btn-login">Login</button>

            <?php if (isset($error)) { echo "<p style='color:red; text-align:center;'>$error</p>"; } ?>
        </form>

    

        <div class="register-link">
            Belum punya akun? <a href="register.php">Daftar disini</a>
        </div>
    </div>

    <!-- Bootstrap JS & Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>
</html>
