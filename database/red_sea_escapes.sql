-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2026 at 05:22 PM
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
-- Database: `red_sea_escapes`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activity_id` int(11) NOT NULL,
  `activity_name` varchar(100) NOT NULL,
  `category` enum('water','desert') NOT NULL,
  `description` text DEFAULT NULL,
  `price_per_person` decimal(10,2) NOT NULL,
  `min_age` int(11) NOT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `total_slots` int(11) NOT NULL DEFAULT 10,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`activity_id`, `activity_name`, `category`, `description`, `price_per_person`, `min_age`, `duration`, `total_slots`, `image_path`, `is_active`) VALUES
(1, 'Dive', 'water', 'Guided diving experience.', 450.00, 12, '2 hours', 10, 'images/Activites/Dive_main.jpg', 1),
(2, 'Snorkeling', 'water', 'Explore coral and marine life.', 250.00, 8, '1.5 hours', 15, 'images/Activites/Snorkeling_main.webp', 1),
(3, 'Sailing', 'water', 'Relaxing Red Sea sailing trip.', 300.00, 10, '2 hours', 12, 'images/Activites/Sailing_main.webp', 1),
(4, 'Kayaking', 'water', 'Kayaking activity near the coast.', 180.00, 10, '1 hour', 1, 'images/Activites/Kayaking_main.webp', 1),
(5, 'Hiking', 'desert', 'Guided desert hiking experience.', 220.00, 12, '2 hours', 10, 'images/Activites/Hiking_main.webp', 1),
(6, 'E-Biking', 'desert', 'Electric biking desert experience.', 350.00, 16, '1.5 hours', 8, 'images/Activites/E-Biking_main.webp', 1);

-- --------------------------------------------------------

--
-- Table structure for table `activity_time_slots`
--

CREATE TABLE `activity_time_slots` (
  `slot_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `slot_time` time NOT NULL,
  `max_people` int(11) NOT NULL DEFAULT 10,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_time_slots`
--

INSERT INTO `activity_time_slots` (`slot_id`, `activity_id`, `slot_time`, `max_people`, `is_active`, `created_at`) VALUES
(1, 1, '09:00:00', 3, 1, '2026-05-21 14:56:22'),
(2, 1, '11:00:00', 10, 1, '2026-05-21 14:56:22'),
(3, 1, '13:00:00', 10, 1, '2026-05-21 14:56:22'),
(4, 1, '15:00:00', 10, 1, '2026-05-21 14:56:22'),
(5, 1, '17:00:00', 10, 1, '2026-05-21 14:56:22'),
(6, 2, '09:00:00', 15, 1, '2026-05-21 14:56:22'),
(7, 2, '11:00:00', 15, 1, '2026-05-21 14:56:22'),
(8, 2, '13:00:00', 15, 1, '2026-05-21 14:56:22'),
(9, 2, '15:00:00', 15, 1, '2026-05-21 14:56:22'),
(10, 2, '17:00:00', 15, 1, '2026-05-21 14:56:22'),
(11, 3, '09:00:00', 12, 1, '2026-05-21 14:56:22'),
(12, 3, '11:00:00', 12, 1, '2026-05-21 14:56:22'),
(13, 3, '13:00:00', 12, 1, '2026-05-21 14:56:22'),
(14, 3, '15:00:00', 12, 1, '2026-05-21 14:56:22'),
(15, 3, '17:00:00', 12, 1, '2026-05-21 14:56:22'),
(16, 4, '09:00:00', 12, 1, '2026-05-21 14:56:22'),
(17, 4, '11:00:00', 12, 1, '2026-05-21 14:56:22'),
(18, 4, '13:00:00', 12, 1, '2026-05-21 14:56:22'),
(19, 4, '15:00:00', 12, 1, '2026-05-21 14:56:22'),
(20, 4, '17:00:00', 12, 1, '2026-05-21 14:56:22'),
(21, 5, '07:00:00', 10, 1, '2026-05-21 14:56:22'),
(22, 5, '09:00:00', 10, 1, '2026-05-21 14:56:22'),
(23, 5, '16:00:00', 10, 1, '2026-05-21 14:56:22'),
(24, 6, '07:00:00', 8, 1, '2026-05-21 14:56:22'),
(25, 6, '09:00:00', 8, 1, '2026-05-21 14:56:22'),
(26, 6, '16:00:00', 8, 1, '2026-05-21 14:56:22'),
(27, 5, '13:00:00', 5, 1, '2026-05-21 15:09:41');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_type` enum('room','activity') NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `time_slot` time DEFAULT NULL,
  `adults` int(11) DEFAULT 1,
  `children` int(11) DEFAULT 0,
  `participants` int(11) DEFAULT 1,
  `user_age` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'confirmed',
  `cancel_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `booking_type`, `room_id`, `activity_id`, `start_date`, `end_date`, `time_slot`, `adults`, `children`, `participants`, `user_age`, `total_price`, `status`, `cancel_reason`, `created_at`) VALUES
(1, 5, 'room', 2, NULL, '2026-05-20', '2026-05-27', NULL, 2, 0, 1, NULL, 17500.00, 'confirmed', NULL, '2026-05-21 14:23:51'),
(2, 6, 'room', 2, NULL, '2026-05-22', '2026-05-24', NULL, 1, 0, 1, NULL, 5000.00, 'confirmed', NULL, '2026-05-21 14:28:30'),
(3, 5, 'activity', NULL, 1, '2026-05-20', '2026-05-20', '09:00:00', 1, 0, 2, 13, 900.00, 'confirmed', NULL, '2026-05-21 14:32:47'),
(4, 6, 'activity', NULL, 1, '2026-05-20', '2026-05-20', '09:00:00', 1, 0, 2, 34, 900.00, 'confirmed', NULL, '2026-05-21 14:34:08'),
(5, 6, 'activity', NULL, 1, '2026-05-20', '2026-05-20', '09:00:00', 1, 0, 2, 54, 900.00, 'confirmed', NULL, '2026-05-21 14:34:32'),
(6, 1, 'activity', NULL, 4, '2026-05-21', '2026-05-21', '09:00:00', 1, 0, 1, 22, 180.00, 'confirmed', NULL, '2026-05-21 14:36:04'),
(7, 1, 'activity', NULL, 4, '2026-05-19', '2026-05-19', '11:00:00', 1, 0, 1, 22, 180.00, 'confirmed', NULL, '2026-05-21 14:36:34'),
(12, 5, 'activity', NULL, 1, '2026-05-21', '2026-05-21', '09:00:00', 1, 0, 2, 21, 900.00, 'confirmed', NULL, '2026-05-21 14:59:12'),
(13, 6, 'activity', NULL, 2, '2026-05-26', '2026-05-26', '09:00:00', 1, 0, 1, 21, 250.00, 'confirmed', NULL, '2026-05-21 15:02:25');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(150) NOT NULL,
  `preference` varchar(50) DEFAULT NULL,
  `rating` varchar(30) NOT NULL,
  `services` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resorts`
