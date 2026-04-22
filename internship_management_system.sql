-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 22, 2026 at 12:28 PM
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
-- Database: `internship management system`
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
  `assessor_type` enum('Lecturer','Industry Supervisor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `proj_mgmt_score` decimal(5,2) DEFAULT '0.00',
  `time_mgmt_score` decimal(5,2) DEFAULT '0.00',
  `comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  ADD UNIQUE KEY `intern_id` (`intern_id`),
  ADD UNIQUE KEY `assessor_id` (`assessor_id`),
  ADD UNIQUE KEY `intern_id_2` (`intern_id`,`assessor_id`);

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
  MODIFY `intern_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_login`
--
ALTER TABLE `user_login`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

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
