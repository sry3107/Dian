-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 15, 2024 at 03:17 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sisfo-masjid`
--

-- --------------------------------------------------------

--
-- Table structure for table `akun`
--

CREATE TABLE `akun` (
  `id_akun` int NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `role` enum('Pengurus','Mubaligh') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'inactive',
  `activation_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `akun`
--

INSERT INTO `akun` (`id_akun`, `username`, `password`, `email`, `role`, `status`, `activation_code`, `created_at`, `updated_at`) VALUES
(4, 'mahrus122', '$2y$10$GE/45AFZ3gmm7vP9X5BsJuQJr6LfCPNDumrqGSOoogOiq/zkM1u4m', 'mahruss9856@gmail.com', 'Mubaligh', 'active', NULL, '2024-12-09 02:59:05', '2024-12-14 15:01:49'),
(5, 'rizaldyy07', '$2y$10$AZ1rvwAGGr5Kkz9aYGDWXe49x5t6tzHbWzxqGMIpcwYvzuCMEyhWu', 'rizaldyanto79@gmail.com', 'Mubaligh', 'active', NULL, '2024-12-09 04:00:09', '2024-12-14 15:01:20'),
(16, 'yasir04', '$2y$10$FJfgGjjctOKwmGDkjrloiet9wQtXBW.0NgS1J8eTxtDHAeBq7N.r.', 'yasirdimba@gmail.com', 'Pengurus', 'active', NULL, '2024-12-13 03:12:38', '2024-12-13 03:12:38'),
(17, 'adimulyadi01', '$2y$10$/bbQOiHBpiJoVjZqnWuaFeI9REgT.JUrHSKEcTWZwMHlFGTi7PBn2', 'mulyadiadi001@gmail.com', 'Pengurus', 'active', NULL, '2024-12-13 03:28:23', '2024-12-14 15:00:50'),
(18, 'nurdin1877', '$2y$10$AvhYswoXnTJF1FDAiwy5xu7x5uBs5Q73ygdx0r30dtykRRo9.l.ie', 'nurdinsunu12@gmail.com', 'Pengurus', 'active', NULL, '2024-12-13 07:27:50', '2024-12-14 15:00:06'),
(19, 'abufatih05', '$2y$10$eCPzCjSyZOsj7h.uqXeoMuFQ3Q1GMD2Z8MVqJDMi4oBt7N95mIQRu', 'abfatihh05@gmail.com', 'Mubaligh', 'active', NULL, '2024-12-13 13:30:47', '2024-12-13 13:30:47'),
(20, 'habibhusein12', '$2y$10$p3oAXv9ChpVUDh6.qtK0z.JqOwX5T6HOmPp55fE4flE0UnF.uO4Nu', 'huseinhamid189@gmail.com', 'Mubaligh', 'active', NULL, '2024-12-13 15:32:21', '2024-12-14 15:02:23'),
(27, 'ahmadd78', '$2y$10$Fnn5YsVKriE.AsqfEkS8CetF/fhCGliMA9BI7aCDhnU45jXtm6aIO', 'ahmdd86816@gmail.com', 'Pengurus', 'active', NULL, '2024-12-13 18:04:32', '2024-12-14 15:03:00'),
(28, 'abdulrojak19', '$2y$10$0K5UsQ5jKOK1S2h.mbiEOuP4HIwSkYEPnjbGCGbGkovD4DwmkdoqK', 'abdlrojak1980@gmail.com', 'Mubaligh', 'active', NULL, '2024-12-14 15:47:10', '2024-12-14 15:47:10'),
(29, 'gassingg17', '$2y$10$cbXwoGUIc99.9bs5kr6o5eyradLjMsJtRwcKQazSkpk9I.mR9tZHe', 'gassing890@gmail.com', 'Mubaligh', 'active', NULL, '2024-12-14 15:56:27', '2024-12-15 11:41:39'),
(30, 'abdulhalimm02', '$2y$10$WtFS4dt1tS4M5g0NNPEquekwzVNyBxlnOzrL3UA96KfcNoLoa4XNC', 'abdulhalimpago@gmail.com', 'Mubaligh', 'inactive', 'e549c169950154240f345f48bbf0f30d', '2024-12-14 15:57:36', '2024-12-14 15:57:36'),
(31, 'haerul31', '$2y$10$HcgxsuogH5Kg/oTn0PBewOejOWWolytPXvJydLAap5EfsaHNVHmiK', 'haerulanwa4910@gmail.com', 'Mubaligh', 'active', NULL, '2024-12-14 16:05:17', '2024-12-14 16:05:17'),
(32, 'junaid1886', '$2y$10$d8HThdU31ZKakHDWD/l/Buzf/kDbIKkM8zU6y8j6v/XahCVgFWaHO', 'junaidibrahim295@gmail.com', 'Mubaligh', 'active', NULL, '2024-12-14 16:08:14', '2024-12-14 16:08:14'),
(33, 'kasimm77', '$2y$10$T3V5TWENUVe1lCylMlZqi.M8WbeVSQr4cxARalFhPytf9/PlV8QsO', 'muhammadkasim3993@gmail.com', 'Mubaligh', 'active', NULL, '2024-12-14 16:11:30', '2024-12-14 16:11:30'),
(34, 'zainall22', '$2y$10$tJs7J0/1batxdyb61Lad1u3PgFMlJYQuEP9nbdrhod6VfiBsZnBg2', 'zainal.abidin4128@gmail.com', 'Mubaligh', 'active', NULL, '2024-12-14 16:15:20', '2024-12-14 16:15:20'),
(35, 'Ibrahim910', '$2y$10$bVfEdpv/OKgd2EP0Rf36/OaG9sWNP4vFCghE4OgqxuRc60MudAbZa', 'ibrahimnurdin910@gmail.com', 'Mubaligh', 'active', NULL, '2024-12-15 11:58:08', '2024-12-15 11:58:08'),
(36, 'basirbasirr54', '$2y$10$TuUykpD9OPQY.9jKSTea0OcbWtwe9iHSrwqkTmAWrKrqiafysJiCS', 'muhbasir264@gmail.com', 'Mubaligh', 'active', NULL, '2024-12-15 13:02:08', '2024-12-15 13:02:08'),
(37, 'Hakimjurumiaa5', '$2y$10$rybNSwH8YzoM8qH2w0ewEuRvWU1YHK9ew3RG/eW2CN84QPYE.rp.2', 'jrmiahakim810@gmail.com', 'Mubaligh', 'active', NULL, '2024-12-15 13:42:16', '2024-12-15 14:00:09'),
(38, 'wahyubstani002', '$2y$10$CX1Fv7d6r53qK7cC8qjAeOmwQmC1r1YBX.UKAn6c.F44BaILMKa1G', 'bstniiwahyu32@gmail.com', 'Mubaligh', 'active', NULL, '2024-12-15 14:01:23', '2024-12-15 14:07:53'),
(39, 'Zakaria1970', '$2y$10$TEC/7cT/PFEIOv7oWExwC.WIRir3qss8kVJQXB3q52GYoUMFJgDSm', 'zkariaa6670@gmail.com', 'Mubaligh', 'active', NULL, '2024-12-15 14:09:13', '2024-12-15 14:12:34'),
(40, 'Hamzahhmuin02', '$2y$10$47uFv5Ih/zYY5UYt4e6bDOibrZUt7Ibp5GI/jMZx5FgVkC4u.bMrq', 'hamzahhmn121@gmail.com', 'Mubaligh', 'active', NULL, '2024-12-15 14:14:08', '2024-12-15 14:20:02');

