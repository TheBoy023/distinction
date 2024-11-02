-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2024 at 09:37 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `finals`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(6) NOT NULL,
  `admin_department` varchar(255) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `admin_department`, `admin_name`, `email`, `password`) VALUES
(1, 'College of Technology', 'John Loie Mata', 'johnloiemata@gmail.com', '$2y$10$TYCK3I7CPK5vZWFhCpNKV.hLrfhz4j0cZM7YYM3qL2tIz/q0lcrvm');

-- --------------------------------------------------------

--
-- Table structure for table `calculate_average`
--

CREATE TABLE `calculate_average` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `year_level` varchar(100) NOT NULL,
  `semester` varchar(100) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `units` decimal(4,2) NOT NULL,
  `grade` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `calculate_average`
--

INSERT INTO `calculate_average` (`id`, `student_id`, `year_level`, `semester`, `course_code`, `subject`, `description`, `units`, `grade`, `created_at`) VALUES
(1, 3210821, 'First Year', 'First Semester', 'CT86 ', 'GEC-RPH ', 'READINGS IN PHILIPPINE HISTORY', '3.00', '1.50', '2024-10-17 06:34:12'),
(2, 3210821, 'First Year', 'First Semester', 'CT87 ', 'GEC-MMW', 'MATHEMATICS IN THE MODERN WORLD ', '3.00', '1.80', '2024-10-17 06:34:12'),
(3, 3210821, 'First Year', 'First Semester', 'CT88 ', 'GEE-TEM ', 'THE ENTREPRENEURIAL MIND', '3.00', '1.30', '2024-10-17 06:34:12'),
(4, 3210821, 'First Year', 'First Semester', 'CT89 ', 'CC 111', 'INTRODUCTION TO COMPUTING', '3.00', '1.90', '2024-10-17 06:34:12'),
(5, 3210821, 'First Year', 'First Semester', 'CT90 ', 'CC 112 ', 'COMPUTER PROGRAMMING 1 (LEC) ', '2.00', '1.50', '2024-10-17 06:34:12'),
(6, 3210821, 'First Year', 'First Semester', 'CT91 ', 'CC 112 L', 'COMPUTER PROGRAMMING 1 (LAB)', '3.00', '1.50', '2024-10-17 06:34:12'),
(7, 3210821, 'First Year', 'First Semester', 'CT92 ', 'AP 1 ', 'MULTIMEDIA', '3.00', '1.60', '2024-10-17 06:34:12'),
(8, 3210821, 'First Year', 'First Semester', 'CT93 ', 'PE 1 ', 'PHYSICAL EDUCATION 1', '2.00', '2.20', '2024-10-17 06:34:12'),
(9, 3210821, 'First Year', 'First Semester', 'NS1 ', 'NSTP 1 ', 'NATIONAL SERVICE TRAINING PROGRAM  1', '3.00', '1.50', '2024-10-17 06:34:12'),
(10, 3210821, 'First Year', 'First Semester', '', '', '', '0.00', '0.00', '2024-10-17 06:34:12'),
(11, 3210821, 'First Year', 'Second Semester', 'CT379 ', 'GEC-PC', 'PURPOSIVE COMMUNICATION ', '3.00', '2.30', '2024-10-17 06:42:45'),
(12, 3210821, 'First Year', 'Second Semester', 'CT380 ', 'GEC-STS ', 'SCIENCE, TECHNOLOGY AND SOCIETY ', '3.00', '1.60', '2024-10-17 06:42:45'),
(13, 3210821, 'First Year', 'Second Semester', 'CT381 ', 'GEC-US', 'UNDERSTANDING THE SELF ', '3.00', '1.80', '2024-10-17 06:42:45'),
(14, 3210821, 'First Year', 'Second Semester', 'CT382 ', 'GEE-GSPS', 'GENDER AND SOCIETY WITH PEACE  STUDIES', '3.00', '1.90', '2024-10-17 06:42:45'),
(15, 3210821, 'First Year', 'Second Semester', 'CT383 ', 'CC 123', 'COMPUTER PROGRAMMING 2 (LEC) ', '2.00', '1.70', '2024-10-17 06:42:45'),
(16, 3210821, 'First Year', 'Second Semester', 'CT384 ', 'CC 123L', ' COMPUTER PROGRAMMING 2 (LAB) ', '3.00', '1.40', '2024-10-17 06:42:45'),
(17, 3210821, 'First Year', 'Second Semester', 'CT385 ', 'PC  121/MATH-E  2', 'DISCRETE MATHEMATICS', '3.00', '1.40', '2024-10-17 06:42:45'),
(18, 3210821, 'First Year', 'Second Semester', 'CT386 ', 'AP 2 ', 'DIGITAL LOGIC DESIGN', '3.00', '1.20', '2024-10-17 06:42:45'),
(19, 3210821, 'First Year', 'Second Semester', 'CT387 ', 'PE 2 ', 'PHYSICAL EDUCATION 2', '2.00', '2.00', '2024-10-17 06:42:45'),
(20, 3210821, 'First Year', 'Second Semester', 'NS1 ', 'NSTP 2', 'NATIONAL SERVICE TRAINING PROGRAM  2', '3.00', '1.40', '2024-10-17 06:42:45'),
(21, 3210821, 'Second Year', 'First Semester', 'CT124 ', 'GEC-E ', 'ETHICS', '3.00', '1.70', '2024-10-17 06:48:38'),
(22, 3210821, 'Second Year', 'First Semester', 'CT125 ', 'GEE-ES', 'ENVIRONMENTAL SCIENCE ', '3.00', '1.90', '2024-10-17 06:48:38'),
(23, 3210821, 'Second Year', 'First Semester', 'CT126 ', 'GEC-LWR', 'LIFE AND WORKS OF RIZAL', '3.00', '1.70', '2024-10-17 06:48:38'),
(24, 3210821, 'Second Year', 'First Semester', 'CT127 ', 'PC 212', 'QUANTITATIVE METHODS (MODELING &  SIMULATION)', '3.00', '2.00', '2024-10-17 06:48:38'),
(25, 3210821, 'Second Year', 'First Semester', 'CT128 ', 'CC 214', 'DATA STRUCTURES AND ALGORITHMS  (LEC)', '2.00', '1.20', '2024-10-17 06:48:38'),
(26, 3210821, 'Second Year', 'First Semester', 'CT129', ' CC 214L', 'DATA STRUCTURE AND ALGORITHMS  (LAB)', '3.00', '1.20', '2024-10-17 06:48:38'),
(27, 3210821, 'Second Year', 'First Semester', 'CT130 ', 'P ELEC 1', 'PROFESSIONAL ELECTIVE 1', '3.00', '2.00', '2024-10-17 06:48:38'),
(28, 3210821, 'Second Year', 'First Semester', 'CT131 ', 'P ELEC 2', 'PROFESSIONAL ELECTIVE 2 ', '3.00', '1.90', '2024-10-17 06:48:38'),
(29, 3210821, 'Second Year', 'First Semester', 'CT132 ', 'PE 3 ', 'PHYSICAL EDUCATION 3 ', '2.00', '1.30', '2024-10-17 06:48:38'),
(30, 3210821, 'Second Year', 'First Semester', '', '', '', '0.00', '0.00', '2024-10-17 06:48:38'),
(31, 3210821, 'Second Year', 'Second Semester', 'CT135 ', 'GEC-TCW', 'THE CONTEMPORARY WORLD ', '3.00', '1.60', '2024-10-17 06:54:22'),
(32, 3210821, 'Second Year', 'Second Semester', 'CT136 ', 'PC 223', 'INTEGRATIVE PROGRAMMING AND  TECHNOLOGIES 1', '3.00', '1.30', '2024-10-17 06:54:22'),
(33, 3210821, 'Second Year', 'Second Semester', 'CT137 ', 'PC 224', 'NETWORKING 1', '3.00', '1.30', '2024-10-17 06:54:22'),
(34, 3210821, 'Second Year', 'Second Semester', 'CT138 ', 'CC 225', 'INFORMATION MANAGEMENT (LEC) ', '2.00', '1.50', '2024-10-17 06:54:22'),
(35, 3210821, 'Second Year', 'Second Semester', 'CT139 ', 'CC 225L', 'INFORMATION MANAGEMENT (LAB) ', '3.00', '1.50', '2024-10-17 06:54:22'),
(36, 3210821, 'Second Year', 'Second Semester', 'CT140 ', 'P ELEC 3', 'PROFESSIONAL ELECTIVE 3', '3.00', '1.40', '2024-10-17 06:54:22'),
(37, 3210821, 'Second Year', 'Second Semester', 'CT141 ', 'AP 3', 'ASP.NET ', '3.00', '1.50', '2024-10-17 06:54:22'),
(38, 3210821, 'Second Year', 'Second Semester', 'CT142 ', 'PE 4 ', 'PHYSICAL EDUCATION 4', '2.00', '1.30', '2024-10-17 06:54:22'),
(39, 3210821, 'Second Year', 'Second Semester', '', '', '', '0.00', '0.00', '2024-10-17 06:54:22'),
(40, 3210821, 'Second Year', 'Second Semester', '', '', '', '0.00', '0.00', '2024-10-17 06:54:22'),
(41, 3210821, 'Third Year', 'First Semester', 'CT236 ', 'GEC-KAF ', 'KOMUNIKASYON SA AKADEMIKONG  FILIPINO', '3.00', '1.50', '2024-10-17 06:58:36'),
(42, 3210821, 'Third Year', 'First Semester', 'CT237 ', 'PC 315', 'NETWORKING 2 (LEC) ', '2.00', '1.40', '2024-10-17 06:58:36'),
(43, 3210821, 'Third Year', 'First Semester', 'CT238 ', 'PC 315L', 'NETWORKING 2 (LAB) ', '3.00', '1.40', '2024-10-17 06:58:36'),
(44, 3210821, 'Third Year', 'First Semester', 'CT239 ', 'PC 316', 'SYSTEMS INTEGRATION AND  ARCHITECTURE 1 ', '3.00', '1.60', '2024-10-17 06:58:36'),
(45, 3210821, 'Third Year', 'First Semester', 'CT240 ', 'PC 317', 'INTRODUCTION TO HUMAN COMPUTER  INTERACTION', '3.00', '1.60', '2024-10-17 06:58:36'),
(46, 3210821, 'Third Year', 'First Semester', 'CT241 ', 'PC 318', 'DATABASE MANAGEMENT SYSTEMS ', '3.00', '1.40', '2024-10-17 06:58:36'),
(47, 3210821, 'Third Year', 'First Semester', 'CT242 ', 'CC 316', 'APPLICATIONS DEVELOPMENT AND  EMERGING TECHNOLOGIES', '3.00', '1.90', '2024-10-17 06:58:36'),
(48, 3210821, 'Third Year', 'First Semester', '', '', '', '0.00', '0.00', '2024-10-17 06:58:36'),
(49, 3210821, 'Third Year', 'First Semester', '', '', '', '0.00', '0.00', '2024-10-17 06:58:36'),
(50, 3210821, 'Third Year', 'First Semester', '', '', '', '0.00', '0.00', '2024-10-17 06:58:36'),
(51, 3210845, 'Third Year', 'Second Semester', 'CT239 ', 'GEC-AA', 'ART APPRECIATION ', '3.00', '1.40', '2024-10-17 07:04:00'),
(52, 3210845, 'Third Year', 'Second Semester', 'CT240 ', 'GEC-PPTP', 'PAGBASA AT PAGSULAT TUNGO SA  PANANALIKSIK ', '3.00', '1.20', '2024-10-17 07:04:00'),
(53, 3210845, 'Third Year', 'Second Semester', 'CT241 ', 'PC 329', 'CAPSTONE PROJECT AND RESEARCH 1  (TECHNOPRENEURSHIP 1) ', '3.00', '1.40', '2024-10-17 07:04:00'),
(54, 3210845, 'Third Year', 'Second Semester', 'CT242 ', 'PC 3210', 'SOCIAL AND PROFESSIONAL ISSUES ', '3.00', '1.60', '2024-10-17 07:04:00'),
(55, 3210845, 'Third Year', 'Second Semester', 'CT243 ', 'PC 3211 ', 'INFORMATION ASSURANCE AND  SECURITY 1 (LEC) ', '2.00', '1.60', '2024-10-17 07:04:00'),
(56, 3210845, 'Third Year', 'Second Semester', 'CT244 ', 'PC 3211L', 'INFORMATION ASSURANCE AND  SECURITY 1 (LAB)', '3.00', '1.40', '2024-10-17 07:04:00'),
(57, 3210845, 'Third Year', 'Second Semester', 'CT245 ', 'AP 4 ', 'IOS MOBILE APPLICATION DEVELOPMENT  CROSS-PLATFORM', '3.00', '1.70', '2024-10-17 07:04:00'),
(58, 3210845, 'Third Year', 'Second Semester', 'CT246 ', 'AP 5', 'TECHNOLOGY AND THE APPLICATION OF  THE INTERNET OF THINGS', '3.00', '1.40', '2024-10-17 07:04:00'),
(59, 3210845, 'Third Year', 'Second Semester', '', '', '', '0.00', '0.00', '2024-10-17 07:04:00'),
(60, 3210845, 'Third Year', 'Second Semester', '', '', '', '0.00', '0.00', '2024-10-17 07:04:00'),
(61, 3210821, 'Fourth Year', 'First Semester', 'CT247 ', 'PC 4114', 'CAPSTONE PROJECT 2 ', '3.00', '1.40', '2024-10-17 07:07:42'),
(62, 3210821, 'Fourth Year', 'First Semester', 'CT248 ', 'PC 4112', 'INFORMATION ASSURANCE AND  SECURITY 2 (LEC) ', '2.00', '1.70', '2024-10-17 07:07:42'),
(63, 3210821, 'Fourth Year', 'First Semester', 'CT249', ' PC 4112L', 'INFORMATION ASSURANCE AND  SECURITY 2 (LAB)', '3.00', '1.50', '2024-10-17 07:07:42'),
(64, 3210821, 'Fourth Year', 'First Semester', 'CT250 ', 'PC 4113', 'SYSTEMS ADMINISTRATION AND  MAINTENANCE ', '3.00', '1.30', '2024-10-17 07:07:42'),
(65, 3210821, 'Fourth Year', 'First Semester', 'CT251 ', 'P ELEC 4', 'PROFESSIONAL ELECTIVE 4', '3.00', '1.60', '2024-10-17 07:07:42'),
(66, 3210821, 'Fourth Year', 'First Semester', 'CT252 ', 'AP 6 ', 'CROSS-PLATFORM SCRIPT  DEVELOPMENT TECHNOLOGY ', '3.00', '1.60', '2024-10-17 07:07:42'),
(67, 3210821, 'Fourth Year', 'First Semester', '', '', '', '0.00', '0.00', '2024-10-17 07:07:42'),
(68, 3210821, 'Fourth Year', 'First Semester', '', '', '', '0.00', '0.00', '2024-10-17 07:07:42'),
(69, 3210821, 'Fourth Year', 'First Semester', '', '', '', '0.00', '0.00', '2024-10-17 07:07:42'),
(70, 3210821, 'Fourth Year', 'First Semester', '', '', '', '0.00', '0.00', '2024-10-17 07:07:42'),
(71, 3210821, 'Fourth Year', 'Second Semester', 'fsdf', 'hjvhvj', 'vjhvjh', '3.00', '1.50', '2024-10-17 07:09:00'),
(72, 3210821, 'Fourth Year', 'Second Semester', 'hvgv', 'dhvh', 'jbdj', '2.00', '1.60', '2024-10-17 07:09:00'),
(73, 3210821, 'Fourth Year', 'Second Semester', 'bjdbkj', 'njbj', 'jkh', '3.00', '1.40', '2024-10-17 07:09:00'),
(74, 3210821, 'Fourth Year', 'Second Semester', 'hbkh', 'bj', 'kjbkj', '3.00', '1.50', '2024-10-17 07:09:00'),
(75, 3210821, 'Fourth Year', 'Second Semester', 'hdhjdb', 'vkjvkdjvb', 'kbdj', '2.00', '1.20', '2024-10-17 07:09:00'),
(76, 3210821, 'Fourth Year', 'Second Semester', '', '', '', '0.00', '0.00', '2024-10-17 07:09:00'),
(77, 3210821, 'Fourth Year', 'Second Semester', '', '', '', '0.00', '0.00', '2024-10-17 07:09:00'),
(78, 3210821, 'Fourth Year', 'Second Semester', '', '', '', '0.00', '0.00', '2024-10-17 07:09:00'),
(79, 3210821, 'Fourth Year', 'Second Semester', '', '', '', '0.00', '0.00', '2024-10-17 07:09:00'),
(80, 3210821, 'Fourth Year', 'Second Semester', '', '', '', '0.00', '0.00', '2024-10-17 07:09:00');

