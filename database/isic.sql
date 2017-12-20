-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 20 дек 2017 в 13:51
-- Версия на сървъра: 5.7.20-0ubuntu0.16.04.1
-- PHP Version: 7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `isic3`
--

-- --------------------------------------------------------

--
-- Структура на таблица `app_users`
--

CREATE TABLE `app_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `roles` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Схема на данните от таблица `app_users`
--

INSERT INTO `app_users` (`id`, `username`, `password`, `email`, `is_active`, `roles`) VALUES
(3, 'test', '$2y$13$tD9nMKAm5UJn/SzDUJnAB.ObU6LOfOCmAn5lw27RlQxdmOQLElelq', 'mpenelova@ucc.uni-sofia.bg', 1, 'a:0:{}');

-- --------------------------------------------------------

--
-- Структура на таблица `archive`
--

CREATE TABLE `archive` (
  `id` int(11) NOT NULL,
  `generate_date` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `archive_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура на таблица `card_type`
--

CREATE TABLE `card_type` (
  `id` int(11) NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Схема на данните от таблица `card_type`
--

INSERT INTO `card_type` (`id`, `name`) VALUES
(1, 'ISIC'),
(2, 'EYC'),
(3, 'СУ'),
(4, 'ITIC');

-- --------------------------------------------------------

--
-- Структура на таблица `isic`
--

CREATE TABLE `isic` (
  `id` int(11) NOT NULL,
  `idwkey_column` int(11) DEFAULT NULL,
  `idwfirst_name_bg` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idwfamily_name_bg` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idwfirst_name_en` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idwfamily_name_en` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idwfaculty_bg` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idwfaculty_en` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idwclass` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idwfaculty_number` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idwlid` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idwbar_code_int` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idwbar_code_field` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idwlidback` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idwbar_code_int_back` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idwbar_code_field_back` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idwphoto` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `egn` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthdate` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `specialty` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `chip_number` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_number` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `names` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT NULL,
  `import_date` datetime NOT NULL,
  `card_type` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура на таблица `permission`
--

CREATE TABLE `permission` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Схема на данните от таблица `permission`
--

INSERT INTO `permission` (`id`, `name`) VALUES
(1, 'user_registration'),
(2, 'edit_role'),
(3, 'login'),
(4, 'menu.html.twig'),
(5, 'list_users'),
(6, 'list_roles'),
(7, 'edit_user'),
(8, 'role_create'),
(9, 'isic_import'),
(10, 'generate_xml'),
(11, 'BCCResqueBundle'),
(12, 'BCCResqueBundle_homepage'),
(13, 'BCCResqueBundle_queue_show'),
(14, 'BCCResqueBundle_failed_list'),
(15, 'BCCResqueBundle_scheduled_list'),
(16, 'BCCResqueBundle_scheduled_timestamp'),
(17, 'search_xml');

-- --------------------------------------------------------

--
-- Структура на таблица `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Схема на данните от таблица `role`
--

INSERT INTO `role` (`id`, `name`) VALUES
(1, 'superadmin'),
(2, 'admin');

-- --------------------------------------------------------

--
-- Структура на таблица `role_permission`
--

CREATE TABLE `role_permission` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Схема на данните от таблица `role_permission`
--

INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17);

-- --------------------------------------------------------

--
-- Структура на таблица `susi`
--

CREATE TABLE `susi` (
  `id` int(11) NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `faculty` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `faculty_number` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `phone_number` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `address_city` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `address_street` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `gender_name` varchar(2) CHARACTER SET utf8 DEFAULT NULL,
  `egn` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `post_code` varchar(7) DEFAULT NULL,
  `birth_date` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура на таблица `users_roles`
--

CREATE TABLE `users_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Схема на данните от таблица `users_roles`
--

INSERT INTO `users_roles` (`user_id`, `role_id`) VALUES
(3, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_users`
--
ALTER TABLE `app_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `archive`
--
ALTER TABLE `archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `card_type`
--
ALTER TABLE `card_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `isic`
--
ALTER TABLE `isic`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5159519060ED558B` (`card_type`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `IDX_6F7DF886D60322AC` (`role_id`),
  ADD KEY `IDX_6F7DF886FED90CCA` (`permission_id`);

--
-- Indexes for table `susi`
--
ALTER TABLE `susi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_roles`
--
ALTER TABLE `users_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `IDX_51498A8EA76ED395` (`user_id`),
  ADD KEY `IDX_51498A8ED60322AC` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_users`
--
ALTER TABLE `app_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `archive`
--
ALTER TABLE `archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `card_type`
--
ALTER TABLE `card_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `isic`
--
ALTER TABLE `isic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `permission`
--
ALTER TABLE `permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `susi`
--
ALTER TABLE `susi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Ограничения за дъмпнати таблици
--

--
-- Ограничения за таблица `isic`
--
ALTER TABLE `isic`
  ADD CONSTRAINT `FK_5159519060ED558B` FOREIGN KEY (`card_type`) REFERENCES `card_type` (`id`);

--
-- Ограничения за таблица `role_permission`
--
ALTER TABLE `role_permission`
  ADD CONSTRAINT `FK_6F7DF886D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`),
  ADD CONSTRAINT `FK_6F7DF886FED90CCA` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`);

--
-- Ограничения за таблица `users_roles`
--
ALTER TABLE `users_roles`
  ADD CONSTRAINT `FK_51498A8EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `app_users` (`id`),
  ADD CONSTRAINT `FK_51498A8ED60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
