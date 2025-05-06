-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 06, 2025 at 01:44 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cthread_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `gender` varchar(100) NOT NULL,
  `job` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `website` varchar(100) NOT NULL,
  `bio` varchar(200) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`id`, `name`, `gender`, `job`, `email`, `website`, `bio`, `created_at`) VALUES
(100000008, 'Mario Felix Fauzi', 'Laki-Laki', 'Freelancer', 'mariofelixfauzi@gmail.com', 'https://pasarweb.vercel.com', 'Founder and Owner of Pasar Web Business', '2025-04-26 01:28:53'),
(100000022, 'Lix', 'Laki-Laki', 'Penyelamat Bumi', 'bubabybub@gmail.com', 'https://github.com/Fvcklyx', 'Nek kowe dipaido wes panggah dijawab,wong liyo ngerti opo', '2025-04-30 16:10:52'),
(100000024, 'hallo ini inggil!!! (⁠◍⁠•⁠ᴗ⁠•⁠◍⁠)', 'Perempuan', 'mahasiswa', 'inggilmeinia4@gmail.com', '', 'eat for life, life for eat 🍜🍱🥗🌮🥞🍰🧋🍨', '2025-04-30 14:04:05'),
(100000026, 'lynx', 'Laki-Laki', 'wev', 'akbarngaceng123@gmail.com', 'https://antukewer.com', 'blabla', '2025-05-05 16:14:59');

-- --------------------------------------------------------

--
-- Table structure for table `threads`
--

CREATE TABLE `threads` (
  `id_order` int NOT NULL,
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `core` varchar(500) NOT NULL,
  `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `threads`
--

INSERT INTO `threads` (`id_order`, `id`, `title`, `core`, `status`, `created_at`) VALUES
(5, 100000008, '🚀 Butuh Website Profesional? Serahkan pada Pasar Web! 🌐', 'Kamu punya bisnis tapi belum punya website? Saatnya naik level bersama Pasar Web! 💼 Kami siap bantu kamu punya website profesional, cepat, dan mobile-friendly dengan desain sesuai kebutuhanmu. Mulai dari toko online, portofolio, hingga website perusahaan — semua bisa kami kerjakan dengan harga terjangkau dan proses yang transparan. Yuk, bawa bisnismu tampil online dan raih lebih banyak pelanggan! Hubungi kami sekarang dan dapatkan konsultasi GRATIS! 📞✨', 'Public', '2025-05-01 01:05:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(10) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(100000008, 'marfeluze', '$2y$10$kNbRO/E6V5wFS27jMx.cN.OWbaLs2G9DVeY31vhhXLrtooZKAHDje', '2025-04-26 00:48:43'),
(100000020, 'admin', '$2y$10$RiuuFk2F3aQ41slSpsUkY.BJ.n7MuU362FFIOEgh0eg/N4VDAgxYG', '2025-04-27 21:35:57'),
(100000022, 'fvcklyx', '$2y$10$JiCBkm/DM2pRsTMc/K8neulCwBsa9B0Jr1fg6A9XFvm7sWilkGz2y', '2025-04-29 21:06:22'),
(100000024, 'inggilcans', '$2y$10$JSENZl9fGL9pGwTt4ZzKPOAu8wiPk3kxCDNTn2FxhqfPnK2q7Vwia', '2025-04-30 14:04:05'),
(100000026, 'lynx', '$2y$10$r9o674Jaqs4n7mYOaNl4ZexPjiQrTBqqS0.Zx.p05fsy186/Qqv/S', '2025-05-05 23:13:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `threads`
--
ALTER TABLE `threads`
  ADD PRIMARY KEY (`id_order`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `threads`
--
ALTER TABLE `threads`
  MODIFY `id_order` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100000027;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
