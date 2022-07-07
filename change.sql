ALTER TABLE `tbl_features` ADD `sub_heading` VARCHAR(100) NULL DEFAULT NULL AFTER `heading`, ADD `feature_name` VARCHAR(100) NULL DEFAULT NULL AFTER `sub_heading`;
ALTER TABLE `tbl_team` CHANGE `content` `sub_heading` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;
ALTER TABLE `tbl_companies` ADD `sub_heading` LONGTEXT NULL DEFAULT NULL AFTER `heading`, ADD `name` VARCHAR(100) NULL DEFAULT NULL AFTER `sub_heading`;

ALTER TABLE `tbl_services` ADD `sub_heading` LONGTEXT NULL DEFAULT NULL AFTER `heading`, ADD `name` LONGTEXT NULL DEFAULT NULL AFTER `sub_heading`;


ALTER TABLE `tbl_services` ADD `fk_chid_menu_id` INT NULL DEFAULT NULL AFTER `fk_sub_menu_id`;

ALTER TABLE `tbl_menu` ADD `function_name` VARCHAR(100) NULL DEFAULT NULL AFTER `menu`;

ALTER TABLE `tbl_sub_menu` ADD `function_name` VARCHAR(100) NULL DEFAULT NULL AFTER `sub_menu_name`;
ALTER TABLE `tbl_child_menu` ADD `function_name` VARCHAR(100) NOT NULL AFTER `child_menu_name`;



-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2022 at 07:26 AM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `egtech`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_contact_details`
--

CREATE TABLE `tbl_user_contact_details` (
  `id` int(11) NOT NULL,
  `fk_lang_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `note` mediumtext DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_user_contact_details`
--
ALTER TABLE `tbl_user_contact_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `fk_lang_id` (`fk_lang_id`),
  ADD KEY `name` (`name`),
  ADD KEY `email` (`email`),
  ADD KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_user_contact_details`
--
ALTER TABLE `tbl_user_contact_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