-- --------------------------------------------------------

--
-- Table structure for table `deans_list_averages`
--

CREATE TABLE `deans_list_averages` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `year_level` varchar(100) NOT NULL,
  `semester` varchar(100) NOT NULL,
  `average_grade` decimal(5,2) NOT NULL,
  `deans_list_status` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deans_list_averages`
--

INSERT INTO `deans_list_averages` (`id`, `student_id`, `year_level`, `semester`, `average_grade`, `deans_list_status`, `created_at`) VALUES
(1, 3210821, 'First Year', 'First Semester', '1.63', 'Yes', '2024-10-17 06:34:12'),
(2, 3210821, 'First Year', 'Second Semester', '1.66', 'Yes', '2024-10-17 06:42:45'),
(3, 3210821, 'Second Year', 'First Semester', '1.69', 'Yes', '2024-10-17 06:48:38'),
(4, 3210821, 'Second Year', 'Second Semester', '1.43', 'Yes', '2024-10-17 06:54:22'),
(5, 3210821, 'Third Year', 'First Semester', '1.55', 'Yes', '2024-10-17 06:58:36'),
(6, 3210845, 'Third Year', 'Second Semester', '1.46', 'Yes', '2024-10-17 07:04:00'),
(7, 3210821, 'Fourth Year', 'First Semester', '1.51', 'Yes', '2024-10-17 07:07:42'),
(8, 3210821, 'Fourth Year', 'Second Semester', '1.45', 'Yes', '2024-10-17 07:09:00');

-- --------------------------------------------------------

--
-- Table structure for table `deans_list_students`
--

CREATE TABLE `deans_list_students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `student_name` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `course` varchar(100) NOT NULL,
  `major` varchar(100) NOT NULL,
  `year_level` int(11) NOT NULL,
  `section` varchar(10) NOT NULL,
  `program` varchar(100) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deans_list_students`
--

INSERT INTO `deans_list_students` (`id`, `student_id`, `student_name`, `department`, `course`, `major`, `year_level`, `section`, `program`, `semester`, `file_path`) VALUES
(1, '3210821', 'Rey Neil Castro', 'College of Technology', 'Bachelor of Science in Information Technology', 'Info Tech', 1, 'A', 'Day', 'First Semester', 'student/uploads/MyGrades-3.pdf'),
(2, '3210821', 'Rey Neil Castro', 'College of Technology', 'Bachelor of Science in Information Technology', 'Info Tech', 1, 'A', 'Day', 'Second Semester', 'student/uploads/MyGrades-3.pdf'),
(3, '3210821', 'Rey Neil Castro', 'College of Technology', 'Bachelor of Science in Information Technology', 'Info Tech', 2, 'A', 'Day', 'First Semester', 'student/uploads/MyGrades-3.pdf'),
(4, '3210821', 'Rey Neil Castro', 'College of Technology', 'Bachelor of Science in Information Technology', 'Info Tech', 2, 'A', 'Day', 'Second Semester', 'student/uploads/MyGrades-3.pdf'),
(5, '3210821', 'Rey Neil Castro', 'College of Technology', 'Bachelor of Science in Information Technology', 'Info Tech', 3, 'A', 'Day', 'First Semester', 'student/uploads/MyGrades-3.pdf'),
(6, '3210821', 'Rey Neil Castro', 'College of Technology', 'Bachelor of Science in Information Technology', 'Info Tech', 3, 'A', 'Day', 'Second Semester', 'student/uploads/MyGrades-3.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `latin_grades`
--

CREATE TABLE `latin_grades` (
  `id` int(6) UNSIGNED NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `student_id` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `course` varchar(100) NOT NULL,
  `year1_sem1` decimal(3,2) NOT NULL,
  `year1_sem2` decimal(3,2) NOT NULL,
  `year2_sem1` decimal(3,2) NOT NULL,
  `year2_sem2` decimal(3,2) NOT NULL,
  `year3_sem1` decimal(3,2) NOT NULL,
  `year3_sem2` decimal(3,2) NOT NULL,
  `year4_sem1` decimal(3,2) NOT NULL,
  `year4_sem2` decimal(3,2) NOT NULL,
  `average_grade` decimal(3,2) DEFAULT NULL,
  `honor` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `latin_grades`
--

INSERT INTO `latin_grades` (`id`, `student_name`, `student_id`, `department`, `course`, `year1_sem1`, `year1_sem2`, `year2_sem1`, `year2_sem2`, `year3_sem1`, `year3_sem2`, `year4_sem1`, `year4_sem2`, `average_grade`, `honor`) VALUES
(1, 'Rey Neil Castro', '3210821', 'College of Technology', 'Bachelor of Science in Information Technology', '1.60', '1.60', '1.50', '1.50', '1.40', '1.40', '1.50', '1.30', '1.48', 'Magna Cum Laude');

-- --------------------------------------------------------

--
-- Table structure for table `latin_honor_students`
--

CREATE TABLE `latin_honor_students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `student_name` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `course` varchar(100) NOT NULL,
  `major` varchar(100) NOT NULL,
  `year_level` int(11) NOT NULL,
  `section` varchar(10) NOT NULL,
  `program` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `latin_honor_students`
