-- Author: Russell Elliott
-- Date Created: 2024/03/20
-- This script initializes the database for a user account system

-- Create the database if it does not already exist
CREATE DATABASE IF NOT EXISTS `user_accounts`;

-- Select the newly created database
USE `user_accounts`;

-- Create the 'users' table with standard fields
-- The table includes fields for user details and authentication
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `mac` VARCHAR(100) DEFAULT NULL,
    `ip` VARCHAR(15) DEFAULT NULL,
    `first_name` VARCHAR(50) DEFAULT NULL,
    `last_name` VARCHAR(50) DEFAULT NULL,
    `date_of_birth` DATE DEFAULT NULL,
    `last_login` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `role` ENUM('admin', 'user', 'guest') DEFAULT 'user',
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Note:
-- The `password` field should be long enough to store a hash (e.g., bcrypt)
-- Adjust the character sets and storage engines (e.g., InnoDB) as needed
