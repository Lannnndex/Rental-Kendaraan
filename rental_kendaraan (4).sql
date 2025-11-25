-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 25 Nov 2025 pada 13.13
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rental_kendaraan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `kendaraan`
--

CREATE TABLE `kendaraan` (
  `no_plat` varchar(20) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `merk` varchar(50) NOT NULL,
  `harga_per_jam` decimal(10,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('tersedia','disewa') DEFAULT 'tersedia',
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kendaraan`
--

INSERT INTO `kendaraan` (`no_plat`, `jenis`, `merk`, `harga_per_jam`, `image`, `status`, `deleted_at`) VALUES
('B 3333 ABC', 'Mobil', 'Toyota Avanza', 100000.00, 'public/uploads/kendaraan/1764063574_a72936e8483e.jpeg', 'disewa', NULL),
('B 3434 PYZ', 'Mobil', 'Mitsubishi Pajero', 100000.00, 'public/uploads/kendaraan/1764063654_f3423a52d510.jpg', 'disewa', NULL),
('B 5544 YZX', 'Mobil', 'Toyota Raize', 150000.00, 'public/uploads/kendaraan/1764063718_09881f8ec131.jpeg', 'tersedia', NULL),
('B 6767 XYZ', 'Mobil', 'Toyota Innova', 150000.00, 'public/uploads/kendaraan/1764064140_b21f6e66594f.jpeg', 'tersedia', NULL),
('D 5679 DEF', 'Mobil', 'Honda Brio', 100000.00, 'public/uploads/kendaraan/1764064207_0fd125548a9f.jpeg', 'disewa', NULL),
('KT 3246 XYZ', 'Motor', 'Yamaha Xsr', 150000.00, 'public/uploads/kendaraan/1764064257_3a99166401ad.jpeg', 'tersedia', NULL),
('KT 3756 XYZ', 'Mobil', 'Honda Jazz', 100000.00, 'public/uploads/kendaraan/1764064299_894fe102de02.jpeg', 'tersedia', NULL),
('KT 4285 XYZ', 'Motor', 'Honda Cbr 150', 150000.00, 'public/uploads/kendaraan/1764064365_bf7664c21abb.jpeg', 'tersedia', NULL),
('KT 4487 XYZ', 'Mobil', 'Toyota Avanza', 100000.00, 'public/uploads/kendaraan/1764063215_6f14a542d206.jpeg', 'tersedia', NULL),
('KT 5431 YTZ', 'Motor', 'Honda Beat', 50000.00, 'public/uploads/kendaraan/1764032031_ad5c3b01f2a4.jpeg', 'disewa', NULL),
('KT 7563 XYZ', 'Mobil', 'Honda Mobilio', 150000.00, 'public/uploads/kendaraan/1764064400_9a11efa19628.jpeg', 'tersedia', NULL),
('KT 8435 XYZ', 'Motor', 'Vespa Sprint 150', 150000.00, 'public/uploads/kendaraan/1764064460_21809939ac60.jpeg', 'tersedia', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggan`
--

CREATE TABLE `pelanggan` (
  `no_ktp` varchar(30) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `no_hp` varchar(25) DEFAULT NULL,
  `foto_sim` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pelanggan`
--

INSERT INTO `pelanggan` (`no_ktp`, `nama`, `alamat`, `no_hp`, `foto_sim`, `deleted_at`) VALUES
('3201234567890001', 'Budi Santoso', 'Jl. Merdeka No. 10, Jakarta', '081234567890', NULL, NULL),
('3202987654321002', 'Citra Lestari', 'Jl. Sudirman Kav. 5, Bandung', '085678901234', NULL, NULL),
('6245782572657285242525', 'Eagan', 'Jln. Balikapan', '0852-8657-29', 'public/uploads/pelanggan/1764060006_5f02d31b899c.jpeg', NULL),
('6249825628956289572895', 'Abrar', 'Jln. Kalimantan', '0843-9523-7552', 'public/uploads/pelanggan/1764030265_8bf3ba24f62f.jpeg', NULL),
('625782527657285', 'Edo', 'Jln Banjar', '0846-3538-252', NULL, NULL),
('625827582895868275', 'Vera', 'Jln. Itk', '0837-4592-7526', 'public/uploads/pelanggan/1764058054_058b3f5bd4a3.jpeg', '2025-11-25 18:12:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_rental` varchar(20) DEFAULT NULL,
  `jumlah_bayar` decimal(12,2) DEFAULT NULL,
  `tgl_bayar` datetime DEFAULT NULL,
  `metode_bayar` enum('tunai','kartu','transfer') DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_rental`, `jumlah_bayar`, `tgl_bayar`, `metode_bayar`, `deleted_at`) VALUES
(1, 'A001', 100000.00, '2025-11-25 00:00:00', 'tunai', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id_pengembalian` int(11) NOT NULL,
  `id_rental` varchar(20) NOT NULL,
  `tanggal_dikembalikan` datetime DEFAULT NULL,
  `denda` decimal(12,2) DEFAULT 0.00,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengembalian`
--

INSERT INTO `pengembalian` (`id_pengembalian`, `id_rental`, `tanggal_dikembalikan`, `denda`, `deleted_at`) VALUES
(2, 'A007', '2025-11-26 00:00:00', 0.00, NULL),
(3, 'A011', '2025-11-26 00:00:00', 6750000.00, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `rental`
--

CREATE TABLE `rental` (
  `id_rental` varchar(20) NOT NULL,
  `no_plat` varchar(20) NOT NULL,
  `id_users` int(11) NOT NULL,
  `no_ktp` varchar(30) NOT NULL,
  `tanggal_sewa` datetime NOT NULL,
  `tanggal_kembali` datetime DEFAULT NULL,
  `jumlah_bayar` decimal(12,2) DEFAULT NULL,
  `tanggal_bayar` datetime DEFAULT NULL,
  `metode_bayar` enum('tunai','kartu','transfer') DEFAULT NULL,
  `total_biaya` decimal(12,2) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rental`
--

INSERT INTO `rental` (`id_rental`, `no_plat`, `id_users`, `no_ktp`, `tanggal_sewa`, `tanggal_kembali`, `jumlah_bayar`, `tanggal_bayar`, `metode_bayar`, `total_biaya`, `deleted_at`) VALUES
('A001', 'KT 5431 YTZ', 1, '6249825628956289572895', '2025-11-25 00:00:00', '2025-11-25 00:00:00', NULL, NULL, NULL, NULL, NULL),
('A002', 'D 5679 DEF', 1, '3201234567890001', '2025-11-25 00:00:00', '2025-11-25 00:00:00', NULL, NULL, NULL, NULL, '2025-11-25 09:26:42'),
('A003', 'B 3333 ABC', 1, '3202987654321002', '2025-11-25 00:00:00', '2025-11-25 00:00:00', NULL, NULL, NULL, 200000.00, NULL),
('A004', 'KT 5431 YTZ', 1, '6249825628956289572895', '2025-11-25 00:00:00', '2025-11-25 00:00:00', 150000.00, '2025-11-25 00:00:00', 'tunai', 150000.00, '2025-11-25 15:30:45'),
('A005', 'D 5679 DEF', 2, '3202987654321002', '2025-11-25 00:00:00', '2025-11-25 00:00:00', 200000.00, '2025-11-25 00:00:00', 'tunai', 200000.00, '2025-11-25 12:12:25'),
('A006', 'KT 5431 YTZ', 1, '6249825628956289572895', '2025-11-25 00:00:00', '2025-11-25 00:00:00', 100000.00, '2025-11-25 00:00:00', 'tunai', 100000.00, NULL),
('A007', 'D 5679 DEF', 1, '3201234567890001', '2025-11-25 00:00:00', '2025-11-25 00:00:00', 100000.00, '2025-11-25 00:00:00', 'tunai', 200000.00, NULL),
('A008', 'B 3434 PYZ', 2, '3201234567890001', '2025-11-25 00:00:00', '2025-11-25 00:00:00', 100000.00, '2025-11-25 00:00:00', 'tunai', 100000.00, NULL),
('A009', 'B 5544 YZX', 2, '6249825628956289572895', '2025-11-25 00:00:00', '2025-11-26 00:00:00', 1200000.00, '2025-11-25 00:00:00', 'tunai', 1200000.00, NULL),
('A010', 'D 5679 DEF', 2, '3202987654321002', '2025-11-25 00:00:00', '2025-11-25 00:00:00', 50000.00, '0000-00-00 00:00:00', 'tunai', 100000.00, NULL),
('A011', 'B 6767 XYZ', 1, '6245782572657285242525', '2025-11-25 00:00:00', '2025-11-25 00:00:00', 150000.00, '2025-11-25 00:00:00', 'tunai', 150000.00, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_users` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manajer','karyawan') NOT NULL DEFAULT 'karyawan',
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_users`, `nama_lengkap`, `username`, `password`, `role`, `deleted_at`) VALUES
(1, 'Edo', 'admin', '$2y$10$p8NxsPnEPBFXwDSVupVPmuAciguCunpUhK50bPlcvU6Xfo/xY8zja', 'admin', NULL),
(2, 'Syahid Ridho', 'karyawan', '$2y$10$rf0KbqHEkSAK2kxzfUrh0OsPxdv9MJ.uQgK2lNf6UJ6SG/j8HB.uS', 'karyawan', NULL),
(3, 'Alexa', 'manajer', '$2y$10$A9k0Bs3Xsvs08u7m644G5.L8x0ZSJOatJ6J0xIC9xCvuYhTk5nOMC', 'manajer', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`no_plat`);

--
-- Indeks untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`no_ktp`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `idx_id_rental` (`id_rental`);

--
-- Indeks untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id_pengembalian`),
  ADD KEY `idx_id_rental` (`id_rental`);

--
-- Indeks untuk tabel `rental`
--
ALTER TABLE `rental`
  ADD PRIMARY KEY (`id_rental`),
  ADD KEY `idx_no_plat` (`no_plat`),
  ADD KEY `idx_id_users` (`id_users`),
  ADD KEY `idx_no_ktp` (`no_ktp`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_users`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id_pengembalian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_users` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_fk_rental` FOREIGN KEY (`id_rental`) REFERENCES `rental` (`id_rental`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `pengembalian_fk_rental` FOREIGN KEY (`id_rental`) REFERENCES `rental` (`id_rental`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `rental`
--
ALTER TABLE `rental`
  ADD CONSTRAINT `rental_fk_kendaraan` FOREIGN KEY (`no_plat`) REFERENCES `kendaraan` (`no_plat`) ON UPDATE CASCADE,
  ADD CONSTRAINT `rental_fk_pelanggan` FOREIGN KEY (`no_ktp`) REFERENCES `pelanggan` (`no_ktp`) ON UPDATE CASCADE,
  ADD CONSTRAINT `rental_fk_users` FOREIGN KEY (`id_users`) REFERENCES `users` (`id_users`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
