-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 13, 2016 at 03:25 PM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 7.0.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `seaolympus`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_number` int(10) UNSIGNED NOT NULL,
  `name` varchar(45) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `id_number`, `name`) VALUES
(2, 190, '2Sample Department1'),
(3, 105, 'Hello Department '),
(4, 901, 'Sample Department'),
(5, 2, 'IT department');

-- --------------------------------------------------------

--
-- Table structure for table `department_supervisors`
--

CREATE TABLE `department_supervisors` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `from` date NOT NULL,
  `to` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `department_supervisors`
--

INSERT INTO `department_supervisors` (`id`, `employee_id`, `department_id`, `from`, `to`) VALUES
(1, 3, 2, '2016-01-20', '2016-01-20'),
(3, 3, 2, '2016-01-20', NULL),
(4, 6, 4, '2016-01-21', NULL),
(5, 18, 5, '2016-03-16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `divisions`
--

CREATE TABLE `divisions` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_number` int(10) UNSIGNED NOT NULL,
  `name` varchar(45) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `divisions`
--

INSERT INTO `divisions` (`id`, `id_number`, `name`) VALUES
(1, 100, 'Division 1'),
(2, 1002, 'Division 3 Hello'),
(3, 1, 'IT division');

-- --------------------------------------------------------

--
-- Table structure for table `division_departments`
--

CREATE TABLE `division_departments` (
  `id` int(10) UNSIGNED NOT NULL,
  `division_id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `from` date NOT NULL,
  `to` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `division_departments`
--

INSERT INTO `division_departments` (`id`, `division_id`, `department_id`, `from`, `to`) VALUES
(1, 1, 2, '2016-01-18', '2016-01-18'),
(2, 2, 2, '2016-01-18', '2016-01-18'),
(3, 1, 2, '2016-01-18', '2016-01-18'),
(4, 2, 2, '2016-01-18', '2016-01-18'),
(5, 1, 2, '2016-01-18', '2016-01-18'),
(6, 2, 2, '2016-01-18', NULL),
(7, 1, 3, '2016-01-18', NULL),
(8, 1, 4, '2016-01-21', NULL),
(9, 3, 5, '2016-03-03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_number` int(10) UNSIGNED DEFAULT NULL,
  `firstname` varchar(50) COLLATE utf8_bin NOT NULL,
  `middleinitial` varchar(50) COLLATE utf8_bin NOT NULL,
  `lastname` varchar(50) COLLATE utf8_bin NOT NULL,
  `birthdate` date NOT NULL,
  `birthplace` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `gender` enum('M','F') COLLATE utf8_bin NOT NULL,
  `civil_status` enum('sg','m','sp','d','w') COLLATE utf8_bin NOT NULL,
  `nationality` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `religion` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `full_address` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `email_address` varchar(45) COLLATE utf8_bin NOT NULL,
  `mobile_number` varchar(15) COLLATE utf8_bin NOT NULL,
  `date_hired` date NOT NULL,
  `login_password` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `sss_number` varchar(45) COLLATE utf8_bin NOT NULL,
  `pagibig_number` varchar(45) COLLATE utf8_bin NOT NULL,
  `tin_number` varchar(45) COLLATE utf8_bin NOT NULL,
  `rfid_uid` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `daily_rate` decimal(15,2) DEFAULT NULL,
  `overtime_rate` decimal(15,2) DEFAULT NULL,
  `allowed_late_period` decimal(15,2) DEFAULT NULL,
  `late_penalty` decimal(15,2) DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `account_type` enum('em','ad') COLLATE utf8_bin NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `id_number`, `firstname`, `middleinitial`, `lastname`, `birthdate`, `birthplace`, `gender`, `civil_status`, `nationality`, `religion`, `full_address`, `email_address`, `mobile_number`, `date_hired`, `login_password`, `sss_number`, `pagibig_number`, `tin_number`, `rfid_uid`, `password`, `daily_rate`, `overtime_rate`, `allowed_late_period`, `late_penalty`, `is_locked`, `account_type`, `created_at`) VALUES