-- --------------------------------------------------------

--
-- Table structure for table `artikel`
--

CREATE TABLE `artikel` (
  `id_artikel` int NOT NULL,
  `judul_artikel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deskripsi_artikel` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tempat_artikel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal_artikel` date NOT NULL,
  `link_artikel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `artikel`
--

INSERT INTO `artikel` (`id_artikel`, `judul_artikel`, `deskripsi_artikel`, `tempat_artikel`, `tanggal_artikel`, `link_artikel`, `created_at`, `updated_at`) VALUES
(3, 'Maulid Nabi Muhammad SAW 1443 Hijriah', 'Peringatan Maulid Nabi Muhammad SAW Tahun 1443 Hijriah Bersama BKMT Pangkajene', 'Masjid Raodhatul Muflihin', '2021-11-07', 'https://matakita.co/2021/11/08/bkmt-pangkajene-gelar-peringatan-maulid-nabi-muhammad-saw-di-masjid-raodhatul-muflihin/', '2024-12-12 17:18:42', '2024-12-12 17:28:00'),
(4, 'Wisata Religi di Raodhatul Muflihin, Masjid Megah di Pangkep', 'FAJAR.CO.ID, MAKASSAR -- Posisinya tepat di pinggir jalan Trans-Sulawesi. Jemaahnya para pengguna jalan. Dominan. Pangkajene dan Kepulauan (Pangkep) pernah dijuluki sebagai kabupaten religi.', 'Masjid Raodhatul Muflihin', '2019-05-23', 'https://fajar.co.id/2019/05/23/wisata-religi-di-raodhatul-muflihin-masjid-megah-di-pangkep/', '2024-12-12 17:24:13', '2024-12-12 17:24:13'),
(5, 'Ghirroh Syiar Islam: MASJID MEGAH RHAUDHATUL MUFLIHIN , KOTA ” BANDENG” PANGKEP SULSEL.', 'Masjid Raudhatul Muflihin, yang berwarna coklat dan mempunyai dua menara kembar yang menempel di bangunan masjid sehingga menjadikan masjid ini terlihat indah di pandang oleh mata.', 'Masjid Raodhatul Muflihin', '2019-11-25', 'https://menaramadinah.com/15749/15749.html', '2024-12-12 18:03:49', '2024-12-12 18:18:21'),
(6, 'Pangdam Hasanuddin Sholat Jumat dan Berikan Kultum di Masjid Raodhatul Muflihin Pangkep, Ini Katanya', 'Di awal kultumnya, Cucu langsung dari seorang ulama dan Pahlawan Nasional Andi Mappanyukki ini mengatakan bahwa kedatangan di Masjid Raodhatul Muflihin bukan karena jabatannya sebagai Pangdam melainkan karena takdir Allah SWT sebagai umat Islam dalam melaksanakan perintah Allah SWT.', 'Masjid Raodhatul Muflihin', '2022-07-29', 'https://kodam14hasanuddin-tniad.mil.id/berita/pangdam-hasanuddin-sholat-jumat-dan-berikan-kultum-di-masjid-raodhatul-muflihin-pangkep-ini-katanya/', '2024-12-12 18:21:04', '2024-12-12 18:21:04'),
(7, 'Tabligh Akbar', 'Tabligh Akbar bersama Ustadz Abdul Somad, Lc.,M.A., di ikuti oleh jemaah Masjid Raodhatul Muflihin dengan antusias.', 'Masjid Raodhatul Muflihin', '2024-06-01', 'https://www.instagram.com/p/C7ffnn9vs1y/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', '2024-12-12 19:05:18', '2024-12-12 19:05:18');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id_jadwal` int NOT NULL,
  `id_masjid` int NOT NULL,
  `id_pengurus` int DEFAULT NULL,
  `id_mubaligh` int DEFAULT NULL,
  `id_kegiatan` int NOT NULL,
  `kode_jadwal` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jadwal`
--

INSERT INTO `jadwal` (`id_jadwal`, `id_masjid`, `id_pengurus`, `id_mubaligh`, `id_kegiatan`, `kode_jadwal`, `created_at`, `updated_at`) VALUES
(24, 4, 12, 6, 57, 'JKJ', '2024-12-13 15:49:28', '2024-12-13 15:49:49'),
(26, 4, 11, 5, 67, 'JKJ', '2024-12-13 16:05:40', '2024-12-13 16:05:40'),
(27, 4, 11, 5, 66, 'JKJ', '2024-12-13 16:06:22', '2024-12-15 14:43:30'),
(28, 4, 11, 8, 68, 'JKJ', '2024-12-13 16:45:58', '2024-12-13 16:45:58'),
(29, 4, 11, 10, 47, 'JKJ', '2024-12-14 15:52:59', '2024-12-14 15:52:59'),
(30, 4, 12, 12, 50, 'JKJ', '2024-12-15 10:22:58', '2024-12-15 10:22:58'),
(31, 4, 11, 7, 46, 'JKJ', '2024-12-15 12:16:05', '2024-12-15 14:43:19'),
(32, 4, 11, 11, 48, 'JKJ', '2024-12-15 12:37:39', '2024-12-15 12:37:39'),
(33, 4, 11, 17, 53, 'JKJ', '2024-12-15 13:05:45', '2024-12-15 13:05:45'),
(34, 4, 12, 14, 51, 'JKJ', '2024-12-15 13:07:43', '2024-12-15 13:07:43'),
(35, 4, 12, 16, 55, 'JKJ', '2024-12-15 13:08:59', '2024-12-15 13:08:59'),
(36, 4, 11, 15, 70, 'JKJ', '2024-12-15 13:13:18', '2024-12-15 13:13:18'),
(37, 4, 12, 15, 71, 'JKJ', '2024-12-15 13:13:36', '2024-12-15 13:13:36'),
(38, 4, 11, 18, 72, 'JKJ', '2024-12-15 13:58:22', '2024-12-15 13:58:22'),
(39, 4, 12, 19, 74, 'JKJ', '2024-12-15 14:03:14', '2024-12-15 14:03:14'),
(40, 4, 11, 20, 58, 'JKJ', '2024-12-15 14:11:30', '2024-12-15 14:11:30'),
(41, 4, 11, 21, 73, 'JKJ', '2024-12-15 14:15:26', '2024-12-15 14:15:26');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id_kegiatan` int NOT NULL,
  `id_masjid` int NOT NULL,
  `kode_kegiatan` varchar(100) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `deskripsi_kegiatan` varchar(255) DEFAULT NULL,
  `tempat_kegiatan` varchar(200) DEFAULT NULL,
  `waktu_kegiatan` time NOT NULL,
  `tanggal_kegiatan` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kegiatan`
--

INSERT INTO `kegiatan` (`id_kegiatan`, `id_masjid`, `kode_kegiatan`, `nama_kegiatan`, `deskripsi_kegiatan`, `tempat_kegiatan`, `waktu_kegiatan`, `tanggal_kegiatan`, `created_at`, `updated_at`) VALUES
(46, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-01-31', '2024-12-13 13:39:45', '2024-12-13 13:39:45'),
(47, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-02-14', '2024-12-13 13:40:38', '2024-12-13 13:40:38'),
(48, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-02-28', '2024-12-13 13:41:21', '2024-12-13 13:41:21'),
(49, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-03-14', '2024-12-13 13:43:06', '2024-12-13 13:43:06'),
(50, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-03-28', '2024-12-13 13:43:48', '2024-12-13 13:43:48'),
(51, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-04-11', '2024-12-13 13:44:40', '2024-12-13 13:44:40'),
(52, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-04-25', '2024-12-13 13:45:24', '2024-12-13 13:45:24'),
(53, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-05-09', '2024-12-13 13:46:12', '2024-12-13 13:46:12'),
(54, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-05-23', '2024-12-13 13:47:01', '2024-12-13 13:47:01'),
(55, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-06-13', '2024-12-13 13:48:00', '2024-12-13 13:48:00'),
(56, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-06-27', '2024-12-13 13:49:04', '2024-12-13 13:49:04'),
(57, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-07-11', '2024-12-13 13:49:50', '2024-12-13 13:49:50'),
(58, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-07-25', '2024-12-13 13:51:03', '2024-12-13 13:51:03'),
(59, 4, 'KTH', 'Tahun Baru Hijriah', 'Peringatan Tahun Baru Islam: 1 Muharam 1446 H', 'Masjid Raodhatul Muflihin', '13:00:00', '2024-07-07', '2024-12-13 14:02:13', '2024-12-13 14:02:13'),
(60, 4, 'KTH', 'Tabligh Akbar', 'Tabligh Akbar bersama Ustadz Abdul Somad, Lc., MA.', 'Masjid Raodhatul Muflihin', '13:00:00', '2024-06-01', '2024-12-13 14:05:16', '2024-12-15 15:07:49'),
(61, 4, 'KTH', 'Isra Mi\'raj', 'Peringatan Isra Mi\'raj 1445 H', 'Masjid Raodhatul Muflihin', '20:00:00', '2024-02-08', '2024-12-13 14:07:27', '2024-12-13 14:07:27'),
(62, 4, 'KTH', 'Maulid Nabi Muhammad', 'Peringatan Maulid Nabi Muhammad 1446 H', 'Masjid Raodhatul Muflihin', '10:00:00', '2024-09-16', '2024-12-13 14:09:14', '2024-12-13 14:09:14'),
(63, 4, 'KTH', 'Idul Fitri', 'Hari Raya Idul Fitri 1445 H', 'Masjid Raodhatul Muflihin', '06:30:00', '2024-04-10', '2024-12-13 14:10:53', '2024-12-13 14:10:53'),
(64, 4, 'KTH', 'Idul Adha', 'Hari Raya Idul Adha 1445 H', 'Masjid Raodhatul Muflihin', '06:30:00', '2024-06-17', '2024-12-13 14:11:48', '2024-12-13 14:11:48'),
(65, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2024-09-27', '2024-12-13 15:03:22', '2024-12-13 15:03:22'),
(66, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-11-07', '2024-12-13 16:04:07', '2024-12-13 16:04:07'),
(67, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2024-11-08', '2024-12-13 16:04:57', '2024-12-13 16:04:57'),
(68, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2024-11-22', '2024-12-13 16:44:35', '2024-12-13 16:44:35'),
(69, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-11-21', '2024-12-13 16:45:17', '2024-12-13 16:45:17'),
(70, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2024-10-25', '2024-12-15 13:11:51', '2024-12-15 13:11:51'),
(71, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-10-24', '2024-12-15 13:12:37', '2024-12-15 13:12:37'),
(72, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-12-26', '2024-12-15 13:46:50', '2024-12-15 13:46:50'),
(73, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-08-08', '2024-12-15 13:55:15', '2024-12-15 13:55:15'),
(74, 4, 'KMG', 'Khutbah Jum\'at', 'Khutbah Jum\'at', 'Masjid Raodhatul Muflihin', '12:00:00', '2025-08-22', '2024-12-15 13:56:23', '2024-12-15 13:56:23');

-- --------------------------------------------------------

--
-- Table structure for table `masjid`
--

CREATE TABLE `masjid` (
  `id_masjid` int NOT NULL,
  `nama_masjid` varchar(100) NOT NULL,
  `alamat_masjid` varchar(200) NOT NULL,
  `tahun_berdiri` year NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `masjid`
--

INSERT INTO `masjid` (`id_masjid`, `nama_masjid`, `alamat_masjid`, `tahun_berdiri`, `created_at`, `updated_at`) VALUES
(4, 'Masjid Raodhatul Muflihin', 'Jl. Sultan Hasanuddin, Sanrangan, Baru-baru Towa', '1985', '2024-12-13 04:03:24', '2024-12-13 04:03:24');

-- --------------------------------------------------------

--
-- Table structure for table `mubaligh`
--

CREATE TABLE `mubaligh` (
  `id_mubaligh` int NOT NULL,
  `id_akun` int NOT NULL,
  `id_masjid` int NOT NULL,
  `nama_mubaligh` varchar(100) NOT NULL,
  `alamat_mubaligh` varchar(200) DEFAULT NULL,
  `no_telepon_mubaligh` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mubaligh`
--

INSERT INTO `mubaligh` (`id_mubaligh`, `id_akun`, `id_masjid`, `nama_mubaligh`, `alamat_mubaligh`, `no_telepon_mubaligh`, `created_at`, `updated_at`) VALUES
(5, 4, 4, 'Dr. KH. Muhammad Mahrus Amri, Lc., MA', 'Pangkajene', '081243428685', '2024-12-13 04:07:31', '2024-12-13 15:04:47'),
(6, 5, 4, 'Rizaldy Febrianto., S.Hi.', 'Pangkajene', '081243428685', '2024-12-13 04:09:04', '2024-12-13 15:42:09'),
(7, 19, 4, 'H. Abu Al Fatih Malik, S.Ag.', 'Makassar', '085210007099', '2024-12-13 13:32:30', '2024-12-13 13:32:30'),
(8, 20, 4, 'Habib Husain bin Ahmad Al-Hamid', 'Pangkajene', '082153139313', '2024-12-13 15:34:10', '2024-12-13 15:34:10'),
(10, 28, 4, 'Abdul Rojak, SH.,MH.,MA', 'Makassar', '081354944444', '2024-12-14 15:51:02', '2024-12-14 15:51:02'),
(11, 29, 4, 'Gassing., S.Ag.', 'Maros', '085242267419', '2024-12-15 10:13:06', '2024-12-15 10:13:06'),
(12, 31, 4, 'Haerul Anwar, S.Ag.,MA.', 'Minasatene', '085340343406', '2024-12-15 10:22:11', '2024-12-15 10:22:11'),
(13, 35, 4, 'Dr. Ibrahim Nurdin, S.Ag., M.Pd.', 'Pangkajene', '081342381921', '2024-12-15 12:07:35', '2024-12-15 12:07:35'),
(14, 32, 4, 'Junaid Ibrahim, S.Ag.', 'Pangkajene', '085341735148', '2024-12-15 12:53:05', '2024-12-15 12:53:05'),
(15, 33, 4, 'Drs. H. Muhammad Kasim, S.Ag', 'Minasatene', '085343551534', '2024-12-15 12:54:23', '2024-12-15 12:54:23'),
(16, 34, 4, 'Kh. Zainal Abidin., MA.', 'Barru', '081242174200', '2024-12-15 12:55:56', '2024-12-15 12:55:56'),
(17, 36, 4, 'Dr. Muh Basir, S.Ag.,M.Ag.', 'Baru-baru', '08134295545', '2024-12-15 13:04:02', '2024-12-15 13:04:02'),
(18, 37, 4, 'Dr. KH. Hakim Jurumia, Lc., MA.', 'Makassar', '088706040765', '2024-12-15 13:44:00', '2024-12-15 13:44:00'),
(19, 38, 4, 'Wahyu Bastani Al Banjari', 'Maros', '082346051664', '2024-12-15 14:02:47', '2024-12-15 14:02:47'),
(20, 39, 4, 'KH. Zakaria, Lc.', 'Makassar', '081289166684', '2024-12-15 14:10:29', '2024-12-15 14:10:29'),
(21, 40, 4, 'H. Hamzah Muin, S.Ag., M.Pd', 'Pangkajene', '085249507300', '2024-12-15 14:14:57', '2024-12-15 14:14:57');

-- --------------------------------------------------------

--
-- Table structure for table `pengurus`
--

CREATE TABLE `pengurus` (
  `id_pengurus` int NOT NULL,
  `id_akun` int NOT NULL,
  `id_masjid` int NOT NULL,
  `nama_pengurus` varchar(100) NOT NULL,
  `jabatan_pengurus` varchar(100) NOT NULL,
  `alamat_pengurus` varchar(200) DEFAULT NULL,
  `no_telepon_pengurus` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengurus`
--

INSERT INTO `pengurus` (`id_pengurus`, `id_akun`, `id_masjid`, `nama_pengurus`, `jabatan_pengurus`, `alamat_pengurus`, `no_telepon_pengurus`, `created_at`, `updated_at`) VALUES
(11, 16, 4, 'Muhamad Yasir, S.Sos.', 'Sekretaris', 'Sanrangan', '085299330534', '2024-12-13 04:04:40', '2024-12-13 04:04:40'),
(12, 17, 4, 'Mulyadi, A.Md.Pi.', 'Ketua', 'Sanrangan', '085299667997', '2024-12-13 04:06:07', '2024-12-13 18:08:36'),
(13, 18, 4, 'H. Nurdin Sunu', 'Bendahara', 'Sanrangan ', '081342320589', '2024-12-13 07:29:35', '2024-12-13 18:09:18'),
(14, 27, 4, 'Ahmad H. Amir, S.Pd.', 'Wakil', 'Sanrangan', '082292050650', '2024-12-13 18:07:40', '2024-12-13 18:08:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `akun`
--
ALTER TABLE `akun`
  ADD PRIMARY KEY (`id_akun`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `artikel`
--
ALTER TABLE `artikel`
  ADD PRIMARY KEY (`id_artikel`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_masjid` (`id_masjid`),
  ADD KEY `id_pengurus` (`id_pengurus`),
  ADD KEY `id_mubaligh` (`id_mubaligh`),
  ADD KEY `id_kegiatan` (`id_kegiatan`);

--
-- Indexes for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id_kegiatan`),
  ADD KEY `id_masjid` (`id_masjid`);

--
-- Indexes for table `masjid`
--
ALTER TABLE `masjid`
  ADD PRIMARY KEY (`id_masjid`);

--
-- Indexes for table `mubaligh`
--
ALTER TABLE `mubaligh`
  ADD PRIMARY KEY (`id_mubaligh`),
  ADD KEY `id_akun` (`id_akun`),
  ADD KEY `id_masjid` (`id_masjid`);

--
-- Indexes for table `pengurus`
--
ALTER TABLE `pengurus`
  ADD PRIMARY KEY (`id_pengurus`),
  ADD KEY `id_akun` (`id_akun`),
  ADD KEY `id_masjid` (`id_masjid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `akun`
--
ALTER TABLE `akun`
  MODIFY `id_akun` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `artikel`
--
ALTER TABLE `artikel`
  MODIFY `id_artikel` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id_jadwal` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id_kegiatan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `masjid`
--
ALTER TABLE `masjid`
  MODIFY `id_masjid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mubaligh`
--
ALTER TABLE `mubaligh`
  MODIFY `id_mubaligh` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `pengurus`
--
ALTER TABLE `pengurus`
  MODIFY `id_pengurus` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`id_masjid`) REFERENCES `masjid` (`id_masjid`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_ibfk_2` FOREIGN KEY (`id_pengurus`) REFERENCES `pengurus` (`id_pengurus`) ON DELETE SET NULL,
  ADD CONSTRAINT `jadwal_ibfk_3` FOREIGN KEY (`id_mubaligh`) REFERENCES `mubaligh` (`id_mubaligh`) ON DELETE SET NULL,
  ADD CONSTRAINT `jadwal_ibfk_4` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan` (`id_kegiatan`) ON DELETE CASCADE;

--
-- Constraints for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD CONSTRAINT `kegiatan_ibfk_1` FOREIGN KEY (`id_masjid`) REFERENCES `masjid` (`id_masjid`) ON DELETE CASCADE;

--
-- Constraints for table `mubaligh`
--
ALTER TABLE `mubaligh`
  ADD CONSTRAINT `mubaligh_ibfk_1` FOREIGN KEY (`id_akun`) REFERENCES `akun` (`id_akun`) ON DELETE CASCADE,
  ADD CONSTRAINT `mubaligh_ibfk_2` FOREIGN KEY (`id_masjid`) REFERENCES `masjid` (`id_masjid`) ON DELETE CASCADE;

--
-- Constraints for table `pengurus`
--
ALTER TABLE `pengurus`
  ADD CONSTRAINT `pengurus_ibfk_1` FOREIGN KEY (`id_akun`) REFERENCES `akun` (`id_akun`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengurus_ibfk_2` FOREIGN KEY (`id_masjid`) REFERENCES `masjid` (`id_masjid`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
