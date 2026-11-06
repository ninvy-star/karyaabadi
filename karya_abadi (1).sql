-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 06, 2026 at 01:23 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `karya_abadi`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Admin',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama`, `created_at`) VALUES
(1, 'admin', '$2y$10$py8w2Z8.nkWY0VLL4/QRuezw2i6dBdoK.9zaGoft8oegYnyflRvIm', 'Owner Karya Abadi', '2026-11-06 12:26:52');

-- --------------------------------------------------------

--
-- Table structure for table `harga`
--

CREATE TABLE `harga` (
  `id` int NOT NULL,
  `produk_id` int NOT NULL,
  `ukuran` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga_jati_super` int DEFAULT NULL,
  `harga_jati_lokal` int DEFAULT NULL,
  `harga_kayu_rimba` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `harga`
--

INSERT INTO `harga` (`id`, `produk_id`, `ukuran`, `harga_jati_super`, `harga_jati_lokal`, `harga_kayu_rimba`, `created_at`) VALUES
(1, 1, '120 x 60 x 75 cm', 2500000, 2000000, 1500000, '2026-11-06 12:26:52'),
(2, 3, '120 x 50 x 200 cm', 5000000, 4000000, 2200000, '2026-11-06 12:26:52'),
(3, 7, '80 x 210 cm', 2500000, 1000000, 1100000, '2026-11-06 12:26:52'),
(4, 5, 'Per Meter', 500000, 300000, 100000, '2026-11-06 12:26:52');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama`, `urutan`) VALUES
(1, 'Meja', 1),
(2, 'Pintu', 2),
(3, 'Kusen', 3),
(4, 'Almari', 4),
(5, 'Kursi', 5),
(6, 'Daun Pintu', 6),
(7, 'Furniture Custom', 99);

-- --------------------------------------------------------

--
-- Table structure for table `material`
--

CREATE TABLE `material` (
  `id` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `harga` int DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'aktif',
  `urutan` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `material`
--

INSERT INTO `material` (`id`, `nama`, `kode`, `deskripsi`, `harga`, `foto`, `status`, `urutan`, `created_at`) VALUES
(1, 'Jati Super', 'JS', 'Kayu kualitas premium dengan serat padat dan ketahanan luar biasa terhadap cuaca dan rayap. Pilihan terbaik untuk furniture jangka panjang.', 2500000, NULL, 'aktif', 1, '2026-11-06 12:26:52'),
(2, 'Jati Lokal', 'JL', 'Alternatif kayu jati dengan harga lebih terjangkau, namun tetap mempertahankan serat kayu alami yang indah.', 300000, NULL, 'aktif', 2, '2026-11-06 12:26:52'),
(3, 'Kayu Rimba', 'KR', 'Bahan ekonomis yang kokoh dan ringan. Sangat cocok untuk furniture indoor dengan finishing cat solid atau kebutuhan budget terbatas.', 1100000, NULL, 'aktif', 3, '2026-11-06 12:26:52');

-- --------------------------------------------------------

--
-- Table structure for table `portofolio`
--

CREATE TABLE `portofolio` (
  `id` int NOT NULL,
  `judul` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_tampil` tinyint(1) DEFAULT '1',
  `urutan` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portofolio`
--

INSERT INTO `portofolio` (`id`, `judul`, `kategori`, `deskripsi`, `foto`, `status_tampil`, `urutan`, `created_at`) VALUES
(1, 'Meja Makan Ukuran Panjang', 'Meja Makan', 'Meja makan panjang dengan material kayu solid yang kuat dan elegan, ideal untuk keluarga besar.', NULL, 1, 1, '2026-11-06 12:26:52'),
(2, 'Set Kursi Tamu Jati', 'Kursi', 'Set kursi tamu dari kayu jati super dengan finishing natural. Tahan lama dan nyaman dipakai bertahun-tahun.', NULL, 1, 2, '2026-11-06 12:26:52'),
(3, 'Almari Kamar Minimalis', 'Almari', 'Almari kamar tidur desain minimalis dengan ruang penyimpanan lega dan finishing rapi.', NULL, 1, 3, '2026-11-06 12:26:52'),
(4, 'Pintu Utama Rumah', 'Pintu', 'Pintu utama kayu jati dengan motif klasik. Memberikan kesan hangat dan elegan untuk rumah.', NULL, 1, 4, '2026-11-06 12:26:52'),
(5, 'Kusen Jendela Custom', 'Kusen', 'Kusen jendela dibuat custom sesuai ukuran, dengan finishing warna sesuai keinginan.', NULL, 1, 5, '2026-11-06 12:26:52');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori_id` int DEFAULT NULL,
  `material_id` int DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `ukuran` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga` int DEFAULT NULL,
  `is_konsultasi` tinyint(1) DEFAULT '0',
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `kategori_id`, `material_id`, `deskripsi`, `ukuran`, `harga`, `is_konsultasi`, `foto`, `created_at`) VALUES
(1, 'Meja Makan', 1, 1, 'Meja makan dari kayu jati pilihan dengan finishing rapi. Cocok untuk ruang makan keluarga.', '120 x 60 x 75 cm', 2500000, 0, NULL, '2026-11-06 12:26:52'),
(2, 'Meja Kayu', 1, 1, 'Meja kayu multifungsi dengan desain minimalis. Bisa dipakai untuk ruang tamu atau ruang kerja.', '±120 x ±60 cm x ±75 cm', 2500000, 0, NULL, '2026-11-06 12:26:52'),
(3, 'Almari', 4, 2, 'Almari kayu dengan dua pintu, ruang penyimpanan luas. Desain elegan untuk kamar tidur.', '120 x 50 x 200 cm', 2000000, 0, NULL, '2026-11-06 12:26:52'),
(4, 'Pintu', 2, 3, 'Pintu kayu solid dengan motif minimalis. Tersedia berbagai ukuran dan finishing.', '80 x 210 cm', 1100000, 0, NULL, '2026-11-06 12:26:52'),
(5, 'Kusen', 3, 2, 'Kusen kayu untuk pintu dan jendela. Dibuat presisi sesuai ukuran yang dibutuhkan.', 'Per Meter', 300000, 0, NULL, '2026-11-06 12:26:52'),
(6, 'Kursi Tamu', 5, 1, 'Kursi tamu kayu jati dengan bantalan empuk. Set 2 kursi dengan desain modern.', '60 x 60 x 90 cm', 1500000, 0, NULL, '2026-11-06 12:26:52'),
(7, 'Daun Pintu', 6, 3, 'Daun pintu kayu dengan motif minimalis. Solusi ekonomis untuk pintu rumah.', '80 x 210 cm', 1000000, 0, NULL, '2026-11-06 12:26:52'),
(8, 'Furniture Custom', 7, NULL, 'Pesan furniture sesuai kebutuhan, ukuran, dan desain sendiri. Konsultasikan konsep Anda dengan kami.', NULL, NULL, 1, NULL, '2026-11-06 12:26:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `harga`
--
ALTER TABLE `harga`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `portofolio`
--
ALTER TABLE `portofolio`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori_id` (`kategori_id`),
  ADD KEY `material_id` (`material_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `harga`
--
ALTER TABLE `harga`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `material`
--
ALTER TABLE `material`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `portofolio`
--
ALTER TABLE `portofolio`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `harga`
--
ALTER TABLE `harga`
  ADD CONSTRAINT `harga_ibfk_1` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `produk_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `material` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
