-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Waktu pembuatan: 10 Apr 2025 pada 16.50
-- Versi server: 8.3.0
-- Versi PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `catering`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `username` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `admin_telp` varchar(14) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `admin_email` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `admin_addres` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `username`, `password`, `admin_telp`, `admin_email`, `admin_addres`) VALUES
(1, 'Administrator', 'admin', '$2y$10$K0JFS.LuTFaRai3ViRf0tuCQgv4m298er4rc2ELTRwe83jqdkMn12', '088213413079', 'admin@gmail.com', 'Kota Bekasi, Jatiasih');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `cart_id` int NOT NULL AUTO_INCREMENT,
  `pelanggan_id` int DEFAULT NULL,
  `session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `idx_cart_pelanggan` (`pelanggan_id`),
  KEY `idx_cart_session` (`session_id`(250))
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `cart`
--

INSERT INTO `cart` (`cart_id`, `pelanggan_id`, `session_id`, `product_id`, `quantity`, `created_at`) VALUES
(7, NULL, '9nsgpuafjn4gajanvr3n765qsp', 3, 1, '2025-04-07 09:34:37'),
(13, NULL, 'j2hhj8ug4r0o9bak0p75rbl9rf', 3, 1, '2025-04-07 11:14:25'),
(15, NULL, 's0o1gjbko33b62o3mpcuhfrdjo', 3, 1, '2025-04-09 17:26:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(9, 'Paket A'),
(10, 'Paket B'),
(12, 'Paket Ikan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pesanan`
--

DROP TABLE IF EXISTS `detail_pesanan`;
CREATE TABLE IF NOT EXISTS `detail_pesanan` (
  `detail_id` int NOT NULL AUTO_INCREMENT,
  `pesanan_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int DEFAULT NULL,
  `price` int DEFAULT NULL,
  PRIMARY KEY (`detail_id`),
  KEY `pesanan_id` (`pesanan_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`detail_id`, `pesanan_id`, `product_id`, `quantity`, `price`) VALUES
