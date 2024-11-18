-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 12, 2024 at 04:43 AM
-- Server version: 8.0.31
-- PHP Version: 7.4.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Comments for temporary character set changes
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Database: `films`

-- --------------------------------------------------------
-- Table structure for table `films`
CREATE TABLE `films` (
  `Movie ID` int NOT NULL,
  `Producer` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `Title` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `Director` varchar(45) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `films`
INSERT INTO `films` (`Movie ID`, `Producer`, `Title`, `Director`) VALUES
(1, 'Michael Bay', 'Pearl Harbor', 'Michael Bay'),
(2, 'Michael Bay', 'The Texas Chainsaw Massacre', 'Marcus Nispel'),
(3, 'Michael Bay', 'Armageddon', 'Michael Bay'),
(4, 'Michael Bay', 'The Amityville Horror', 'Andrew Douglas'),
(5, 'Michael Bay', 'The Texas Chainsaw Massacre: The Beginning', 'Jonathan Liebesman'),
(6, 'Michael Bay', 'The Island', 'Michael Bay'),
(7, 'Michael Bay', 'The Hitcher', 'Dave Meyers'),
(8, 'Michael Bay', 'Friday the 13th', ' Marcus Nispel');

-- Indexes for dumped tables
ALTER TABLE `films`
  ADD PRIMARY KEY (`Movie ID`);

-- AUTO_INCREMENT for table `films`
ALTER TABLE `films`
  MODIFY `Movie ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

-- Restore original character set settings
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
