-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 22, 2026 at 01:21 PM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `internship_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `assessor`
--

CREATE TABLE `assessor` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone_number` varchar(22) DEFAULT NULL,
  `email_address` varchar(128) NOT NULL,
  `organisation` varchar(128) DEFAULT NULL,
  `assessor_type` enum('Lecturer','Supervisor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `assessor`
--

INSERT INTO `assessor` (`user_id`, `full_name`, `phone_number`, `email_address`, `organisation`, `assessor_type`) VALUES
(2, 'John Lecturer', '60388430263', 'johnLecturer@gmail.com', 'Nottingham', 'Lecturer'),
(3, 'John Supervisor', '60320988768', 'johnSupervisor@gmail.com', 'Google', 'Supervisor');

-- --------------------------------------------------------

--
-- Table structure for table `internship`
--

CREATE TABLE `internship` (
  `intern_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `internship_company` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `report_status` enum('Drafting','In Progress','Suspended','Finalisation','Complete') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `internship`
--

INSERT INTO `internship` (`intern_id`, `supervisor_id`, `lecturer_id`, `student_id`, `internship_company`, `start_date`, `end_date`, `report_status`) VALUES
(1, 3, 2, 1, 'Google', '2026-04-22', '2027-04-22', 'In Progress');

-- --------------------------------------------------------

--
-- Table structure for table `internship_report`
--

CREATE TABLE `internship_report` (
  `report_id` int(11) NOT NULL,
  `intern_id` int(11) NOT NULL,
  `assessor_id` int(11) NOT NULL,
  `task_score` decimal(5,2) DEFAULT '0.00',
  `safety_score` decimal(5,2) DEFAULT '0.00',
  `theory_score` decimal(5,2) DEFAULT '0.00',
  `present_score` decimal(5,2) DEFAULT '0.00',
  `clarity_score` decimal(5,2) DEFAULT '0.00',
  `learning_score` decimal(5,2) DEFAULT '0.00',
  `proj_mgmt_score` decimal(5,2) DEFAULT '0.00',
  `time_mgmt_score` decimal(5,2) DEFAULT '0.00',
  `comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `internship_report`
--

INSERT INTO `internship_report` (`report_id`, `intern_id`, `assessor_id`, `task_score`, `safety_score`, `theory_score`, `present_score`, `clarity_score`, `learning_score`, `proj_mgmt_score`, `time_mgmt_score`, `comment`) VALUES
(5, 1, 2, 80.00, 80.00, 80.00, 80.00, 80.00, 80.00, 80.00, 80.00, 'Lecturer - Decent guy'),
(6, 1, 3, 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, 'Supervisor - Good employee');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `student_reg_number` varchar(20) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `programme` varchar(255) DEFAULT NULL,
  `enrollment_date` date DEFAULT NULL,
  `account_status` enum('Active','Graduated','On-leave','Suspended') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `student_reg_number`, `student_name`, `email_address`, `programme`, `enrollment_date`, `account_status`) VALUES
(1, '20708501', 'John Student', 'johnStudent@gmail.com', 'Computer Science', '2026-04-22', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `user_login`
--

CREATE TABLE `user_login` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `system_role` enum('Admin','Assessor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_login`
--

INSERT INTO `user_login` (`user_id`, `username`, `password`, `system_role`) VALUES
(1, 'root', 'root', 'Admin'),
(2, 'lecturer', 'lecturer', 'Assessor'),
(3, 'supervisor', 'supervisor', 'Assessor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessor`
--
ALTER TABLE `assessor`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email_address` (`email_address`);

--
-- Indexes for table `internship`
--
ALTER TABLE `internship`
  ADD PRIMARY KEY (`intern_id`),
  ADD KEY `supervisor_id` (`supervisor_id`),
  ADD KEY `lecturer_id` (`lecturer_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `internship_report`
--
ALTER TABLE `internship_report`
  ADD PRIMARY KEY (`report_id`),
  ADD UNIQUE KEY `intern_id` (`intern_id`,`assessor_id`),
  ADD KEY `assessor_id` (`assessor_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_reg_number` (`student_reg_number`),
  ADD UNIQUE KEY `email_address` (`email_address`);

--
-- Indexes for table `user_login`
--
ALTER TABLE `user_login`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `internship`
--
ALTER TABLE `internship`
  MODIFY `intern_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `internship_report`
--
ALTER TABLE `internship_report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_login`
--
ALTER TABLE `user_login`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessor`
--
ALTER TABLE `assessor`
  ADD CONSTRAINT `assessor_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_login` (`user_id`);

--
-- Constraints for table `internship`
--
ALTER TABLE `internship`
  ADD CONSTRAINT `internship_ibfk_1` FOREIGN KEY (`supervisor_id`) REFERENCES `assessor` (`user_id`),
  ADD CONSTRAINT `internship_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `assessor` (`user_id`),
  ADD CONSTRAINT `internship_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);

--
-- Constraints for table `internship_report`
--
ALTER TABLE `internship_report`
  ADD CONSTRAINT `internship_report_ibfk_1` FOREIGN KEY (`intern_id`) REFERENCES `internship` (`intern_id`),
  ADD CONSTRAINT `internship_report_ibfk_2` FOREIGN KEY (`assessor_id`) REFERENCES `assessor` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