(32, 27, 3, 988, 29640000),
(33, 28, 3, 2, 60000),
(34, 29, 3, 1, 30000),
(35, 30, 3, 1, 30000),
(36, 31, 3, 2, 60000),
(37, 32, 3, 1, 30000),
(38, 33, 3, 3, 90000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `metode_pembayaran`
--

DROP TABLE IF EXISTS `metode_pembayaran`;
CREATE TABLE IF NOT EXISTS `metode_pembayaran` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `payment_method` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_details` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_logo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `metode_pembayaran`
--

INSERT INTO `metode_pembayaran` (`payment_id`, `payment_method`, `payment_details`, `payment_logo`) VALUES
(1, 'BCA', 'Nomor Rekening: 8191010010100', '../images/bca.jpg'),
(2, 'DANA', 'Nomor Rekening: 089602471192', '../images/dana.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggan`
--

DROP TABLE IF EXISTS `pelanggan`;
CREATE TABLE IF NOT EXISTS `pelanggan` (
  `pelanggan_id` int NOT NULL AUTO_INCREMENT,
  `pelanggan_name` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pelanggan_email` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pelanggan_password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `pelanggan_telp` varchar(14) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pelanggan_address` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`pelanggan_id`),
  UNIQUE KEY `pelanggan_email` (`pelanggan_email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pelanggan`
--

INSERT INTO `pelanggan` (`pelanggan_id`, `pelanggan_name`, `pelanggan_email`, `pelanggan_password`, `pelanggan_telp`, `pelanggan_address`) VALUES
(3, 'Haical ravinda rassya', 'haical@gmail.com', '$2y$10$BT8WBFEIPPB6UHzH0c.unO/b08KI1YTBKRJkoIEpuZGtcffLx/Zzm', '089602471192', 'Bekasi'),
(4, 'ZICO MARCHELLINO', 'zicomarchellino2@gmail.com', '$2y$10$jkFr8caM/3HipGxGvjOGyO2yKYawFi0ihg2UBpD.rJ90Eybsem84K', '081288905250', 'JL NAROGONG MEGAH'),
(5, 'kemal', 'kemal@kemal.com', '$2y$10$nD/ef964mFazg4y/P24H6uOTxtV57QfuVI94pCzOk.A0Y4z1iQJ06', '081288905250', 'jalan raya narogong');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

DROP TABLE IF EXISTS `pembayaran`;
CREATE TABLE IF NOT EXISTS `pembayaran` (
  `pembayaran_id` int NOT NULL AUTO_INCREMENT,
  `pesanan_id` int NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_method` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_status` varchar(30) COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `total_paid` int DEFAULT NULL,
  PRIMARY KEY (`pembayaran_id`),
  KEY `pesanan_id` (`pesanan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan`
--

DROP TABLE IF EXISTS `pesanan`;
CREATE TABLE IF NOT EXISTS `pesanan` (
  `pesanan_id` int NOT NULL AUTO_INCREMENT,
  `pelanggan_id` int DEFAULT NULL,
  `total_price` int DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status_pesanan` varchar(30) COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `payment_status` enum('LUNAS','BELUM LUNAS') COLLATE utf8mb4_general_ci NOT NULL,
  `payment_proof` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pelanggan_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pelanggan_address` text COLLATE utf8mb4_general_ci,
  `pelanggan_kota` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_method` enum('Bank Transfer','COD') COLLATE utf8mb4_general_ci NOT NULL,
  `pelanggan_provinsi` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pelanggan_kodepos` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pelanggan_negara` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pelanggan_nohp` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notif_dismissed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`pesanan_id`),
  KEY `pelanggan_id` (`pelanggan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pesanan`
--

INSERT INTO `pesanan` (`pesanan_id`, `pelanggan_id`, `total_price`, `order_date`, `status_pesanan`, `payment_status`, `payment_proof`, `pelanggan_name`, `pelanggan_address`, `pelanggan_kota`, `payment_method`, `pelanggan_provinsi`, `pelanggan_kodepos`, `pelanggan_negara`, `pelanggan_nohp`, `notif_dismissed`) VALUES
(27, NULL, 29640000, '2025-04-07 10:52:03', 'Dikirim', 'LUNAS', '1744023123_BANNER 17AN.png', 'MIKA CHRISNOVA', 'tambun selatan', 'BEKASI', 'Bank Transfer', 'Jawa Barat', '17115', 'Indonesia', '081288905251', 0),
(28, NULL, 60000, '2025-04-07 11:06:20', 'Pending', 'LUNAS', '1744023980_pfp.png', 'MIKA CHRISNOVA', 'tambun selatan', 'BEKASI', 'Bank Transfer', 'Jawa Barat', '17115', 'Indonesia', '081288905251', 1),
(29, NULL, 30000, '2025-04-09 17:04:15', 'Pending', 'LUNAS', '', 'MIKA CHRISNOVA', 'tambun selatan', 'BEKASI', 'Bank Transfer', 'Jawa Barat', '17115', 'Indonesia', '081288905251', 1),
(30, NULL, 30000, '2025-04-10 07:01:18', 'Pending', 'LUNAS', '', 'MIKA CHRISNOVA', 'tambun selatan', 'BEKASI', 'COD', 'Jawa Barat', '17115', 'Indonesia', '081288905251', 1),
(31, NULL, 60000, '2025-04-10 07:01:56', 'Selesai', 'LUNAS', '', 'MIKA CHRISNOVA', 'tambun selatan', 'BEKASI', 'COD', 'Jawa Barat', '17115', 'Indonesia', '081288905251', 0),
(32, NULL, 30000, '2025-04-10 07:30:53', 'Pending', 'LUNAS', '', 'MIKA CHRISNOVA', 'tambun selatan', 'BEKASI', 'COD', 'Jawa Barat', '17115', 'Indonesia', '081288905251', 1),
(33, NULL, 90000, '2025-04-10 16:44:44', 'Pending', 'LUNAS', '', 'MIKA CHRISNOVA', 'tambun selatan', 'BEKASI', 'COD', 'Jawa Barat', '17115', 'Indonesia', '081288905251', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `product_name` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `product_price` int DEFAULT NULL,
  `product_description` text COLLATE utf8mb4_general_ci,
  `product_image` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `product_stock` varchar(999) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `product`
--

INSERT INTO `product` (`product_id`, `category_id`, `product_name`, `product_price`, `product_description`, `product_image`, `product_stock`, `data_created`) VALUES
(2, 10, 'Ayam kecap', 50000, 'Potongan ayam yang dimasak hingga empuk dan meresap dengan bumbu kecap manis khas, dipadukan dengan rempah-rempah pilihan seperti bawang putih, bawang merah, dan sedikit jahe. Rasanya manis gurih dengan sentuhan aroma karamel yang menggoda. Cocok dinikmati dengan nasi hangat.\r\n\r\nCocok untuk: Menu rumahan, catering harian, nasi kotak\r\nHighlight: Rasa manis gurih khas Indonesia, disukai segala usia', 'paket_ayam.jpg', '0', '2024-12-26 13:28:51'),
(3, 9, 'Cah Buncis Putren / Acar kucin', 30000, 'Tumisan segar dari buncis muda dan jagung putren yang dimasak dengan bumbu pilihan. Perpaduan rasa gurih dan manis alami dari sayuran segar ini menghasilkan hidangan sehat yang lezat dan bergizi. Cocok sebagai lauk pendamping nasi maupun menu diet harian Anda.\r\n\r\nCocok untuk: Makan siang, catering sehat, menu harian\r\nHighlight: Rendah kalori, tinggi serat, tanpa MSG\r\n\r\n🥒 Acar Kucin (Acar Kuning Cincang)\r\nAcar khas dengan potongan kecil sayuran seperti wortel, mentimun, dan bawang yang dimasak dalam bumbu kuning rempah-rempah. Rasanya segar, sedikit asam, dan gurih—pas banget untuk menyeimbangkan hidangan utama yang berbumbu kuat atau berminyak.\r\n\r\nCocok untuk: Pelengkap lauk goreng/bakar, menu prasmanan\r\nHighlight: Segar, menyegarkan lidah, non-pedas', 'paket_ayam1.jpg', '0', '2024-12-26 13:31:53'),
(4, 12, 'Ikan Asin', 50000, 'Ikan Asin Goreng\r\nIkan asin pilihan yang digoreng hingga renyah keemasan, memberikan cita rasa gurih yang khas dan menggugah selera. Cocok jadi lauk pendamping nasi hangat, sambal, dan sayur bening—membawa nuansa masakan rumahan yang autentik.\r\n\r\nCocok untuk: Menu tradisional, nasi rames, lauk pelengkap\r\nHighlight: Gurih, renyah, awet disimpan', 'paketikan.jpg', '0', '2024-12-26 13:41:30');

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`pesanan_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`pesanan_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`pelanggan_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
