-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2025 at 03:38 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ewaste_connect`
--

-- --------------------------------------------------------

--
-- Table structure for table `accepted_items`
--

CREATE TABLE `accepted_items` (
  `id` int(11) NOT NULL,
  `center_id` int(11) NOT NULL,
  `item_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accepted_items`
--

INSERT INTO `accepted_items` (`id`, `center_id`, `item_type`) VALUES
(1, 1, 'phones'),
(2, 1, 'laptops'),
(3, 1, 'tablets'),
(4, 1, 'batteries'),
(5, 1, 'cables'),
(6, 2, 'phones'),
(7, 2, 'laptops'),
(8, 2, 'tablets'),
(9, 2, 'batteries'),
(10, 2, 'cables');

-- --------------------------------------------------------

--
-- Table structure for table `collection_centers`
--

CREATE TABLE `collection_centers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `lat` decimal(10,8) NOT NULL,
  `lon` decimal(11,8) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `rating` decimal(2,1) NOT NULL,
  `reviews` int(11) DEFAULT 0,
  `hours` varchar(100) NOT NULL,
  `certifications` varchar(255) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `collection_centers`
--

INSERT INTO `collection_centers` (`id`, `name`, `address`, `lat`, `lon`, `phone`, `email`, `rating`, `reviews`, `hours`, `certifications`, `website`) VALUES
(1, 'Webesi Recyclers', 'PO Box 167 Giakanja', 36.90829828, -1.09932882, '0743726276', 'jake@mail.com', 4.5, 2, '4AM - 10PM', 'SAPPI, SAOS', 'https://knec.ac.ke'),
(2, 'Webesi Recyclers', 'PO Box 167 Giakanja', 36.90829828, -1.09932882, '0743726276', 'jake@mail.com', 4.5, 2, '4AM - 10PM', 'SAPPI, SAOS', 'https://knec.ac.ke');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accepted_items`
--
ALTER TABLE `accepted_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `center_id` (`center_id`);

--
-- Indexes for table `collection_centers`
--
ALTER TABLE `collection_centers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accepted_items`
--
ALTER TABLE `accepted_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `collection_centers`
--
ALTER TABLE `collection_centers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accepted_items`
--
ALTER TABLE `accepted_items`
  ADD CONSTRAINT `accepted_items_ibfk_1` FOREIGN KEY (`center_id`) REFERENCES `collection_centers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
