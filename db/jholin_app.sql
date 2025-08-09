-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 10, 2025 at 02:35 AM
-- Server version: 10.11.13-MariaDB-cll-lve
-- PHP Version: 8.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jholin_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `Mobile_Numbers`
--

CREATE TABLE `Mobile_Numbers` (
  `mobile_id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `Mobile_Numbers`
--

INSERT INTO `Mobile_Numbers` (`mobile_id`, `owner_id`, `mobile_number`, `is_verified`) VALUES
(1, 1, '1234567890', 0),
(3, 3, '7588070543', 0),
(4, 4, '12345987', 0),
(8, 5, '250001', 0),
(10, 2, '9867999773', 0);

-- --------------------------------------------------------

--
-- Table structure for table `Owners`
--

CREATE TABLE `Owners` (
  `owner_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `locality` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `Owners`
--

INSERT INTO `Owners` (`owner_id`, `first_name`, `middle_name`, `last_name`, `locality`) VALUES
(1, '', NULL, '', ''),
(2, 'Arc', 'S', 'Singh', 'Warje'),
(3, '', NULL, '', ''),
(4, '', NULL, '', ''),
(5, '', NULL, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `Pets`
--

CREATE TABLE `Pets` (
  `pet_id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `unique_id` varchar(6) NOT NULL,
  `pet_name` varchar(50) NOT NULL,
  `species` enum('Canine','Feline','Avian','Tortoise','Exotic') NOT NULL,
  `breed` varchar(50) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `dob` date NOT NULL,
  `qr_path` varchar(255) DEFAULT NULL,
  `barcode_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `Pets`
--

INSERT INTO `Pets` (`pet_id`, `owner_id`, `unique_id`, `pet_name`, `species`, `breed`, `gender`, `dob`, `qr_path`, `barcode_path`) VALUES
(3, 2, '250001', 'Romeo Jainz', 'Canine', 'German Shepard', 'Male', '2018-05-05', 'uploads/qr/250001.png', 'uploads/barcode/250001.png'),
(4, 3, '250002', '', 'Canine', '', 'Male', '2025-08-08', NULL, NULL),
(5, 1, '250003', '', 'Canine', '', 'Male', '2025-08-08', NULL, NULL),
(6, 4, '250004', '', 'Canine', '', 'Male', '2025-08-08', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Pet_Images`
--

CREATE TABLE `Pet_Images` (
  `image_id` int(11) NOT NULL,
  `pet_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `PlanSteps`
--

CREATE TABLE `PlanSteps` (
  `step_id` int(11) NOT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `type` enum('vaccination','deworming','tick_flea','other') NOT NULL,
  `treatment_name` varchar(100) NOT NULL,
  `spacing_days` int(11) NOT NULL,
  `duration_months` int(11) DEFAULT NULL,
  `species_tags` varchar(255) DEFAULT 'All'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Reminders`
--

CREATE TABLE `Reminders` (
  `reminder_id` int(11) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `due_date` date NOT NULL,
  `sent` tinyint(1) DEFAULT 0,
  `method` enum('sms','whatsapp','email') DEFAULT 'sms'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Schedules`
--

CREATE TABLE `Schedules` (
  `schedule_id` int(11) NOT NULL,
  `pet_id` int(11) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `type` enum('vaccination','deworming','tick_flea','other') NOT NULL,
  `treatment_name` varchar(100) NOT NULL,
  `date_administered` date DEFAULT NULL,
  `next_due` date DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Settings`
--

CREATE TABLE `Settings` (
  `setting_id` int(11) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `field_name` varchar(50) NOT NULL,
  `field_type` varchar(20) NOT NULL,
  `field_options` text DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `Settings`
--

INSERT INTO `Settings` (`setting_id`, `table_name`, `field_name`, `field_type`, `field_options`, `is_required`) VALUES
(1, 'Pets', 'species', 'ENUM', 'Canine,Feline,Avian,Tortoise,Exotic', 1),
(2, 'Pets', 'gender', 'ENUM', 'Male,Female', 1),
(3, 'Pets', 'breed', 'VARCHAR', NULL, 1),
(4, 'Owners', 'first_name', 'VARCHAR', NULL, 1),
(5, 'Owners', 'middle_name', 'VARCHAR', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `TreatmentPlans`
--

CREATE TABLE `TreatmentPlans` (
  `plan_id` int(11) NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Visits`
--

CREATE TABLE `Visits` (
  `visit_id` int(11) NOT NULL,
  `pet_id` int(11) DEFAULT NULL,
  `visit_date` date NOT NULL,
  `next_visit_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Mobile_Numbers`
--
ALTER TABLE `Mobile_Numbers`
  ADD PRIMARY KEY (`mobile_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `Owners`
--
ALTER TABLE `Owners`
  ADD PRIMARY KEY (`owner_id`);

--
-- Indexes for table `Pets`
--
ALTER TABLE `Pets`
  ADD PRIMARY KEY (`pet_id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `Pet_Images`
--
ALTER TABLE `Pet_Images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `pet_id` (`pet_id`);

--
-- Indexes for table `PlanSteps`
--
ALTER TABLE `PlanSteps`
  ADD PRIMARY KEY (`step_id`),
  ADD KEY `plan_id` (`plan_id`);

--
-- Indexes for table `Reminders`
--
ALTER TABLE `Reminders`
  ADD PRIMARY KEY (`reminder_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `Schedules`
--
ALTER TABLE `Schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `plan_id` (`plan_id`);

--
-- Indexes for table `Settings`
--
ALTER TABLE `Settings`
  ADD PRIMARY KEY (`setting_id`);

--
-- Indexes for table `TreatmentPlans`
--
ALTER TABLE `TreatmentPlans`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `Visits`
--
ALTER TABLE `Visits`
  ADD PRIMARY KEY (`visit_id`),
  ADD KEY `pet_id` (`pet_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Mobile_Numbers`
--
ALTER TABLE `Mobile_Numbers`
  MODIFY `mobile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `Owners`
--
ALTER TABLE `Owners`
  MODIFY `owner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Pets`
--
ALTER TABLE `Pets`
  MODIFY `pet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `Pet_Images`
--
ALTER TABLE `Pet_Images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PlanSteps`
--
ALTER TABLE `PlanSteps`
  MODIFY `step_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT for table `Reminders`
--
ALTER TABLE `Reminders`
  MODIFY `reminder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `Schedules`
--
ALTER TABLE `Schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `Settings`
--
ALTER TABLE `Settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `TreatmentPlans`
--
ALTER TABLE `TreatmentPlans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `Visits`
--
ALTER TABLE `Visits`
  MODIFY `visit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Mobile_Numbers`
--
ALTER TABLE `Mobile_Numbers`
  ADD CONSTRAINT `Mobile_Numbers_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `Owners` (`owner_id`);

--
-- Constraints for table `Pets`
--
ALTER TABLE `Pets`
  ADD CONSTRAINT `Pets_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `Owners` (`owner_id`);

--
-- Constraints for table `Pet_Images`
--
ALTER TABLE `Pet_Images`
  ADD CONSTRAINT `Pet_Images_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `Pets` (`pet_id`);

--
-- Constraints for table `PlanSteps`
--
ALTER TABLE `PlanSteps`
  ADD CONSTRAINT `PlanSteps_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `TreatmentPlans` (`plan_id`);

--
-- Constraints for table `Reminders`
--
ALTER TABLE `Reminders`
  ADD CONSTRAINT `Reminders_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `Schedules` (`schedule_id`);

--
-- Constraints for table `Schedules`
--
ALTER TABLE `Schedules`
  ADD CONSTRAINT `Schedules_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `Pets` (`pet_id`),
  ADD CONSTRAINT `Schedules_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `TreatmentPlans` (`plan_id`);

--
-- Constraints for table `Visits`
--
ALTER TABLE `Visits`
  ADD CONSTRAINT `Visits_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `Pets` (`pet_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
