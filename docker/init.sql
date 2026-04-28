-- =============================================
-- Database: cost_db
-- Original dump dari phpMyAdmin (MariaDB 10.4)
-- =============================================

CREATE DATABASE IF NOT EXISTS `cost_db`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `cost_db`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Struktur dari tabel `estimasi_harga`
--

CREATE TABLE `estimasi_harga` (
  `id` int(10) UNSIGNED NOT NULL,
  `perangkat` enum('macbook','windows','pc','imac','other') NOT NULL,
  `kerusakan` enum('lcd','battery','ssd','thermal','other') NOT NULL,
  `harga_min` decimal(12,0) NOT NULL,
  `harga_max` decimal(12,0) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `estimasi_harga`
--

INSERT INTO `estimasi_harga` (`id`, `perangkat`, `kerusakan`, `harga_min`, `harga_max`, `keterangan`) VALUES
(1, 'macbook', 'lcd', 1500000, 3500000, 'Tergantung ukuran layar dan tipe panel'),
(2, 'macbook', 'battery', 800000, 1500000, 'Baterai original Apple'),
(3, 'macbook', 'ssd', 600000, 2000000, 'Upgrade 256GB - 1TB NVMe'),
(4, 'macbook', 'thermal', 250000, 450000, 'Thermal paste + deep cleaning'),
(5, 'macbook', 'other', 300000, 1500000, 'Estimasi menyesuaikan kerusakan'),
(6, 'windows', 'lcd', 400000, 1200000, 'Tergantung ukuran dan resolusi layar'),
(7, 'windows', 'battery', 250000, 600000, 'Baterai kompatibel sesuai tipe laptop'),
(8, 'windows', 'ssd', 300000, 1200000, 'Upgrade 256GB - 1TB SSD SATA/NVMe'),
(9, 'windows', 'thermal', 150000, 300000, 'Thermal paste + cleaning fan'),
(10, 'windows', 'other', 200000, 800000, 'Estimasi menyesuaikan kerusakan'),
(11, 'pc', 'lcd', 350000, 1000000, 'Monitor PC berbagai ukuran'),
(12, 'pc', 'battery', 50000, 150000, 'Baterai CMOS / UPS'),
(13, 'pc', 'ssd', 300000, 1500000, 'Upgrade SSD + migrasi data'),
(14, 'pc', 'thermal', 100000, 250000, 'Thermal paste CPU + cleaning'),
(15, 'pc', 'other', 150000, 750000, 'Estimasi menyesuaikan kerusakan'),
(16, 'imac', 'lcd', 2000000, 5000000, 'Panel layar all-in-one iMac'),
(17, 'imac', 'battery', 500000, 1000000, 'Baterai Magic Mouse / keyboard'),
(18, 'imac', 'ssd', 800000, 2500000, 'Upgrade SSD internal iMac'),
(19, 'imac', 'thermal', 300000, 600000, 'Thermal paste + cleaning iMac'),
(20, 'imac', 'other', 400000, 2000000, 'Estimasi menyesuaikan kerusakan'),
(21, 'other', 'lcd', 300000, 1500000, 'Estimasi menyesuaikan perangkat'),
(22, 'other', 'battery', 200000, 700000, 'Estimasi menyesuaikan perangkat'),
(23, 'other', 'ssd', 250000, 1000000, 'Estimasi menyesuaikan perangkat'),
(24, 'other', 'thermal', 100000, 300000, 'Estimasi menyesuaikan perangkat'),
(25, 'other', 'other', 150000, 750000, 'Hubungi teknisi untuk estimasi lebih akurat');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
-- (dibuat duluan karena servis & servis_log reference ke users)
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('pelanggan','teknisi') NOT NULL DEFAULT 'pelanggan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `no_telepon`, `password`, `role`, `created_at`) VALUES
(3, 'Pelanggan Demo', 'pelanggan@geeko.com', NULL, '$2y$10$U9f3qDc5sxlpFpfRXLEj9eL3HO.uMWP7.n9MWthT.LZWe5a8udAZy', 'pelanggan', '2026-04-12 03:45:32'),
(4, 'Teknisi Demo', 'teknisi@geeko.com', NULL, '$2y$10$U9f3qDc5sxlpFpfRXLEj9eL3HO.uMWP7.n9MWthT.LZWe5a8udAZy', 'teknisi', '2026-04-12 03:45:32'),
(5, 'Yazid Mochammad', 'moch2804yazid@gmail.com', '1234567890', '$2y$10$vfGvtOkvNtw.vqObQAVEtOuaxVdwu6DHJ7kEGqoMKxzUrhYL/yOoS', 'pelanggan', '2026-04-16 05:43:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `servis`
--

CREATE TABLE `servis` (
  `id` int(10) UNSIGNED NOT NULL,
  `nomor_tiket` varchar(20) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `perangkat` enum('macbook','windows','pc','imac','other') NOT NULL,
  `jenis_kerusakan` enum('lcd','battery','ssd','thermal','other') NOT NULL,
  `cabang` enum('surabaya') NOT NULL,
  `deskripsi` text NOT NULL,
  `estimasi_harga` decimal(12,2) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('Diterima','Sedang dicek','Perbaikan','Testing','Selesai') NOT NULL DEFAULT 'Diterima',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `servis`
--

INSERT INTO `servis` (`id`, `nomor_tiket`, `user_id`, `nama_pelanggan`, `email`, `no_telepon`, `perangkat`, `jenis_kerusakan`, `cabang`, `deskripsi`, `estimasi_harga`, `foto`, `status`, `created_at`) VALUES
(2, 'GK-20260415-CFAD', 3, 'Yazid Mochammad', 'moch2804yazid@gmail.com', '12365478987845', 'imac', 'thermal', 'surabaya', 'fdsafdsafasfas', 600000.00, NULL, 'Selesai', '2026-04-15 09:32:47'),
(3, 'GK-20260415-48DC', 3, 'Yazid Mochammad', 'moch2804yazid@gmail.com', '12365478987845', 'pc', 'lcd', 'surabaya', 'fdsa', 1000000.00, NULL, 'Diterima', '2026-04-15 09:44:39'),
(4, 'GK-20260415-BE43', 3, 'Yazid Mochammad', 'moch2804yazid@gmail.com', '12365478987845', 'windows', 'ssd', 'surabaya', 'fasgewq', 1200005.00, NULL, 'Diterima', '2026-04-15 09:45:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `servis_log`
--

CREATE TABLE `servis_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `servis_id` int(10) UNSIGNED NOT NULL,
  `status` enum('Diterima','Sedang dicek','Perbaikan','Testing','Selesai') NOT NULL,
  `catatan` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `servis_log`
--

INSERT INTO `servis_log` (`id`, `servis_id`, `status`, `catatan`, `foto`, `updated_by`, `updated_at`) VALUES
(8, 2, 'Diterima', 'Booking baru masuk dari pelanggan.', NULL, 3, '2026-04-15 09:32:47'),
(9, 2, 'Sedang dicek', 'okeh', NULL, 4, '2026-04-15 09:33:58'),
(10, 2, 'Perbaikan', NULL, NULL, 4, '2026-04-15 09:34:09'),
(11, 2, 'Testing', NULL, NULL, 4, '2026-04-15 09:34:20'),
(12, 2, 'Selesai', NULL, NULL, 4, '2026-04-15 09:34:28'),
(13, 3, 'Diterima', 'Booking baru masuk dari pelanggan.', NULL, 3, '2026-04-15 09:44:39'),
(14, 4, 'Diterima', 'Booking baru masuk dari pelanggan.', NULL, 3, '2026-04-15 09:45:00'),
(15, 4, 'Diterima', 'Data servis diperbarui oleh teknisi.', NULL, 4, '2026-04-15 09:57:12');

-- --------------------------------------------------------

--
-- Indeks untuk tabel yang dibuang
--

ALTER TABLE `estimasi_harga`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_kombinasi` (`perangkat`,`kerusakan`);

ALTER TABLE `servis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_tiket` (`nomor_tiket`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `servis_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servis_id` (`servis_id`),
  ADD KEY `updated_by` (`updated_by`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

ALTER TABLE `estimasi_harga`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

ALTER TABLE `servis`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `servis_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Foreign Keys)
--

ALTER TABLE `servis`
  ADD CONSTRAINT `servis_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `servis_log`
  ADD CONSTRAINT `servis_log_ibfk_1` FOREIGN KEY (`servis_id`) REFERENCES `servis` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `servis_log_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
