-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 23, 2025 at 09:01 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `recensione_libro`
--

-- --------------------------------------------------------

--
-- Table structure for table `libro`
--

CREATE TABLE `libro` (
  `id` int(11) NOT NULL,
  `titolo` varchar(100) NOT NULL,
  `autore` varchar(50) DEFAULT NULL,
  `descrizione` varchar(255) DEFAULT NULL,
  `isbn` varchar(13) NOT NULL,
  `genere` varchar(50) DEFAULT NULL,
  `anno_pubblicazione` year(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `libro`
--

INSERT INTO `libro` (`id`, `titolo`, `autore`, `descrizione`, `isbn`, `genere`, `anno_pubblicazione`) VALUES
(1, 'Il nome della rosa', 'Umberto Eco', 'Romanzo storico e giallo ambientato in un monastero medievale.', '9788804663545', 'Romanzo storico', '1980'),
(2, 'La coscienza di Zeno', 'Italo Svevo', 'Romanzo psicologico che esplora le contraddizioni della mente umana.', '9788807888123', 'Romanzo psicologico', '1923'),
(3, 'Il Gattopardo', 'Giuseppe Tomasi di Lampedusa', 'Racconto della decadenza di una famiglia aristocratica siciliana.', '9788807888124', 'Romanzo storico', '1958'),
(4, 'Se questo è un uomo', 'Primo Levi', 'Testimonianza dell’esperienza dell’autore nei campi di concentramento.', '9788806211491', 'Memorie', '1947'),
(5, 'Il barone rampante', 'Italo Calvino', 'La storia di un giovane nobile che decide di vivere sugli alberi.', '9788807888125', 'Romanzo fantastico', '1957'),
(6, '1984', 'George Orwell', 'Romanzo distopico che descrive un futuro totalitario e sorvegliato.', '9780451524935', 'Distopia', '1949'),
(7, 'To Kill a Mockingbird', 'Harper Lee', 'Romanzo ambientato nel profondo Sud degli Stati Uniti, che affronta temi di giustizia e pregiudizio.', '9780060935467', 'Narrativa', '1960'),
(8, 'Pride and Prejudice', 'Jane Austen', 'Classico della letteratura inglese che esplora le dinamiche sociali e le relazioni sentimentali.', '9780141439518', 'Romanzo', '0000'),
(9, 'The Great Gatsby', 'F. Scott Fitzgerald', 'Ritratto della società americana degli anni Venti, tra sfarzo e delusione.', '9780743273565', 'Romanzo', '1925');

-- --------------------------------------------------------

--
-- Table structure for table `recensione`
--

CREATE TABLE `recensione` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_libro` int(11) NOT NULL,
  `voto` decimal(2,1) NOT NULL,
  `commento` varchar(255) DEFAULT NULL,
  `data_recensione` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `cognome` varchar(50) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nome`, `cognome`, `username`, `password`, `token`) VALUES
(1, 'Mario', 'Rossi', 'mario', '$2y$10$qjmd5gOWhllP3aznOrabBOvY0OWGgFQIKk3oPqMvdkcwLy.AT20IS', 'e2ed49857e2074ce8ad9f1074684db20558aeef7b2870bf6cb0a32ebfeb81884');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `libro`
--
ALTER TABLE `libro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`);

--
-- Indexes for table `recensione`
--
ALTER TABLE `recensione`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_libro` (`id_libro`);

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
-- AUTO_INCREMENT for table `libro`
--
ALTER TABLE `libro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `recensione`
--
ALTER TABLE `recensione`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `recensione`
--
ALTER TABLE `recensione`
  ADD CONSTRAINT `recensione_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `recensione_ibfk_2` FOREIGN KEY (`id_libro`) REFERENCES `libro` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
