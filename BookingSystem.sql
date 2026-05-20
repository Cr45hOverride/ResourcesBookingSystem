-- BookingSystem.sql
-- Database initialization and seed script for Kismec Booking System

CREATE DATABASE IF NOT EXISTS `KismecBookingSystem` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `KismecBookingSystem`;

-- --------------------------------------------------------
-- 1. Create 'users' table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `role` ENUM('admin', 'user') DEFAULT 'user',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Create 'resources' table (Labs & Classrooms)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `resources` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `type` ENUM('lab', 'classroom') NOT NULL,
    `status` ENUM('active', 'maintenance') DEFAULT 'active',
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Create 'bookings' table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bookings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `resource_id` INT NOT NULL,
    `booking_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `purpose` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`resource_id`) REFERENCES `resources`(`id`) ON DELETE CASCADE,
    INDEX (`booking_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Seed default system users
-- --------------------------------------------------------
INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`) VALUES
(1, 'admin', '$2y$10$AXCS2gEePut6EN9GnL1lueGORMjKhCH/jjugq1D8vWJQ16zNDbZwu', 'System Administrator', 'admin@kismec.org.my', 'admin'),
(2, 'staff', '$2y$10$QqWdRrSZDEPAW.F46SJ15eErpr.X44DKrIvmcZDtAzrfBK0eEJgrO', 'Kismec Staff Member', 'staff@kismec.org.my', 'user')
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`);

-- --------------------------------------------------------
-- 5. Seed default resources (5 computer labs, 5 classrooms)
-- --------------------------------------------------------
INSERT INTO `resources` (`id`, `name`, `type`, `description`, `status`) VALUES
(1, 'Computer Lab 1', 'lab', 'High-performance workstations with CAD software.', 'active'),
(2, 'Computer Lab 2', 'lab', 'General purpose computer training lab.', 'active'),
(3, 'Computer Lab 3', 'lab', 'Programming and database software setup.', 'active'),
(4, 'Computer Lab 4', 'lab', 'Networking and security simulations lab.', 'active'),
(5, 'Computer Lab 5', 'lab', 'Advanced AI and systems engineering lab.', 'active'),
(6, 'Classroom A', 'classroom', 'Capacity 40, equipped with smart projector.', 'active'),
(7, 'Classroom B', 'classroom', 'Capacity 40, whiteboards and sound system.', 'active'),
(8, 'Classroom C', 'classroom', 'Capacity 30, flexible seating layout.', 'active'),
(9, 'Classroom D', 'classroom', 'Capacity 50, auditorial layout.', 'active'),
(10, 'Classroom E', 'classroom', 'Capacity 25, ideal for discussions & seminars.', 'active')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
