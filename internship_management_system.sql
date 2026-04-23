-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 23, 2026 at 06:54 PM
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
  `assessor_type` enum('Lecturer','Supervisor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `assessor`
--

INSERT INTO `assessor` (`user_id`, `full_name`, `phone_number`, `email_address`, `organisation`, `assessor_type`) VALUES
(2, 'John Lecturer', '60388430263', 'johnLecturer@gmail.com', 'Nottingham', 'Lecturer'),
(3, 'John Supervisor', '60320988768', 'johnSupervisor@gmail.com', 'Google', 'Supervisor'),
(9, 'Adam Conover', '60384347632', 'adamConover@gmail.com', 'Dropout', 'Supervisor'),
(10, 'Steve Jannesson', '60123347672', 'steveJannesson@gmail.com', 'Nottingham', 'Lecturer'),
(12, 'Jaya Kumar', '60324592304', 'jayaKumar@hotmail.com', 'Nottingham', 'Lecturer'),
(13, 'Mark Zuckerberg', '60324142504', 'marckzuckerberg@meta.com', 'Meta', 'Supervisor');

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
(1, 3, 2, 1, 'Google', '2026-04-22', '2027-04-22', 'In Progress'),
(2, 3, 2, 3, 'Google', '2026-04-22', '2027-04-22', 'In Progress'),
(3, 9, 10, 7, 'Dropout', '2026-02-22', '2026-04-30', 'Finalisation'),
(4, 13, 12, 4, 'Meta', '2026-04-27', '2027-04-22', 'Drafting'),
(5, 3, 2, 5, 'Google', '2023-07-13', '2023-10-18', 'Complete'),
(6, 9, 12, 6, 'Dropout', '2026-04-22', '2027-04-22', 'In Progress'),
(7, 3, 10, 2, 'Google', '2026-04-22', '2027-04-22', 'In Progress'),
(8, 13, 10, 2, 'Meta', '2025-04-22', '2026-03-22', 'Complete'),
(9, 3, 12, 2, 'Google', '2026-01-22', '2026-02-21', 'Complete'),
(10, 9, 12, 10, 'Dropout', '2026-02-22', '2026-04-30', 'Suspended');

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
  `comment` text,
  `total_marks` decimal(5,2) GENERATED ALWAYS AS ((((((((`task_score` + `safety_score`) + `theory_score`) + `present_score`) + `clarity_score`) + `learning_score`) + `proj_mgmt_score`) + `time_mgmt_score`)) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `internship_report`
--

INSERT INTO `internship_report` (`report_id`, `intern_id`, `assessor_id`, `task_score`, `safety_score`, `theory_score`, `present_score`, `clarity_score`, `learning_score`, `proj_mgmt_score`, `time_mgmt_score`, `comment`) VALUES
(5, 1, 2, 80.00, 80.00, 80.00, 80.00, 80.00, 80.00, 80.00, 80.00, 'Lecturer - Decent guy'),
(6, 1, 3, 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, 'Supervisor - Good employee'),
(7, 2, 2, 85.00, 78.00, 92.00, 88.00, 80.00, 75.00, 84.00, 90.00, 'Excellent technical skills shown.'),
(8, 2, 3, 80.00, 82.00, 85.00, 75.00, 78.00, 80.00, 82.00, 85.00, 'Reliable and proactive team player.'),
(9, 3, 9, 65.00, 70.00, 68.00, 72.00, 60.00, 75.00, 68.00, 70.00, 'Needs improvement in documentation.'),
(10, 3, 10, 70.00, 68.00, 75.00, 65.00, 70.00, 68.00, 72.00, 65.00, 'Satisfactory performance overall.'),
(11, 4, 12, 95.00, 90.00, 88.00, 92.00, 95.00, 85.00, 90.00, 92.00, 'Outstanding project management skills.'),
(12, 4, 13, 90.00, 88.00, 85.00, 90.00, 88.00, 92.00, 85.00, 90.00, 'Very professional conduct.'),
(13, 5, 2, 78.00, 85.00, 80.00, 72.00, 85.00, 78.00, 80.00, 82.00, 'Good understanding of safety rules.'),
(14, 5, 3, 82.00, 80.00, 78.00, 85.00, 80.00, 82.00, 78.00, 80.00, 'Met all core objectives.'),
(15, 6, 9, 88.00, 92.00, 85.00, 80.00, 88.00, 90.00, 85.00, 82.00, 'Strong theoretical knowledge application.'),
(16, 6, 12, 85.00, 88.00, 82.00, 85.00, 90.00, 88.00, 82.00, 85.00, 'Consistent and hardworking.'),
(17, 7, 3, 60.00, 65.00, 70.00, 62.00, 65.00, 68.00, 60.00, 72.00, 'Struggled with time management.'),
(18, 7, 10, 65.00, 62.00, 68.00, 70.00, 62.00, 65.00, 68.00, 60.00, 'Basic requirements fulfilled.'),
(19, 8, 10, 72.00, 75.00, 78.00, 80.00, 72.00, 75.00, 78.00, 80.00, 'Solid presentation of results.'),
(20, 8, 13, 75.00, 78.00, 80.00, 72.00, 75.00, 78.00, 80.00, 72.00, 'Great attitude toward learning.'),
(21, 9, 3, 92.00, 95.00, 90.00, 88.00, 92.00, 95.00, 90.00, 88.00, 'Top tier coding ability.'),
(22, 9, 12, 90.00, 92.00, 95.00, 90.00, 88.00, 92.00, 95.00, 90.00, 'Exceptional attention to detail.'),
(23, 10, 3, 84.00, 80.00, 82.00, 85.00, 88.00, 82.00, 85.00, 80.00, 'Effective communicator.'),
(24, 10, 12, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'Guy burned the company building down');

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
(1, '20708501', 'John Student', 'johnStudent@gmail.com', 'Computer Science', '2026-04-22', 'Active'),
(2, '20708502', 'Alice Tan', 'alice.tan@example.com', 'Computer Science', '2025-09-01', 'Active'),
(3, '20708503', 'Mohammad Razak', 'm.razak@example.com', 'Biotech', '2025-09-01', 'Active'),
(4, '20708504', 'Siti Aminah', 'siti.a@example.com', 'Education', '2026-01-15', 'Active'),
(5, '20708505', 'Kevin Wong', 'kwong99@example.com', 'Business', '2025-09-01', 'Active'),
(6, '20708506', 'Priya Devi', 'p.devi@example.com', 'Computer Science', '2026-01-15', 'Active'),
(7, '20708507', 'Marcus Lim', 'marcus.l@example.com', 'Creative Writing', '2025-09-01', 'Active'),
(8, '19807422', 'Sarah Jenkins', 's.jenkins@alumni.com', 'Software Engineering', '2022-09-01', 'Graduated'),
(9, '20609910', 'Zulfikar Ali', 'zali@example.com', 'Information Technology', '2024-09-01', 'On-leave'),
(10, '20709001', 'Sol Badguy', 'guiltyGear@strive.com', 'Mechanical Engineering', '2025-09-01', 'Suspended');

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
(3, 'supervisor', 'supervisor', 'Assessor'),
(9, 'adam104', 'password1234', 'Assessor'),
(10, 'steveMario', 'terraria', 'Assessor'),
(11, 'Micheal', 'HATESTUDENTS34', 'Admin'),
(12, 'JayaAwesome', 'LoveMyStudents', 'Assessor'),
(13, 'MarkieZuckie', 'alienMothership', 'Assessor');

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
  MODIFY `intern_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `internship_report`
--
ALTER TABLE `internship_report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_login`
--
ALTER TABLE `user_login`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
