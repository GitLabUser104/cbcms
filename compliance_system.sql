-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2024 at 06:09 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `compliance_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `user_id`, `action`, `details`, `action_time`) VALUES
(1, 1, 'login', 'Admin user logged in', '2024-10-30 04:21:20'),
(2, 2, 'add', 'User added a new document', '2024-10-30 04:21:20');

-- --------------------------------------------------------

--
-- Table structure for table `compliance_tasks`
--

CREATE TABLE `compliance_tasks` (
  `task_id` int(11) NOT NULL,
  `task_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('pending','in-progress','completed') DEFAULT 'pending',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `compliance_tasks`
--

INSERT INTO `compliance_tasks` (`task_id`, `task_name`, `description`, `assigned_to`, `due_date`, `status`, `priority`, `created_at`) VALUES
(1, 'Task 1', 'Description for task 1', 1, '2024-12-31', 'pending', 'high', '2024-10-30 04:21:20'),
(2, 'Task r', 'Description for task 2', 2, '2024-11-30', 'in-progress', 'medium', '2024-10-30 04:21:20');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `document_id` int(11) NOT NULL,
  `document_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`document_id`, `document_name`, `description`, `file_path`, `upload_date`) VALUES
(1, 'Compliance Document 1', 'Description of document 1', 'uploads/doc1.pdf', '2024-10-30 04:21:20'),
(2, 'Compliance Document 2', 'Description of document 2', 'uploads/doc2.pdf', '2024-10-30 04:21:20'),
(4, 'cv', 'v', 'uploads/Development of a Web-Based Firewall Management System Using Shell Scripting_ Enhancing Network Security through Automated and User-Friendly Solutions - Karan Sinha.docx', '2024-10-30 04:57:21');

-- --------------------------------------------------------

--
-- Table structure for table `issue_tracker`
--

CREATE TABLE `issue_tracker` (
  `issue_id` int(11) NOT NULL,
  `issue_description` text NOT NULL,
  `status` enum('open','in-progress','resolved') DEFAULT 'open',
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issue_tracker`
--

INSERT INTO `issue_tracker` (`issue_id`, `issue_description`, `status`, `assigned_to`, `created_at`) VALUES
(1, 'Database connection error', 'open', 1, '2024-10-30 04:21:20'),
(2, 'File upload issue', 'in-progress', 2, '2024-10-30 04:21:20');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `notification_type` enum('info','warning','alert') DEFAULT 'info',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `message`, `notification_type`, `created_at`) VALUES
(1, 'System maintenance scheduled for 10 PM', 'info', '2024-10-30 04:21:20'),
(2, 'Compliance deadline approaching', 'warning', '2024-10-30 04:21:20'),
(3, 'ff', 'info', '2024-10-30 04:33:22');

-- --------------------------------------------------------

--
-- Table structure for table `policies`
--

CREATE TABLE `policies` (
  `policy_id` int(11) NOT NULL,
  `policy_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `policies`
--

INSERT INTO `policies` (`policy_id`, `policy_name`, `description`, `created_at`) VALUES
(1, 'Data Protection Polic', 'Policy on data protection and security', '2024-10-30 04:21:20'),
(2, 'User Access Policy', 'Policy on user access levels and permissions', '2024-10-30 04:21:20');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_id` int(11) NOT NULL,
  `setting_name` varchar(100) NOT NULL,
  `setting_value` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_id`, `setting_name`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'site_maintenance', 'false', 'Enable or disable site maintenance mode', '2024-10-30 04:21:20'),
(2, 'max_login_attempts', '5', 'Maximum allowed login attempts before lockout', '2024-10-30 04:21:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@example.com', 'admin', '2024-10-30 04:21:20'),
(2, 'test_user', '$2y$10$samplehashforpassword', 'user@example.com', 'user', '2024-10-30 04:21:20'),
(3, 'raju', '202cb962ac59075b964b07152d234b70', 'r@gmail.com', 'user', '2024-10-30 05:03:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `compliance_tasks`
--
ALTER TABLE `compliance_tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`document_id`);

--
-- Indexes for table `issue_tracker`
--
ALTER TABLE `issue_tracker`
  ADD PRIMARY KEY (`issue_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `policies`
--
ALTER TABLE `policies`
  ADD PRIMARY KEY (`policy_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `compliance_tasks`
--
ALTER TABLE `compliance_tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `issue_tracker`
--
ALTER TABLE `issue_tracker`
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `policies`
--
ALTER TABLE `policies`
  MODIFY `policy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `compliance_tasks`
--
ALTER TABLE `compliance_tasks`
  ADD CONSTRAINT `compliance_tasks_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `issue_tracker`
--
ALTER TABLE `issue_tracker`
  ADD CONSTRAINT `issue_tracker_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