--

INSERT INTO `latin_honor_students` (`id`, `student_id`, `student_name`, `department`, `course`, `major`, `year_level`, `section`, `program`) VALUES
(1, '3210821', 'Rey Neil Castro', 'College of Technology', 'Bachelor of Science in Information Technology', 'Info Tech', 4, 'A', 'Day');

-- --------------------------------------------------------

--
-- Table structure for table `recent_updates`
--

CREATE TABLE `recent_updates` (
  `id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recent_updates`
--

INSERT INTO `recent_updates` (`id`, `message`, `timestamp`) VALUES
(3, 'Added student ', '2024-10-14 19:18:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `student_id`, `email`, `password`, `department`) VALUES
(1, 'Rey Neil Castro', '3210821', 'reyneilcastro@gmail.com', '$2y$10$Nd6ngGD3EJQs/JuROi52Re9yRN576Ogdd5AjBRK6Utynpmz.VPmGW', 'College of Technology'),
(3, 'loy', '3151227', 'loymata89@gmail.com', '$2y$10$JtSnPNYOTLGiAkCQZrCekex4c8YtSGXprXcJbq8FrzK6lwE0nu5i6', 'College of Technology'),
(4, 'Ruthchelle Ponce', '3210845', 'ruthchelleponce@gmail.com', '$2y$10$gYx.KWu4TPSryGiqCG2jhepYuxzJ7S1FoynTdkveTbP5jp6luXFOW', 'College of Technology');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `calculate_average`
--
ALTER TABLE `calculate_average`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deans_list_averages`
--
ALTER TABLE `deans_list_averages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deans_list_students`
--
ALTER TABLE `deans_list_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `latin_grades`
--
ALTER TABLE `latin_grades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `latin_honor_students`
--
ALTER TABLE `latin_honor_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recent_updates`
--
ALTER TABLE `recent_updates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `calculate_average`
--
ALTER TABLE `calculate_average`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `deans_list_averages`
--
ALTER TABLE `deans_list_averages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `deans_list_students`
--
ALTER TABLE `deans_list_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `latin_grades`
--
ALTER TABLE `latin_grades`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `latin_honor_students`
--
ALTER TABLE `latin_honor_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `recent_updates`
--
ALTER TABLE `recent_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