(3, 1, 'JULITO', 'GARCIA', 'CASTANEDA', '1995-06-20', 'Cebu City', 'M', 'w', 'Filipino', 'Roman Catholic', 'Mandaue City, Cebu', 'natabioadr@gmail.com', '09434524412', '2016-12-15', NULL, '1232', '13', 'ss', '00000003', '21232f297a57a5a743894a0e4a801fc3', '560.00', '150.00', '15.00', '15.00', 0, 'ad', '2016-01-17 02:07:01'),
(6, 1003, 'Marvin', 'M', 'Agruda', '2016-01-01', 'Cebu', 'M', 'm', 'Filipino', 'Roman Catholic', 'Cebu City', 'sample@gmail.com', '09233887588', '2016-01-01', NULL, '123', '123', '1232', '6C1C73D0', 'dba0079f1cb3a3b56e102dd5e04fa2af', '350.00', '1.50', '15.00', '15.00', 0, 'em', '2016-01-21 05:55:20'),
(17, NULL, 'Luigie', 'Deo', 'Develos', '1993-06-10', 'Cebu Cuty', 'M', 'sg', 'Filipino', 'Roman Catholic', '', 'wej@gmail.com', '24982387', '2016-01-08', NULL, '1231', '456', '789', NULL, 'e4a9c6ed142135f0ba0c638376562830', '350.00', '1.20', '14.00', '15.00', 0, 'em', '2016-03-14 05:55:35'),
(18, NULL, 'Kevin', 'Juntilla', 'Sandal', '1992-11-15', '', 'M', 'sg', 'filipino', 'christian', '', 'Kevin@gmail.com', '0921323123', '2016-01-11', NULL, '2321321', '123213', '123123', NULL, '6f4922f45568161a8cdf4ad2299f6d23', '500.00', '150.00', '0.00', '5.00', 1, 'em', '2016-03-14 06:13:31'),
(31, NULL, 'Nicole Rey', 'L', 'Arriesga', '2016-05-17', '', 'M', 'sg', '', '', '', 'nicolereya@gmail.com', '09234251308', '2016-05-20', NULL, '12345', '123456', '', NULL, 'fc63f87c08d505264caba37514cd0cfd', '2222.22', '2.00', '2.00', '2.22', 0, 'ad', '2016-05-02 07:19:32');

-- --------------------------------------------------------

--
-- Table structure for table `employee_attendance`
--

CREATE TABLE `employee_attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `datetime_in` timestamp NULL DEFAULT NULL,
  `datetime_out` timestamp NULL DEFAULT NULL,
  `request_id` int(10) UNSIGNED DEFAULT NULL,
  `upload_batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `employee_attendance`
--

