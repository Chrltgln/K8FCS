-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 30, 2024 at 06:43 AM
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
-- Database: `k8fcs`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `action` text NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_email`, `action`, `file_name`, `timestamp`) VALUES
(124, 'taglinao06@gmail.com', 'uploaded a file', 'K8FCS.sql', '2024-11-12 16:46:30'),
(125, 'taglinao06@gmail.com', 'deleted a file', 'K8FCS.sql', '2024-11-12 16:48:28'),
(126, 'taglinao06@gmail.com', 'uploaded a file', 'K8FCS.sql', '2024-11-12 16:55:11'),
(127, 'taglinao06@gmail.com', 'uploaded a file', 'adasd asdas_ORCR_2024-11-12_12-57-30_0.jpg', '2024-11-12 16:57:03'),
(128, 'taglinao06@gmail.com', 'uploaded a file', 'NOVEMBER 9 LATEST 3RD UPDATE.sql', '2024-11-12 17:00:37'),
(129, 'taglinao06@gmail.com', 'uploaded a file', 'NOVEMBER 9 LATEST 6TH UPDATE.sql', '2024-11-12 17:07:57'),
(131, 'Employee@employee.com', 'Automatically Declined by the system w/ Transaction ID: 2024-353830-45938', 'N/A', '2024-11-13 09:10:18'),
(132, 'Employee@employee.com', 'Automatically Declined by the system w/ Transaction ID: 2024-353830-45938', 'N/A', '2024-11-13 09:10:57'),
(133, 'Employee@employee.com', 'Accepted Appointment of Transaction ID: 2024-353830-45938', 'N/A', '2024-11-13 09:14:12'),
(134, 'Employee@employee.com', 'Approved Transaction ID: 2024-353830-45938', 'N/A', '2024-11-13 09:15:03'),
(135, 'Employee@employee.com', 'Accepted Payment of Transaction ID: 2024-353830-45938', 'N/A', '2024-11-13 09:18:07'),
(136, 'Employee@employee.com', 'Accepted Appointment of Transaction ID: 2024-834952-90508', 'N/A', '2024-11-13 09:22:58'),
(137, 'Employee@employee.com', 'Accepted Appointment of Transaction ID: 2024-834952-90508', 'N/A', '2024-11-13 09:26:10'),
(138, 'Employee@employee.com', 'Accepted Appointment of Transaction ID: 2024-834952-90508', 'N/A', '2024-11-13 09:29:17'),
(139, 'Employee@employee.com', 'Accepted Appointment of Transaction ID: 2024-544143-25479', 'N/A', '2024-11-13 10:21:19'),
(140, 'Employee@employee.com', 'Approved Transaction ID: 2024-544143-25479', 'N/A', '2024-11-13 10:21:55'),
(141, 'Employee@employee.com', 'Accepted Payment of Transaction ID: 2024-544143-25479', 'N/A', '2024-11-13 10:22:16'),
(142, 'Employee@employee.com', 'Declined Transaction ID: 2024-544143-25479', 'N/A', '2024-11-13 10:35:36'),
(143, 'Employee@employee.com', 'Declined Transaction ID: 2024-181664-70478', 'N/A', '2024-11-13 10:36:23'),
(144, 'Employee@employee.com', 'Declined Transaction ID: 2024-711226-72037', 'N/A', '2024-11-13 10:37:04'),
(145, 'Employee@employee.com', 'Declined Transaction ID: 2024-544143-25479', 'decline-appointment.php', '2024-11-13 10:39:25'),
(146, 'Employee@employee.com', 'Declined Transaction ID: 2024-181664-70478', 'decline-appointment.php', '2024-11-13 10:40:43'),
(147, 'Employee@employee.com', 'Declined Transaction ID: 2024-711226-72037', 'decline-appointment.php', '2024-11-13 10:42:35'),
(148, 'Employee@employee.com', 'Decline wala lang w/ Transaction ID: 2024-544143-25479', 'N/A', '2024-11-13 10:44:07'),
(149, 'Employee@employee.com', 'Decline ccccc w/ Transaction ID: 2024-711226-72037', 'N/A', '2024-11-13 10:45:05'),
(150, 'Employee@employee.com', 'Accepted Payment of Transaction ID: 2024-711226-72037', 'N/A', '2024-11-13 10:46:14'),
(151, 'Employee@employee.com', 'Accepted Payment of Transaction ID: 2024-544143-25479', 'N/A', '2024-11-13 10:46:56'),
(152, 'justinecarlalbay@gmail.com', 'Profile Picture Changed', 'IMG_2310.webp', '2024-11-13 12:36:06'),
(153, 'justinecarlalbay@gmail.com', 'Profile Picture Changed', 'IMG_2345.webp', '2024-11-13 12:36:24'),
(154, 'justinecarlalbay@gmail.com', 'uploaded a file', '1 Batch 1_ Merchandise Pricing and Availability p1.png', '2024-11-13 12:38:19'),
(155, 'Employee@employee.com', 'Accepted Payment of Transaction ID: 2024-544143-25479', 'N/A', '2024-11-13 15:45:59'),
(156, 'Employee@employee.com', 'Accepted Appointment of Transaction ID: 2024-639969-73478', 'N/A', '2024-11-13 15:56:53'),
(157, 'Employee@employee.com', 'Accepted Appointment of Transaction ID: 2024-362094-84023', 'N/A', '2024-11-13 15:56:58'),
(158, 'Employee@employee.com', 'Approved Transaction ID: 2024-639969-73478', 'N/A', '2024-11-13 15:57:34'),
(159, 'Employee@employee.com', 'Approved Transaction ID: 2024-362094-84023', 'N/A', '2024-11-13 15:58:07'),
(160, 'Employee@employee.com', 'Accepted Payment of Transaction ID: 2024-639969-73478', 'N/A', '2024-11-13 15:58:13'),
(161, 'Employee@employee.com', 'Accepted Payment of Transaction ID: 2024-362094-84023', 'N/A', '2024-11-13 15:58:18'),
(162, 'Employee@employee.com', 'Accepted Appointment of Transaction ID: 2024-946802-26121', 'N/A', '2024-11-14 05:56:53'),
(163, 'Employee@employee.com', 'Accepted Appointment of Transaction ID: 2024-016275-81714', 'N/A', '2024-11-14 05:57:28'),
(164, 'Employee@employee.com', 'Approved Transaction ID: 2024-946802-26121', 'N/A', '2024-11-14 06:05:56'),
(165, 'Employee@employee.com', 'Accepted Payment of Transaction ID: 2024-946802-26121', 'N/A', '2024-11-14 06:13:58'),
(166, 'kreianlanaria@gmail.com', 'Profile Picture Changed', 'Screenshot 2024-10-02 at 8.28.43 AM.webp', '2024-11-19 23:26:42'),
(167, 'kreianlanaria@gmail.com', 'uploaded a file', 'IMG_2310.webp', '2024-11-20 06:45:04'),
(168, 'taglinao06@gmail.com', 'Automatic Deletion by the system', 'K8FCS.sql', '2024-11-20 09:18:43'),
(169, 'taglinao06@gmail.com', 'Automatic Deletion by the system', 'adasd asdas_ORCR_2024-11-12_12-57-30_0.jpg', '2024-11-20 09:18:43'),
(170, 'taglinao06@gmail.com', 'Automatic Deletion by the system', 'NOVEMBER 9 LATEST 3RD UPDATE.sql', '2024-11-20 09:18:43'),
(171, 'taglinao06@gmail.com', 'Automatic Deletion by the system', 'NOVEMBER 9 LATEST 6TH UPDATE.sql', '2024-11-20 09:18:43'),
(172, 'ederlynlyn2@gmail.com', 'Profile Picture Changed', 'ca5c389f-9e4d-4336-8123-ccba3728fbd7.webp', '2024-11-20 11:45:21'),
(173, 'Employee@employee.com', 'Automatically Declined by the system w/ Transaction ID: 2024-187544-09719', 'N/A', '2024-11-22 09:13:27'),
(174, 'Employee@employee.com', 'Accepted Appointment of Transaction ID: 2024-187544-09719', 'N/A', '2024-11-22 09:15:36'),
(175, 'Employee@employee.com', 'Approved Transaction ID: 2024-187544-09719', 'N/A', '2024-11-22 09:16:57'),
(176, 'Employee@employee.com', 'Accepted Payment of Transaction ID: 2024-187544-09719', 'N/A', '2024-11-22 09:18:56'),
(177, 'kreianlanaria@gmail.com', 'uploaded a file', 'NOVEMBER 20 LATEST.sql', '2024-11-23 15:30:45'),
(178, 'admin@admin.com', 'Accepted appointment w/ Transaction ID: 2024-658313-70257', 'N/A', '2024-11-25 15:11:48'),
(179, 'kreianlanaria@gmail.com', 'Profile Picture Changed', 'IMG_7720.webp', '2024-11-26 14:43:06'),
(180, 'jca092080@gmail.com', 'Profile Picture Changed', 'Screenshot_20241126_143956_YouTube.webp', '2024-11-27 02:16:30'),
(181, 'Employee@employee.com', 'Accepted Appointment of Transaction ID: 2024-157018-64999', 'N/A', '2024-11-27 02:31:11'),
(182, 'Employee@employee.com', 'Accepted Appointment of Transaction ID: 2024-498826-38684', 'N/A', '2024-11-27 09:09:20'),
(183, 'Employee@employee.com', 'Approved Transaction ID: 2024-498826-38684', 'N/A', '2024-11-27 09:10:21'),
(184, 'Employee@employee.com', 'Accepted Payment of Transaction ID: 2024-498826-38684', 'N/A', '2024-11-27 09:11:56'),
(185, 'quilapiomatthew31@gmail.com', 'Profile Information and Profile Picture is Changed', 'IMG_20241127_211904.webp', '2024-11-27 13:20:29'),
(186, 'quilapiomatthew31@gmail.com', 'Information Changed', 'N/A', '2024-11-27 13:23:13'),
(187, 'quilapiomatthew31@gmail.com', 'Profile Picture Changed', 'images (2).webp', '2024-11-27 13:23:27'),
(188, 'hikarunagi@yahoo.com', 'Accepted Appointment of Transaction ID: 2024-579973-43440', 'N/A', '2024-11-27 13:58:13'),
(189, 'hikarunagi@yahoo.com', 'Approved Transaction ID: 2024-579973-43440', 'N/A', '2024-11-27 14:00:52'),
(190, 'hikarunagi@yahoo.com', 'Accepted Payment of Transaction ID: 2024-579973-43440', 'N/A', '2024-11-27 14:01:33'),
(191, 'Employee@employee.com', 'Accepted Appointment of Transaction ID: 2024-658313-70257', 'N/A', '2024-11-29 15:17:53'),
(192, 'Employee@employee.com', 'Accepted Appointment of Transaction ID: 2024-658313-70257', 'N/A', '2024-11-29 15:20:29'),
(193, 'Employee@employee.com', 'Accepted Appointment of Transaction ID: 2024-658313-70257', 'N/A', '2024-11-29 15:22:04'),
(194, 'Employee@employee.com', 'Accepted Appointment of Transaction ID: 2024-658313-70257', 'N/A', '2024-11-29 15:26:26');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `clientname` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Processing',
  `decline_at` varchar(155) DEFAULT NULL,
  `recieve_at` timestamp NULL DEFAULT current_timestamp(),
  `approve_at` timestamp NULL DEFAULT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `form_type` varchar(50) DEFAULT NULL,
  `payment_description` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `archived` tinyint(1) DEFAULT 0,
  `paid` int(1) DEFAULT NULL,
  `bank_partner` varchar(255) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `term` varchar(50) DEFAULT NULL,
  `amount_finance` decimal(10,2) DEFAULT NULL,
  `maturity` date DEFAULT NULL,
  `check_release` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `clientname`, `transaction_id`, `email`, `status`, `decline_at`, `recieve_at`, `approve_at`, `accepted_at`, `form_type`, `payment_description`, `amount`, `archived`, `paid`, `bank_partner`, `remarks`, `appointment_date`, `appointment_time`, `term`, `amount_finance`, `maturity`, `check_release`) VALUES
(21, 'Charlie Taglinao', '2024-658313-70257', 'taglinao06@gmail.com', 'Accepted', NULL, '2024-11-25 12:49:07', NULL, '2024-11-29 15:26:26', 'brand-new', NULL, NULL, 0, NULL, NULL, NULL, '2024-11-28', '13:00:00', NULL, NULL, NULL, NULL),
(22, 'Jhune Ante', '2024-157018-64999', 'jca092080@gmail.com', 'Accepted', NULL, '2024-11-27 02:30:19', NULL, '2024-11-27 02:31:11', 'brand-new', NULL, NULL, 0, NULL, NULL, NULL, '2024-11-29', '10:30:00', NULL, NULL, NULL, NULL),
(26, 'Kevin Bagasbas', '2024-932720-31914', 'quilapiomatthew31@gmail.com', 'Processing', NULL, '2024-11-27 13:06:45', NULL, NULL, 'second-hand', NULL, NULL, 0, NULL, NULL, NULL, '2024-11-28', '09:30:00', NULL, NULL, NULL, NULL),
(34, 'Roma Almendras', '2024-809081-39210', 'romalennalmendras@gmail.com', 'Processing', NULL, '2024-11-27 14:14:42', NULL, NULL, 'brand-new', NULL, NULL, 0, NULL, NULL, NULL, '2024-12-02', '10:00:00', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_description` text DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `user_email`, `file_name`, `file_description`, `upload_date`) VALUES
(39, 'justinecarlalbay@gmail.com', '1 Batch 1_ Merchandise Pricing and Availability p1.png', 'Identification Documents', '2024-11-13 12:38:19'),
(40, 'kreianlanaria@gmail.com', 'IMG_2310.webp', 'Identification Documents', '2024-11-20 06:45:04'),
(41, 'kreianlanaria@gmail.com', 'NOVEMBER 20 LATEST.sql', 'Income Verification', '2024-11-23 15:30:45'),
(42, 'hikarunagi@yahoo.com', 'images (1).jpeg', NULL, '2024-11-27 14:05:50'),
(43, 'hikarunagi@yahoo.com', 'Welcome.pdf', NULL, '2024-11-27 14:06:36'),
(44, 'hikarunagi@yahoo.com', 'Welcome(1).pdf', NULL, '2024-11-27 14:06:40'),
(45, 'hikarunagi@yahoo.com', 'Welcome(2).pdf', NULL, '2024-11-27 14:06:42'),
(46, 'hikarunagi@yahoo.com', 'Welcome(3).pdf', NULL, '2024-11-27 14:06:43'),
(47, 'hikarunagi@yahoo.com', 'Welcome(4).pdf', NULL, '2024-11-27 14:06:45');

