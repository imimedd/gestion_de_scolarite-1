-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2026 at 04:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `usthb_enseignant`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrateurs`
--

CREATE TABLE `administrateurs` (
  `id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administrateurs`
--

INSERT INTO `administrateurs` (`id`, `email`, `password`, `role`) VALUES
(1, 'admin@usthb.dz', 'adminusthb', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `enseignants`
--

CREATE TABLE `enseignants` (
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `matricule` int(50) NOT NULL,
  `email` text NOT NULL,
  `password` int(11) NOT NULL,
  `id_enseignant` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enseignants`
--

INSERT INTO `enseignants` (`nom`, `prenom`, `matricule`, `email`, `password`, `id_enseignant`) VALUES
('Laachemi', ' Mohamed', 1234, 'laachemi@usthb.dz', 456, 1),
('meddour', 'imene', 3456, 'imene@gmail.com', 2345, 2),
('ouali', 'yacine', 2345, 'ouali.yacine@gmail.com', 6789, 3);

-- --------------------------------------------------------

--
-- Table structure for table `enseignant_module`
--

CREATE TABLE `enseignant_module` (
  `id_enseignant` int(11) NOT NULL,
  `id_module` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enseignant_module`
--

INSERT INTO `enseignant_module` (`id_enseignant`, `id_module`) VALUES
(2, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `etudiants`
--

CREATE TABLE `etudiants` (
  `id_etudiant` int(11) NOT NULL,
  `matricule` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `id_groupe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `etudiants`
--

INSERT INTO `etudiants` (`id_etudiant`, `matricule`, `nom`, `prenom`, `id_groupe`) VALUES
(1, '221931001', 'Amrani', 'Karim', 1),
(2, '221931002', 'Benali', 'Sara', 1),
(3, '221931003', 'Cherif', 'Youcef', 1),
(4, '221931004', 'Djebbar', 'Lina', 1),
(5, '221931005', 'Ferhat', 'Amine', 1),
(6, '221932001', 'Hamidi', 'Nour', 2),
(7, '221932002', 'Kaci', 'Riad', 2),
(8, '221932003', 'Lounis', 'Asma', 2),
(9, '221932004', 'Meziane', 'Bilal', 2),
(10, '221932005', 'Ouali', 'Meriem', 2),
(11, '221933001', 'Rahmani', 'Djamel', 3),
(12, '221933002', 'Saadi', 'Imane', 3),
(13, '221933003', 'Tabet', 'Omar', 3),
(14, '221933004', 'Yahiaoui', 'Fatima', 3),
(15, '221933005', 'Ziani', 'Walid', 3),
(16, '221934001', 'Aissa', 'Ryad', 4),
(17, '221934002', 'Boudali', 'Nadia', 4),
(18, '221934003', 'Chabane', 'Sofiane', 4),
(19, '221934004', 'Daoudi', 'Hajar', 4),
(20, '221934005', 'Ghezali', 'Tarek', 4);

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `id_evaluation` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `id_groupe` int(11) NOT NULL,
  `type_eval` enum('Contrôle 1','Contrôle 2','Examen final','TP') NOT NULL,
  `date_eval` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`id_evaluation`, `id_module`, `id_groupe`, `type_eval`, `date_eval`) VALUES
(1, 1, 1, 'Contrôle 1', '2026-04-17'),
(2, 1, 1, 'Contrôle 2', '2026-04-17'),
(3, 1, 1, 'TP', '2026-04-17'),
(4, 1, 1, 'Examen final', '2026-04-17'),
(5, 4, 3, 'Contrôle 1', '2026-04-17'),
(6, 4, 3, 'Contrôle 2', '2026-04-17'),
(7, 4, 3, 'TP', '2026-04-17'),
(8, 4, 3, 'Examen final', '2026-04-17');

-- --------------------------------------------------------

--
-- Table structure for table `groupes`
--

