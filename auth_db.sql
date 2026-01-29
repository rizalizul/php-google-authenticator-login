-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 29, 2026 at 08:10 AM
-- Server version: 10.11.11-MariaDB-log
-- PHP Version: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `auth_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `google_secret` varchar(255) DEFAULT NULL,
  `backup_codes` text DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `google_secret`, `backup_codes`, `reset_token`, `reset_expires`, `failed_attempts`, `locked_until`, `created_at`, `remember_token`) VALUES
(1, 'admin', '', '$2y$12$GT22HSg79VLYEucs/x/wbubWv4N0MFKHZnd/ZmVL.clw965xDXyru', 'W4WXQPUCFSLGOBF3', NULL, NULL, NULL, 2, NULL, '2026-01-29 07:48:57', NULL),
(2, 'rizal', '', '$2y$12$x.P7dKRJHWIBp9HoJqAd3e6alQ7RKkfJ3wuxMOXF.jPKWQQGVE8lu', 'HV4ARFDR6PY35BD7', NULL, NULL, NULL, 1, NULL, '2026-01-29 07:48:57', NULL),
(3, 'rizal1', '', '$2y$12$8wMBhSViCy2o4UkkeP1cvuxF6MwHK5f3Bb6fD/AzyXR9i6T0M/J/6', 'FI5KQAJCUXPB5P7H', '[\"6558dac2\",\"03cf8c7e\",\"6eee37c7\",\"27f0173b\"]', NULL, NULL, 0, NULL, '2026-01-29 07:48:57', NULL),
(4, 'rizalsegala1', 'rizalsegala1@mail.com', '$2y$12$Q9JhmFEE14UkR7TQHa8WION0UuqfmI9bmpR4HxeJbB7MXbETgZufS', 'W66CLUKDDUN674G2', '[\"f365b17e\",\"8aea3afb\",\"cac9db3b\",\"79ac1d93\",\"d932afa0\"]', NULL, NULL, 0, NULL, '2026-01-29 07:48:57', 'd7e6157f175dbcb4a0afe11c2df5b490d22f9ca940b6a829903e60ab8763f600');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