-- --------------------------------------------------------

--
-- Table structure for table `forms_brandnew_applicants`
--

CREATE TABLE `forms_brandnew_applicants` (
  `id` int(11) NOT NULL,
  `year_model` int(11) DEFAULT NULL,
  `make` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `transmition_type` varchar(155) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `place_of_birth` varchar(255) DEFAULT NULL,
  `marital_status` varchar(50) DEFAULT NULL,
  `present_address` text DEFAULT NULL,
  `years_present_address` varchar(255) DEFAULT NULL,
  `ownership` varchar(50) DEFAULT NULL,
  `ownership_other` varchar(255) DEFAULT NULL,
  `previous_address` text DEFAULT NULL,
  `years_previous_address` varchar(255) DEFAULT NULL,
  `contact_number_1` varchar(20) DEFAULT NULL,
  `contact_number_2` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tin_number` varchar(255) DEFAULT NULL,
  `sss_number` varchar(255) DEFAULT NULL,
  `dependents` varchar(255) DEFAULT NULL,
  `mother_maiden_first_name` varchar(255) DEFAULT NULL,
  `mother_maiden_last_name` varchar(255) DEFAULT NULL,
  `mother_maiden_middle_name` varchar(255) DEFAULT NULL,
  `father_first_name` varchar(255) DEFAULT NULL,
  `father_last_name` varchar(255) DEFAULT NULL,
  `father_middle_name` varchar(255) DEFAULT NULL,
  `income_source` varchar(255) DEFAULT NULL,
  `income_source_other` varchar(255) DEFAULT NULL,
  `employer_name` varchar(255) DEFAULT NULL,
  `office_address` text DEFAULT NULL,
  `office_number` varchar(20) DEFAULT NULL,
  `company_email` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `years_service` int(11) DEFAULT NULL,
  `monthly_income` varchar(255) DEFAULT NULL,
  `credit_cards` text DEFAULT NULL,
  `credit_history` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `form_type` varchar(50) DEFAULT 'brand-new',
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `relationship_borrower` varchar(255) DEFAULT NULL,
  `first_name_borrower` varchar(255) DEFAULT NULL,
  `last_name_borrower` varchar(255) DEFAULT NULL,
  `middle_name_borrower` varchar(255) DEFAULT NULL,
  `date_of_birth_borrower` date DEFAULT NULL,
  `place_birth_borrower` varchar(255) DEFAULT NULL,
  `residential_address_borrower` text DEFAULT NULL,
  `years_stay_borrower` varchar(255) DEFAULT NULL,
  `contact_number_borrower` varchar(20) DEFAULT NULL,
  `email_address_borrower` varchar(255) DEFAULT NULL,
  `tin_number_borrower` varchar(255) DEFAULT NULL,
  `sss_number_borrower` varchar(255) DEFAULT NULL,
  `employer_name_borrower` varchar(255) DEFAULT NULL,
  `office_address_borrower` text DEFAULT NULL,
  `position_borrower` varchar(255) DEFAULT NULL,
  `years_service_borrower` varchar(255) DEFAULT NULL,
  `monthly_income_borrower` varchar(255) DEFAULT NULL,
  `credit_cards_borrower` text DEFAULT NULL,
  `appointed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forms_brandnew_applicants`
--

INSERT INTO `forms_brandnew_applicants` (`id`, `year_model`, `make`, `type`, `transmition_type`, `first_name`, `last_name`, `middle_name`, `dob`, `place_of_birth`, `marital_status`, `present_address`, `years_present_address`, `ownership`, `ownership_other`, `previous_address`, `years_previous_address`, `contact_number_1`, `contact_number_2`, `email`, `tin_number`, `sss_number`, `dependents`, `mother_maiden_first_name`, `mother_maiden_last_name`, `mother_maiden_middle_name`, `father_first_name`, `father_last_name`, `father_middle_name`, `income_source`, `income_source_other`, `employer_name`, `office_address`, `office_number`, `company_email`, `position`, `years_service`, `monthly_income`, `credit_cards`, `credit_history`, `comments`, `transaction_id`, `form_type`, `appointment_date`, `appointment_time`, `relationship_borrower`, `first_name_borrower`, `last_name_borrower`, `middle_name_borrower`, `date_of_birth_borrower`, `place_birth_borrower`, `residential_address_borrower`, `years_stay_borrower`, `contact_number_borrower`, `email_address_borrower`, `tin_number_borrower`, `sss_number_borrower`, `employer_name_borrower`, `office_address_borrower`, `position_borrower`, `years_service_borrower`, `monthly_income_borrower`, `credit_cards_borrower`, `appointed_at`) VALUES
(11, 1981, 'Raphael Sweeney', 'Obcaecati sed occaec', 'Automatic', 'Charlie', 'Taglinao', 'Dela Cruz', '1999-11-12', 'Sit placeat ratione', 'Separated', 'Nesciunt omnis tota', '2005', 'Free living with Parents', '', 'Ex ea officia offici', '1978', '09123456789', '09123456785', 'taglinao06@gmail.com', '504', '953', '382', 'Yen', 'Moran', 'Jeremy Branch', 'Lois', 'Morgan', 'Hedwig Holland', 'Employed', '', 'Jocelyn Fry', 'Veniam nisi volupta', '660', 'zefasacak@mailinator.com', 'Sint omnis optio et', 1984, '878.00', '50', 'Est exercitation cu', 'Quaerat voluptatem d', '2024-658313-70257', 'brand-new', '2024-11-28', '13:00:00', 'Qui in sequi natus o', 'Noble', 'Snow', 'Lewis Harrell', '1996-10-15', 'Anim nihil sed amet', 'Recusandae Sed earu', '1973', '759', 'sefu@mailinator.com', '372', '282', 'Alma Atkins', 'Aspernatur impedit ', 'Dolores porro aliqui', '1982', '200.00', '64', '2024-11-30 05:42:09'),
(12, 2024, 'FORD RAPTOR', 'Pick up', 'Automatic', 'Jhune', 'Ante', 'GILTENDEZ', '1970-09-20', 'DASMA', 'Married', 'Dasmarinas', '24', 'Rented', '', 'DASMARINAS', '30', '09167755539', '', 'jca092080@gmail.com', '', '', '2', '', '', '', '', '', '', 'Employed', '', 'For se', 'For survey', '3453469', 'forsurveyonly@gmail.com', 'OWNER', 5, '2000.00', '1346346534629764', 'Good', '', '2024-157018-64999', 'brand-new', '2024-11-29', '10:30:00', 'COUSIN', 'Daniel', 'Tan', 'A', '1978-11-21', '', 'Dasmarinas', '5', '09346699245', 'danieltan@gmail.com', '', '', 'For survey', 'For survey', 'Owner', '5', '20000.00', '3465346966258427', '2024-11-30 05:42:09'),
(18, 2024, 'Toyota Vios', 'Sedan', 'Automatic', 'Roma', 'Almendras', 'Lenn', '1988-01-16', 'Manila', 'Married', 'Liora Homes', '6', 'Owned', '', 'Imus City', '10', '09173393308', '09173393308', 'romalennalmendras@gmail.com', '111222333444', '0022674392', '1', 'Stephany', 'Franca', 'Noble', 'Felix', 'Pedres', 'Cael', 'Employed', '', 'Deltek', 'Makati City', '7894657', 'romaaalmendras@deltek.com', 'Analyst', 10, '50000', '6578345200359151', 'Good', 'Application Docs', '2024-809081-39210', 'brand-new', '2024-12-02', '10:00:00', 'Spouse', 'Felman', 'Sumortin', 'Pedres', '1980-10-06', 'Bacolod', 'Liora Homes', '6', '09286574416', 'felman.sumortin@gmail.com', '054765239', '6496386550', 'Deltek', 'Makati City', 'Sr Analyst', '11', '50000', '6497225600359789', '2024-11-30 05:42:09');

-- --------------------------------------------------------

--
-- Table structure for table `forms_sanglaorcr_applicants`
--

CREATE TABLE `forms_sanglaorcr_applicants` (
  `id` int(11) NOT NULL,
  `year_model` varchar(255) DEFAULT NULL,
  `make` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `transmition_type` varchar(155) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `dob` varchar(255) DEFAULT NULL,
  `place_of_birth` varchar(255) DEFAULT NULL,
  `marital_status` varchar(255) DEFAULT NULL,
  `present_address` text DEFAULT NULL,
  `years_present_address` varchar(255) DEFAULT NULL,
  `ownership` varchar(255) DEFAULT NULL,
  `ownership_other` varchar(255) DEFAULT NULL,
  `previous_address` text DEFAULT NULL,
  `years_previous_address` varchar(255) DEFAULT NULL,
  `contact_number_1` varchar(20) DEFAULT NULL,
  `contact_number_2` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tin_number` varchar(255) DEFAULT NULL,
  `sss_number` varchar(255) DEFAULT NULL,
  `dependents` varchar(255) DEFAULT NULL,
  `mother_maiden_first_name` varchar(255) DEFAULT NULL,
  `mother_maiden_last_name` varchar(255) DEFAULT NULL,
  `mother_maiden_middle_name` varchar(255) DEFAULT NULL,
  `father_first_name` varchar(255) DEFAULT NULL,
  `father_last_name` varchar(255) DEFAULT NULL,
  `father_middle_name` varchar(255) DEFAULT NULL,
  `income_source` varchar(255) DEFAULT NULL,
  `income_source_other` varchar(255) DEFAULT NULL,
  `employer_name` varchar(255) DEFAULT NULL,
  `office_address` text DEFAULT NULL,
  `office_number` varchar(20) DEFAULT NULL,
  `company_email` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `years_service` varchar(255) DEFAULT NULL,
  `monthly_income` varchar(255) DEFAULT NULL,
  `credit_cards` varchar(255) DEFAULT NULL,
  `credit_history` text DEFAULT NULL,
  `relationship_borrower` varchar(255) DEFAULT NULL,
  `first_name_borrower` varchar(255) DEFAULT NULL,
  `last_name_borrower` varchar(255) DEFAULT NULL,
  `middle_name_borrower` varchar(255) DEFAULT NULL,
  `date_of_birth_borrower` date DEFAULT NULL,
  `place_birth_borrower` varchar(255) DEFAULT NULL,
  `residential_address_borrower` text DEFAULT NULL,
  `years_stay_borrower` varchar(255) DEFAULT NULL,
  `contact_number_borrower` varchar(20) DEFAULT NULL,
  `email_address_borrower` varchar(255) DEFAULT NULL,
  `tin_number_borrower` varchar(255) DEFAULT NULL,
  `sss_number_borrower` varchar(255) DEFAULT NULL,
  `mother_maiden_first_name_CoBorrower` varchar(255) DEFAULT NULL,
  `mother_maiden_last_name_CoBorrower` varchar(255) DEFAULT NULL,
  `mother_maiden_middle_name_CoBorrower` varchar(255) DEFAULT NULL,
  `father_first_name_CoBorrower` varchar(255) DEFAULT NULL,
  `father_last_name_CoBorrower` varchar(255) DEFAULT NULL,
  `father_middle_name_CoBorrower` varchar(255) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `form_type` varchar(50) NOT NULL DEFAULT 'sangla-orcr',
  `file_name` varchar(255) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `ORCR_filename` varchar(255) DEFAULT NULL,
  `appointed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forms_secondhand_applicants`
--

CREATE TABLE `forms_secondhand_applicants` (
  `id` int(11) NOT NULL,
  `year_model` varchar(255) DEFAULT NULL,
  `make` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `transmition_type` varchar(155) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `place_of_birth` varchar(255) DEFAULT NULL,
  `marital_status` varchar(255) DEFAULT NULL,
  `present_address` text DEFAULT NULL,
  `years_present_address` varchar(255) DEFAULT NULL,
  `ownership` varchar(255) DEFAULT NULL,
  `ownership_other` varchar(255) DEFAULT NULL,
  `previous_address` text DEFAULT NULL,
  `years_previous_address` varchar(255) DEFAULT NULL,
  `contact_number_1` varchar(255) DEFAULT NULL,
  `contact_number_2` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tin_sss` varchar(255) DEFAULT NULL,
  `tin_number` varchar(255) DEFAULT NULL,
  `sss_number` varchar(255) DEFAULT NULL,
  `dependents` varchar(255) DEFAULT NULL,
  `mother_maiden_first_name` varchar(255) DEFAULT NULL,
  `mother_maiden_last_name` varchar(255) DEFAULT NULL,
  `mother_maiden_middle_name` varchar(255) DEFAULT NULL,
  `father_first_name` varchar(255) DEFAULT NULL,
  `father_last_name` varchar(255) DEFAULT NULL,
  `father_middle_name` varchar(255) DEFAULT NULL,
  `income_source` varchar(255) DEFAULT NULL,
  `income_source_other` varchar(255) DEFAULT NULL,
  `employer_name` varchar(255) DEFAULT NULL,
  `office_address` text DEFAULT NULL,
  `office_number` varchar(255) DEFAULT NULL,
  `company_email` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `years_service` varchar(255) DEFAULT NULL,
  `monthly_income` varchar(255) DEFAULT NULL,
  `credit_cards` varchar(255) DEFAULT NULL,
  `credit_history` text DEFAULT NULL,
  `relationship_borrower` varchar(255) DEFAULT NULL,
  `first_name_borrower` varchar(255) DEFAULT NULL,
  `last_name_borrower` varchar(255) DEFAULT NULL,
  `middle_name_borrower` varchar(255) DEFAULT NULL,
  `date_of_birth_borrower` date DEFAULT NULL,
  `place_birth_borrower` varchar(255) DEFAULT NULL,
  `residential_address_borrower` text DEFAULT NULL,
  `years_stay_borrower` varchar(255) DEFAULT NULL,
  `contact_number_borrower` varchar(255) DEFAULT NULL,
  `email_address_borrower` varchar(255) DEFAULT NULL,
  `tin_number_coborrower` varchar(20) DEFAULT NULL,
  `sss_number_coborrower` varchar(20) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `form_type` varchar(50) DEFAULT 'second-hand',
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `appointed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forms_secondhand_applicants`
--

INSERT INTO `forms_secondhand_applicants` (`id`, `year_model`, `make`, `type`, `transmition_type`, `first_name`, `last_name`, `middle_name`, `dob`, `place_of_birth`, `marital_status`, `present_address`, `years_present_address`, `ownership`, `ownership_other`, `previous_address`, `years_previous_address`, `contact_number_1`, `contact_number_2`, `email`, `tin_sss`, `tin_number`, `sss_number`, `dependents`, `mother_maiden_first_name`, `mother_maiden_last_name`, `mother_maiden_middle_name`, `father_first_name`, `father_last_name`, `father_middle_name`, `income_source`, `income_source_other`, `employer_name`, `office_address`, `office_number`, `company_email`, `position`, `years_service`, `monthly_income`, `credit_cards`, `credit_history`, `relationship_borrower`, `first_name_borrower`, `last_name_borrower`, `middle_name_borrower`, `date_of_birth_borrower`, `place_birth_borrower`, `residential_address_borrower`, `years_stay_borrower`, `contact_number_borrower`, `email_address_borrower`, `tin_number_coborrower`, `sss_number_coborrower`, `transaction_id`, `form_type`, `appointment_date`, `appointment_time`, `appointed_at`) VALUES
(11, '2500', 'Hino', 'Don Aldrin', 'Manual', 'Kevin', 'Bagasbas', 'X.', '2000-02-29', 'Dongcheng, China', 'Separated', 'Wuhan, China', '16', 'Free living with Parents', '', 'N/A', '0', '09999999912', '09999999945', 'quilapiomatthew31@gmail.com', NULL, '0000000000000', '11888888833', '2', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Business', '', 'Philippine Offshore Gaming Operators', 'Pasay, Metro Manila', '7777777', 'quilapiomatthew31@gmail.com', 'Pogo Manager', '4', '1000000.00', '9999555577771623', 'Negative Credit Score', 'Kabet', 'Alice', 'Guo', 'Ping', '2003-01-01', 'Bamban, Tarlac', 'N/A', '12', '000', 'aliceguo@gmail.com', '', '', '2024-932720-31914', 'second-hand', '2024-11-28', '09:30:00', '2024-11-30 05:43:04');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `background_image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `font_color` varchar(10) DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `subtitle`, `background_image`, `created_at`, `font_color`, `expiry_date`) VALUES
(51, ' K8 Financial Consultancy Services ', 'Unlock the Keys to Your New Ride with Our Car Loans', 'clientheader.webp', '2024-11-11 14:38:32', 'White', '2024-11-30 22:38:00'),
(52, ' K8 Financial Consultancy Services ', 'Hurry up don\'t waste your time', 'header1.webp', '2024-11-11 14:39:03', 'white', '2024-11-30 22:38:00');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiry` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `login_time` datetime NOT NULL,
  `logout_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `session_id`, `login_time`, `logout_time`) VALUES