CREATE TABLE `groupes` (
  `id_groupe` int(11) NOT NULL,
  `nom_groupe` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groupes`
--

INSERT INTO `groupes` (`id_groupe`, `nom_groupe`) VALUES
(1, 'Groupe 1'),
(2, 'Groupe 2'),
(3, 'Groupe 3'),
(4, 'Groupe 4');

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id_module` int(11) NOT NULL,
  `nom_module` varchar(100) NOT NULL,
  `code_module` varchar(20) DEFAULT NULL,
  `niveau` varchar(50) DEFAULT NULL,
  `semestre` varchar(20) DEFAULT NULL,
  `coef` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id_module`, `nom_module`, `code_module`, `niveau`, `semestre`, `coef`) VALUES
(1, 'Algorithmique', 'ALGO', 'L1 Informatique', 'Semestre 1', 3),
(2, 'Base de données', 'BDD', 'L2 Informatique', 'Semestre 3', 4),
(3, 'Systèmes ','exploitation', 'SE', 'L2 Informatique', 'Semestre 4', 3),
(4, 'Réseaux informatiques', 'RI', 'L3 Informatique', 'Semestre 5', 4),
(5, 'Programmation web', 'PW', 'L3 Informatique', 'Semestre 6', 3);

-- --------------------------------------------------------

--
-- Table structure for table `module_groupe`
--

CREATE TABLE `module_groupe` (
  `id_module` int(11) NOT NULL,
  `id_groupe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `module_groupe`
--

INSERT INTO `module_groupe` (`id_module`, `id_groupe`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(2, 1),
(2, 2),
(3, 3),
(3, 4),
(4, 1),
(4, 3),
(5, 2),
(5, 4);

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id_note` int(11) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `id_evaluation` int(11) NOT NULL,
  `note` decimal(4,2) NOT NULL CHECK (`note` >= 0 and `note` <= 20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id_note`, `id_etudiant`, `id_evaluation`, `note`) VALUES
(1, 1, 1, 0.00),
(2, 1, 2, 0.00),
(3, 2, 1, 0.00),
(4, 2, 4, 0.00),
(5, 11, 5, 0.00),
(6, 11, 6, 0.00),
(7, 3, 1, 0.00),
(8, 4, 1, 0.00),
(9, 5, 1, 0.00),
(10, 2, 2, 0.00),
(11, 3, 2, 0.00),
(12, 4, 2, 0.00),
(13, 5, 2, 0.00),
(14, 1, 3, 0.00),
(15, 2, 3, 0.00),
(16, 3, 3, 0.00),
(17, 4, 3, 0.00),
(18, 5, 3, 0.00),
(19, 1, 4, 0.00),
(20, 3, 4, 0.00),
(21, 4, 4, 0.00),
(22, 5, 4, 0.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrateurs`
--
ALTER TABLE `administrateurs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enseignants`
--
ALTER TABLE `enseignants`
  ADD UNIQUE KEY `nom` (`nom`,`prenom`,`matricule`,`email`) USING HASH;

--
-- Indexes for table `enseignant_module`
--
ALTER TABLE `enseignant_module`
  ADD PRIMARY KEY (`id_enseignant`,`id_module`);

--
-- Indexes for table `etudiants`
--
ALTER TABLE `etudiants`
  ADD PRIMARY KEY (`id_etudiant`),
  ADD UNIQUE KEY `matricule` (`matricule`),
  ADD KEY `id_groupe` (`id_groupe`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id_evaluation`),
  ADD KEY `id_module` (`id_module`),
  ADD KEY `id_groupe` (`id_groupe`);

--
-- Indexes for table `groupes`
--
ALTER TABLE `groupes`
  ADD PRIMARY KEY (`id_groupe`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id_module`);

--
-- Indexes for table `module_groupe`
--
ALTER TABLE `module_groupe`
  ADD PRIMARY KEY (`id_module`,`id_groupe`),
  ADD KEY `id_groupe` (`id_groupe`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id_note`),
  ADD UNIQUE KEY `unique_note` (`id_etudiant`,`id_evaluation`),
  ADD KEY `id_evaluation` (`id_evaluation`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrateurs`
--
ALTER TABLE `administrateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `etudiants`
--
ALTER TABLE `etudiants`
  MODIFY `id_etudiant` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id_evaluation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `groupes`
--
ALTER TABLE `groupes`
  MODIFY `id_groupe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id_module` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id_note` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `etudiants`
--
ALTER TABLE `etudiants`
  ADD CONSTRAINT `etudiants_ibfk_1` FOREIGN KEY (`id_groupe`) REFERENCES `groupes` (`id_groupe`) ON DELETE CASCADE;

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`id_module`) REFERENCES `modules` (`id_module`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`id_groupe`) REFERENCES `groupes` (`id_groupe`) ON DELETE CASCADE;

--
-- Constraints for table `module_groupe`
--
ALTER TABLE `module_groupe`
  ADD CONSTRAINT `module_groupe_ibfk_1` FOREIGN KEY (`id_module`) REFERENCES `modules` (`id_module`) ON DELETE CASCADE,
  ADD CONSTRAINT `module_groupe_ibfk_2` FOREIGN KEY (`id_groupe`) REFERENCES `groupes` (`id_groupe`) ON DELETE CASCADE;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`id_evaluation`) REFERENCES `evaluations` (`id_evaluation`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
