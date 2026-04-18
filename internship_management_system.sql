-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 18, 2026 at 06:33 PM
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
  `UserID` int(11) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `PhoneNumber` varchar(22) DEFAULT NULL,
  `EmailAddress` varchar(128) NOT NULL,
  `Organisation` varchar(128) DEFAULT NULL,
  `AssessorType` enum('Lecturer','Industry Supervisor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `internship`
--

CREATE TABLE `internship` (
  `InternID` int(11) NOT NULL,
  `SupervisorID` int(11) NOT NULL,
  `LecturerID` int(11) NOT NULL,
  `StudentID` int(11) NOT NULL,
  `InternshipCompany` varchar(255) DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `ReportStatus` enum('Drafting','In Progress','Suspended','Finalisation','Complete') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `internshipreport`
--

CREATE TABLE `internshipreport` (
  `InternID` int(11) NOT NULL,
  `taskScore` decimal(5,2) DEFAULT '0.00',
  `safetyScore` decimal(5,2) DEFAULT '0.00',
  `theoryScore` decimal(5,2) DEFAULT '0.00',
  `presentScore` decimal(5,2) DEFAULT '0.00',
  `clarityScore` decimal(5,2) DEFAULT '0.00',
  `proj_mgmt_score` decimal(5,2) DEFAULT '0.00',
  `time_mgmt_score` decimal(5,2) DEFAULT '0.00',
  `comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `StudentID` int(11) NOT NULL,
  `StudentRegNumber` varchar(20) NOT NULL,
  `StudentName` varchar(255) NOT NULL,
  `EmailAddress` varchar(255) NOT NULL,
  `Programme` varchar(255) DEFAULT NULL,
  `EnrollmentDate` date DEFAULT NULL,
  `AccountStatus` enum('Active','Graduated','On-leave','Suspended') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `userlogin`
--

CREATE TABLE `userlogin` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `SystemRole` enum('Admin','Assessor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessor`
--
ALTER TABLE `assessor`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `EmailAddress` (`EmailAddress`);

--
-- Indexes for table `internship`
--
ALTER TABLE `internship`
  ADD PRIMARY KEY (`InternID`),
  ADD KEY `SupervisorID` (`SupervisorID`),
  ADD KEY `LecturerID` (`LecturerID`),
  ADD KEY `StudentID` (`StudentID`);

--
-- Indexes for table `internshipreport`
--
ALTER TABLE `internshipreport`
  ADD PRIMARY KEY (`InternID`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`StudentID`),
  ADD UNIQUE KEY `StudentRegNumber` (`StudentRegNumber`),
  ADD UNIQUE KEY `EmailAddress` (`EmailAddress`);

--
-- Indexes for table `userlogin`
--
ALTER TABLE `userlogin`
  ADD PRIMARY KEY (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `internship`
--
ALTER TABLE `internship`
  MODIFY `InternID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `StudentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `userlogin`
--
ALTER TABLE `userlogin`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessor`
--
ALTER TABLE `assessor`
  ADD CONSTRAINT `assessor_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `userlogin` (`UserID`);

--
-- Constraints for table `internship`
--
ALTER TABLE `internship`
  ADD CONSTRAINT `internship_ibfk_1` FOREIGN KEY (`SupervisorID`) REFERENCES `assessor` (`UserID`),
  ADD CONSTRAINT `internship_ibfk_2` FOREIGN KEY (`LecturerID`) REFERENCES `assessor` (`UserID`),
  ADD CONSTRAINT `internship_ibfk_3` FOREIGN KEY (`StudentID`) REFERENCES `student` (`StudentID`);

--
-- Constraints for table `internshipreport`
--
ALTER TABLE `internshipreport`
  ADD CONSTRAINT `internshipreport_ibfk_1` FOREIGN KEY (`InternID`) REFERENCES `internship` (`InternID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