(220, 22, 'r9ggp4rluh2qrdgf8bakf8q73i', '2024-11-15 23:36:00', '2024-11-15 15:37:33'),
(221, 22, 's6mtgbnd3gt9e9kq01f7iifbo1', '2024-11-15 23:37:43', '2024-11-15 15:37:46'),
(222, 22, '8cba3gj8bam0e9hmvj5012em92', '2024-11-16 02:55:54', '2024-11-15 18:55:58'),
(223, 29, 'qincarn5e5l7hj45475paj23g0', '2024-11-17 14:42:42', '2024-11-18 07:48:33'),
(224, 22, 'e0ah2uf04k0n9lqs6dr7sh7dmd', '2024-11-20 14:41:53', '2024-11-20 07:02:02'),
(225, 112, 'mb0k23u5kp4dh2cp9e74lm1u6a', '2024-11-18 14:19:37', '2024-11-18 06:21:53'),
(226, 48, 'dhiesdo23amu2u13rht4fke5to', '2024-11-18 14:22:01', '2024-11-18 06:23:10'),
(227, 29, 'qvqgs9dt60nlq5ubl23hobf3ip', '2024-11-18 14:23:17', '2024-11-18 06:26:57'),
(228, 122, 'aicla04ckgecmnbtbaeqao126v', '2024-11-18 14:24:30', '2024-11-18 06:24:44'),
(229, 48, '7bf85b4j02frvr4ajjd6mjhrgr', '2024-11-20 07:17:02', '2024-11-19 23:18:40'),
(230, 124, 'ktu8i3n1489fqcem3v1ur37t3p', '2024-11-18 21:43:46', '2024-11-24 19:18:37'),
(231, 112, 'b27hro39mvp8mir48bjd5ic827', '2024-11-20 07:27:50', '2024-11-19 23:30:26'),
(232, 29, 'ubglv5u18q0qf45ltj02njrgq2', '2024-11-20 12:33:54', '2024-11-20 04:43:24'),
(233, 48, 'jn7n52pl1qummvbjidqclhtoqb', '2024-11-20 12:45:15', '2024-11-20 04:50:52'),
(234, 29, '18k9p17qitlsohahn1ctq95ph1', '2024-11-20 13:02:40', '2024-11-20 06:30:06'),
(235, 112, '1cdsirm13ksed4595mo5k7p5uc', '2024-11-20 14:43:40', '2024-11-20 07:06:03'),
(236, 48, 'pe4jfumdndk1k9anrnb3oaaiak', '2024-11-20 15:09:19', '2024-11-20 07:15:52'),
(237, 48, 'ls6974miv2nfdirj3elgvr9s8q', '2024-11-20 15:20:08', '2024-11-20 07:21:05'),
(238, 29, '5g258ianl1q38vu96h4ptp1n1v', '2024-11-20 15:25:44', '2024-11-20 16:14:50'),
(239, 22, '7h8g3vck9g8h82oh0lbis9tko1', '2024-11-20 15:27:48', '2024-11-20 16:14:41'),
(240, 22, '7h8g3vck9g8h82oh0lbis9tko1', '2024-11-20 16:14:43', '2024-11-20 08:20:36'),
(241, 112, '25jq2hjlagv4o2i5gh2lgekp8k', '2024-11-21 19:59:27', '2024-11-21 12:00:35'),
(242, 22, 'qincarn5e5l7hj45475paj23g0', '2024-11-20 17:07:02', '2024-11-20 17:35:18'),
(243, 22, 'qincarn5e5l7hj45475paj23g0', '2024-11-20 17:40:16', '2024-11-20 09:47:47'),
(244, 48, '54li0q1ro68tasdg240kdhrcmv', '2024-11-22 17:10:29', '2024-11-22 09:11:59'),
(245, 125, 'd0dl42v647l5s51k9pmhdl73jm', '2024-11-20 19:42:00', '2024-11-20 11:42:32'),
(246, 125, 'fi2710aegb3gv0kod9kp6e8pp7', '2024-11-20 19:43:16', '2024-11-20 19:48:35'),
(247, 125, 'fi2710aegb3gv0kod9kp6e8pp7', '2024-11-20 19:48:45', '2024-11-20 12:36:07'),
(248, 126, 'aucs4kquclcc1pbvsv6glkk6kl', '2024-11-20 20:06:41', NULL),
(249, 22, '2k304voulgpgb4bfridg405jh2', '2024-11-22 10:05:52', '2024-11-22 02:06:00'),
(250, 112, '5o9ckhgvldtni2potdfl4v4vif', '2024-11-23 16:19:06', '2024-11-23 23:28:35'),
(251, 22, '597vrm064sbjmuls6er0s2s58s', '2024-11-22 13:05:27', '2024-11-22 05:14:29'),
(252, 22, '7uq5uquob521orbbeasgjj9etv', '2024-11-22 17:06:40', '2024-11-22 09:07:22'),
(253, 29, 'gbhqocs2v1snop1p7osglenrd1', '2024-11-22 17:07:47', '2024-11-22 09:10:18'),
(254, 48, '419m5lj2rp5p68akrnu6f4t2r8', '2024-11-22 17:13:23', '2024-11-22 09:23:08'),
(255, 29, '1btcogim3e4e7m8t2tjasahban', '2024-11-23 02:02:27', '2024-11-22 18:17:05'),
(256, 112, 'lllim93cmq86ea690me8ml3ml6', '2024-11-23 23:43:00', '2024-11-23 15:45:21'),
(257, 29, 'n3pber73djtt7c57rsu7pi6pam', '2024-11-23 23:45:29', '2024-11-23 15:46:17'),
(258, 22, 'sg6quecs4khqvakjjmfk1un5h8', '2024-11-24 13:26:46', '2024-11-25 13:58:49'),
(259, 48, 'nq8dgb52nomiig34i4hnguejq1', '2024-11-24 18:09:37', '2024-11-24 10:10:33'),
(260, 29, 'btgvumt4e7qo7ea4a47lmksra0', '2024-11-24 18:11:03', '2024-11-24 10:19:13'),
(261, 29, 'eejiohg0hal1hasseaist865kf', '2024-11-25 14:01:26', '2024-11-25 06:01:42'),
(262, 48, 'sg6quecs4khqvakjjmfk1un5h8', '2024-11-25 14:01:02', '2024-11-25 06:01:05'),
(263, 48, 'n731p6go3aclsger2rk4chfq5a', '2024-11-25 21:26:31', '2024-11-25 21:36:02'),
(264, 112, 'jub911u216ns2g1mahfid4rpb2', '2024-11-25 18:51:37', '2024-11-25 10:52:08'),
(265, 112, 'n3tr4rbsm9bh72s3d3p3rqare2', '2024-11-25 18:56:19', '2024-11-25 19:14:38'),
(266, 112, 'n3tr4rbsm9bh72s3d3p3rqare2', '2024-11-25 19:14:50', '2024-11-25 11:15:22'),
(267, 112, '6oj728kum27hgl6jii6lsh7u71', '2024-11-25 20:02:12', '2024-11-25 12:57:53'),
(268, 22, 'af0l9g4d60ejgve61kj826374n', '2024-11-25 19:42:52', '2024-11-25 11:44:03'),
(269, 22, 'h1oevavbe63rq1ft67ibg47iio', '2024-11-25 19:45:39', '2024-11-25 20:47:59'),
(270, 22, 'h1oevavbe63rq1ft67ibg47iio', '2024-11-25 20:48:08', '2024-11-25 12:49:17'),
(271, 128, '32qc0vr684f6e4km002305bku2', '2024-11-25 20:58:44', '2024-11-25 13:05:58'),
(272, 121, '1l37tt3icoj458jif5f6bbaqf0', '2024-11-25 21:01:46', '2024-11-25 13:01:50'),
(273, 121, 's74gqbnuafl6rqfrht4g33silc', '2024-11-25 21:10:56', '2024-11-25 13:10:59'),
(274, 112, 'qmbsto0n7g0bltriqnhd6rrc78', '2024-11-25 21:17:52', '2024-11-25 13:35:00'),
(275, 121, 'oddd34442rv6effm5bkh3ulpcg', '2024-11-25 21:22:10', '2024-11-25 13:22:12'),
(276, 22, 'g86m1g237t8rm2o4gugu6s3n0a', '2024-11-25 21:36:42', '2024-11-25 13:36:47'),
(277, 29, 'e4a8fn1hhddt5dco3rr6anpdj9', '2024-11-25 21:38:42', '2024-11-25 13:38:45'),
(278, 22, '19mqgn63dp142797q4abrhp8e5', '2024-11-25 21:42:45', '2024-11-25 13:42:53'),
(279, 112, 'kkfmsd2a3coldipnqkurhuojbp', '2024-11-25 21:47:48', '2024-11-25 13:54:06'),
(280, 121, 'n731p6go3aclsger2rk4chfq5a', '2024-11-25 21:44:10', '2024-11-25 13:44:24'),
(281, 48, '7t1jfg67e07t4docifm0t8quqk', '2024-11-25 21:45:05', '2024-11-25 13:47:33'),
(282, 121, '73ts4u8j3pahu70t4td3gk3nbp', '2024-11-25 22:22:04', '2024-11-27 16:09:02'),
(283, 29, 'fi7ombms03gij1foo0303f6617', '2024-11-25 23:11:41', '2024-11-25 15:13:50'),
(284, 48, '4963a8bovcjn9rkgktuom9v43u', '2024-11-26 00:25:54', '2024-11-25 16:26:25'),
(285, 112, 'pe31f0keins8m0kq3fc12efbvf', '2024-11-26 11:39:41', '2024-11-26 16:57:23'),
(286, 29, 'rehhqsgdnqqqlk6kev4h083f5e', '2024-11-26 17:19:58', '2024-11-26 09:23:40'),
(287, 112, 'onbiqhgrslvm43t7o2ill8re9f', '2024-11-26 16:57:34', '2024-11-26 08:57:38'),
(288, 112, '1i3pdtbklv2qm5e5qeo84ancdd', '2024-11-26 22:42:13', '2024-11-26 14:43:22'),
(289, 48, 'kaldgag8ap59e40s870m57a3u5', '2024-11-26 22:44:04', '2024-11-26 14:44:17'),
(290, 29, 'lbbgouhovld7a4sc4eutl21nks', '2024-11-26 22:44:28', '2024-11-26 14:45:19'),
(291, 112, 'r0lhjq5juv8f9svhrhucdqkvcc', '2024-11-26 22:56:12', '2024-11-26 15:03:23'),
(292, 29, 'e63irpeqimmcm3rsk9dj9hdusd', '2024-11-27 16:30:57', '2024-11-27 16:55:01'),
(293, 130, '0kled8ug0021lpmuh7flfi0gs2', '2024-11-27 10:15:16', '2024-11-27 10:33:12'),
(294, 48, '1s43k2349qij90cs8se8imu529', '2024-11-27 10:31:02', '2024-11-27 02:53:40'),
(295, 130, '0kled8ug0021lpmuh7flfi0gs2', '2024-11-27 10:33:34', NULL),
(296, 112, 'c413re5odkbkp4o000t0f4o7t8', '2024-11-27 11:01:53', '2024-11-27 03:02:32'),
(297, 48, 'ie355rvcglr3qiubf2d9s7e8tu', '2024-11-27 11:02:50', '2024-11-27 03:03:29'),
(298, 112, 's77mgo64uhflhovqndn2lr7go7', '2024-11-27 16:19:11', '2024-11-27 08:51:00'),
(299, 112, '93mfd58ljigs4qp2s8orpii6dc', '2024-11-27 16:52:59', '2024-11-27 08:58:50'),
(300, 48, 'dqb3totnc3chf3splgi1tavprn', '2024-11-27 17:01:39', '2024-11-27 09:05:24'),
(301, 121, '3spldsqe1qbl6hlvd6s2rqnc7d', '2024-11-27 17:07:22', '2024-11-27 09:08:55'),
(302, 48, 'uegobdagap3det91qjbpa61og6', '2024-11-27 17:12:39', '2024-11-27 09:12:42'),
(303, 48, 'bjlt9sjveido5rhshq3ikqskin', '2024-11-27 17:12:54', '2024-11-27 09:13:00'),
(304, 112, 'c2dv6nqsv8g42sg3927pojdplu', '2024-11-27 17:22:48', '2024-11-27 17:43:36'),
(305, 112, 'es0ntedr4ugk45ptf4gs75cs73', '2024-11-27 17:50:10', '2024-11-27 09:54:08'),
(306, 48, 'ukopr4hfhtjhvjt8kqlqn0k369', '2024-11-27 17:54:52', '2024-11-27 10:16:55'),
(307, 29, 'k61dqg1niebsslo2pvr96a78qm', '2024-11-27 18:22:11', '2024-11-27 19:31:33'),
(308, 112, '3uurmga9odvbetv5r75k2666fa', '2024-11-27 19:09:34', '2024-11-27 11:31:11'),
(309, 132, 'aml3t3fu6l0sadtmeg7uc6ohan', '2024-11-27 19:42:14', '2024-11-27 19:47:52'),
(310, 132, 'aml3t3fu6l0sadtmeg7uc6ohan', '2024-11-27 19:48:15', '2024-11-27 20:13:57'),
(311, 133, 'm71sjdupf23u33jhaul7c3oa4r', '2024-11-27 21:19:31', '2024-11-27 13:23:52'),
(312, 132, 'aml3t3fu6l0sadtmeg7uc6ohan', '2024-11-27 20:53:09', '2024-11-27 21:52:53'),
(313, 48, '9he4ooqr7kur6ifu29pr1os7ki', '2024-11-27 21:17:59', '2024-11-27 21:45:12'),
(314, 29, 'o9su61of7vkq9bb6pre0fsfbqk', '2024-11-27 21:39:50', '2024-11-27 13:53:25'),
(315, 112, '2nvsa8fbulbudh9bs7bp2sh5p9', '2024-11-27 21:45:19', '2024-11-27 21:59:37'),
(316, 135, 'eghfvv71103u1kdgvrc9qkqdor', '2024-11-27 22:04:43', '2024-11-27 14:07:36'),
(317, 112, '2nvsa8fbulbudh9bs7bp2sh5p9', '2024-11-27 22:00:24', '2024-11-27 14:10:30'),
(318, 136, 'm00c9bb4f2iaua0d2mkb9k4k8o', '2024-11-27 22:07:46', '2024-11-27 14:25:20'),
(319, 112, 'fa6brf3d9a1s78s136orft9qak', '2024-11-29 23:07:02', '2024-11-29 16:07:06'),
(320, 48, 'n1qhhu6mqv2j11qapd165e19ro', '2024-11-27 23:06:15', '2024-11-27 16:09:24'),
(321, 48, '3a4qkgclfsn91gp3upigmvcmsl', '2024-11-27 23:15:58', '2024-11-27 16:16:14'),
(322, 48, 'h759ii0c3q43la4psptfsptrra', '2024-11-28 00:33:26', '2024-11-27 18:16:29'),
(323, 112, '2cgjo03nuk6kkput59vrgijtm6', '2024-11-29 23:08:06', '2024-11-29 16:11:50'),
(324, 48, 'tqmkubl862776m2o1bscnqk0i3', '2024-11-30 13:30:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `status_updates`
--

CREATE TABLE `status_updates` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `status_type` varchar(50) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status_updates`
--

INSERT INTO `status_updates` (`id`, `appointment_id`, `status`, `status_type`, `updated_at`) VALUES
(91, 5, 'Accepted', 'Accepted', '2024-11-22 09:15:36'),
(92, 5, 'Approved', 'Approved', '2024-11-22 09:16:57'),
(93, 21, 'Processing', 'Processing', '2024-11-25 12:49:07'),
(94, 22, 'Processing', 'Processing', '2024-11-27 02:30:19'),
(95, 22, 'Accepted', 'Accepted', '2024-11-27 02:31:11'),
(96, 23, 'Processing', 'Processing', '2024-11-27 09:08:47'),
(97, 23, 'Accepted', 'Accepted', '2024-11-27 09:09:20'),
(98, 23, 'Approved', 'Approved', '2024-11-27 09:10:21'),
(99, 24, 'Processing', 'Processing', '2024-11-27 11:43:19'),
(100, 25, 'Processing', 'Processing', '2024-11-27 11:43:55'),
(101, 26, 'Processing', 'Processing', '2024-11-27 13:06:45'),
(102, 27, 'Processing', 'Processing', '2024-11-27 13:48:34'),
(103, 28, 'Processing', 'Processing', '2024-11-27 13:49:38'),
(104, 29, 'Processing', 'Processing', '2024-11-27 13:52:29'),
(105, 25, 'Accepted', 'Accepted', '2024-11-27 13:58:13'),
(106, 30, 'Application received', 'Processing', '2024-11-27 13:58:27'),
(107, 25, 'Approved', 'Approved', '2024-11-27 14:00:52'),
(108, 31, 'Processing', 'Processing', '2024-11-27 14:01:13'),
(109, 32, 'Processing', 'Processing', '2024-11-27 14:02:17'),
(110, 33, 'Application received', 'Processing', '2024-11-27 14:03:20'),
(111, 34, 'Processing', 'Processing', '2024-11-27 14:14:42'),
(112, 35, 'Processing', 'Processing', '2024-11-27 14:22:10'),
(113, 21, 'Accepted', 'Accepted', '2024-11-29 15:17:53'),
(114, 21, 'Accepted', 'Accepted', '2024-11-29 15:20:29'),
(115, 21, 'Accepted', 'Accepted', '2024-11-29 15:22:04'),
(116, 21, 'Accepted', 'Accepted', '2024-11-29 15:26:26');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `task`, `created_at`) VALUES
(9, 48, 'Add Validation for Approval (Sangla) and Decline Appointment ', '2024-11-27 13:20:17'),
(10, 48, 'Fix fetch of View Client Files', '2024-11-27 13:20:31'),
(11, 135, 'Kain', '2024-11-27 14:02:27'),
(12, 135, 'Tulog', '2024-11-27 14:02:38'),
(13, 135, 'Hanap project', '2024-11-27 14:02:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `dob` date NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `middle_name`, `last_name`, `age`, `gender`, `dob`, `address`, `email`, `phone`, `password`, `role`, `profile_picture`, `created_at`) VALUES
(22, 'Charlie', 'Dela Cruz', 'Taglinao', 21, 'Male', '2003-06-26', '0548 Purok 3 Barangay Pinagtipunan City of General Trias, Cavite', 'taglinao06@gmail.com', '09212288244', '$2y$10$wGdiJjczReiN1QK7spU8Pe5Xh4AQsTSi6lmKvleLPzp8Qxn.o08m.', 'Client', 'uploads/profile_picture/taglinao06@gmail.com/453305309_3765345913785914_7193141333388046034_n.webp', '2024-10-11 15:22:29'),
(29, 'Miko', 'Badinng', 'Admin', 99, 'Male', '2003-02-20', 'admin st.', 'admin@admin.com', '12345678910', '$2y$10$fF02nvJMPv2ttN6KLU9csOf16IYAFRGmZlG8obepQkE5nM2YxKW/C', 'Admin', '', '2024-10-11 15:22:29'),
(48, 'Employee', 'Employee', 'Employee', 99, 'Male', '1900-01-01', 'Employee', 'Employee@employee.com', '09123456789', '$2y$10$YRLZZzfRpEVP9keLTL5fQuElwXxnSGGiF8egjvqYerVHgc5jU3tse', 'Employee', '', '2024-10-11 15:22:29'),
(112, 'Kreian', 'De La Cruz', 'Lanaria', 21, 'Male', '2003-06-19', 'Dasmarinas City, Cavite', 'kreianlanaria@gmail.com', '09456954559', '$2y$10$UQ.z4RXA6DWGfG0OnwGrAuESDOzfwg3lcog5ex0amNnoUd.fEi4TK', 'Client', 'uploads/profile_picture/kreianlanaria@gmail.com/IMG_7720.webp', '2024-10-11 15:22:29'),
(121, 'Marion Mimiko', 'P.', 'Paril', 22, 'Male', '1988-01-01', 'Gen Tri', 'mimikoparil@gmail.com', '09123456789', '$2y$10$bn/tECo4FNj8n.EUOWXYd.84hDBgAQMgzfaqhbTdEJRfV7zE7WoOO', 'Client', NULL, '2024-11-13 09:04:21'),
(122, 'Justine Carl', 'D.', 'Albay', 22, 'Male', '2002-08-05', '293, Lanceville Subd., Malagasang 2A, Imus, Cavite', 'justinecarlalbay@gmail.com', '09686710612', '$2y$10$v/WgYA7s2bLMTuQkxZk8h.6WcsY9epiB78X/aCrBpnaqBDUf19c5W', 'Client', 'uploads/profile_picture/justinecarlalbay@gmail.com/IMG_2345.webp', '2024-11-13 12:35:15'),
(124, 'Franz', 'Altez', 'Acuna', 12, 'Male', '2003-02-23', 'Pascam 2 General Trias City', 'znarf0223@gmail.com', '09695397487', '$2y$10$GfiaJ543hcrjx6PA5QmIDebGaX3l02PtrUd7h8kUY1f/qECcFu5lG', 'Client', NULL, '2024-11-18 13:43:34'),
(125, 'Ederlyn', 'P.', 'Berdiago', 27, 'Female', '1997-03-14', '657 Pinagtipunan General Trias Cavite', 'ederlynlyn2@gmail.com', '09208782151', '$2y$10$IJEFIySBmLv7.yTUO8kOkeZslX3T.Ko6Tx2qGhqJbnZ39qVfcFl.W', 'Client', 'uploads/profile_picture/ederlynlyn2@gmail.com/ca5c389f-9e4d-4336-8123-ccba3728fbd7.webp', '2024-11-20 11:41:53'),
(126, 'EderSr', 'S.', 'Berdiago', 59, 'Male', '1965-09-01', '0595 Sta. Maria st Barangay Pinagtipunan City of General trias, Cavite', 'ederberdiago5@gmail.com', '09353983484', '$2y$10$EhpaSaUqelxA6pTKfJ6l3uN0C5CZxKk36Fu.TdSlEE3DV80Qrid2u', 'Client', NULL, '2024-11-20 12:06:36'),
(127, 'test', 'test', 'test', 21, 'Male', '2003-06-26', 'gentri', 'admin2@admin.com', '09212288244', '$2y$10$ekJXoLP523IfnMQGp7HIteSp1CSVrmiTjPS.25kngybv6vLLeO2zW', 'Admin', NULL, '2024-11-22 18:03:51'),
(128, 'Chyle', 'De La Cruz', 'Lanaria', 22, 'Male', '2003-06-19', 'Dasma', 'liquifienx@gmail.com', '09456954559', '$2y$10$IBE7x/vMZgD3AXIY8XaNk.XRvFtY534xKHjzuUvYThULPB.dx52Jq', 'Client', NULL, '2024-11-25 12:58:33'),
(129, 'Miko', 'A', 'Pena', 22, 'Male', '2001-02-09', 'asdasd', 'test@test.test', '09123456789', '$2y$10$kgCChX6WvHAj.bvXbSik4ufL2R9A1SE7F13Gf9jZWE1KoOb4jSN7a', 'Client', NULL, '2024-11-25 13:14:46'),
(130, 'Jhune', 'GILTENDEZ', 'Ante', 54, 'Male', '1970-09-20', 'Dasmariñas', 'jca092080@gmail.com', '09167755539', '$2y$10$GLof4bDAfdaFO/ti2un3VecBp3zw6zO1uZzKnQD9Dj15zHmx1RlYC', 'Client', 'uploads/profile_picture/jca092080@gmail.com/Screenshot_20241126_143956_YouTube.webp', '2024-11-27 02:15:12'),
(131, 'Kreian', 'De La Cruz', 'Lanaria', 22, 'Male', '2003-12-06', 'Dasmarinas City, Cavite', 'liquifienx2@gmail.com', '09456954559', '$2y$10$NhgzX2IlvVDr7RgjAnPSjemMXMGcjh2HevRR8crhx1aTJkSHz1g0u', 'Client', NULL, '2024-11-27 11:34:25'),
(132, 'Miko', 'P', 'Paril', 22, 'Male', '2002-07-26', 'gen tri', 'marionparil26@gmail.com', '09123456789', '$2y$10$.UAUDyKaom8IHIcIXI4LteMZm5upAwmZKgzwC0MySnaG2ziKUuufW', 'Client', NULL, '2024-11-27 11:42:03'),
(133, 'Kevin', 'X.', 'Bagasbas', 23, 'Male', '2024-02-29', 'Linang, Quezon', 'quilapiomatthew31@gmail.com', '09988888877', '$2y$10$nl4RTAEUH8IfY2dHF8R8dO2qq..NqigGfDAqwxrgF0GrLg2aR1tKi', 'Client', 'uploads/profile_picture/quilapiomatthew31@gmail.com/images (2).webp', '2024-11-27 12:27:15'),
(134, 'Calvin', 'Yang', 'Klein', 30, 'Male', '2000-12-25', 'Real, Laguna', 'jerichorosales@gmail.com', '09774531523', '$2y$10$qSTJHft5NkxArwIHhYYBXeNGWE3h52lP0iV/4ydmEkd0fddV3NdRG', 'Employee', NULL, '2024-11-27 13:44:42'),
(135, 'Hikaru', '', 'Nagi', 27, 'Female', '2000-03-01', 'Tokyo, Japan', 'hikarunagi@yahoo.com', '12345678910', '$2y$10$EK38gn7rxfesvZQ8x3EgTOW/FS4zFNurOEaNUg5Vn50mtHxzAa4RG', 'Employee', NULL, '2024-11-27 13:48:13'),
(136, 'Roma', 'Lenn', 'Almendras', 36, 'Female', '1988-01-16', 'Liora Homes', 'romalennalmendras@gmail.com', '09173393308', '$2y$10$RPKTUv80Wm5bweastBeU6uVHHiiJbULXF8yYZOeZ/uZINY.ZAA7qK', 'Client', NULL, '2024-11-27 14:07:33'),
(145, 'Kreian', 'De La Cruz', 'Lanaria', 21, 'Male', '2003-06-19', 'Dasmarinas CIty, Cavite', 'liquifienx4@gmail.com', '09456954559', '$2y$10$C8rqTt2s9EoJCPs2OA9KzutjEqXmu4OnBgFdf2cYhwzKxcwMP4/QS', 'Client', NULL, '2024-11-30 05:23:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_email` (`user_email`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_email` (`user_email`);

--
-- Indexes for table `forms_brandnew_applicants`
--
ALTER TABLE `forms_brandnew_applicants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forms_sanglaorcr_applicants`
--
ALTER TABLE `forms_sanglaorcr_applicants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `forms_secondhand_applicants`
--
ALTER TABLE `forms_secondhand_applicants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `status_updates`
--
ALTER TABLE `status_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `forms_brandnew_applicants`
--
ALTER TABLE `forms_brandnew_applicants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `forms_sanglaorcr_applicants`
--
ALTER TABLE `forms_sanglaorcr_applicants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `forms_secondhand_applicants`
--
ALTER TABLE `forms_secondhand_applicants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=326;

--
-- AUTO_INCREMENT for table `status_updates`
--
ALTER TABLE `status_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_email`) REFERENCES `users` (`email`);

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`user_email`) REFERENCES `users` (`email`);

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
