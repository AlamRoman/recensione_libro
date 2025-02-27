-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 27, 2025 at 05:32 PM
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
  `voto` decimal(3,1) NOT NULL,
  `commento` varchar(255) DEFAULT NULL,
  `data_ultima_modifica` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recensione`
--

INSERT INTO `recensione` (`id`, `id_user`, `id_libro`, `voto`, `commento`, `data_ultima_modifica`) VALUES
(3, 1, 2, 10.0, 'libro bellissimo', '2025-02-27 10:50:44'),
(5, 1, 3, 8.0, 'troppo deppresso', '2025-02-26 08:34:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `cognome` varchar(50) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nome`, `cognome`, `username`, `token`) VALUES
(1, 'Mario', 'Rossi', 'mario', '7123a062ef08af773b5cff8ed91081d1dcc1d75c23cf99fbf72cacc8bb0aef12'),
(2, 'luigi', 'rossi', 'luigi', 'fb22eba6ee525515540432746b71fd1e8084488f2d56abd40f16c1c5e8a65a13'),
(3, 'ciao', 'rossi', 'luigi7', '05ed52b00aa32a15f5a2423ab91193cc930ccb8cfd69128a0d471bf9a4021586');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