INSERT INTO `employee_attendance` (`id`, `employee_id`, `datetime_in`, `datetime_out`, `request_id`, `upload_batch`) VALUES
(259, 18, '2016-03-10 16:00:00', '2016-03-11 12:00:00', 35, 0),
(260, 3, '2016-05-01 14:00:00', '2016-05-01 20:00:00', 35, 0),
(261, 6, '2016-03-09 06:21:53', '2016-03-09 10:22:41', NULL, 0),
(263, 17, '2016-03-01 00:05:06', '2016-03-01 11:05:30', NULL, 0),
(264, 17, '2016-03-01 00:05:06', '2016-03-01 11:05:30', NULL, 0),
(265, 17, '2016-03-01 00:05:06', '2016-03-01 11:05:30', NULL, 0),
(267, 18, '2016-03-01 00:05:06', '2016-03-01 12:05:30', NULL, 0),
(2215, 3, '2016-05-02 14:00:00', '2016-05-03 04:00:00', NULL, 0),
(2216, 3, '2016-05-03 14:00:00', '2016-05-03 20:00:00', NULL, 0),
(2217, 3, '2016-05-02 01:00:00', '2016-05-02 04:05:00', NULL, 0),
(2218, 3, '2015-11-24 03:10:31', '2015-11-24 03:10:39', NULL, 1),
(2219, 3, '2015-11-27 03:13:22', NULL, NULL, 1),
(2220, 3, '2016-05-01 14:00:00', '2016-05-02 04:00:00', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `employee_departments`
--

CREATE TABLE `employee_departments` (
  `id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `from` date NOT NULL,
  `to` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `employee_departments`
--

INSERT INTO `employee_departments` (`id`, `department_id`, `employee_id`, `from`, `to`) VALUES
(2, 2, 3, '2016-01-18', '2016-01-18'),
(4, 3, 3, '2016-01-18', '2016-01-18'),
(5, 2, 3, '2016-01-18', '2016-01-21'),
(9, 2, 6, '2016-01-21', '2016-01-21'),
(10, 4, 6, '2016-01-21', NULL),
(11, 4, 3, '2016-01-21', '2016-02-08'),
(12, 3, 3, '2016-02-08', '2016-03-02'),
(14, 4, 3, '2016-03-02', NULL),
(23, 5, 17, '2016-03-14', NULL),
(24, 5, 18, '2016-03-14', NULL),
(37, 3, 31, '2016-05-02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_positions`
--

CREATE TABLE `employee_positions` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `position_id` int(10) UNSIGNED NOT NULL,
  `from` date NOT NULL,
  `to` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `employee_positions`
--

INSERT INTO `employee_positions` (`id`, `employee_id`, `position_id`, `from`, `to`) VALUES
(1, 6, 1, '2016-01-28', '2016-01-28'),
(2, 6, 2, '2016-01-28', '2016-02-02'),
(6, 6, 3, '2016-02-02', '2016-03-14'),
(7, 3, 5, '2016-02-08', '2016-05-13'),
(20, 6, 2, '2016-03-14', '2016-03-14'),
(21, 6, 1, '2016-03-14', '2016-03-14'),
(26, 6, 4, '2016-03-14', '2016-03-14'),
(27, 6, 3, '2016-03-14', '2016-03-14'),
(28, 6, 1, '2016-03-14', NULL),
(29, 17, 4, '2016-03-14', '2016-03-17'),
(30, 18, 6, '2016-03-14', '2016-03-16'),
(31, 18, 3, '2016-03-16', NULL),
(32, 17, 3, '2016-03-17', NULL),
(45, 31, 4, '2016-05-02', NULL),
(46, 3, 2, '2016-05-13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_requests`
--

CREATE TABLE `employee_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `type` enum('matpat','sl','wml','vl','o') COLLATE utf8_bin NOT NULL COMMENT 'matpat - maternity/paternity leave\nsl - sick leave\nmpl - mens paid leave\nwml - womens menstruation leave\nvl - vacation leave\no - others',
  `custom_type_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `datetime_filed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `title` varchar(100) COLLATE utf8_bin NOT NULL,
  `content` text COLLATE utf8_bin,
  `approved_by` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('p','a','da') COLLATE utf8_bin NOT NULL COMMENT 'p - pending\na - approved\nda - disapproved',
  `is_acknowledged` tinyint(1) NOT NULL DEFAULT '0',
  `halfday` enum('am','pm') COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `employee_requests`
--

INSERT INTO `employee_requests` (`id`, `sender_id`, `department_id`, `type`, `custom_type_name`, `datetime_filed`, `date_start`, `date_end`, `title`, `content`, `approved_by`, `status`, `is_acknowledged`, `halfday`) VALUES
(34, 3, 4, 'sl', NULL, '2016-03-13 16:15:38', '2016-03-01', '2016-03-01', '', 'Sick', NULL, 'a', 0, 'pm'),
(35, 3, 4, 'sl', NULL, '2016-03-13 16:17:36', '2016-03-10', '2016-03-11', '', 'Sick again\r\n', NULL, 'a', 0, NULL),
(36, 17, 5, 'sl', NULL, '2016-03-16 05:27:20', '2016-03-22', '2016-03-23', '', 'fever', NULL, 'p', 0, NULL),
(37, 3, 4, 'sl', NULL, '2016-03-17 16:16:10', '2016-03-01', '2016-03-01', '', 'Hehe', NULL, 'p', 0, 'pm'),
(38, 3, 4, 'sl', NULL, '2016-03-20 14:37:04', '2016-03-01', '2016-03-01', '', 'asdsadsad', NULL, 'p', 0, NULL),
(39, 3, 4, 'sl', NULL, '2016-03-20 14:37:17', '2016-03-28', '2016-03-28', '', 'asdsadsad', NULL, 'p', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) UNSIGNED NOT NULL,
  `loan_date` timestamp NULL DEFAULT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `loan_amount` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `loan_date`, `employee_id`, `loan_amount`) VALUES
(5, '2016-04-30 16:00:00', 3, '100.00');

-- --------------------------------------------------------

--
-- Table structure for table `payment_terms`
--

CREATE TABLE `payment_terms` (
  `id` int(10) NOT NULL,
  `loan_id` int(10) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_amount` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payment_terms`
--

INSERT INTO `payment_terms` (`id`, `loan_id`, `payment_date`, `payment_amount`) VALUES
(18, 13, '2016-05-17', '55.55'),
(19, 13, '2016-05-20', '55.55'),
(20, 13, '2016-05-17', '55.55'),
(21, 13, '2016-05-20', '55.55'),
(42, 14, '2016-05-03', '11111.11'),
(43, 1, '2016-05-06', '50.00'),
(44, 1, '2016-05-07', '50.00'),
(45, 2, '2016-05-04', '50.00'),
(46, 2, '2016-05-05', '50.00'),
(47, 3, '2016-05-04', '50.00'),
(48, 3, '2016-05-05', '50.00'),
(49, 4, '2016-05-03', '50.00'),
(50, 4, '2016-05-05', '50.00'),
(53, 5, '2016-05-03', '50.00'),
(54, 5, '2016-05-05', '50.00');

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `days_rendered` float DEFAULT '0',
  `overtime_hours_rendered` float DEFAULT '0',
  `late_minutes` float DEFAULT '0',
  `current_daily_wage` decimal(13,2) DEFAULT '0.00',
  `daily_wage_units` int(11) NOT NULL,
  `wage_adjustment` decimal(13,2) DEFAULT '0.00',
  `current_late_penalty` decimal(13,2) DEFAULT '0.00',
  `overtime_pay` decimal(13,2) DEFAULT '0.00',
  `created_by` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `employee_id`, `start_date`, `end_date`, `days_rendered`, `overtime_hours_rendered`, `late_minutes`, `current_daily_wage`, `daily_wage_units`, `wage_adjustment`, `current_late_penalty`, `overtime_pay`, `created_by`) VALUES
(13, 3, '2016-05-01', '2016-05-15', 1, 0, 0, '560.00', 0, '0.00', '15.00', '0.00', 3);

-- --------------------------------------------------------

--
-- Table structure for table `payroll_particulars`
--

CREATE TABLE `payroll_particulars` (
  `id` int(10) UNSIGNED NOT NULL,
  `payroll_id` int(10) UNSIGNED NOT NULL,
  `particulars_id` int(10) UNSIGNED NOT NULL,
  `units` int(11) NOT NULL,
  `amount` decimal(13,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `payroll_particulars`
--

INSERT INTO `payroll_particulars` (`id`, `payroll_id`, `particulars_id`, `units`, `amount`) VALUES
(13, 13, 2, 0, '1.11'),
(14, 13, 3, 0, '11.11');

-- --------------------------------------------------------

--
-- Table structure for table `pay_modifiers`
--

CREATE TABLE `pay_modifiers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8_bin NOT NULL,
  `particular_type` enum('d','m') COLLATE utf8_bin NOT NULL COMMENT 'd - daily     m - monthly',
  `type` enum('a','d') COLLATE utf8_bin NOT NULL COMMENT 'a - additional\nd - deductions'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `pay_modifiers`
--

INSERT INTO `pay_modifiers` (`id`, `name`, `particular_type`, `type`) VALUES
(1, 'SSS Premium', 'd', 'd'),
(2, 'Allowance', 'm', 'd'),
(3, 'Bonus', 'd', 'a');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(45) COLLATE utf8_bin NOT NULL,
  `attendance_type` enum('re','fl') COLLATE utf8_bin DEFAULT NULL,
  `workday` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `name`, `attendance_type`, `workday`) VALUES
(1, 'General Manager', 're', ''),
(2, 'Regular Employee', 're', '[{"from_day":"7","to_day":"1","time":{"from_time_1":"10:00 PM","to_time_1":"4:00 AM","from_time_2":"9:00 AM","to_time_2":"12:00 PM"},"first_hours":6,"second_hours":3,"total_working_hours":9},{"from_day":"1","to_day":"2","time":{"from_time_1":"10:00 PM","to_time_1":"4:00 AM","from_time_2":"9:00 AM","to_time_2":"12:00 PM"},"first_hours":6,"second_hours":3,"total_working_hours":9},{"from_day":"2","to_day":"3","time":{"from_time_1":"10:00 PM","to_time_1":"4:00 AM","from_time_2":"9:00 AM","to_time_2":"12:00 PM"},"first_hours":6,"second_hours":3,"total_working_hours":9}]'),
(3, 'Supervisor', 're', ''),
(4, 'Payroll Manager', 're', ''),
(5, 'Site Admin', 're', '[{"from_day":"7","to_day":"1","time":{"from_time_1":"10:00 PM","to_time_1":"2:15 AM","from_time_2":"9:00 AM","to_time_2":"12:00 PM"},"time_breakdown":{"7":[["10:00 PM","12:00 AM"]],"1":[["12:00 AM","2:15 AM"],["9:00 AM","12:00 PM"]]}}]'),
(6, 'Sample Position', 're', ''),
(7, 'Regular Employee class B', 're', '[{"from_day":"1","to_day":"1","time":{"from_time_1":"1:15 AM","to_time_1":"2:00 AM","from_time_2":"5:30 AM","to_time_2":"7:15 AM"},"time_breakdown":{"1":[]}}]');

-- --------------------------------------------------------

--
-- Table structure for table `salary_particulars`
--

CREATE TABLE `salary_particulars` (
  `employee_id` int(10) UNSIGNED NOT NULL,
  `particulars_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `salary_particulars`
--

INSERT INTO `salary_particulars` (`employee_id`, `particulars_id`, `amount`) VALUES
(18, 2, '1800.00'),
(3, 3, '11.11'),
(3, 2, '1.11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `employee_id` int(10) UNSIGNED NOT NULL,
  `password` varchar(45) COLLATE utf8_bin NOT NULL,
  `type` enum('hr','sv','po','re','su') COLLATE utf8_bin NOT NULL COMMENT 'hr - human resource officer\nsv - supervisor\npo - payroll officer\nre - regular employee\nsu - superuser',
  `is_locked` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`employee_id`, `password`, `type`, `is_locked`, `created_at`) VALUES
(3, '8c4205ec33d8f6caeaaaa0c10a14138c', 'su', NULL, '2016-01-19 16:20:59'),
(6, '5f4dcc3b5aa765d61d8327deb882cf99', 'sv', NULL, '2016-01-21 05:56:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`),
  ADD UNIQUE KEY `id_number_UNIQUE` (`id_number`);

--
-- Indexes for table `department_supervisors`
--
ALTER TABLE `department_supervisors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_supervisors_employee_id_foreign_idx` (`employee_id`),
  ADD KEY `department_supervisors_department_id_foreign_idx` (`department_id`);

--
-- Indexes for table `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_number_UNIQUE` (`id_number`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`);

--
-- Indexes for table `division_departments`
--
ALTER TABLE `division_departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `division_departments_division_id_foreign_idx` (`division_id`),
  ADD KEY `division_departments_department_id_foreign_idx` (`department_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_address_UNIQUE` (`email_address`),
  ADD UNIQUE KEY `employee_number_UNIQUE` (`id_number`),
  ADD UNIQUE KEY `rfid_uid_UNIQUE` (`rfid_uid`);

--
-- Indexes for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_attendance_employee_id_foreign_idx` (`employee_id`),
  ADD KEY `employee_attendance_request_id_foreign_idx` (`request_id`);

--
-- Indexes for table `employee_departments`
--
ALTER TABLE `employee_departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_departments_department_id_foreign_idx` (`department_id`),
  ADD KEY `employee_departments_employee_id_foreign_idx` (`employee_id`);

--
-- Indexes for table `employee_positions`
--
ALTER TABLE `employee_positions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_positions_employee_id_foreign_idx` (`employee_id`),
  ADD KEY `employee_positions_position_id_foreign_idx` (`position_id`);

--
-- Indexes for table `employee_requests`
--
ALTER TABLE `employee_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_requests_sender_id_foreign_idx` (`sender_id`),
  ADD KEY `employee_requests_department_id_foreign_idx` (`department_id`),
  ADD KEY `employee_requests_approved_by_foreign_idx` (`approved_by`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_terms`
--
ALTER TABLE `payment_terms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_created_by_foreign_idx` (`created_by`),
  ADD KEY `payroll_employee_id_foreign_idx` (`employee_id`);

--
-- Indexes for table `payroll_particulars`
--
ALTER TABLE `payroll_particulars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_particulars_payroll_id_foreign_idx` (`payroll_id`),
  ADD KEY `payroll_particulars_particulars_id_foreign_idx` (`particulars_id`);

--
-- Indexes for table `pay_modifiers`
--
ALTER TABLE `pay_modifiers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`);

--
-- Indexes for table `salary_particulars`
--
ALTER TABLE `salary_particulars`
  ADD KEY `salary_particulars_particulars_id_foreign_idx` (`particulars_id`),
  ADD KEY `salary_particulars_employee_id_foreign_idx` (`employee_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `department_supervisors`
--
ALTER TABLE `department_supervisors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `division_departments`
--
ALTER TABLE `division_departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2221;
--
-- AUTO_INCREMENT for table `employee_departments`
--
ALTER TABLE `employee_departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT for table `employee_positions`
--
ALTER TABLE `employee_positions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
--
-- AUTO_INCREMENT for table `employee_requests`
--
ALTER TABLE `employee_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `payment_terms`
--
ALTER TABLE `payment_terms`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `payroll_particulars`
--
ALTER TABLE `payroll_particulars`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `pay_modifiers`
--
ALTER TABLE `pay_modifiers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `department_supervisors`
--
ALTER TABLE `department_supervisors`
  ADD CONSTRAINT `department_supervisors_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `department_supervisors_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `division_departments`
--
ALTER TABLE `division_departments`
  ADD CONSTRAINT `division_departments_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `division_departments_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  ADD CONSTRAINT `employee_attendance_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `employee_attendance_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `employee_requests` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `employee_departments`
--
ALTER TABLE `employee_departments`
  ADD CONSTRAINT `employee_departments_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `employee_departments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `employee_positions`
--
ALTER TABLE `employee_positions`
  ADD CONSTRAINT `employee_positions_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `employee_positions_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `employee_requests`
--
ALTER TABLE `employee_requests`
  ADD CONSTRAINT `employee_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `employees` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `employee_requests_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `employee_requests_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `payroll_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `payroll_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `payroll_particulars`
--
ALTER TABLE `payroll_particulars`
  ADD CONSTRAINT `payroll_particulars_particulars_id_foreign` FOREIGN KEY (`particulars_id`) REFERENCES `pay_modifiers` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `payroll_particulars_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payroll` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `salary_particulars`
--
ALTER TABLE `salary_particulars`
  ADD CONSTRAINT `salary_particulars_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `salary_particulars_particulars_id_foreign` FOREIGN KEY (`particulars_id`) REFERENCES `pay_modifiers` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
