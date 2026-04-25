-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 24, 2026 at 02:48 PM
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
-- Database: `gestion_scolarite`--

-- --------------------------------------------------------

--
-- Table structure for table `administrateurs`
--

CREATE TABLE `administrateurs` (
  `id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administrateurs`
--

INSERT INTO `administrateurs` (`id`, `email`, `password`, `role`, `nom`, `prenom`) VALUES
(1, 'admin@usthb.dz', 'adminusthb', 'admin', 'med', 'mohamed');

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
  `numero` int(11) NOT NULL,
  `palier` varchar(5) NOT NULL,
  `specialite` varchar(10) NOT NULL,
  `section` varchar(5) NOT NULL,
  `matricule` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(70) NOT NULL,
  `etat` varchar(15) NOT NULL,
  `groupe_td` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `etudiants`
--

INSERT INTO `etudiants` (`numero`, `palier`, `specialite`, `section`, `matricule`, `nom`, `prenom`, `etat`, `groupe_td`) VALUES
(1, 'L2', 'ISIL', 'C', '212431859912', 'ABAOUI', 'MELISSA-LYNA', 'ADM', 2),
(2, 'L2', 'ISIL', 'C', '232431546203', 'ABBAS', 'MAYA MYRIAM', 'ADM', 2),
(3, 'L2', 'ISIL', 'C', '242431599204', 'ABDELHAMID', 'AKRAM', 'ADC', 4),
(4, 'L2', 'ISIL', 'C', '222231609707', 'ABDELLAOUI', 'YOUCEF', 'RNTG', 4),
(5, 'L2', 'ISIL', 'C', '242431676416', 'ABDELLATIF', 'SARA', 'ADM', 1),
(6, 'L2', 'ISIL', 'C', '232331500107', 'AISSA', 'NIHAD', 'AJR', 3),
(7, 'L2', 'ISIL', 'C', '242431370909', 'AISSAOUI', 'IMAD EDDINE', 'ADC', 2),
(8, 'L2', 'ISIL', 'C', '232331413601', 'AISSAOUI', 'YOUSRA', 'ADM', 4),
(9, 'L2', 'ISIL', 'C', '242431368913', 'AIT KACI', 'ABDELMALEK', 'ADM', 2),
(10, 'L2', 'ISIL', 'C', '222231413217', 'AIT MEHDI', 'IMED FAROUK', 'AJR', 4),
(11, 'L2', 'ISIL', 'C', '242431438719', 'AIT OUAMAR', 'AYA', 'ADM', 2),
(12, 'L2', 'ISIL', 'C', '242431577510', 'AKACEM', 'ABDENOUR', 'ADM', 1),
(13, 'L2', 'ISIL', 'C', '222231581410', 'AKOUIRADJEMOU', 'OUAIL ABD ERRAOUF', 'RNTG', 4),
(14, 'L2', 'ISIL', 'C', '242431461716', 'AKTOUF', 'IKRAM', 'ADM', 4),
(15, 'L2', 'ISIL', 'C', '232333374911', 'ALI', 'MOHAMMED AMINE ABDERRAOUF', 'AJR', 4),
(16, 'L2', 'ISIL', 'C', '242431453208', 'ALIM', 'KAMEL', 'ADC', 1),
(17, 'L2', 'ISIL', 'C', '222231438707', 'AMDIDOUCHE', 'ASMAA', 'AJR', 3),
(18, 'L2', 'ISIL', 'C', '232332170007', 'AMMICHE', 'NOUR ELHOUDA', 'ADM', 3),
(19, 'L2', 'ISIL', 'C', '232331519001', 'AMOR', 'YOUNES', 'RNTG', 1),
(20, 'L2', 'ISIL', 'C', '232331499219', 'ASSABAT', 'ABDELMALEK', 'AJR', 1),
(21, 'L2', 'ISIL', 'C', '232333087110', 'AZZOUG', 'Mélissa', 'RNTG', 1),
(22, 'L2', 'ISIL', 'C', '232331738702', 'AZZOUZ', 'ABDENNOUR', 'AJR', 3),
(23, 'L2', 'ISIL', 'C', '242431730502', 'AZZOUZI', 'MEHDI', 'ADM', 3),
(24, 'L2', 'ISIL', 'C', '222233370909', 'BAHA', 'NESRINE', 'AJR', 2),
(25, 'L2', 'ISIL', 'C', '242431620609', 'BAOUZ', 'DOUAA', 'ADM', 4),
(26, 'L2', 'ISIL', 'C', '232331388007', 'BARA', 'IMADEDDINE', 'AJR', 1),
(27, 'L2', 'ISIL', 'C', '232331412506', 'BEARCIA', 'ISSAM EDDINE', 'ADM', 2),
(28, 'L2', 'ISIL', 'C', '242431597817', 'BELABED', 'IMENE ZOHRA', 'ADM', 3),
(29, 'L2', 'ISIL', 'C', '232331667419', 'BELABRIK', 'YASMINE FATMA ZOHRA', 'AJR', 2),
(30, 'L2', 'ISIL', 'C', '232331441703', 'BELARBI', 'REDA ABDELKARIM', 'AJR', 3),
(31, 'L2', 'ISIL', 'C', '232331715109', 'BELHADJ', 'ABDELHAKIM', 'ADC', 4),
(32, 'L2', 'ISIL', 'C', '242431715620', 'BELKHIR', 'MOHAMED', 'ADM', 1),
(33, 'L2', 'ISIL', 'C', '222231345706', 'BEN AISSA CHRIF', 'AYA', 'AJR', 3),
(34, 'L2', 'ISIL', 'C', '242431460816', 'BEN BACHIR', 'MOHAMED LOKMAN', 'ADC', 1),
(35, 'L2', 'ISIL', 'C', '242431461920', 'BENABDELLATIF', 'OMAR', 'ADC', 4),
(36, 'L2', 'ISIL', 'C', '242431786010', 'BENAISSA', 'BOUCHRA', 'TRFU', 4),
(37, 'L2', 'ISIL', 'C', '232331692611', 'BENAMARA', 'RANIA', 'AJR', 1),
(38, 'L2', 'SIGL', 'A', '222432307908', 'BENAOUDA', 'KHEIRA', 'ADM', 1),
(39, 'L2', 'ISIL', 'C', '242431596411', 'BENCHEIKH', 'NADA', 'ADM', 3),
(40, 'L2', 'ISIL', 'C', '212431656304', 'BENGUESMIA', 'FARAH FARIDA', 'ADM', 4),
(41, 'L2', 'ISIL', 'C', '242431680418', 'BENMOKHTAR', 'YASSER', 'ADC', 1),
(42, 'L2', 'ISIL', 'C', '242431675005', 'BENYAHIA', 'EL WALID ISSAM', 'ADM', 4),
(43, 'L2', 'ISIL', 'C', '232431652101', 'BESSAA', 'MOHAMED AMINE', 'ADM', 4),
(44, 'L2', 'ISIL', 'C', '242431622804', 'BETTAYEB', 'SID ALI', 'ADM', 3),
(45, 'L2', 'ISIL', 'C', '232331424405', 'BOUALI', 'ZINEB AZHAR', 'AJR', 3),
(46, 'L2', 'SIGL', 'A', '232438165206', 'BOUCHIKHI', 'M\'hamed azzeddine', 'ADM', 1),
(47, 'L2', 'ISIL', 'C', '242431440109', 'BOUDANI', 'FATMA ZOHRA', 'ADM', 4),
(48, 'L2', 'ISIL', 'C', '232331499415', 'BOUDAOUD', 'FAIROUZ', 'AJR', 2),
(49, 'L2', 'ISIL', 'C', '232335477206', 'BOUDERRAZ', 'Maroua', 'ADM', 4),
(50, 'L2', 'ISIL', 'C', '192431546202', 'BOUDINE', 'MALIK', 'ADC', 3),
(51, 'L2', 'ISIL', 'C', '242431843605', 'BOUDJANA', 'RADJAA', 'ADC', 1),
(52, 'L2', 'ISIL', 'C', '232331698617', 'BOUDRAF', 'Mouhyeddine ibrahim', 'ADC', 4),
(53, 'L2', 'ISIL', 'C', '232331740411', 'BOUHADDA', 'HAOUA', 'ADC', 1),
(54, 'L2', 'ISIL', 'C', '242431424613', 'BOUHADJA', 'NOURELHOUDA', 'ADC', 4),
(55, 'L2', 'SIGL', 'A', '232339482406', 'BOUHOUNALI', 'ABDELAZIZ', 'ADM', 1),
(56, 'L2', 'ISIL', 'C', '232331544604', 'BOUKERDOUS', 'HANANE', 'RNTG MED', 3),
(57, 'L2', 'ISIL', 'C', '232331621308', 'BOUKHALFA', 'LINA HADIL', 'ADM', 1),
(58, 'L2', 'ISIL', 'C', '232431650501', 'BOUKHARI', 'ADLANE', 'ADM', 4),
(59, 'L2', 'ISIL', 'C', '242431434219', 'BOUKHARI', 'YASSER ABDELMOUMAN', 'ADM', 3),
(60, 'L2', 'ISIL', 'C', '242431577705', 'BOUKTITE', 'MOHAMED ADAM', 'ADC', 1),
(61, 'L2', 'ISIL', 'C', '242431625010', 'BOULAHABAL', 'MARWA', 'TRFU', 4),
(62, 'L2', 'ISIL', 'C', '242431223007', 'BOUMEDIENE', 'LINA MARIA', 'ADM', 4),
(63, 'L2', 'ISIL', 'C', '232331072415', 'BOUMEDINE', 'MOHAMED LYES', 'ADM', 1),
(64, 'L2', 'ISIL', 'C', '242431433019', 'BOUSBAA', 'TADJ EL BAHA LYNA', 'ADM', 1),
(65, 'L2', 'ISIL', 'C', '232331433007', 'BOUSSOUSSOU', 'ABDESSAMED RIADH', 'ADC', 4),
(66, 'L2', 'ISIL', 'C', '242431472812', 'BOUTRAH', 'CHEYMA', 'ADM', 3),
(67, 'L2', 'ISIL', 'C', '232331413914', 'BOUZIANE', 'ABDELLAH', 'RNTG', 2),
(68, 'L2', 'ISIL', 'C', '232331553511', 'BRAHIMI', 'ABDELKRIM', 'ADC', 1),
(69, 'L2', 'ISIL', 'C', '232431859207', 'CHABANE', 'ALA EDDINE', 'ADC', 3),
(70, 'L2', 'ISIL', 'C', '242431362004', 'CHAOUADI', 'ABDALLAH', 'ADC', 2),
(71, 'L2', 'ISIL', 'C', '232331641011', 'CHEBBAH', 'MANEL CHAIMA', 'AJR', 3),
(72, 'L2', 'ISIL', 'C', '232331674009', 'CHELABI', 'CHAIMA', 'AJR', 2),
(73, 'L2', 'ISIL', 'C', '242431598307', 'CHERFAOUI', 'FARES', 'ADM', 4),
(74, 'L2', 'ISIL', 'C', '232331488404', 'CHERGUI', 'SAFIA', 'AJR', 3),
(75, 'L2', 'ISIL', 'C', '222231619218', 'CHERIFI', 'ABDERRAHMANE', 'ADC', 1),
(76, 'L2', 'ISIL', 'C', '242431414302', 'CHERIFI', 'RAHMA', 'ADM', 2),
(77, 'L2', 'ISIL', 'C', '242431577704', 'CHORFI', 'YACINE', 'ADM', 3),
(78, 'L2', 'ISIL', 'C', '232331600106', 'DADDA', 'YACINE', 'RNTG', 2),
(79, 'L2', 'ISIL', 'C', '242431679715', 'DAHMANI', 'ANAIS', 'ADM', 1),
(80, 'L2', 'ISIL', 'C', '232331781916', 'DAYA', 'AYOUB', 'AJR', 1),
(81, 'L2', 'ISIL', 'C', '242431370906', 'DERRADJI', 'ABDERRAHMANE', 'ADM', 2),
(82, 'L2', 'ISIL', 'C', '232335051703', 'DIAFI', 'AYAT ERRAHMANE', 'AJR', 4),
(83, 'L2', 'ISIL', 'C', '232331406306', 'DJAHEL', 'YOUSRA', 'AJR', 1),
(84, 'L2', 'ISIL', 'C', '242431597707', 'DJENNADI', 'ACHRAF ISLAM', 'ADM', 2),
(85, 'L2', 'ISIL', 'C', '242431414315', 'DOUDOU', 'SALSABILA', 'ADM', 2),
(86, 'L2', 'ISIL', 'C', '242431475412', 'DOUKHI', 'SALAH EDDIN', 'ADC', 3),
(87, 'L2', 'ISIL', 'C', '242431454420', 'DRIDI', 'WALID', 'ADC', 3),
(88, 'L2', 'ISIL', 'C', '242431413006', 'FERKOUS', 'MOHAMED HOUSSAM', 'ADC', 4),
(89, 'L2', 'ISIL', 'C', '242431423103', 'FERRAH', 'NIHAL YASMINE', 'ADM', 1),
(90, 'L2', 'ISIL', 'C', '242431486406', 'FERRANI', 'OUSSAMA ABDELKARIM', 'ADM', 2),
(91, 'L2', 'ISIL', 'C', '232331440603', 'FISSAH', 'KHADIDIJA', 'AJR', 2),
(92, 'L2', 'ISIL', 'C', '232331418809', 'GHARBI', 'AICHA', 'ADM', 1),
(93, 'L2', 'ISIL', 'C', '242431461709', 'GHERMOUL', 'ANES', 'ADC', 3),
(94, 'L2', 'ISIL', 'C', '242431616006', 'GUETTACHE', 'CERINE', 'ADM', 3),
(95, 'L2', 'ISIL', 'C', '242431776615', 'HABBOUCHE', 'NOUH', 'ADM', 2),
(96, 'L2', 'ISIL', 'C', '242431621102', 'HAFI', 'ABDERRAOUF', 'ADM', 1),
(97, 'L2', 'ISIL', 'C', '242432464917', 'HAIF', 'ISRAA', 'ADM', 3),
(98, 'L2', 'ISIL', 'C', '222231620901', 'HAMANI', 'HIBA MERIEM', 'AJR', 3),
(99, 'L2', 'ISIL', 'C', '232331601509', 'HAMITI', 'SIRINE', 'AJR', 4),
(100, 'L2', 'ISIL', 'C', '242431777702', 'HAMMADOU', 'INES SALSABIL', 'ADC', 3),
(101, 'L2', 'ISIL', 'C', '242431624503', 'HASNI', 'HOCINE', 'ADC', 1),
(102, 'L2', 'ISIL', 'C', '232331453804', 'HERSOUS', 'YASMINE', 'RNTG', 3),
(103, 'L2', 'ISIL', 'C', '232333149512', 'IDJOUBAR', 'LITISSIA', 'ADM', 3),
(104, 'L2', 'ISIL', 'C', '242431433013', 'IKRAM', 'BOUTINE', 'ADM', 1),
(105, 'L2', 'ISIL', 'C', '232331338314', 'KABOUCHE', 'YAHIA', 'ADM', 3),
(106, 'L2', 'ISIL', 'C', '232331430512', 'KADRI', 'OUAIS', 'ADM', 3),
(107, 'L2', 'ISIL', 'C', '242431476317', 'KEDDAR', 'MOHAMED ACYL', 'ADM', 4),
(108, 'L2', 'ISIL', 'C', '232331572613', 'KEDIDAH', 'SAFOUANE ABDERRAHMANE', 'RNTG', 4),
(109, 'L2', 'ISIL', 'C', '232331674415', 'KESSI', 'YAZID', 'AJR', 4),
(110, 'L2', 'ISIL', 'C', '212431546808', 'KHALFOUN', 'HADIL', 'ADM', 4),
(111, 'L2', 'ISIL', 'C', '232432511703', 'KHELIFA', 'MOHAMED BACHIR', 'ADM', 1),
(112, 'L2', 'ISIL', 'C', '242431575703', 'KHELIL', 'MERIEM', 'ADM', 1),
(113, 'L2', 'ISIL', 'C', '242431486807', 'KHELLAS', 'MARIA', 'ADM', 1),
(114, 'L2', 'ISIL', 'C', '232331734515', 'KHETTAB', 'IMEDEDDIEN', 'RNTG', 2),
(115, 'L2', 'ISIL', 'C', '242431431503', 'LAGRAA', 'ABDERRAHMANE', 'ADM', 3),
(116, 'L2', 'ISIL', 'C', '242431454303', 'LAIB', 'ABD EL DJALIL', 'ADC', 3),
(117, 'L2', 'ISIL', 'C', '232331532706', 'LAIDI', 'RACIM', 'ADM', 2),
(118, 'L2', 'ISIL', 'C', '242431577219', 'LAKEHAL', 'DEKRAH', 'ADM', 3),
(119, 'L2', 'ISIL', 'C', '222231412710', 'LAMARA', 'AYOUB', 'ADC', 4),
(120, 'L2', 'ISIL', 'C', '242431618608', 'LAMARA', 'MELYNA', 'ADC', 2),
(121, 'L2', 'ISIL', 'C', '232331531201', 'LARBI', 'LINA', 'AJR', 2),
(122, 'L2', 'ISIL', 'C', '242431386417', 'LASLEDJ', 'NOURA', 'ADC', 3),
(123, 'L2', 'ISIL', 'C', '232331639705', 'LOUCIF', 'AISSA', 'AJR', 2),
(124, 'L2', 'ISIL', 'C', '242431441601', 'MADIOU', 'SALEM', 'ADM', 3),
(125, 'L2', 'ISIL', 'C', '23239DZA20982', 'MADJENE', 'MALAK', 'ADM', 4),
(126, 'L2', 'ISIL', 'C', '242431579806', 'MAHALELAINE', 'AYMEN AYOUB SOFIANE', 'ADC', 1),
(127, 'L2', 'ISIL', 'C', '242431475712', 'MAHDI', 'MELINA', 'ADM', 4),
(128, 'L2', 'ISIL', 'C', '232331717713', 'MAHDI', 'MOHAMED NAZIM', 'ADC', 4),
(129, 'L2', 'ISIL', 'C', '232431549320', 'MAHROUG', 'MARYA', 'ADC', 1),
(130, 'L2', 'ISIL', 'C', '232331503216', 'MAMMERI', 'YASMINE', 'ADC', 3),
(131, 'L2', 'ISIL', 'C', '242431562616', 'MAOUCHE', 'MOHAMED RAFIK', 'ADC', 1),
(132, 'L2', 'ISIL', 'C', '232331602210', 'MECHAI', 'OUIAM', 'RNTG', 2),
(133, 'L2', 'ISIL', 'C', '232331223806', 'MECHTI', 'ABDALLAH', 'ADC', 4),
(134, 'L2', 'ISIL', 'C', '242431559810', 'MEDDOUR', 'IMENE', 'ADC', 1),
(135, 'L2', 'ISIL', 'C', '232331532312', 'MEDJAHED', 'RYMA', 'AJR', 2),
(136, 'L2', 'ISIL', 'C', '242431777714', 'MEDJDOUBI', 'NOUR EL ISLAM', 'ADC', 1),
(137, 'L2', 'ISIL', 'C', '212131040805', 'MEFTAH', 'ANFEL', 'TRFE', 3),
(138, 'L2', 'ISIL', 'C', '232331431614', 'MEKDAM', 'MOHAMED IDIR', 'AJR', 4),
(139, 'L2', 'ISIL', 'C', '242431431613', 'MEKKI', 'FATIMA ZAHRA', 'ADM', 4),
(140, 'L2', 'ISIL', 'C', '242431731319', 'MENIA', 'YOUCEF', 'ADM', 2),
(141, 'L2', 'ISIL', 'C', '242431591407', 'MERAR', 'IKRAM', 'ADC', 2),
(142, 'L2', 'ISIL', 'C', '242431622106', 'MESSAOUDI', 'WASSIM', 'ADM', 4),
(143, 'L2', 'ISIL', 'C', '242431434209', 'MEZIANI', 'ABD RAOUF', 'ADM', 3),
(144, 'L2', 'ISIL', 'C', '242431666406', 'MEZIANI', 'SERINE MELISSA', 'ADM', 4),
(145, 'L2', 'ISIL', 'C', '222231498417', 'MOKHTARI', 'ABDERRAHMAN', 'AJR', 4),
(146, 'L2', 'ISIL', 'C', '212131087391', 'MOKNINE', 'ALI', 'TRFU', 2),
(147, 'L2', 'ISIL', 'C', '232331674811', 'MOSTEFA', 'MOHAMED HOCINE', 'ADC', 2),
(148, 'L2', 'ISIL', 'C', '232431861119', 'MOUSSOUS', 'SARAH CHYRAZ', 'ADM', 3),
(149, 'L2', 'ISIL', 'C', '232431535911', 'MOUZAOUI', 'KENZA', 'ADM', 3),
(150, 'L2', 'ISIL', 'C', '242431618418', 'NAIMI', 'MUSTAPHA IYAD', 'ADM', 4),
(151, 'L2', 'ISIL', 'C', '242439340418', 'NASRI', 'ANES ZAKARIA', 'ADC', 2),
(152, 'L2', 'ISIL', 'C', '242431367805', 'NEDIR', 'AHMED BAHA EDDINE', 'ADM', 4),
(153, 'L2', 'ISIL', 'C', '232331032114', 'NID', 'Souheil', 'ADM', 3),
(154, 'L2', 'ISIL', 'C', '242431398806', 'OUAREZKI', 'HICHEM', 'ADM', 1),
(155, 'L2', 'ISIL', 'C', '242431621819', 'OUFERHAT', 'MEHDI', 'ADM', 1),
(156, 'L2', 'ISIL', 'C', '242431433205', 'OUGUENOUNE', 'FERHAT MOHAMED ANIS', 'ADC', 1),
(157, 'L2', 'ISIL', 'C', '232331595914', 'OULDEDINE', 'CHAIMA', 'ADC', 1),
(158, 'L2', 'ISIL', 'C', '232431531515', 'RABEHI', 'CELINA', 'ADC', 3),
(159, 'L2', 'ISIL', 'C', '242431572814', 'RAHILI', 'ANIS', 'ADC', 2),
(160, 'L2', 'ISIL', 'C', '232331430814', 'RAMDANI', 'DOUAA HIBAT ELLAH', 'AJR', 1),
(161, 'L2', 'ISIL', 'C', '242431422801', 'RAMOUL', 'MERIEM', 'ADC', 1),
(162, 'L2', 'ISIL', 'C', '242431383508', 'REMRAM', 'AHMED ELAMINE', 'ADC', 2),
(163, 'L2', 'ISIL', 'C', '232431859120', 'RETIM', 'ABDALLAH', 'ADC', 4),
(164, 'L2', 'ISIL', 'C', '242431624912', 'REZKI', 'RIAD', 'ADC', 1),
(165, 'L2', 'ISIL', 'C', '232433341813', 'ROUIBAH', 'AMINA', 'ADM', 4),
(166, 'L2', 'ISIL', 'C', '232331698506', 'SAADI', 'ISLEM', 'AJR', 2),
(167, 'L2', 'ISIL', 'C', '232331105319', 'SAIB', 'MEZIANE', 'AJR', 4),
(168, 'L2', 'ISIL', 'C', '242431370913', 'SAIDANI', 'MOHAMED DHIAEDDINE', 'ADC', 2),
(169, 'L2', 'ISIL', 'C', '232431526712', 'SBAI', 'AIMEN ABDELOUAHID', 'ADM', 3),
(170, 'L2', 'ISIL', 'C', '242431601409', 'SEDAOUI', 'YOUNES MEHDI', 'ADM', 3),
(171, 'L2', 'ISIL', 'C', '242431696012', 'SELAMA', 'WASSIM', 'ADM', 1),
(172, 'L2', 'ISIL', 'C', '242431621604', 'SEMMAR', 'MOHAMED RACIM', 'ADM', 1),
(173, 'L2', 'ISIL', 'C', '242431423801', 'SERAY', 'IMENE', 'ADC', 3),
(174, 'L2', 'ISIL', 'C', '242431680409', 'SKENDER', 'NEWFEL', 'ADM', 3),
(175, 'L2', 'ISIL', 'C', '232331659203', 'SLIMANI', 'ANIS', 'ADC', 4),
(176, 'L2', 'ISIL', 'C', '242431461919', 'SLIMANI', 'IMAD', 'ADC', 1),
(177, 'L2', 'ISIL', 'C', '232331597818', 'SOUFI', 'MOUDJIB EL RAHMANE', 'AJR', 2),
(178, 'L2', 'ISIL', 'C', '232331500812', 'SOUIDI', 'ICHRAK', 'ADM', 3),
(179, 'L2', 'ISIL', 'C', '232332212801', 'TAKRATI', 'ABDEL MOUMENE', 'AJR', 2),
(180, 'L2', 'ISIL', 'C', '242431572917', 'TAS', 'ISLAM', 'ADC', 3),
(181, 'L2', 'ISIL', 'C', '242431624311', 'TATA', 'ANES', 'ADM', 2),
(182, 'L2', 'ISIL', 'C', '242431596506', 'TAYEB', 'NACIM', 'ADM', 1),
(183, 'L2', 'ISIL', 'C', '232431845311', 'TEHAR', 'SAMI AYOUB', 'ADC', 2),
(184, 'L2', 'ISIL', 'C', '242431722303', 'TEMAM', 'MOHAMED DJAOUED', 'ADM', 2),
(185, 'L2', 'ISIL', 'C', '232331734201', 'TEMLALI', 'OUSSAMA', 'ADC', 4),
(186, 'L2', 'ACAD', 'C', '242431423516', 'TIAR', 'YOUCEF ABDERRAHMANE', 'ADM', 3),
(187, 'L2', 'ISIL', 'C', '242431680215', 'TOUAT', 'MOHAMED ADEM AYOUB', 'ADC', 4),
(188, 'L2', 'ISIL', 'C', '242431730516', 'TSAMDA', 'NESSRINE', 'ADM', 4),
(189, 'L2', 'ISIL', 'C', '242435427010', 'YESSAD', 'Abdelhak', 'TRFU', 2),
(190, 'L2', 'ISIL', 'C', '232331338807', 'ZAHAF', 'ABD ELRAHMAN', 'AJR', 3),
(191, 'L2', 'ISIL', 'C', '232331394803', 'ZAHED', 'RAYAN', 'AJR', 2),
(192, 'L2', 'ISIL', 'C', '232431844615', 'ZAKARIA', 'Madi', 'ADC', 2),
(193, 'L2', 'ISIL', 'C', '232431534320', 'ZEGHDANE', 'ABDENOUR', 'ADC', 2),
(194, 'L2', 'ISIL', 'C', '242431748813', 'ZERAIA', 'MAYA', 'ADM', 1),
(195, 'L2', 'ISIL', 'C', '232331481012', 'ZERGOUN', 'ILYES', 'ADC', 2),
(196, 'L2', 'ISIL', 'C', '222431858709', 'ZERGUI', 'RITADJE', 'ADC', 1),
(197, 'L2', 'ISIL', 'C', '242431680417', 'ZERKOUK', 'WALID', 'ADM', 1),
(198, 'L2', 'ISIL', 'C', '242431614911', 'ZERTIT', 'RABAH HICHEM', 'ADM', 4),
(199, 'L2', 'ISIL', 'C', '232431847516', 'ZIANE', 'DAMIA FARIEL', 'ADM', 1),
(200, 'L2', 'ISIL', 'C', '232331414107', 'ZIGADI', 'YACINE', 'RNTG', 2),
(201, 'L2', 'ISIL', 'C', '232335330411', 'ZIGHED', 'IMEN', 'RNTG', 1),
(202, 'L2', 'ISIL', 'C', '232331650909', 'ZITOUNI', 'SABER', 'ADC', 4),
(203, 'L2', 'ISIL', 'C', '232331346601', 'عزوزي', 'أحلام', 'ADM', 2);

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
  ADD PRIMARY KEY (`numero`);

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
  MODIFY `numero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

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
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`id_evaluation`) REFERENCES `evaluations` (`id_evaluation`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