--

CREATE TABLE `resorts` (
  `resort_id` int(11) NOT NULL,
  `resort_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resorts`
--

INSERT INTO `resorts` (`resort_id`, `resort_name`, `description`, `location`, `image_path`, `is_active`) VALUES
(1, 'Shebara', 'Luxury overwater villas with sea views.', 'Red Sea', 'images/shebara_main.jpg', 1),
(2, 'Dunes', 'Desert wellness resort with calm mountain views.', 'Red Sea Desert', 'images/dunes_main.webp', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `resort_id` int(11) NOT NULL,
  `room_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `max_adults` int(11) NOT NULL,
  `max_children` int(11) NOT NULL DEFAULT 0,
  `total_rooms` int(11) NOT NULL DEFAULT 1,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `resort_id`, `room_name`, `description`, `price_per_night`, `max_adults`, `max_children`, `total_rooms`, `image_path`, `is_active`) VALUES
(1, 1, 'Four Bedroom Beach Royal Villa', 'Spacious villa for families and groups.', 6200.00, 8, 4, 2, 'images/Shebara/FourBedroomBeachRoyalVilla.jpg', 1),
(2, 1, 'One Bedroom Villa', 'Private villa for couples.', 2500.00, 2, 0, 2, 'images/Shebara/OneBedroom.jpg', 1),
(3, 1, 'Three Bedroom Beach Crown Villa', 'Beach villa with private pool.', 4800.00, 6, 3, 3, 'images/Shebara/ThreeBedroomBeachCrownVilla.webp', 1),
(4, 2, 'Wadi King Room', 'Comfortable desert room with valley view.', 1800.00, 2, 0, 8, 'images/Dunes/1_Wadi_King_Room_bedroom_view.webp', 1),
(5, 2, 'Two Bedroom Sunset Pool Villa', 'Private pool villa with sunset view.', 3900.00, 4, 2, 5, 'images/Dunes/2_Two_Bedroom_Sunset_Pool_Villa_exterior.webp', 1);

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `upload_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`upload_id`, `user_id`, `file_name`, `file_path`, `file_type`, `file_size`, `uploaded_at`) VALUES
(3, 5, 'profile_5_1779370813.jpg', 'uploads/profiles/profile_5_1779370813.jpg', 'jpg', 215928, '2026-05-21 13:40:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `profile_photo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password_hash`, `phone`, `role`, `profile_photo`, `is_active`, `created_at`) VALUES
(1, 'Admin', 'admin@redsea.com', '$2y$12$rYLI1UDgGohBzjZhDTH3/.P4BSMQzKnvNF.wTqxQeCIVLuPY4I74.', NULL, 'admin', NULL, 1, '2026-05-21 12:19:10'),
(5, 'norah', 'Norahalhazab@gmail.com', '$2y$10$FbCGc.fuUudpNdzAmWuyLeI0fgaU0KKaZYjw4JBfnlYiyW1dxUW3K', '+966503879832', 'user', 'uploads/profiles/profile_5_1779370813.jpg', 1, '2026-05-21 13:39:16'),
(6, 'shahad', 'savshahad10@gmail.com', '$2y$10$oE.TU9aY6U/sCE7bLDasruHgUJuKA6UlifBev2L4YLKr25Gw.AZqO', NULL, 'user', NULL, 1, '2026-05-21 14:27:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `activity_time_slots`
--
ALTER TABLE `activity_time_slots`
  ADD PRIMARY KEY (`slot_id`),
  ADD UNIQUE KEY `unique_activity_time` (`activity_id`,`slot_time`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `activity_id` (`activity_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `resorts`
--
ALTER TABLE `resorts`
  ADD PRIMARY KEY (`resort_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `resort_id` (`resort_id`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`upload_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `activity_time_slots`
--
ALTER TABLE `activity_time_slots`
  MODIFY `slot_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resorts`
--
ALTER TABLE `resorts`
  MODIFY `resort_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `upload_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_time_slots`
--
ALTER TABLE `activity_time_slots`
  ADD CONSTRAINT `activity_time_slots_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON DELETE SET NULL;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`resort_id`) REFERENCES `resorts` (`resort_id`) ON DELETE CASCADE;

--
-- Constraints for table `uploads`
--
ALTER TABLE `uploads`
  ADD CONSTRAINT `uploads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
