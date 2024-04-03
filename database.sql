-- Author: Russell Elliott
-- Date Created: 2024/03/20
-- This script initializes the database for a user account system

-- Create the database if it does not already exist
CREATE DATABASE IF NOT EXISTS `user_accounts`;

-- Select the newly created database
USE `user_accounts`;

-- Create the 'user_accounts' table with standard fields
-- The table includes fields for user details and authentication
CREATE TABLE IF NOT EXISTS `user_accounts` (
	`userid` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(20) NOT NULL,
	`email` VARCHAR(50) NOT NULL,
	`password` VARCHAR(255) NOT NULL,
	`active` TINYINT(1) NOT NULL DEFAULT 1,
	`banned` TINYINT(1) NOT NULL DEFAULT 0,
	`mac` VARCHAR(100) DEFAULT NULL,
	`ip` VARCHAR(15) DEFAULT NULL,
	`last_login` DATETIME DEFAULT NULL,
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`role` ENUM('admin', 'user', 'guest') DEFAULT 'user',
	PRIMARY KEY (`userid`),
	UNIQUE KEY `username` (`username`),
	UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create the 'user_profiles' table with standard fields
-- The table includes fields for user profile info and content
CREATE TABLE IF NOT EXISTS `user_profiles` (
	`userid` BIGINT(20) NOT NULL,
	`first_name` VARCHAR(25) DEFAULT NULL,
	`last_name` VARCHAR(25) DEFAULT NULL,
	`date_of_birth` DATE DEFAULT NULL,
	`gender` ENUM('Male', 'Female', 'Other') DEFAULT NULL,
	`profile_picture` VARCHAR(50) DEFAULT NULL,
	`street_address` VARCHAR(50) DEFAULT NULL,
	`city` VARCHAR(50) DEFAULT NULL,
	`state` VARCHAR(2) DEFAULT NULL,
	`country` VARCHAR(25) DEFAULT NULL,
	`zip_code` VARCHAR(20) DEFAULT NULL,
	`phone_number` VARCHAR(20) DEFAULT NULL,
	`about_me` TEXT DEFAULT NULL,
	PRIMARY KEY (`userid`),
	FOREIGN KEY (`userid`) REFERENCES `user_accounts` (`userid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create the 'forgot_passwords' table with standard fields
-- The table includes fields for users that forgot password
CREATE TABLE IF NOT EXISTS `forgot_passwords` (
	`userid` BIGINT(20) NOT NULL,
	`email` VARCHAR(50) NOT NULL,
	`code` VARCHAR(6) NOT NULL,
	PRIMARY KEY (`userid`),
	FOREIGN KEY (`userid`) REFERENCES `user_accounts` (`userid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create the 'audit_logs' table with standard fields
-- The table includes fields for security and analytics
CREATE TABLE IF NOT EXISTS `audit_logs` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`userid` BIGINT(20) NOT NULL,
	`action` VARCHAR(50) NOT NULL,
	`description` TEXT DEFAULT NULL,
	`ip` VARCHAR(15) DEFAULT NULL,
	`timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`userid`) REFERENCES `user_accounts` (`userid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Note:
-- The `password` field should be long enough to store a hash (e.g., bcrypt)
-- Adjust the character sets and storage engines (e.g., InnoDB) as needed
