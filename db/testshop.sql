-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Сен 30 2019 г., 18:47
-- Версия сервера: 10.1.38-MariaDB
-- Версия PHP: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `testshop`
--

-- --------------------------------------------------------

--
-- Структура таблицы `banned_users`
--

CREATE TABLE `banned_users` (
  `id` int(11) NOT NULL,
  `banned_id` int(11) NOT NULL,
  `banned_by` int(11) NOT NULL,
  `ban_time` datetime NOT NULL,
  `ban_description` varchar(30) COLLATE utf8_bin NOT NULL,
  `ban_active` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `banned_users`
--

INSERT INTO `banned_users` (`id`, `banned_id`, `banned_by`, `ban_time`, `ban_description`, `ban_active`) VALUES
(6, 10, 1, '2019-06-19 20:03:53', 'Black points', 0),
(17, 2, 1, '2019-07-26 21:56:05', 'Manually', 0),
(18, 2, 0, '2019-07-27 15:31:07', 'Black points', 0),
(19, 2, 0, '2019-07-27 15:33:24', 'Black points', 0),
(20, 2, 0, '2019-07-27 15:34:22', 'Black points', 0),
(21, 2, 1, '2019-08-02 15:40:51', 'Manually', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `black_points`
--

CREATE TABLE `black_points` (
  `bp_id` int(11) NOT NULL,
  `bp_userid` int(11) NOT NULL,
  `bp_description` varchar(30) COLLATE utf8_bin NOT NULL,
  `bp_date` datetime NOT NULL,
  `bp_givenby` int(11) NOT NULL,
  `bp_active` int(11) NOT NULL DEFAULT '1' COMMENT '1-active; 0-inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `black_points`
--

INSERT INTO `black_points` (`bp_id`, `bp_userid`, `bp_description`, `bp_date`, `bp_givenby`, `bp_active`) VALUES
(1, 2, 'Item check', '2019-07-27 19:19:34', 1, 1),
(2, 2, 'Item check', '2019-07-27 15:19:34', 1, 0),
(3, 2, 'Item check', '2019-07-27 15:19:34', 1, 0),
(4, 2, 'Item check', '2019-07-27 15:19:34', 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `buy_events`
--

CREATE TABLE `buy_events` (
  `id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `seller_multiplier` float NOT NULL DEFAULT '0.7',
  `seller_ref_multiplier` float NOT NULL DEFAULT '0.03',
  `buy_time` datetime NOT NULL,
  `rated` int(11) NOT NULL DEFAULT '0',
  `confirmedby_withdrawed` int(11) NOT NULL DEFAULT '0' COMMENT '1 - withdrawed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `buy_events`
--

INSERT INTO `buy_events` (`id`, `buyer_id`, `seller_id`, `item_id`, `price`, `seller_multiplier`, `seller_ref_multiplier`, `buy_time`, `rated`, `confirmedby_withdrawed`) VALUES
(5, 1, 1, 8, 110, 0.7, 0, '2019-05-03 21:44:46', 1, 0),
(6, 1, 1, 29, 100, 0.7, 0, '2019-06-02 15:08:29', 1, 0),
(12, 6, 1, 29, 100, 0.7, 0, '2019-06-11 21:26:21', 1, 0),
(14, 6, 1, 18, 100, 0.7, 0, '2019-06-11 21:28:38', 0, 0),
(15, 6, 1, 13, 100, 0.7, 0, '2019-06-11 21:30:20', 0, 0),
(17, 1, 6, 23, 31, 0.7, 0, '2019-06-12 20:49:29', 0, 0),
(24, 2, 6, 49, 110, 0.7, 0, '2019-06-25 22:11:45', 0, 0),
(25, 1, 1, 28, 144, 0.7, 0, '2019-06-28 19:57:30', 1, 0),
(26, 61, 1, 51, 111, 0.7, 0, '2019-07-18 19:34:46', 0, 0),
(27, 61, 61, 107, 100, 0.7, 0, '2019-07-18 19:42:38', 0, 0),
(28, 62, 61, 107, 100, 0.7, 0, '2019-07-23 15:07:06', 0, 0),
(29, 62, 1, 29, 100, 0.7, 0, '2019-07-23 18:29:21', 0, 0),
(30, 62, 1, 18, 100, 0.7, 0, '2019-07-23 18:35:01', 0, 0),
(31, 62, 1, 17, 100, 0.7, 0.05, '2019-07-23 20:53:55', 0, 0),
(32, 61, 1, 29, 100, 0.7, 0.03, '2019-07-27 19:22:32', 0, 0),
(33, 1, 1, 108, 100, 0.7, 0.03, '2019-07-28 15:26:25', 0, 0),
(34, 1, 1, 53, 111, 0.7, 0.03, '2019-07-28 16:16:13', 0, 0),
(35, 1, 1, 37, 111, 0.7, 0.03, '2019-08-02 19:30:18', 0, 0),
(36, 63, 63, 117, 30, 0.7, 0.03, '2019-09-04 10:52:45', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `city`
--

CREATE TABLE `city` (
  `city_id` int(11) NOT NULL,
  `city_name` varchar(30) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `city`
--

INSERT INTO `city` (`city_id`, `city_name`) VALUES
(1, 'Praha'),
(2, 'Benešov'),
(18, 'Soběhrdy');

-- --------------------------------------------------------

--
-- Структура таблицы `codes`
--

CREATE TABLE `codes` (
  `code_id` int(11) NOT NULL,
  `code_type` varchar(10) COLLATE utf8_bin NOT NULL,
  `code` varchar(10) COLLATE utf8_bin NOT NULL,
  `code_value` int(11) NOT NULL COMMENT 'Used for balance code',
  `code_generatedby` int(11) NOT NULL,
  `code_created` datetime NOT NULL,
  `code_expiration` datetime NOT NULL,
  `code_used` int(11) NOT NULL DEFAULT '0',
  `code_activated` datetime NOT NULL,
  `code_activatedby` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `codes`
--

INSERT INTO `codes` (`code_id`, `code_type`, `code`, `code_value`, `code_generatedby`, `code_created`, `code_expiration`, `code_used`, `code_activated`, `code_activatedby`) VALUES
(1, 'balance', '123456789a', 1000, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
(7, 'balance', '8NYDNF7XN5', 100, 1, '2019-06-07 22:21:11', '2019-07-07 22:21:11', 1, '2019-06-08 19:29:33', 1),
(8, 'vip', 'P6ZDNCSBVV', 1, 6, '2019-06-07 22:29:03', '2019-06-13 22:29:03', 0, '2019-06-13 21:51:04', 1),
(10, 'vip', '9731YX8JNZ', 11, 1, '2019-06-08 21:48:18', '2019-07-08 21:48:18', 1, '2019-06-09 21:21:32', 1),
(12, 'balance', '0SMV5SGOHX', 500, 1, '2019-06-28 21:13:09', '2019-07-03 21:13:09', 1, '2019-06-28 21:16:41', 57),
(13, 'vip', '7BWO1YLX6Z', 1, 1, '2019-07-09 19:38:03', '2019-07-14 19:38:03', 1, '2019-07-09 19:38:53', 57),
(15, 'balance', 'ZGRK1MOAG3', 1000, 1, '2019-07-18 19:33:36', '2019-07-23 19:33:36', 1, '2019-07-18 19:34:31', 61),
(16, 'vip', 'N1ME6GLDCL', 1, 1, '2019-08-31 16:09:40', '2019-09-05 16:09:40', 1, '2019-08-31 16:09:52', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `MAINTAIN_MODE` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `config`
--

INSERT INTO `config` (`id`, `MAINTAIN_MODE`) VALUES
(1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `district`
--

CREATE TABLE `district` (
  `district_id` int(11) NOT NULL,
  `district_name` varchar(30) COLLATE utf8_bin NOT NULL,
  `city_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `district`
--

INSERT INTO `district` (`district_id`, `district_name`, `city_id`) VALUES
(1, 'Praha 3', 1),
(2, 'Červené Vršky', 2),
(3, 'Praha 1', 1),
(4, 'Praha 2', 1),
(5, 'Praha 4', 1),
(6, 'Praha 5', 1),
(7, 'Praha 6', 1),
(8, 'Praha 7', 1),
(9, 'Praha 8', 1),
(10, 'Praha 9', 1),
(11, 'Praha 10', 1),
(12, 'Praha 11', 1),
(13, 'Praha 12', 1),
(14, 'Praha 13', 1),
(16, 'Praha 15', 1),
(17, 'Praha 16', 1),
(18, 'Praha 17', 1),
(19, 'Praha 18', 1),
(20, 'Praha 19', 1),
(21, 'Praha 20', 1),
(32, 'Soběhrdy - centrum', 18),
(33, 'Praha - Měcholupy', 1),
(34, 'Kos+čěk', 18);

-- --------------------------------------------------------

--
-- Структура таблицы `errors_log`
--

CREATE TABLE `errors_log` (
  `username` varchar(40) COLLATE utf8_bin NOT NULL,
  `user_errormsg` varchar(300) COLLATE utf8_bin NOT NULL,
  `system_errormsg` varchar(300) COLLATE utf8_bin NOT NULL,
  `event_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `errors_log`
--

INSERT INTO `errors_log` (`username`, `user_errormsg`, `system_errormsg`, `event_time`) VALUES
('admin', 'Some error occurred during the search of an image.', '', '2019-05-01 17:52:31'),
('admin', 'Some error occurred during the search of an image.', '', '2019-05-01 17:52:54'),
('admin', 'Some error occurred during the search of an image.', '', '2019-05-01 17:53:39'),
('admin', 'Some error occurred during the search of an image.', '', '2019-05-01 17:53:40'),
('admin', 'Some error occurred during the search of an image.', '', '2019-05-01 17:54:17'),
('admin', 'Some error occurred during the search of an image.', '', '2019-05-01 17:56:03'),
('admin', 'Some error occurred during the search of an image.', '', '2019-05-01 17:56:06'),
('admin', 'Some error occurred during the search of an image.', '', '2019-05-01 17:56:26'),
('admin', 'Given a wrong combination of email and hash.', '', '2019-05-01 20:07:41'),
('admin', 'You are already logged in!(user_message)', '', '2019-05-02 16:20:32'),
('admin', 'You are already logged in!(user_message)', '', '2019-05-02 16:20:44'),
('admin', 'You are already logged in!(user_message)', '', '2019-05-02 16:21:45'),
('admin', 'You have not enough permissions to see this page!', '', '2019-05-12 17:00:11'),
('admin', 'You have not enough permissions to see this page!', '', '2019-05-12 17:00:22'),
('admin', 'You have not enough permissions to see this page!', '', '2019-05-12 17:00:27'),
('admin', 'There was no amount of money recieved!', 'withdraw_action', '2019-05-12 18:28:41'),
('admin', 'There was no amount of money recieved!', 'withdraw_action', '2019-05-12 18:51:06'),
('', 'You have to login first!', '', '2019-05-12 21:24:13'),
('', 'You have to login first!', '', '2019-05-14 16:05:35'),
('', 'You have to login first!', '', '2019-05-14 21:34:54'),
('', 'You have to login first!', '', '2019-05-16 08:39:52'),
('admin', 'This user doesn\'t exist!', 'profile_show (wrong id)', '2019-05-19 18:26:15'),
('admin', 'This school doesn\'t exist!', 'school_info wrong school id', '2019-05-19 18:34:35'),
('admin', 'This school doesn\'t exist!', 'school_info wrong school id', '2019-05-19 18:34:43'),
('admin', 'This school doesn\'t exist!', 'school_info wrong school id', '2019-05-19 18:35:02'),
('admin', 'This school doesn\'t exist!', 'school_info wrong school id', '2019-05-19 18:35:11'),
('admin', 'This school doesn\'t exist!', 'school_info wrong school id', '2019-05-19 18:36:18'),
('admin', 'This school doesn\'t exist!', 'school_info wrong school id', '2019-05-19 18:36:31'),
('admin', 'This school doesn\'t exist!', 'school_info wrong school id', '2019-05-19 18:36:42'),
('admin', 'You have already set up your school', '', '2019-05-19 18:36:46'),
('admin', 'This school doesn\'t exist!', 'school_info wrong school id', '2019-05-19 18:44:51'),
('admin', 'You have already set up your school', '', '2019-05-19 18:44:58'),
('admin', 'This request doesn\'t exist!', 'validators_requests_action', '2019-05-19 19:26:55'),
('admin', 'This user doesn\'t exist!', 'profile_show (wrong id)', '2019-05-19 20:57:10'),
('admin', 'There was no id or message content recieved!', 'message_reply', '2019-05-20 20:35:51'),
('admin', 'There was no id or message content recieved!', 'message_reply', '2019-05-20 20:43:02'),
('', 'You have to login first!', '', '2019-05-20 20:44:15'),
('', 'You have to login first!', '', '2019-05-20 20:44:16'),
('', 'You have to login first!', '', '2019-05-20 20:44:16'),
('', 'You have to login first!', '', '2019-05-20 20:44:17'),
('', 'You have to login first!', '', '2019-05-20 20:44:17'),
('', 'You have to login first!', '', '2019-05-20 20:44:17'),
('', 'You have to login first!', '', '2019-05-20 20:44:17'),
('', 'You have to login first!', '', '2019-05-20 20:44:53'),
('admin', 'You are already logged in!(user_message)', '', '2019-05-20 20:51:44'),
('', 'You have to login first!', '', '2019-05-20 20:55:35'),
('', 'You have to login first!', '', '2019-05-20 20:55:36'),
('', 'You have to login first!', '', '2019-05-20 20:55:36'),
('', 'You have to login first!', '', '2019-05-20 20:55:36'),
('', 'You have to login first!', '', '2019-05-20 20:55:37'),
('', 'You have to login first!', '', '2019-05-20 20:55:37'),
('', 'You have to login first!', '', '2019-05-20 20:55:37'),
('', 'You have to login first!', '', '2019-05-20 20:57:29'),
('admin', 'Something went wrong during creating school change request.', '', '2019-05-20 21:18:20'),
('', 'You have to login first!', '', '2019-05-20 22:22:47'),
('kololo', 'You have not enough permissions to see this page!', '', '2019-05-21 14:53:23'),
('kololo', 'You have not enough permissions to see this page!', '', '2019-05-21 14:53:23'),
('admin', 'Wrong profile id recieved.', 'profile_show (0 id - System)', '2019-05-21 15:07:34'),
('admin', 'Wrong profile id recieved.', 'profile_show (0 id - System)', '2019-05-21 15:07:40'),
('admin', 'Wrong profile id recieved.', 'profile_show (0 id - System)', '2019-05-21 15:07:52'),
('admin', 'There was no id recieved!', 'admin_action_user', '2019-05-22 21:13:29'),
('admin', 'There was no id recieved!', 'admin_action_user', '2019-05-22 21:15:09'),
('admin', 'There was no id recieved!', 'admin_action_user', '2019-05-22 22:05:45'),
('', 'You have to login first!', '', '2019-05-23 21:51:48'),
('admin', 'You are already logged in!(user_message)', '', '2019-05-23 21:52:47'),
('admin', 'You are already logged in!(user_message)', '', '2019-05-23 21:54:02'),
('ukulele', 'You haven\'t set your school yet!', '', '2019-05-23 21:54:57'),
('admin', 'You have not enough permissions to see this page!', '', '2019-05-24 22:56:16'),
('admin', 'There was no id recieved!', 'admin_action_user', '2019-05-24 23:01:33'),
('admin', 'There was no id recieved!', 'admin_action_user', '2019-05-24 23:02:39'),
('admin', 'There was no id recieved!', 'admin_action_user', '2019-05-24 23:04:07'),
('admin', 'You are already logged in!(user_message)', '', '2019-05-25 18:46:48'),
('admin', 'There was no id recieved.', 'ticket_show', '2019-05-25 21:33:50'),
('admin', 'There was no id recieved.', 'ticket_show', '2019-05-25 21:34:05'),
('admin', 'There was no id recieved.', 'ticket_show', '2019-05-25 21:34:06'),
('admin', 'There was no id recieved.', 'ticket_show', '2019-05-25 21:34:11'),
('admin', 'There was no id recieved.', 'ticket_show', '2019-05-25 21:34:25'),
('admin', 'There was no id recieved.', 'ticket_show', '2019-05-25 21:34:33'),
('admin', 'There was no id recieved.', 'ticket_show', '2019-05-25 21:34:45'),
('admin', 'Wrong profile id recieved.', 'profile_show (0 id - System)', '2019-05-25 21:53:39'),
('admin', 'Some unexpected error occurred.', 'faq_action', '2019-05-26 18:03:38'),
('admin', 'Some unexpected error occurred.', 'faq_action', '2019-05-26 18:18:51'),
('admin', 'Some unexpected error occurred.', 'faq_action', '2019-05-26 18:19:14'),
('admin', 'Some unexpected error occurred.', 'faq_action', '2019-05-26 18:20:06'),
('admin', 'There was no id recieved.', 'ticket_show', '2019-05-26 18:22:41'),
('admin', 'There was no id recieved.', 'ticket_show', '2019-05-26 18:22:53'),
('admin', 'Some unexpected error occurred.', 'faq_action', '2019-05-26 18:58:33'),
('admin', 'Some unexpected error occurred.', 'ticket_action', '2019-05-26 19:31:00'),
('imagee', 'You have not enough permissions to see this page!', '', '2019-05-26 19:31:18'),
('admin', 'Some unexpected error occurred.', 'ticket_action', '2019-05-26 20:39:07'),
('kololo', 'Wrong profile id recieved.', 'profile_show (0 id - System)', '2019-05-27 21:36:51'),
('admin', 'You are already logged in!(user_message)', '', '2019-05-30 20:54:17'),
('admin', 'This user doesn\'t exist!', 'profile_show (wrong id)', '2019-06-01 15:29:37'),
('admin', 'This user doesn\'t exist!', 'profile_show (wrong id)', '2019-06-01 15:53:00'),
('admin', 'You are already logged in!(user_message)', '', '2019-06-01 21:18:35'),
('admin', 'Some unexpected error occurred.', 'ticket_action', '2019-06-01 21:20:44'),
('admin', 'You have already set up your school', '', '2019-06-02 21:06:30'),
('admin', 'Wrong profile id recieved.', 'profile_show (0 id - System)', '2019-06-04 20:45:08'),
('admin', 'You have not enough permissions to see this page!', '', '2019-06-05 16:23:20'),
('admin', 'Wrong profile id recieved.', 'profile_show (0 id - System)', '2019-06-05 21:26:51'),
('admin', 'There was no id recieved!', 'admin_action_user', '2019-06-07 22:16:11'),
('admin', 'There were not enough parameters recieved!', 'admin_codes_action', '2019-06-08 16:53:50'),
('admin', 'There were not enough parameters recieved!', 'admin_codes_action', '2019-06-08 16:54:23'),
('', 'You are banned! <a href=\'signout.php\'>Sign out</a>', '', '2019-06-10 21:11:32'),
('admin', 'You can remove only your messages!', '', '2019-06-10 21:34:06'),
('admin', 'You can remove only your messages!', '', '2019-06-10 21:34:14'),
('admin', 'You can remove only your messages!', '', '2019-06-10 21:34:20'),
('', 'You are banned! <a href=\'signout.php\'>Sign out</a>', '', '2019-06-12 21:41:51'),
('', 'You are banned! <a href=\'signout.php\'>Sign out</a>', '', '2019-06-13 19:10:40'),
('', 'You are banned! <a href=\'signout.php\'>Sign out</a>', '', '2019-06-13 19:11:59'),
('', 'You are banned! <a href=\'signout.php\'>Sign out</a>', '', '2019-06-13 19:12:08'),
('', 'You have to login first!', '', '2019-06-13 19:12:31'),
('admin', 'There weren\'t enough parameters recieved.', 'profile_settings_action', '2019-06-13 21:20:10'),
('admin', 'There weren\'t enough parameters recieved.', 'profile_settings_action', '2019-06-13 21:34:56'),
('', 'You have to login first!', '', '2019-06-13 21:53:47'),
('admin', 'There were not enough parameters recieved!', 'admin_codes_action', '2019-06-14 22:18:50'),
('admin', 'You are already logged in!(user_message)', '', '2019-06-15 16:50:54'),
('', 'You have to login first!', '', '2019-06-15 19:55:11'),
('', 'You have to login first!', '', '2019-06-15 20:07:51'),
('', 'You have to login first!', '', '2019-06-15 20:08:00'),
('admin', 'You already have set up your profile! To edit your profile you can go to settings', '', '2019-06-16 20:53:04'),
('admin', 'Not enought params recieved.', 'shop_action_admin', '2019-06-17 20:59:02'),
('admin', 'Not enought params recieved.', 'shop_action_admin', '2019-06-17 20:59:18'),
('admin', 'Not enought params recieved.', 'shop_action_admin', '2019-06-17 20:59:22'),
('admin', 'Not enought params recieved.', 'shop_action_admin', '2019-06-18 20:06:50'),
('admin', 'Some unexpected error occurred.', 'ticket_action', '2019-06-20 16:37:55'),
('admin', 'Some unexpected error occurred.', 'ticket_action', '2019-06-20 16:38:46'),
('admin', 'Some unexpected error occurred.', 'ticket_action', '2019-06-20 16:39:22'),
('admin', 'Wrong profile id recieved.', 'profile_show (0 id - System)', '2019-06-20 19:02:17'),
('admin', 'There was no id(school_check) recieved!', '', '2019-06-20 19:42:50'),
('admin', 'There was no id(school_check) recieved!', '', '2019-06-20 19:45:32'),
('tester', 'You have not enough permissions to see this page!', '', '2019-06-20 20:23:44'),
('admin', 'This school doesn\'t exist!', 'school_info (wrong school_id)', '2019-06-21 08:13:03'),
('admin', 'This school doesn\'t exist!', 'school_info (wrong school_id)', '2019-06-21 20:17:30'),
('admin', 'Some problem occured during info select.', '', '2019-06-21 21:25:07'),
('kololo', 'This user doesn\'t exist!', 'profile_show (wrong id)', '2019-06-25 22:05:12'),
('imagee', 'You don\'t have access to item from this school!', 'item_buy', '2019-06-25 22:11:27'),
('', 'Something went wrong during account validation!', '', '2019-06-26 19:51:35'),
('', 'Given a wrong combination of email and hash.', '', '2019-06-26 19:52:18'),
('user0', 'You already have set up your profile! To edit your profile you can go to settings', '', '2019-06-26 20:19:14'),
('admin', 'Wrong profile id recieved.', 'profile_show (0 id - System)', '2019-06-26 21:39:12'),
('tester', 'Wrong profile id recieved.', 'profile_show (0 id - System)', '2019-06-26 21:40:42'),
('admin', 'This user doesn\'t exist!', 'profile_show (wrong id)', '2019-06-26 22:04:38'),
('admin', 'This user doesn\'t exist!', 'profile_show (wrong id)', '2019-06-27 08:29:36'),
('admin', 'This user doesn\'t exist!', 'profile_show (wrong id)', '2019-06-27 08:29:41'),
('admin', 'This user doesn\'t exist!', 'profile_show (wrong id)', '2019-06-27 08:29:42'),
('admin', 'This user doesn\'t exist!', 'profile_show (wrong id)', '2019-06-27 08:29:44'),
('admin', 'This user doesn\'t exist!', 'profile_show (wrong id)', '2019-06-27 08:29:44'),
('admin', 'This user doesn\'t exist!', 'profile_show (wrong id)', '2019-06-27 08:29:47'),
('admin', 'This user doesn\'t exist!', 'profile_show (wrong id)', '2019-06-27 08:29:58'),
('admin', 'This user hadn\'t been activated!', 'profile_show', '2019-06-27 08:30:44'),
('tester', 'This user wasn\'t activated!', 'profile_show', '2019-06-27 08:43:11'),
('admin', 'You have already set up your school', '', '2019-06-28 14:57:47'),
('admin', 'You have already set up your school', '', '2019-06-28 14:57:55'),
('user02', 'You have not enough permissions to see this page!', '', '2019-06-28 21:16:42'),
('tester', 'You have not enough permissions to see this page!', '', '2019-06-29 16:58:36'),
('admin', 'Wrong profile id recieved.', 'profile_show (0 id - System)', '2019-06-29 20:05:18'),
('admin', 'You have already set up your school', '', '2019-06-30 14:44:08'),
('admin', 'There were not enough parameters recieved!', 'tutorial_action', '2019-06-30 15:54:41'),
('admin', 'You have already set up your school', '', '2019-06-30 16:22:03'),
('admin', 'There were not enough parameters recieved!', 'tutorial_action', '2019-06-30 17:04:58'),
('admin', 'You have already set up your school', '', '2019-06-30 17:09:56'),
('admin', 'You have already set up your school', '', '2019-06-30 17:09:57'),
('admin', 'You are already logged in!(user_message)', '', '2019-07-05 21:00:25'),
('admin', 'You are already logged in!(user_message)', '', '2019-07-05 21:02:12'),
('admin', 'There were not enough parameters recieved!', 'user_codes_action', '2019-07-07 12:30:09'),
('admin', 'There were not enough parameters recieved!', 'user_codes_action', '2019-07-07 15:29:42'),
('admin', 'You have not enough permissions to see this page!', '', '2019-07-07 15:51:29'),
('kololo', 'You are banned! <a href=\'signout.php\'>Sign out</a>', '', '2019-07-07 16:11:52'),
('kololo', 'You are banned! <a href=\'signout.php\'>Sign out</a>', '', '2019-07-07 16:12:04'),
('kololo', 'You are banned! <a href=\'signout.php\'>Sign out</a>', '', '2019-07-07 16:12:12'),
('kololo', 'You are banned! <a href=\'signout.php\'>Sign out</a>', '', '2019-07-07 16:12:58'),
('', 'You are banned! <a href=\'signout.php\'>Sign out</a>', '', '2019-07-07 16:13:01'),
('', 'You are banned! <a href=\'signout.php\'>Sign out</a>', '', '2019-07-07 16:13:11'),
('', 'You are banned! <a href=\'signout.php\'>Sign out</a>', '', '2019-07-07 16:14:54'),
('kololo', 'You are banned! <a href=\'signout.php\'>Sign out</a>', '', '2019-07-07 16:18:00'),
('kololo', 'You are banned! <a href=\'signout.php\'>Sign out</a>', '', '2019-07-07 16:18:02'),
('admin', 'You have not enough permissions to see this page!', '', '2019-07-07 17:45:09'),
('admin', 'You have not enough permissions to see this page!', '', '2019-07-07 17:45:14'),
('admin', 'Wrong profile id recieved.', 'profile_show (0 id - System)', '2019-07-07 19:54:14'),
('admin', 'You have not enough permissions to see this page!', '', '2019-07-10 20:31:31'),
('admin', 'UÅ¾ mÃ¡te nastavenou Å¡kolu', '', '2019-07-13 15:13:33'),
('admin', 'Wrong profile id recieved.', 'profile_show (0 id - System)', '2019-07-13 15:40:36'),
('tester', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-13 20:00:04'),
('tester', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-13 20:00:33'),
('admin', 'UÅ¾ jste pÅ™ihlÃ¡Å¡en!(user_message)', '', '2019-07-14 20:33:09'),
('tester', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:20:59'),
('tester', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:21:53'),
('tester', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:22:19'),
('imagee', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:31:47'),
('imagee', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:32:39'),
('imagee', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:34:06'),
('imagee', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:34:15'),
('imagee', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:34:42'),
('imagee', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:34:46'),
('imagee', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:34:48'),
('imagee', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:35:15'),
('imagee', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:35:19'),
('imagee', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:37:38'),
('imagee', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:38:47'),
('imagee', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:39:56'),
('imagee', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:40:30'),
('imagee', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:41:01'),
('imagee', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 19:41:37'),
('admin', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 20:02:30'),
('admin', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 20:12:13'),
('admin', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 20:12:42'),
('admin', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 20:13:38'),
('admin', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 20:14:10'),
('admin', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 20:14:40'),
('admin', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 20:15:28'),
('admin', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 20:15:46'),
('admin', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 20:17:12'),
('admin', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-17 20:19:25'),
('admin', 'Tento uÅ¾ivatel neexistuje!', 'profile_show (wrong id)', '2019-07-18 17:12:47'),
('', 'Byly zÃ­skÃ¡ny nekorektnÃ­ parametry!', '', '2019-07-19 15:03:01'),
('', 'Tento uÅ¾ivatel neexistuje!', '(forgotten_password)', '2019-07-19 15:04:41'),
('', 'ZadÃ¡na nesprÃ¡vnÃ¡ kombinace hash a emailu.', '', '2019-07-19 15:24:55'),
('', 'ZadÃ¡na nesprÃ¡vnÃ¡ kombinace hash a emailu.', '', '2019-07-19 15:25:07'),
('admin', 'ZÃ­skÃ¡no Å¡patnÃ© id uÅ¾ivatele.', 'profile_show (0 id - System)', '2019-07-19 15:47:33'),
('admin', 'ZÃ­skÃ¡no Å¡patnÃ© id uÅ¾ivatele.', 'profile_show (0 id - System)', '2019-07-19 15:47:50'),
('admin', 'ZÃ­skÃ¡no Å¡patnÃ© id uÅ¾ivatele.', 'profile_show (0 id - System)', '2019-07-19 15:48:17'),
('admin', 'ZÃ­skÃ¡no Å¡patnÃ© id uÅ¾ivatele.', 'profile_show (0 id - System)', '2019-07-19 15:49:23'),
('testpp', 'UÅ¾ mÃ¡te nastavenou Å¡kolu', '', '2019-07-19 17:21:51'),
('testpp', 'UÅ¾ mÃ¡te nastavenou Å¡kolu', '', '2019-07-19 17:21:57'),
('testpp', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-07-20 18:30:59'),
('testpp', 'DoÅ¡lo k chybÄ› pÅ™i zmÄ›nÄ› Å¡koly.', 'school_change', '2019-07-22 20:57:24'),
('testpp', 'DoÅ¡lo k chybÄ› pÅ™i zmÄ›nÄ› Å¡koly.', 'school_change', '2019-07-22 20:57:46'),
('admin', 'DoÅ¡lo k chybÄ› pÅ™i zmÄ›nÄ› Å¡koly.', 'school_change', '2019-07-22 20:58:05'),
('referraltest', 'VÃ¡Å¡ profil byl jiÅ¾ nastaven. Pro dalÅ¡Ã­ zmÄ›ny, vyuÅ¾ijte nastavenÃ­!', '', '2019-07-23 21:24:31'),
('referraltest', 'UÅ¾ mÃ¡te nastavenou Å¡kolu', '', '2019-07-23 21:28:50'),
('tester', 'Byl jste zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-25 15:25:59'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-25 15:34:09'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-25 15:34:18'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-25 15:34:24'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-25 15:34:25'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-25 15:34:26'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-25 15:34:27'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-25 15:34:54'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-25 15:34:55'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-25 15:34:57'),
('admin', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-26 18:40:17'),
('admin', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-26 18:40:31'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-26 19:14:00'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-26 19:14:05'),
('admin', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-26 21:29:40'),
('admin', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-26 21:29:57'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-27 15:31:10'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-27 15:31:18'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-27 15:33:31'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-27 15:34:22'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-07-27 15:34:26'),
('admin', 'Tento uÅ¾ivatel neexistuje!', 'profile_show (wrong id)', '2019-07-28 14:33:50'),
('admin', 'Nebylo zÃ­skÃ¡no dostateÄnÄ› parametrÅ¯!', 'lottery_action', '2019-07-29 20:46:19'),
('admin', 'NemÃ¡te oprÃ¡vnÄ›nÃ­ vidÄ›t obsah tÃ©to strÃ¡nky!', '', '2019-08-01 15:34:01'),
('admin', 'DoÅ¡lo k chybÄ› pÅ™i zmÄ›nÄ› Å¡koly.', 'school_change', '2019-08-01 20:58:46'),
('tester', 'Nebylo zÃ­skÃ¡no dostateÄnÄ› informacÃ­!', 'validators_apply', '2019-08-01 21:30:19'),
('admin', 'DoÅ¡lo k chybÄ› pÅ™i zmÄ›nÄ› Å¡koly.', 'school_change', '2019-08-02 15:32:36'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-08-02 15:40:59'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-08-02 15:41:00'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-08-02 15:41:27'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-08-02 15:41:27'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-08-02 15:41:29'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-08-02 15:41:33'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-08-02 15:41:33'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-08-02 15:41:36'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-08-02 15:41:40'),
('tester', 'VÃ¡Å¡ ÃºÄet byl zablokovÃ¡n! <a href=\'signout.php\'>OdhlÃ¡sit se</a>', '', '2019-08-02 15:41:41'),
('admin', 'DoÅ¡lo k chybÄ› pÅ™i zmÄ›nÄ› Å¡koly.', 'school_change', '2019-08-02 16:07:52'),
('admin', 'DoÅ¡lo k chybÄ› pÅ™i zmÄ›nÄ› Å¡koly.', 'school_change', '2019-08-02 16:08:28'),
('admin', 'ZÃ­skÃ¡no Å¡patnÃ© id uÅ¾ivatele.', 'profile_show (0 id - System)', '2019-08-02 16:12:24'),
('admin', 'ZÃ­skÃ¡no Å¡patnÃ© id uÅ¾ivatele.', 'profile_show (0 id - System)', '2019-08-02 16:12:30'),
('admin', 'Nebyly zÃ­skÃ¡ny potÅ™ebnÃ© parametry', 'profile_show', '2019-08-02 20:29:50'),
('admin', 'Vyskytl se problÃ©m s databÃ¡zÃ­, opravÃ­me ho, jakmile to bude moÅ¾nÃ©.', 'NÄ›co se pokazilo pÅ™i zÃ­kÃ¡vÃ¡nÃ­ informace o uÅ¾ivateli z databÃ¡ze!', '2019-08-05 15:13:43'),
('admin', 'DoÅ¡lo k chybÄ› pÅ™i zmÄ›nÄ› Å¡koly.', 'school_change', '2019-08-07 19:19:12'),
('admin', 'DoÅ¡lo k chybÄ› pÅ™i zmÄ›nÄ› Å¡koly.', 'school_change', '2019-08-07 19:43:02'),
('admin', 'DoÅ¡lo k chybÄ› pÅ™i zmÄ›nÄ› Å¡koly.', 'school_change', '2019-08-07 20:35:18'),
('admin', 'UÅ¾ mÃ¡te nastavenou Å¡kolu', '', '2019-08-09 21:40:49'),
('admin', 'UÅ¾ mÃ¡te nastavenou Å¡kolu', '', '2019-08-10 14:19:58'),
('admin', 'ZÃ­skÃ¡no Å¡patnÃ© id uÅ¾ivatele.', 'profile_show (0 id - System)', '2019-08-10 16:52:41'),
('admin', 'ZatÃ­m nemÃ¡te nastavenou Å¡kolu!', 'school_info (session[school_id]=0)', '2019-08-10 16:54:49'),
('admin', 'ZatÃ­m nemÃ¡te nastavenou Å¡kolu!', 'school_info (session[school_id]=0)', '2019-08-10 16:54:58'),
('admin', 'UÅ¾ mÃ¡te nastavenou Å¡kolu', '', '2019-08-10 16:55:20'),
('admin', 'ZatÃ­m nemÃ¡te nastavenou Å¡kolu!', 'school_info (session[school_id]=0)', '2019-08-10 16:55:27'),
('admin', 'Už máte nastavenou školu', '', '2019-08-10 19:22:40'),
('try2', 'Váš účet byl zablokován! <a href=\'signout.php\'>Odhlásit se</a>', '', '2019-09-04 16:20:03'),
('try2', 'Váš účet byl zablokován! <a href=\'signout.php\'>Odhlásit se</a>', '', '2019-09-04 16:20:03'),
('admin', 'Tento uživatel neexistuje!', 'profile_show (wrong id)', '2019-09-29 19:14:22'),
('admin', 'Tento uživatel neexistuje!', 'profile_show (wrong id)', '2019-09-29 19:14:29'),
('admin', 'Tento uživatel neexistuje!', 'profile_show (wrong id)', '2019-09-29 19:15:04'),
('admin', 'Tento uživatel neexistuje!', 'profile_show (wrong id)', '2019-09-29 19:15:58'),
('admin', 'Získáno špatné id uživatele.', 'profile_show (0 id - System)', '2019-09-29 19:25:27'),
('admin', 'Získáno špatné id uživatele.', 'profile_show (0 id - System)', '2019-09-29 20:49:51'),
('admin', 'Už máte nastavenou školu', '', '2019-09-29 20:56:44');

-- --------------------------------------------------------

--
-- Структура таблицы `faq`
--

CREATE TABLE `faq` (
  `faq_id` int(11) NOT NULL,
  `faq_title` varchar(100) COLLATE utf8_bin NOT NULL,
  `faq_content` varchar(1000) COLLATE utf8_bin NOT NULL,
  `faq_category` varchar(20) COLLATE utf8_bin NOT NULL,
  `faq_visible` int(11) NOT NULL DEFAULT '1',
  `faq_created` datetime NOT NULL,
  `faq_createdby` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `faq`
--

INSERT INTO `faq` (`faq_id`, `faq_title`, `faq_content`, `faq_category`, `faq_visible`, `faq_created`, `faq_createdby`) VALUES
(1, 'How to create account', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Rem aperiam hic doloremque aliquam eum modi dolorum, itaque, quibusdam, ea expedita cum similique ipsam voluptatibus sed corporis assumenda natus vel sint. Veritatis cum sequi veniam voluptatem neque, nam eos debitis sit accusantium, nihil a numquam qui, asperiores rem deserunt iure unde.', 'Accounts', 0, '0000-00-00 00:00:00', 0),
(2, 'How to delete account', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Debitis cumque explicabo perferendis iure ea. Suscipit nesciunt numquam molestiae quas magnam! Iusto perferendis quisquam ducimus neque eligendi a ea, sapiente! Ipsam!', 'Accounts', 0, '0000-00-00 00:00:00', 0),
(3, 'How to buy items', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quo, quis provident nisi eveniet ducimus aliquid ab impedit ullam totam nesciunt accusamus eos quos praesentium placeat facilis tenetur, quasi mollitia eius quisquam hic dolorum consequuntur. Quia eos libero reprehenderit hic itaque ratione ipsam, explicabo repellat dolor voluptates laudantium, nam impedit! Suscipit beatae sequi, laudantium aperiam dolor pariatur ipsa aut quam nemo?', 'Shop', 0, '0000-00-00 00:00:00', 0),
(4, 'Hello', 'asdasdasd asdasdasd', '', 0, '2019-05-25 19:04:08', 1),
(5, 'How to be nice', 'Lorem asdajdjaoidj oaijdoija oisdj oiajsdoi aosidoiajosdjao sij asoidj oiasd asijdiajsoj aoisjdoiasoijajsd oijaoisdj', '', 0, '2019-05-25 20:06:39', 1),
(6, 'How to make deposit', 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of &quot;de Finibus Bonorum et Malorum&quot; (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, &quot;Lorem ipsum dolor sit amet..&quot;, comes from a line in section 1.10.32.', '', 0, '2019-05-25 20:07:11', 1),
(7, 'How to make a withdraw', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).', '', 0, '2019-05-25 20:07:29', 1),
(8, 'Nice to meet you', 'aisdjkaisdjoiajdso\r\nasdiasdkaopsdkpas\r\n\r\n\r\nasdasdasdasda', '', 0, '2019-05-25 20:07:49', 1),
(9, 'asdasfdasdas', 'dasdasdasdasd &lt;br&gt; asdaisjdiajsdasdasd', '', 0, '2019-05-25 20:08:06', 1),
(10, 'asdasda', 'fafaafafaf', '', 0, '2019-05-26 21:53:26', 1),
(11, 'Statusy a skupiny', 'LOREM', '', 0, '2019-07-15 16:25:58', 1),
(12, 'Vklad a v&yacute;bÄ›r', '', '', 0, '2019-07-15 16:26:13', 1),
(13, '', '', '', 0, '2019-07-15 16:27:11', 1),
(14, '&lt;button&gt;asd&lt;/button&gt;asd', '&lt;button&gt;asd&lt;/button&gt;asd', '', 0, '2019-07-15 16:28:07', 1),
(15, 'Vklad penÄ›z', '<button>asdasdasd</button>', '', 0, '2019-07-18 17:01:51', 1),
(16, 'V&yacute;bÄ›r penÄ›z', 'asdasd', '', 0, '2019-07-18 17:02:10', 1),
(17, 'VIP Status', 'VIP', '', 0, '2019-07-18 17:02:22', 1),
(18, 'Skupiny', 'Skupiny', '', 0, '2019-07-18 17:02:54', 1),
(19, 'Jak&eacute; skupiny existuj&iacute;?', 'asdsad', '', 1, '2019-07-18 17:03:38', 1),
(20, 'Co je VIP status?', 'asd', '', 1, '2019-07-18 17:03:54', 1),
(21, 'Jak vybrat pen&iacute;ze z &uacute;Ätu?', 'asdasd', '', 1, '2019-07-18 17:04:11', 1),
(22, 'Jak vloÅ¾it pen&iacute;ze na &uacute;Äet?', 'asd', '', 1, '2019-07-18 17:04:23', 1),
(23, 'Jak nakupovat?', 'asdasd', '', 1, '2019-07-18 17:04:49', 1),
(24, 'Jak vložit peníze na ůčet', 'dOBRÝ DEN. iaksidkakíášuáíěéíšěš+ěščřžýáíéˇoó', '', 1, '2019-09-29 18:52:05', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `groups`
--

CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(30) COLLATE utf8_bin NOT NULL,
  `group_description` varchar(200) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `groups`
--

INSERT INTO `groups` (`group_id`, `group_name`, `group_description`) VALUES
(1, 'Administrator', 'Full access'),
(2, 'Validator', 'Confirms SQL requests'),
(3, 'Support', 'Answers a questions.'),
(4, 'Main administrator', 'Main administrator');

-- --------------------------------------------------------

--
-- Структура таблицы `images`
--

CREATE TABLE `images` (
  `image_id` int(11) NOT NULL,
  `file_path` varchar(255) COLLATE utf8_bin NOT NULL,
  `upload_date` datetime NOT NULL,
  `status` enum('1','0') COLLATE utf8_bin NOT NULL DEFAULT '1',
  `image_createdby_userid` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `images`
--

INSERT INTO `images` (`image_id`, `file_path`, `upload_date`, `status`, `image_createdby_userid`, `shop_id`) VALUES
(1, 'uploads/admin_cake-1.jpeg', '2019-05-02 19:52:31', '1', 1, 1),
(2, 'uploads/admin_coffee-bean.png', '2019-05-02 19:52:31', '1', 1, 1),
(3, 'uploads/admin_image-1.jpeg', '2019-05-02 20:18:39', '1', 1, 2),
(4, 'uploads/admin_image-2.jpeg', '2019-05-02 20:18:39', '1', 1, 2),
(5, 'uploads/admin_intro-1.jpg', '2019-05-02 20:19:14', '1', 1, 3),
(6, 'uploads/admin_intro-2.jpeg', '2019-05-02 20:19:14', '1', 1, 3),
(7, 'uploads/admin_intro-2.jpeg', '2019-05-02 20:20:49', '1', 1, 4),
(8, 'uploads/admin_intro-3.jpeg', '2019-05-02 20:20:49', '1', 1, 4),
(9, 'uploads/admin_bg-4.jpeg', '2019-05-02 20:22:03', '1', 1, 5),
(10, 'uploads/admin_intro-1.jpg', '2019-05-02 20:43:41', '1', 1, 6),
(11, 'uploads/admin_intro-2.jpeg', '2019-05-02 20:43:41', '1', 1, 6),
(14, 'uploads/admin_image-2.jpeg', '2019-05-02 21:15:30', '1', 1, 8),
(15, 'uploads/admin_intro-1.jpg', '2019-05-02 21:15:30', '1', 1, 8),
(16, 'uploads/admin_image-1.jpeg', '2019-05-10 23:04:04', '1', 1, 9),
(17, 'uploads/admin_image-2.jpeg', '2019-05-10 23:04:04', '1', 1, 9),
(18, 'uploads/admin_intro-1.jpg', '2019-05-10 23:04:04', '1', 1, 9),
(19, 'uploads/kololo_image-2.jpeg', '2019-05-12 21:15:24', '1', 6, 10),
(20, 'uploads/kololo_intro-1.jpg', '2019-05-12 21:15:24', '1', 6, 10),
(21, 'uploads/admin_coffee-bean.png', '2019-05-13 13:53:17', '1', 1, 11),
(23, 'uploads/admin_cake-1.jpeg', '2019-05-13 21:24:56', '1', 1, 13),
(24, 'uploads/admin_coffee-bg.jpeg', '2019-05-13 21:45:22', '1', 1, 14),
(25, 'uploads/admin_coffee-cup.jpg', '2019-05-13 21:45:22', '1', 1, 14),
(26, 'uploads/admin_intro-2.jpeg', '2019-05-14 15:31:02', '1', 1, 15),
(27, 'uploads/admin_intro-3.jpeg', '2019-05-14 15:31:02', '1', 1, 15),
(28, 'uploads/admin_intro-1.jpg', '2019-05-14 15:52:44', '1', 1, 16),
(29, 'uploads/admin_intro-2.jpeg', '2019-05-14 15:55:11', '1', 1, 17),
(30, 'uploads/admin_coffee-bean.png', '2019-05-14 15:57:20', '1', 1, 18),
(31, 'uploads/admin_bg-1.jpeg', '2019-05-17 21:38:13', '1', 1, 19),
(35, 'uploads/kololo_default â€” ÐºÐ¾Ð¿Ð¸Ñ.jpg', '2019-05-27 21:26:39', '1', 6, 23),
(36, 'uploads/admin_default.jpg', '2019-05-29 20:19:12', '1', 1, 27),
(37, 'uploads/admin_admin_cake-1.jpeg', '2019-05-29 20:19:55', '1', 1, 28),
(38, 'uploads/admin_61040030_2287883557958163_6643549825423900672_n.jpg', '2019-05-29 21:16:09', '1', 1, 29),
(39, 'uploads/admin_60814711_545630582631296_6612883917844250624_n.jpg', '2019-05-29 21:16:51', '1', 1, 30),
(40, 'uploads/admin_60833256_291273795088036_4160240549950914560_n.jpg', '2019-05-29 21:16:51', '1', 1, 30),
(41, 'uploads/admin_60351718_470260310386203_925117590278242304_n.jpg', '2019-05-29 21:17:11', '1', 1, 31),
(42, 'uploads/admin_60474760_473186820088055_298514387579174912_n.jpg', '2019-05-29 21:17:11', '1', 1, 31),
(44, 'uploads/admin_facebook.png', '2019-06-11 20:33:30', '1', 1, 33),
(45, 'uploads/admin_instagram.png', '2019-06-11 20:54:05', '1', 1, 34),
(46, 'uploads/admin_snapchat.png', '2019-06-11 20:54:05', '1', 1, 34),
(49, 'uploads/admin_instagram.png', '2019-06-12 21:35:44', '1', 1, 36),
(50, 'uploads/admin_snapchat.png', '2019-06-12 21:36:04', '1', 1, 37),
(56, 'uploads/imagee_404.png', '2019-06-21 18:17:58', '1', 55, 46),
(57, 'uploads/admin_no-pasaran.png', '2019-06-28 12:50:59', '1', 1, 51),
(58, 'uploads/admin_pexels-photo-730803.jpeg', '2019-06-28 12:56:19', '1', 1, 53),
(59, 'uploads/admin_pexels-photo-2110950.jpeg', '2019-06-28 12:56:19', '1', 1, 53),
(60, 'uploads/admin_pexels-photo-2328867.jpeg', '2019-06-28 12:56:19', '1', 1, 53),
(61, 'uploads/admin_pexels-photo-2480854.jpeg', '2019-06-28 12:56:19', '1', 1, 53),
(64, 'uploads/user02_pexels-photo-730803.jpeg', '2019-06-28 21:04:48', '1', 57, 75),
(65, 'uploads/user02_pexels-photo-2110950.jpeg', '2019-06-28 21:04:48', '1', 57, 75),
(66, 'uploads/user02_pexels-photo-2328867.jpeg', '2019-06-28 21:04:48', '1', 57, 75),
(67, 'uploads/user02_pexels-photo-2480854.jpeg', '2019-06-28 21:04:48', '1', 57, 75),
(72, 'uploads/admin_020719_192730received_2271880629791322.jpeg', '2019-07-02 19:27:30', '1', 1, 80),
(73, 'uploads/admin_020719_192951Screenshot_20190702-150856.png', '2019-07-02 19:29:51', '1', 1, 81),
(74, 'uploads/admin_020719_193042Screenshot_20190702-150856.png', '2019-07-02 19:30:42', '1', 1, 82),
(75, 'uploads/admin_020719_193255Screenshot_20190701-122549.png', '2019-07-02 19:32:55', '1', 1, 83),
(76, 'uploads/admin_020719_193255Screenshot_20190701-182423.png', '2019-07-02 19:32:55', '1', 1, 83),
(77, 'uploads/admin_020719_194239Screenshot_20190702-180845.png', '2019-07-02 19:42:39', '1', 1, 85),
(78, 'uploads/admin_020719_194239Screenshot_20190702-150856.png', '2019-07-02 19:42:39', '1', 1, 85),
(79, 'uploads/admin_020719_194404Screenshot_20190702-180845.png', '2019-07-02 19:44:04', '1', 1, 86),
(80, 'uploads/admin_020719_194404IMG_20190629_132425_039.jpg', '2019-07-02 19:44:04', '1', 1, 86),
(81, 'uploads/admin_020719_194404Screenshot_20190702-150856.png', '2019-07-02 19:44:04', '1', 1, 86),
(82, 'uploads/admin_020719_19572920190628_213417.jpg', '2019-07-02 19:57:29', '1', 1, 90),
(83, 'uploads/admin_020719_19572920190628_213408.jpg', '2019-07-02 19:57:29', '1', 1, 90),
(84, 'uploads/admin_020719_19572920190702_165327.jpg', '2019-07-02 19:57:29', '1', 1, 90),
(85, 'uploads/admin_020719_19572920190628_213417(0).jpg', '2019-07-02 19:57:29', '1', 1, 90),
(86, 'uploads/admin_020719_203711pexels-photo-2328867.jpeg', '2019-07-02 20:37:11', '1', 1, 93),
(87, 'uploads/admin_020719_20384620190628_213417.jpg', '2019-07-02 20:38:46', '1', 1, 94),
(88, 'uploads/admin_020719_20384720190628_213408.jpg', '2019-07-02 20:38:47', '1', 1, 94),
(89, 'uploads/admin_020719_205236IMG_20190702_205215.jpg', '2019-07-02 20:52:36', '1', 1, 95),
(90, 'uploads/admin_020719_205236IMG_20190702_205202.jpg', '2019-07-02 20:52:36', '1', 1, 95),
(91, 'uploads/admin_020719_205237IMG_20190702_205148.jpg', '2019-07-02 20:52:37', '1', 1, 95),
(92, 'uploads/admin_020719_210217IMG_1022.JPG', '2019-07-02 21:02:17', '1', 1, 96),
(93, 'uploads/admin_020719_210217IMG_1018.JPG', '2019-07-02 21:02:17', '1', 1, 96),
(94, 'uploads/admin_020719_210217IMG_1019.JPG', '2019-07-02 21:02:17', '1', 1, 96),
(95, 'uploads/admin_020719_210501IMG_1022.JPG', '2019-07-02 21:05:01', '1', 1, 97),
(96, 'uploads/admin_020719_210501IMG_1018.JPG', '2019-07-02 21:05:01', '1', 1, 97),
(97, 'uploads/admin_020719_210502IMG_1019.JPG', '2019-07-02 21:05:02', '1', 1, 97),
(98, 'uploads/admin_020719_213054received_2271880629791322.jpeg', '2019-07-02 21:30:54', '1', 1, 98),
(99, 'uploads/admin_020719_21305420190702_165327.jpg', '2019-07-02 21:30:54', '1', 1, 98),
(100, 'uploads/admin_020719_213054Screenshot_20190629-171756.png', '2019-07-02 21:30:54', '1', 1, 98),
(101, 'uploads/admin_020719_21305420190701_065621.jpg', '2019-07-02 21:30:54', '1', 1, 98),
(102, 'uploads/admin_050719_204940palm-fronds-and-sky.png', '2019-07-05 20:49:40', '1', 1, 99),
(103, 'uploads/admin_050719_204940pexels-photo-730803.jpeg', '2019-07-05 20:49:40', '1', 1, 99),
(104, 'uploads/admin_050719_204940pexels-photo-2110950.jpeg', '2019-07-05 20:49:40', '1', 1, 99),
(105, 'uploads/admin_050719_204940pexels-photo-2328867.jpeg', '2019-07-05 20:49:40', '1', 1, 99),
(106, 'uploads/admin_050719_205033palm-fronds-and-sky.png', '2019-07-05 20:50:33', '1', 1, 100),
(107, 'uploads/admin_050719_205033pexels-photo-730803.jpeg', '2019-07-05 20:50:33', '1', 1, 100),
(108, 'uploads/admin_050719_205033pexels-photo-2110950.jpeg', '2019-07-05 20:50:33', '1', 1, 100),
(109, 'uploads/admin_050719_205033pexels-photo-2328867.jpeg', '2019-07-05 20:50:33', '1', 1, 100),
(110, 'uploads/admin_050719_205108palm-fronds-and-sky.png', '2019-07-05 20:51:08', '1', 1, 101),
(111, 'uploads/admin_050719_205108pexels-photo-730803.jpeg', '2019-07-05 20:51:08', '1', 1, 101),
(112, 'uploads/admin_050719_205108pexels-photo-2110950.jpeg', '2019-07-05 20:51:08', '1', 1, 101),
(113, 'uploads/admin_050719_205108pexels-photo-2328867.jpeg', '2019-07-05 20:51:08', '1', 1, 101),
(114, 'uploads/admin_100719_215006logo-large.png', '2019-07-10 21:50:06', '1', 1, 103),
(115, 'uploads/admin_100719_215006maintenance.png', '2019-07-10 21:50:06', '1', 1, 103),
(116, 'uploads/admin_100719_215006palm-fronds-and-sky.png', '2019-07-10 21:50:06', '1', 1, 103),
(117, 'uploads/admin_100719_215006pexels-photo-730803.jpeg', '2019-07-10 21:50:06', '1', 1, 103),
(118, 'uploads/admin_100719_215332pexels-photo-730803.jpeg', '2019-07-10 21:53:32', '1', 1, 105),
(119, 'uploads/admin_100719_215332pexels-photo-2110950.jpeg', '2019-07-10 21:53:32', '1', 1, 105),
(120, 'uploads/admin_100719_215332pexels-photo-2328867.jpeg', '2019-07-10 21:53:32', '1', 1, 105),
(121, 'uploads/admin_100719_215332pexels-photo-2480854.jpeg', '2019-07-10 21:53:32', '1', 1, 105),
(122, 'uploads/admin_100719_215501admin_020719_19572920190628_213408.jpg', '2019-07-10 21:55:01', '1', 1, 106),
(123, 'uploads/admin_100719_215503admin_020719_19572920190628_213417(0).jpg', '2019-07-10 21:55:03', '1', 1, 106),
(124, 'uploads/admin_100719_215503admin_020719_19572920190628_213417.jpg', '2019-07-10 21:55:03', '1', 1, 106),
(125, 'uploads/admin_100719_215504admin_020719_19572920190702_165327.jpg', '2019-07-10 21:55:04', '1', 1, 106),
(126, 'uploads/testpp_180719_193916palm-fronds-and-sky.png', '2019-07-18 19:39:16', '1', 61, 107),
(127, 'uploads/testpp_180719_193916pexels-photo-730803.jpeg', '2019-07-18 19:39:16', '1', 61, 107),
(128, 'uploads/testpp_180719_193916pexels-photo-2110950.jpeg', '2019-07-18 19:39:16', '1', 61, 107),
(129, 'uploads/testpp_180719_193916pexels-photo-2328867.jpeg', '2019-07-18 19:39:16', '1', 61, 107),
(130, 'uploads/admin_280719_151150admin_020719_205237IMG_20190702_205148.jpg', '2019-07-28 15:11:50', '1', 1, 108),
(131, 'uploads/admin_280719_151150admin_15617206915681438382931.jpg', '2019-07-28 15:11:50', '1', 1, 108),
(132, 'uploads/admin_280719_151151admin_1561720614896100074764.jpg', '2019-07-28 15:11:51', '1', 1, 108),
(133, 'uploads/admin_280719_151151admin_020719_19572920190628_213417(0).jpg', '2019-07-28 15:11:51', '1', 1, 108),
(134, 'uploads/admin_280719_151305admin_020719_19572920190628_213408.jpg', '2019-07-28 15:13:05', '1', 1, 109),
(135, 'uploads/admin_280719_151305admin_020719_20384720190628_213408.jpg', '2019-07-28 15:13:05', '1', 1, 109),
(136, 'uploads/admin_280719_151306admin_100719_215501admin_020719_19572920190628_213408.jpg', '2019-07-28 15:13:06', '1', 1, 109),
(137, 'uploads/admin_280719_151306admin_020719_205237IMG_20190702_205148.jpg', '2019-07-28 15:13:06', '1', 1, 109),
(138, 'uploads/admin_280719_151401admin_280719_151150admin_020719_205237IMG_20190702_205148.jpg', '2019-07-28 15:14:01', '1', 1, 110),
(139, 'uploads/admin_110819_144600admin_020719_21305420190702_165327.jpg', '2019-08-11 14:46:00', '1', 1, 111),
(140, 'uploads/admin_110819_144600admin_100719_215504admin_020719_19572920190702_165327.jpg', '2019-08-11 14:46:00', '1', 1, 111),
(141, 'uploads/admin_110819_144600admin20180817_103916.jpg', '2019-08-11 14:46:00', '1', 1, 111),
(142, 'uploads/admin_110819_144834admin_110819_144600admin_100719_215504admin_020719_19572920190702_165327.jpg', '2019-08-11 14:48:34', '1', 1, 112),
(143, 'uploads/admin_110819_144834admin20180817_103916.jpg', '2019-08-11 14:48:34', '1', 1, 112),
(144, 'uploads/admin_110819_151326admin_15583750263751064670502.jpg', '2019-08-11 15:13:26', '1', 1, 114),
(145, 'uploads/admin_110819_151327admin_280719_151306admin_100719_215501admin_020719_19572920190628_213408.jpg', '2019-08-11 15:13:27', '1', 1, 114),
(146, 'uploads/admin_110819_151327admin_020719_205237IMG_20190702_205148.jpg', '2019-08-11 15:13:27', '1', 1, 114),
(147, 'uploads/admin_110819_151610admin_020719_21305420190701_065621.jpg', '2019-08-11 15:16:10', '1', 1, 115),
(148, 'uploads/admin_110819_151610admin_15583750263751064670502.jpg', '2019-08-11 15:16:10', '1', 1, 115),
(149, 'uploads/admin_110819_151611admin_020719_19572920190628_213408.jpg', '2019-08-11 15:16:11', '1', 1, 115),
(150, 'uploads/admin_110819_151720admin_280719_151150admin_15617206915681438382931.jpg', '2019-08-11 15:17:20', '1', 1, 116),
(151, 'uploads/matoustest_040919_104815palm-fronds-and-sky.png', '2019-09-04 10:48:15', '1', 63, 117),
(152, 'uploads/matoustest_040919_104815pexels-photo-730803.jpeg', '2019-09-04 10:48:15', '1', 63, 117);

-- --------------------------------------------------------

--
-- Структура таблицы `lottery`
--

CREATE TABLE `lottery` (
  `lottery_id` int(11) NOT NULL,
  `lottery_userid` int(11) NOT NULL,
  `lottery_joined` datetime NOT NULL,
  `lottery_place` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `lottery`
--

INSERT INTO `lottery` (`lottery_id`, `lottery_userid`, `lottery_joined`, `lottery_place`) VALUES
(1, 1, '2019-07-09 00:00:00', 0),
(2, 1, '2019-06-30 00:00:00', 0),
(3, 1, '2019-07-12 00:00:00', 0),
(4, 1, '2019-07-14 00:00:00', 0),
(5, 1, '2019-07-19 00:00:00', 0),
(6, 2, '2019-07-16 00:00:00', 0),
(7, 2, '2019-07-03 00:00:00', 0),
(8, 2, '2019-07-10 00:00:00', 0),
(9, 2, '2019-07-17 00:00:00', 0),
(10, 2, '2019-07-03 00:00:00', 0),
(11, 2, '2019-07-24 00:00:00', 0),
(12, 2, '2019-07-09 00:00:00', 0),
(13, 2, '2019-07-16 00:00:00', 0),
(14, 2, '2019-07-24 00:00:00', 0),
(15, 2, '2019-07-01 00:00:00', 0),
(16, 2, '2019-07-10 00:00:00', 0),
(17, 2, '2019-07-03 00:00:00', 0),
(18, 2, '2019-07-02 00:00:00', 0),
(19, 6, '2019-08-01 00:00:00', 0),
(21, 1, '2019-08-01 18:52:47', 0),
(22, 1, '2019-08-01 18:58:00', 0),
(23, 2, '2019-08-02 00:00:00', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `message_from` int(11) NOT NULL,
  `message_to` int(11) NOT NULL,
  `message_content` varchar(400) COLLATE utf8_bin NOT NULL,
  `message_date` datetime NOT NULL,
  `message_removed` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `messages`
--

INSERT INTO `messages` (`message_id`, `message_from`, `message_to`, `message_content`, `message_date`, `message_removed`) VALUES
(1, 0, 1, '<span class=\'font-weight-bold\'>CODES: </span>Your vip status was successfully activated!<br>Expiration date: <span class=\'font-weight-bold\'>2019-06-13 20:26:20</span>', '2019-06-12 20:26:20', 1),
(2, 0, 1, 'Your vip status expired!', '2019-06-12 20:27:02', 1),
(3, 0, 1, '<span class=\'font-weight-bold\'>CODES: </span>Your vip status was successfully activated!<br>Expiration date: <span class=\'font-weight-bold\'>2019-06-13 20:27:46</span>', '2019-06-12 20:27:46', 1),
(4, 0, 1, 'Your vip status expired!', '2019-06-12 20:27:58', 1),
(5, 0, 1, '<span class=\'font-weight-bold\'>CODES: </span>Your vip status was successfully activated!<br>Expiration date: <span class=\'font-weight-bold\'>2019-06-13 20:29:21</span>', '2019-06-12 20:29:21', 1),
(6, 0, 1, 'Your vip status expired!', '2019-06-12 20:29:36', 1),
(7, 0, 1, '<span class=\'font-weight-bold\'>CODES: </span>Your vip status was successfully activated!<br>Expiration date: <span class=\'font-weight-bold\'>2019-06-13 20:34:29</span>', '2019-06-12 20:34:29', 1),
(8, 0, 1, 'Your vip status expired!', '2019-06-12 21:16:42', 1),
(9, 0, 1, 'Your request for add item <span class=\'font-weight-bold\'>Select second</span> was successfully confirmed by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-12 21:39:34', 1),
(10, 0, 1, 'Your request for add item <span class=\'font-weight-bold\'>Buy first</span> was successfully confirmed by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-12 21:42:33', 1),
(11, 0, 1, '<span class=\'font-weight-bold\'>CODES: </span>Your vip status was successfully activated!<br>Expiration date: <span class=\'font-weight-bold\'>2019-06-13 21:47:16</span>', '2019-06-12 21:47:16', 1),
(12, 0, 6, 'Item that you had bought was removed from our marketplace, because it had been violating our rules. Full price was restored on your balance.', '2019-06-12 21:52:42', 0),
(13, 0, 2, 'Item that you had bought was removed from our marketplace, because it had been violating our rules. Full price was restored on your balance.', '2019-06-12 21:52:43', 0),
(14, 0, 0, 'Item that you placed on our marketplace was removed, because it had been violating our rules. Money which you had earned with this item, was removed from your balance. (Removed 0 Kc)', '2019-06-12 21:52:43', 0),
(15, 0, 2, 'Item that you had bought was removed from our marketplace, because it had been violating our rules. Full price was restored on your balance.', '2019-06-12 21:53:25', 0),
(16, 0, 0, 'Item that you placed on our marketplace was removed, because it had been violating our rules. Money which you had earned with this item, was removed from your balance. (Removed 0 Kc)', '2019-06-12 21:53:25', 0),
(17, 0, 1, 'Your vip status expired!', '2019-06-13 21:50:34', 1),
(18, 0, 1, '<span class=\'font-weight-bold\'>CODES: </span>Your vip status was successfully activated!<br>Expiration date: <span class=\'font-weight-bold\'>2019-06-14 21:51:03</span>', '2019-06-13 21:51:04', 1),
(19, 0, 1, 'Your vip status expired!', '2019-06-14 21:52:27', 1),
(20, 0, 0, 'Your item was removed from our marketplace, because it was violating our rules!', '2019-06-18 20:48:45', 0),
(21, 0, 0, 'Your item was restored on our marketplace!', '2019-06-18 20:51:17', 0),
(22, 0, 0, 'Your item was restored on our marketplace!', '2019-06-18 20:51:20', 0),
(23, 0, 1, 'Your item Ffaaffawas restored on our marketplace!', '2019-06-18 20:51:57', 1),
(24, 0, 6, 'Your item <span class=\'font-weight-bold\'>Fafafafafaf</span> was restored on our marketplace!', '2019-06-18 20:52:41', 0),
(25, 0, 1, 'Your item <span class=\'font-weight-bold\'>PUPPO</span> was removed from our marketplace, because it was violating our rules! If you have any questions, you can create new ticket.', '2019-06-18 20:52:46', 1),
(26, 0, 1, 'Your item <span class=\'font-weight-bold\'>Konstanknknknk</span> was removed from our marketplace, because it was violating our rules! If you have any questions, you can create <a href=\'tickets.php\'>new ticket</a>.', '2019-06-18 20:54:50', 1),
(27, 0, 1, 'Your item <span class=\'font-weight-bold\'>Bbbbb</span> was removed from our marketplace, because it was violating our rules! If you have any questions, you can create <a href=\'tickets.php\' clas=\'text-primary\'><u>new ticket</u></a>.', '2019-06-18 20:55:36', 1),
(28, 0, 1, 'Your item <span class=\'font-weight-bold\'>Bbbbb</span> was restored on our marketplace!', '2019-06-18 20:55:48', 1),
(29, 0, 1, 'Your item <span class=\'font-weight-bold\'>PUPPO</span> was restored on our marketplace!', '2019-06-18 20:55:50', 1),
(30, 0, 1, 'Your item <span class=\'font-weight-bold\'>Konstanknknknk</span> was restored on our marketplace!', '2019-06-18 20:55:52', 1),
(31, 0, 6, 'Your item <span class=\'font-weight-bold\'>Fafafafafaf</span> was removed from our marketplace, because it was violating our rules! If you have any questions, you can create <a href=\'tickets.php\' clas=\'text-primary\'><u>new ticket</u></a>.', '2019-06-19 16:27:34', 0),
(32, 0, 1, 'Your item <span class=\'font-weight-bold\'>PUPPO</span> was removed from our marketplace, because it was violating our rules! If you have any questions, you can create <a href=\'tickets.php\' clas=\'text-primary\'><u>new ticket</u></a>.', '2019-06-19 16:28:05', 1),
(33, 0, 0, 'Your item <span class=\'font-weight-bold\'></span> was removed from our marketplace, because it was violating our rules! If you have any questions, you can create <a href=\'tickets.php\' clas=\'text-primary\'><u>new ticket</u></a>.', '2019-06-19 16:45:38', 0),
(34, 0, 0, 'Your item <span class=\'font-weight-bold\'></span> was removed from our marketplace, because it was violating our rules! If you have any questions, you can create <a href=\'tickets.php\' clas=\'text-primary\'><u>new ticket</u></a>.', '2019-06-19 16:46:04', 0),
(35, 0, 1, 'Your item <span class=\'font-weight-bold\'>Konstanknknknk</span> was removed from our marketplace, because it was violating our rules! If you have any questions, you can create <a href=\'tickets.php\' clas=\'text-primary\'><u>new ticket</u></a>.', '2019-06-19 16:49:23', 1),
(36, 0, 0, 'Your item <span class=\'font-weight-bold\'></span> was removed from our marketplace, because it was violating our rules! If you have any questions, you can create <a href=\'tickets.php\' clas=\'text-primary\'><u>new ticket</u></a>.', '2019-06-19 16:56:51', 0),
(37, 0, 0, 'Your item <span class=\'font-weight-bold\'></span> was restored on our marketplace!', '2019-06-19 16:58:06', 0),
(38, 0, 0, 'Your item <span class=\'font-weight-bold\'></span> was restored on our marketplace!', '2019-06-19 16:58:29', 0),
(39, 0, 0, 'Your item <span class=\'font-weight-bold\'></span> was restored on our marketplace!', '2019-06-19 16:59:03', 0),
(40, 0, 0, 'Your item <span class=\'font-weight-bold\'></span> was restored on our marketplace!', '2019-06-19 16:59:08', 0),
(41, 0, 0, 'Your item <span class=\'font-weight-bold\'></span> was restored on our marketplace!', '2019-06-19 16:59:49', 0),
(42, 0, 0, 'Your item <span class=\'font-weight-bold\'></span> was restored on our marketplace!', '2019-06-19 16:59:59', 0),
(43, 0, 0, 'Your item <span class=\'font-weight-bold\'></span> was restored on our marketplace!', '2019-06-19 17:01:24', 0),
(44, 0, 1, 'Your item <span class=\'font-weight-bold\'>Konstanknknknk</span> was restored on our marketplace!', '2019-06-19 17:04:02', 1),
(45, 0, 0, 'Your item <span class=\'font-weight-bold\'></span> was removed from our marketplace, because it was violating our rules! If you have any questions, you can create <a href=\'tickets.php\' clas=\'text-primary\'><u>new ticket</u></a>.', '2019-06-19 17:06:23', 0),
(46, 0, 1, 'Your item <span class=\'font-weight-bold\'>Konstanknknknk</span> was removed from our marketplace, because it was violating our rules! If you have any questions, you can create <a href=\'tickets.php\' clas=\'text-primary\'><u>new ticket</u></a>.', '2019-06-19 17:06:59', 1),
(47, 0, 1, 'Your item <span class=\'font-weight-bold\'>Ffaaffa</span> was removed from our marketplace, because it was violating our rules! If you have any questions, you can create <a href=\'tickets.php\' clas=\'text-primary\'><u>new ticket</u></a>.', '2019-06-19 17:07:05', 1),
(48, 0, 1, 'Your item <span class=\'font-weight-bold\'>Ffaaffa</span> was restored on our marketplace!', '2019-06-19 17:07:47', 1),
(49, 0, 1, 'Your item <span class=\'font-weight-bold\'>Konstanknknknk</span> was restored on our marketplace!', '2019-06-19 17:07:53', 1),
(50, 0, 1, 'Your item <span class=\'font-weight-bold\'>PUPPO</span> was restored on our marketplace!', '2019-06-19 17:08:07', 1),
(51, 0, 6, 'Your  status expired!', '2019-06-20 18:41:26', 0),
(52, 6, 1, 'fgg', '2019-06-20 18:41:35', 1),
(53, 0, 1, 'Your  status expired!', '2019-06-20 18:41:37', 1),
(54, 0, 55, 'Your request for add item <span class=\'font-weight-bold\'>TEST ADD</span> was declined by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: &lt;button&gt;4fun&lt;/button&gt;.', '2019-06-20 19:14:19', 0),
(55, 0, 1, 'Your request for add item <span class=\'font-weight-bold\'>Test 0000.8</span> was declined by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: gagg.', '2019-06-20 19:15:48', 1),
(56, 0, 1, 'Your request for add item <span class=\'font-weight-bold\'>Sadasd</span> was declined by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: &lt;button&gt;4fun&lt;/button&gt;.', '2019-06-20 19:17:04', 1),
(57, 0, 1, 'School  was <span class=\'text-danger font-weight-bold\'>deleted</span> by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=\'><u>admin</u></a>.', '2019-06-20 20:02:15', 1),
(58, 0, 1, 'School asdads was <span class=\'text-danger font-weight-bold\'>deleted</span> by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-20 20:04:08', 1),
(59, 0, 1, 'School adasdasda was <span class=\'text-danger font-weight-bold\'>deleted</span> by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-20 20:04:44', 1),
(60, 0, 2, 'Your request for validators group was <span class=\'text-secondary font-weight-bold\'>declined</span> by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: d.', '2019-06-20 20:24:52', 0),
(61, 0, 55, 'You were <span class=\'text-danger font-weight-bold\'>removed</span> from supports group by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-20 20:25:28', 0),
(62, 0, 55, 'Your request for validators group was <span class=\'text-secondary font-weight-bold\'>declined</span> by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: a.', '2019-06-20 20:28:16', 0),
(63, 0, 55, '<span class=\'text-secondary font-weight-bold\'>Congratulations, now you are a new support of our project!</span> You was set by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-20 20:28:29', 0),
(64, 0, 1, 'Your request for validators group was <span class=\'text-secondary font-weight-bold\'>declined</span> by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: You don\'t meet our requirements.', '2019-06-21 07:59:30', 1),
(65, 0, 1, 'Your request for validators group was <span class=\'text-secondary font-weight-bold\'>declined</span> by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: activity\r\n&lt;button&gt;asdasd&lt;/button&gt;.', '2019-06-21 08:00:01', 1),
(66, 0, 1, 'Your request for add item <span class=\'font-weight-bold\'>TEST</span> was declined by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: Wrong content.', '2019-06-21 20:14:09', 1),
(67, 0, 55, 'Your request for add item <span class=\'font-weight-bold\'>OTHER SCHOOL</span> was successfully confirmed by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-21 20:18:43', 0),
(68, 0, 1, 'Your request for adding 11111dasd school was <span class=\'text-danger font-weight-bold\'>declined</span> by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: This school doesn\'t exist.', '2019-06-21 20:45:21', 1),
(69, 0, 1, 'Your request for adding 11111dasd school was <span class=\'text-success font-weight-bold\'>confirmed</span> by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-21 21:05:09', 1),
(70, 0, 1, 'Your request for change to Dukelska school was <span class=\'text-danger font-weight-bold\'>declined</span> by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: You are allowed to change your school only 2 weeks after select.', '2019-06-21 21:24:47', 1),
(71, 0, 1, 'Your request for change to Dukelska school was <span class=\'text-success font-weight-bold\'>confirmed</span> by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-21 21:26:21', 1),
(72, 0, 1, 'Your request for change to Gymnazium na Prazacce school was <span class=\'text-success font-weight-bold\'>confirmed</span> by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-21 21:26:45', 1),
(73, 0, 1, 'Your request for change to Dukelska school was <span class=\'text-danger font-weight-bold\'>declined</span> by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: asd.', '2019-06-21 21:29:58', 1),
(74, 0, 1, 'Your request for change to Gymnazium na Prazacce school was <span class=\'text-danger font-weight-bold\'>declined</span> by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: dasd.', '2019-06-21 21:30:03', 1),
(75, 0, 1, 'Your request for change to 11111dasd school was <span class=\'text-danger font-weight-bold\'>declined</span> by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: You are allowed to change your school only 2 weeks after select.', '2019-06-21 21:30:16', 1),
(76, 1, 6, 'Hello\r\n!', '2019-06-22 16:11:11', 0),
(77, 0, 1, 'Your request for change to Dukelska school was <span class=\'text-danger font-weight-bold\'>declined</span> by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: You are allowed to change your school only 2 weeks after select.', '2019-06-22 20:33:45', 1),
(78, 0, 1, 'Your balance was set on <span class=\'font-weight-bold\'>1111</span> Kc by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: Hi :)', '2019-06-25 22:00:00', 1),
(79, 0, 1, 'Item that you had bought was removed from our marketplace, because it had been violating our rules. Full price was restored on your balance.', '2019-06-25 22:02:04', 1),
(80, 0, 0, 'Item that you placed on our marketplace was removed, because it had been violating our rules. Money which you had earned with this item, was removed from your balance. (Removed 0 Kc)', '2019-06-25 22:02:04', 0),
(81, 0, 6, 'Item that you had bought was removed from our marketplace, because it had been violating our rules. Full price was restored on your balance.', '2019-06-25 22:06:32', 0),
(82, 0, 1, 'Item that you had bought was removed from our marketplace, because it had been violating our rules. Full price was restored on your balance.', '2019-06-25 22:06:32', 1),
(83, 0, 0, 'Item that you placed on our marketplace was removed, because it had been violating our rules. Money which you had earned with this item, was removed from your balance. (Removed 0 Kc)', '2019-06-25 22:06:32', 0),
(84, 0, 1, 'Item that you had bought was removed from our marketplace, because it had been violating our rules. Full price was restored on your balance.', '2019-06-25 22:07:59', 1),
(85, 0, 6, 'Item that you placed on our marketplace was removed, because it had been violating our rules. Money which you had earned with this item, was removed from your balance. (Removed 0 Kc)', '2019-06-25 22:07:59', 0),
(86, 0, 1, 'Item that you had bought was removed from our marketplace, because it had been violating our rules. Full price was restored on your balance.', '2019-06-25 22:13:06', 1),
(87, 0, 2, 'Item that you had bought was removed from our marketplace, because it had been violating our rules. Full price was restored on your balance.', '2019-06-25 22:13:06', 0),
(88, 0, 6, 'Item that you placed on our marketplace was removed, because it had been violating our rules. Money which you had earned with this item, was removed from your balance. (Removed 154 Kc)', '2019-06-25 22:13:06', 0),
(89, 0, 4, '<span class=\'text-secondary font-weight-bold\'>Congratulations, now you are a new administrator of our project!</span> To get a password for admin panel, write a message to user, who you was set by. (On top panel click \'Send message\') You was set by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-26 21:49:12', 0),
(90, 0, 7, '<span class=\'text-secondary font-weight-bold\'>Congratulations, now you are a new support of our project!</span> You was set by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-26 21:49:26', 0),
(91, 0, 11, '<span class=\'text-secondary font-weight-bold\'>Congratulations, now you are a new validator of our project!</span> You was set by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-26 21:49:53', 0),
(92, 0, 2, '<span class=\'text-secondary font-weight-bold\'>Congratulations, now you are a new validator for your school!</span> Your application was confirmed by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-26 21:51:27', 0),
(93, 0, 4, '<span class=\'text-secondary font-weight-bold\'>Congratulations, now you are a new validator for your school!</span> Your application was confirmed by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-26 21:58:13', 0),
(94, 0, 4, 'You were <span class=\'text-danger font-weight-bold\'>removed</span> from validators group by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-26 21:58:45', 0),
(95, 0, 4, '<span class=\'text-secondary font-weight-bold\'>Congratulations, now you are a new validator of our project!</span> You was set by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-26 21:58:51', 0),
(96, 0, 6, 'Item that you had bought was removed from our marketplace, because it had been violating our rules. Full price was restored on your balance.', '2019-06-27 09:05:47', 0),
(97, 0, 1, 'Item that you had bought was removed from our marketplace, because it had been violating our rules. Full price was restored on your balance.', '2019-06-27 09:05:47', 1),
(98, 0, 2, 'Item that you had bought was removed from our marketplace, because it had been violating our rules. Full price was restored on your balance.', '2019-06-27 09:05:47', 0),
(99, 0, 1, 'Item that you placed on our marketplace was removed, because it had been violating our rules. Money which you had earned with this item, was removed from your balance. (Removed 220 Kc)', '2019-06-27 09:05:47', 1),
(100, 0, 2, '<span class=\'text-secondary font-weight-bold\'>Congratulations, now you are a new validator for your school!</span> Your application was confirmed by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-06-28 15:28:05', 0),
(101, 0, 1, 'Your request for add item <span class=\'font-weight-bold\'>Image test</span> was declined by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: Wrong image.', '2019-06-28 21:09:57', 1),
(102, 1, 57, 'adasdasd', '2019-06-28 21:10:37', 0),
(103, 0, 57, '<span class=\'font-weight-bold\'>CODES: </span><span class=\'font-weight-bold\'>500</span> Kc were successfully added to your balance!', '2019-06-28 21:16:41', 0),
(104, 57, 1, 'Pica', '2019-06-28 21:17:10', 1),
(105, 0, 1, 'Your report on item Fafafafafaf was declined by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'>admin</a>.', '2019-06-29 16:46:44', 1),
(106, 0, 1, 'Your withdraw request for 190 Kc was confirmed by <a class=\'text-primary font-weight-bold\' href=\'/testshop/profile_show.php?profile_id=1\'><u>admin</u></a>. You will recieve your money within 3 days.', '2019-06-29 18:44:28', 1),
(107, 0, 6, '<span class=\'text-secondary font-weight-bold\'>Congratulations, now you are a new administrator of our project!</span> To get a password for admin panel, write a message to user, who you was set by. (On top panel click \'Send message\') You was set by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-07-07 16:45:27', 0),
(108, 0, 6, 'Your account was banned. You were also removed from all groups.', '2019-07-07 16:45:33', 0),
(109, 0, 6, 'Your account was unbanned.', '2019-07-07 16:45:35', 0),
(110, 0, 2, 'Your account was unbanned. If you think, that you was banned by mistake, create a <a href=\'tickets.php\' class=\'font-weight-bold text-primary\'>ticket</a>.', '2019-07-08 16:12:59', 0),
(111, 0, 6, 'Your account was banned. You were also removed from all groups.', '2019-07-08 16:13:16', 0),
(112, 0, 6, 'Your account was unbanned. If you think, that you was banned by mistake, create a <a href=\'tickets.php\' class=\'font-weight-bold text-primary\'>ticket</a>.', '2019-07-08 16:14:07', 0),
(113, 0, 6, 'Your account was banned. You were also removed from all groups.', '2019-07-08 16:14:10', 0),
(114, 0, 6, 'Your account was unbanned. If you think, that you was banned by mistake, create a <a href=\'tickets.php\' class=\'font-weight-bold text-primary\'>ticket</a>.', '2019-07-08 20:22:33', 0),
(115, 0, 6, '<span class=\'text-secondary font-weight-bold\'>Congratulations, now you are a new administrator of our project!</span> To get a password for admin panel, write a message to user, who you was set by. (On top panel click \'Send message\') You was set by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-07-08 20:22:45', 0),
(116, 0, 6, 'You were <span class=\'text-danger font-weight-bold\'>removed</span> from administrators group by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-07-09 16:30:40', 0),
(117, 0, 6, '<span class=\'text-secondary font-weight-bold\'>Congratulations, now you are a new administrator of our project!</span> To get a password for admin panel, write a message to user, who you was set by. (On top panel click \'Send message\') You was set by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-07-09 16:31:00', 0),
(118, 0, 57, '<span class=\'font-weight-bold\'>CODES: </span>Your vip status was successfully activated! Expiration date: <span class=\'font-weight-bold\'>2019-07-10 19:38:53</span>', '2019-07-09 19:38:53', 0),
(119, 0, 57, 'Your vip status expired!', '2019-07-09 19:48:36', 0),
(120, 1, 6, 'Ahoj, jak se daÅ™Ã­?\r\n<button>asdasd</button>', '2019-07-12 06:46:56', 0),
(121, 1, 6, '&lt;button&gt;asdads&lt;/button&gt;', '2019-07-12 06:47:45', 0),
(122, 0, 2, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ Å¡koly adsasda byla <span class=\'text-danger font-weight-bold\'>odmÃ­tnuta</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: Tato Å¡kola neexistuje.', '2019-07-13 14:48:34', 0),
(123, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ Å¡koly Try 2 byla <span class=\'text-danger font-weight-bold\'>odmÃ­tnuta</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: Tato Å¡kola neexistuje.', '2019-07-13 14:48:41', 0),
(124, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ Å¡koly Hello byla <span class=\'text-danger font-weight-bold\'>odmÃ­tnuta</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: SADSAD.', '2019-07-13 14:48:45', 0),
(125, 0, 2, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ Å¡koly cccc byla <span class=\'text-success font-weight-bold\'>schvÃ¡lena</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-07-13 15:35:10', 0),
(126, 0, 2, 'Your balance was set on <span class=\'font-weight-bold\'>500</span> Kc by <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Reason: Jen tak. +Ä›Å¡ÄÅ™Å¾Ã½Ã¡Ñ„Ñ‹Ð²Ð°Ð²Ð¿Ð¿Ñ‹Ð°Ð°Ð²', '2019-07-13 17:06:52', 0),
(127, 0, 1, 'VaÅ¡e Å¾Ã¡dost o vÃ½bÄ›r 47 KÄ byla odmÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'/testshop/profile_show.php?profile_id=1\'><u>admin</u></a>. V pÅ™Ã­padÄ› dotazu mÅ¯Å¾ete vytvoÅ™it <a class=\'font-weight-bold text-primary\' href=\'tickets.php\'>novÃ½ tiket</a>.', '2019-07-15 16:29:02', 0),
(128, 0, 1, 'VaÅ¡e Å¾Ã¡dost o vÃ½bÄ›r 47 KÄ byla odmÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'/testshop/profile_show.php?profile_id=1\'><u>admin</u></a>. V pÅ™Ã­padÄ› dotazu mÅ¯Å¾ete vytvoÅ™it <a class=\'font-weight-bold text-primary\' href=\'tickets.php\'>novÃ½ tiket</a>.', '2019-07-15 16:32:03', 0),
(129, 0, 6, 'VaÅ¡e Å¾Ã¡dost o vÃ½bÄ›r 95 KÄ byla schvÃ¡lena uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'/testshop/profile_show.php?profile_id=1\'><u>admin</u></a>. PenÃ­ze budete mÃ­t na ÃºÄtÄ› do 3 pracovnÃ­ch dnÃ­.', '2019-07-16 16:25:17', 0),
(130, 0, 1, 'VaÅ¡e Å¾Ã¡dost o vÃ½bÄ›r 100 KÄ byla odmÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'/testshop/profile_show.php?profile_id=1\'><u>admin</u></a>. V pÅ™Ã­padÄ› dotazu mÅ¯Å¾ete vytvoÅ™it <a class=\'font-weight-bold text-primary\' href=\'tickets.php\'>novÃ½ tiket</a>.', '2019-07-16 16:47:24', 0),
(131, 0, 1, 'VaÅ¡e Å¾Ã¡dost o vÃ½bÄ›r 105 KÄ byla schvÃ¡lena uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'/testshop/profile_show.php?profile_id=1\'><u>admin</u></a>. PenÃ­ze budete mÃ­t na ÃºÄtÄ› do 3 pracovnÃ­ch dnÃ­.', '2019-07-16 16:47:26', 0),
(132, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Sad</span> byla ÃºspÄ›Å¡nÄ› schvÃ¡lena uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-07-16 20:01:55', 0),
(133, 0, 1, 'VaÅ¡e Å¾Ã¡dost o vÃ½bÄ›r 100000 KÄ byla odmÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'/testshop/profile_show.php?profile_id=1\'><u>admin</u></a>. V pÅ™Ã­padÄ› dotazu mÅ¯Å¾ete vytvoÅ™it <a class=\'font-weight-bold text-primary\' href=\'tickets.php\'>novÃ½ tiket</a>.', '2019-07-17 17:07:09', 0),
(134, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Asda</span> byla ÃºspÄ›Å¡nÄ› schvÃ¡lena uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=2\'><u>tester</u></a>.', '2019-07-17 19:23:10', 0),
(135, 0, 6, 'VaÅ¡e Å¾Ã¡dost o vÃ½bÄ›r 100 KÄ byla odmÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'/testshop/profile_show.php?profile_id=1\'><u>admin</u></a>. V pÅ™Ã­padÄ› dotazu mÅ¯Å¾ete vytvoÅ™it <a class=\'font-weight-bold text-primary\' href=\'tickets.php\'>novÃ½ tiket</a>.', '2019-07-17 20:36:28', 0),
(136, 0, 61, '<span class=\'font-weight-bold\'>KÃ“DY: </span><span class=\'font-weight-bold\'>1000</span> KÄ bylo ÃºspÄ›Å¡nÄ› pÅ™ipsÃ¡no na vaÅ¡e konto!', '2019-07-18 19:34:31', 0),
(137, 0, 61, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Otec</span> byla ÃºspÄ›Å¡nÄ› schvÃ¡lena uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-07-18 19:40:26', 0),
(138, 0, 61, 'Å kola Testik byla <span class=\'text-danger font-weight-bold\'>smazÃ¡na</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-07-19 17:38:38', 0),
(139, 0, 61, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Otec</span> byla ÃºspÄ›Å¡nÄ› schvÃ¡lena uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-07-19 21:21:56', 0),
(140, 0, 61, '<span class=\'text-secondary font-weight-bold\'>Gratulace, od teÄ jste novÃ½m ValidÃ¡torem naÅ¡eho projektu!</span> PÅ™idal VÃ¡s do skupiny uÅ¾ivatel <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-07-20 18:32:10', 0),
(141, 0, 61, 'Tento uÅ¾ivatel byl <span class=\'text-danger font-weight-bold\'>odstranÄ›n</span> ze skupiny ValidÃ¡torÅ¯ uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-07-20 20:44:10', 0),
(142, 0, 61, 'VaÅ¡e Å¾Ã¡dost o zmÄ›nu Å¡koly na KKKKK byla <span class=\'text-danger font-weight-bold\'>odmÃ­tnuta</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: ws.', '2019-07-22 20:57:15', 0),
(143, 0, 61, '<span class=\'text-secondary font-weight-bold\'>Gratulace, od teÄ jste novÃ½m ValidÃ¡torem naÅ¡eho projektu!</span> PÅ™idal VÃ¡s do skupiny uÅ¾ivatel <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-07-22 21:03:44', 0),
(144, 1, 61, 'Hello', '2019-07-22 21:06:50', 0),
(145, 61, 1, 'jolka\r\n', '2019-07-22 21:07:15', 0),
(146, 0, 61, 'Tento uÅ¾ivatel byl <span class=\'text-danger font-weight-bold\'>odstranÄ›n</span> ze skupiny ValidÃ¡torÅ¯ uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-07-22 21:19:42', 0),
(147, 0, 61, '<span class=\'text-secondary font-weight-bold\'>Gratulace, od teÄ jste novÃ½m ValidÃ¡torem naÅ¡eho projektu!</span> PÅ™idal VÃ¡s do skupiny uÅ¾ivatel <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-07-24 20:04:29', 0),
(148, 0, 2, 'VÃ¡Å¡ ÃºÄet byl zabanovÃ¡n a takÃ© odstranÄ›n ze vÅ¡ech skupin.', '2019-07-25 15:25:47', 0),
(149, 0, 2, 'VÃ¡Å¡ ÃºÄet byl odblokovÃ¡n. Jestli si myslÃ­te, Å¾e doÅ¡lo k chybÄ›, mÅ¯Å¾ete vytvoÅ™it <a href=\'tickets.php\' class=\'font-weight-bold text-primary\'>novÃ½ tiket</a>.', '2019-07-25 15:27:06', 0),
(150, 0, 2, 'VÃ¡Å¡ ÃºÄet byl zabanovÃ¡n a takÃ© odstranÄ›n ze vÅ¡ech skupin, jelikoÅ¾ mÃ¡te hodnÄ› trestnÃ½ch bodÅ¯.', '2019-07-25 15:32:24', 0),
(151, 0, 2, 'VÃ¡Å¡ ÃºÄet byl zabanovÃ¡n a takÃ© odstranÄ›n ze vÅ¡ech skupin, jelikoÅ¾ mÃ¡te hodnÄ› trestnÃ½ch bodÅ¯.', '2019-07-25 15:32:26', 0),
(152, 0, 2, 'VÃ¡Å¡ ÃºÄet byl zabanovÃ¡n a takÃ© odstranÄ›n ze vÅ¡ech skupin, jelikoÅ¾ mÃ¡te hodnÄ› trestnÃ½ch bodÅ¯.', '2019-07-25 15:32:56', 0),
(153, 0, 2, 'VÃ¡Å¡ ÃºÄet byl zabanovÃ¡n a takÃ© odstranÄ›n ze vÅ¡ech skupin, jelikoÅ¾ mÃ¡te hodnÄ› trestnÃ½ch bodÅ¯.', '2019-07-25 15:33:13', 0),
(154, 0, 2, 'VÃ¡Å¡ ÃºÄet byl zabanovÃ¡n a takÃ© odstranÄ›n ze vÅ¡ech skupin, jelikoÅ¾ mÃ¡te hodnÄ› trestnÃ½ch bodÅ¯.', '2019-07-25 15:33:18', 0),
(155, 0, 2, 'VÃ¡Å¡ ÃºÄet byl zabanovÃ¡n a takÃ© odstranÄ›n ze vÅ¡ech skupin, jelikoÅ¾ mÃ¡te hodnÄ› trestnÃ½ch bodÅ¯.', '2019-07-25 15:34:08', 0),
(156, 0, 2, 'VÃ¡Å¡ ÃºÄet byl zabanovÃ¡n a takÃ© odstranÄ›n ze vÅ¡ech skupin, jelikoÅ¾ mÃ¡te hodnÄ› trestnÃ½ch bodÅ¯.', '2019-07-25 15:34:53', 0),
(157, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>7hbhj</span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ¡ pÅ™Ã­loha.', '2019-07-26 18:26:46', 0),
(158, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Cfha</span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ¡ pÅ™Ã­loha.', '2019-07-26 18:31:11', 0),
(159, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Hdbx</span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ¡ pÅ™Ã­loha.', '2019-07-26 18:31:21', 0),
(160, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Uvzgzg</span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ¡ pÅ™Ã­loha.', '2019-07-26 18:32:02', 0),
(161, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Cabrio</span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ¡ pÅ™Ã­loha.', '2019-07-26 18:34:03', 0),
(162, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'></span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ¡ pÅ™Ã­loha.', '2019-07-26 18:34:14', 0),
(163, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Å½ebyna</span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ¡ pÅ™Ã­loha.', '2019-07-26 18:34:43', 0),
(164, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>HvÄ›zduBruce</span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ½ obsah. <span class=\'font-weight-bold text-danger\'>Byl VÃ¡m pÅ™ipsÃ¡n trestnÃ½ bod!</span>.', '2019-07-26 18:38:15', 0),
(165, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Av kolik budes</span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ¡ pÅ™Ã­loha. <span class=\'font-weight-bold text-danger\'>Byl VÃ¡m pÅ™ipsÃ¡n trestnÃ½ bod!</span>.', '2019-07-26 18:38:35', 0),
(166, 0, 1, 'VÃ¡Å¡ ÃºÄet byl zabanovÃ¡n a takÃ© odstranÄ›n ze vÅ¡ech skupin, z dÅ¯vodu velkÃ©ho mnoÅ¾stvÃ­ trestnÃ½ch bodÅ¯.', '2019-07-26 18:40:16', 0),
(167, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Acourl</span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ¡ pÅ™Ã­loha.', '2019-07-26 18:44:18', 0),
(168, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Asdafa</span> byla ÃºspÄ›Å¡nÄ› schvÃ¡lena uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-07-26 18:47:02', 0),
(169, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Jdj</span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ¡ pÅ™Ã­loha.', '2019-07-26 18:49:05', 0),
(170, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Gtiokb</span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ¡ pÅ™Ã­loha.', '2019-07-26 18:49:24', 0),
(171, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>ASDASIDJ</span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ¡ pÅ™Ã­loha.', '2019-07-26 19:01:18', 0),
(172, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Hasdolasdo</span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ¡ pÅ™Ã­loha.', '2019-07-26 19:01:50', 0),
(173, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>HOLOLO2</span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ¡ pÅ™Ã­loha. <span class=\'font-weight-bold text-danger\'>Byl VÃ¡m pÅ™ipsÃ¡n trestnÃ½ bod!</span>.', '2019-07-26 19:04:30', 0),
(174, 0, 2, 'VÃ¡Å¡ ÃºÄet byl zabanovÃ¡n a takÃ© odstranÄ›n ze vÅ¡ech skupin, z dÅ¯vodu velkÃ©ho mnoÅ¾stvÃ­ trestnÃ½ch bodÅ¯.', '2019-07-26 19:14:00', 0),
(175, 0, 2, 'VÃ¡Å¡ ÃºÄet byl odblokovÃ¡n. Pokud si myslÃ­te, Å¾e doÅ¡lo k chybÄ›, mÅ¯Å¾ete vytvoÅ™it <a href=\'tickets.php\' class=\'font-weight-bold text-primary\'>novÃ½ tiket</a>.', '2019-07-26 19:14:51', 0),
(176, 0, 2, 'VÃ¡Å¡ ÃºÄet byl zabanovÃ¡n a takÃ© odstranÄ›n ze vÅ¡ech skupin.', '2019-07-26 21:56:05', 0),
(177, 0, 2, 'VÃ¡Å¡ ÃºÄet byl odblokovÃ¡n. Pokud si myslÃ­te, Å¾e doÅ¡lo k chybÄ›, mÅ¯Å¾ete vytvoÅ™it <a href=\'tickets.php\' class=\'font-weight-bold text-primary\'>novÃ½ tiket</a>.', '2019-07-26 21:56:54', 0),
(178, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Fuhwu</span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ½ obsah. <span class=\'font-weight-bold text-danger\'>Byl VÃ¡m pÅ™ipsÃ¡n trestnÃ½ bod!</span>.', '2019-07-27 15:17:09', 0),
(179, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Efvbvv</span> byla zamÃ­tnuta uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Å patnÃ½ obsah. <span class=\'font-weight-bold text-danger\'>Byl VÃ¡m pÅ™ipsÃ¡n trestnÃ½ bod!</span>.', '2019-07-27 15:19:34', 0),
(180, 0, 2, 'VÃ¡Å¡ ÃºÄet byl odblokovÃ¡n. Pokud si myslÃ­te, Å¾e doÅ¡lo k chybÄ›, mÅ¯Å¾ete vytvoÅ™it <a href=\'tickets.php\' class=\'font-weight-bold text-primary\'>novÃ½ tiket</a>.', '2019-07-27 15:33:17', 0),
(181, 0, 2, 'VÃ¡Å¡ ÃºÄet byl odblokovÃ¡n. Pokud si myslÃ­te, Å¾e doÅ¡lo k chybÄ›, mÅ¯Å¾ete vytvoÅ™it <a href=\'tickets.php\' class=\'font-weight-bold text-primary\'>novÃ½ tiket</a>.', '2019-07-27 15:34:14', 0),
(182, 0, 2, 'VÃ¡Å¡ ÃºÄet byl zabanovÃ¡n a takÃ© odstranÄ›n ze vÅ¡ech skupin, z dÅ¯vodu velkÃ©ho mnoÅ¾stvÃ­ trestnÃ½ch bodÅ¯.', '2019-07-27 15:34:22', 0),
(183, 0, 2, 'VÃ¡Å¡ ÃºÄet byl odblokovÃ¡n. Pokud si myslÃ­te, Å¾e doÅ¡lo k chybÄ›, mÅ¯Å¾ete vytvoÅ™it <a href=\'tickets.php\' class=\'font-weight-bold text-primary\'>novÃ½ tiket</a>.', '2019-07-27 15:46:03', 0),
(184, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ testu <span class=\'font-weight-bold\'>Item answers_checked</span> byla ÃºspÄ›Å¡nÄ› schvÃ¡lena uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-07-28 15:22:40', 0),
(185, 0, 1, '<span class=\'text-secondary\'>Gratulujeme k vÃ½hÅ™e v naÅ¡Ã­ loterii!</span> UmÃ­stil/-a jste se na 1. mÃ­stÄ› - VaÅ¡e vÃ½hra je 600 KÄ', '2019-08-01 19:48:26', 0),
(186, 0, 2, '<span class=\'text-secondary\'>Gratulujeme k vÃ½hÅ™e v naÅ¡Ã­ loterii!</span> UmÃ­stil/-a jste se na 2. mÃ­stÄ› - VaÅ¡e vÃ½hra je 300 KÄ', '2019-08-01 19:48:26', 0),
(187, 0, 1, '<span class=\'text-secondary\'>Gratulujeme k vÃ½hÅ™e v naÅ¡Ã­ loterii!</span> UmÃ­stil/-a jste se na 3. mÃ­stÄ› - VaÅ¡e vÃ½hra je 100 KÄ', '2019-08-01 19:48:26', 0),
(188, 0, 1, '<span class=\'text-secondary\'>Gratulujeme k vÃ½hÅ™e v naÅ¡Ã­ loterii!</span> UmÃ­stil/-a jste se na 1. mÃ­stÄ› - VaÅ¡e vÃ½hra je 600 KÄ', '2019-08-01 19:54:35', 0),
(189, 0, 2, '<span class=\'text-secondary\'>Gratulujeme k vÃ½hÅ™e v naÅ¡Ã­ loterii!</span> UmÃ­stil/-a jste se na 2. mÃ­stÄ› - VaÅ¡e vÃ½hra je 300 KÄ', '2019-08-01 19:54:35', 0),
(190, 0, 1, '<span class=\'text-secondary\'>Gratulujeme k vÃ½hÅ™e v naÅ¡Ã­ loterii!</span> UmÃ­stil/-a jste se na 3. mÃ­stÄ› - VaÅ¡e vÃ½hra je 100 KÄ', '2019-08-01 19:54:35', 0),
(191, 0, 2, 'VaÅ¡e Å¾Ã¡dost o vstup do skupiny validÃ¡torÅ¯ byla <span class=\'text-secondary font-weight-bold\'>odmÃ­tnuta</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Nevyhovujete poÅ¾adavkÅ¯m.', '2019-08-02 15:09:03', 0),
(192, 0, 2, 'VÃ¡Å¡ ÃºÄet byl zabanovÃ¡n a takÃ© odstranÄ›n ze vÅ¡ech skupin.', '2019-08-02 15:40:51', 0),
(193, 0, 2, 'VÃ¡Å¡ ÃºÄet byl odblokovÃ¡n. Pokud si myslÃ­te, Å¾e doÅ¡lo k chybÄ›, mÅ¯Å¾ete vytvoÅ™it <a href=\'tickets.php\' class=\'font-weight-bold text-primary\'>novÃ½ tiket</a>.', '2019-08-02 15:41:58', 0),
(194, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ Å¡koly  byla <span class=\'text-success font-weight-bold\'>schvÃ¡lena</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-08-08 20:40:43', 0),
(195, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ Å¡koly  byla <span class=\'text-success font-weight-bold\'>schvÃ¡lena</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-08-09 21:17:56', 0),
(196, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ Å¡koly  byla <span class=\'text-success font-weight-bold\'>schvÃ¡lena</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-08-09 21:21:22', 0),
(197, 0, 0, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ Å¡koly  byla <span class=\'text-danger font-weight-bold\'>odmÃ­tnuta</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Tato Å¡kola neexistuje.', '2019-08-09 21:21:53', 0),
(198, 0, 0, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ Å¡koly FAS byla <span class=\'text-danger font-weight-bold\'>odmÃ­tnuta</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. DÅ¯vod: Tato Å¡kola neexistuje.', '2019-08-09 21:23:18', 0),
(199, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ Å¡koly  byla <span class=\'text-success font-weight-bold\'>schvÃ¡lena</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-08-10 15:49:06', 0),
(200, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ Å¡koly  byla <span class=\'text-success font-weight-bold\'>schvÃ¡lena</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-08-10 16:02:51', 0),
(201, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ Å¡koly  byla <span class=\'text-success font-weight-bold\'>schvÃ¡lena</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-08-10 16:05:00', 0),
(202, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ Å¡koly  byla <span class=\'text-success font-weight-bold\'>schvÃ¡lena</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-08-10 16:29:35', 0),
(203, 0, 1, 'VaÅ¡e Å¾Ã¡dost o pÅ™idÃ¡nÃ­ Å¡koly  byla <span class=\'text-success font-weight-bold\'>schvÃ¡lena</span> uÅ¾ivatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-08-10 16:44:50', 0),
(204, 0, 1, 'Vaše žádost o přidání školy  byla <span class=\'text-success font-weight-bold\'>schválena</span> uživatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-08-10 19:29:18', 0),
(205, 0, 1, 'Vaše žádost o přidání školy  byla <span class=\'text-success font-weight-bold\'>schválena</span> uživatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-08-10 20:25:24', 0),
(206, 1, 61, 'jolka_response\r\n', '2019-08-11 14:37:41', 0),
(207, 0, 1, '<span class=\'font-weight-bold\'>KÓDY: </span>Váš <a class=\'text-primary font-weight-bold\' data-toggle=\'modal\' data-target=\'#vipInfoModal\'>vip status</a> byl úspěšně aktivován! Datum expirace: <span class=\'font-weight-bold\'>2019-09-01 16:09:52</span>', '2019-08-31 16:09:52', 0),
(208, 0, 1, 'Váš vip status vypršel!', '2019-09-04 10:43:16', 0),
(209, 0, 63, 'Vaše žádost o přidání testu <span class=\'font-weight-bold\'>HHHheee</span> byla úspěšně schválena uživatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=6\'><u>kololo</u></a>.', '2019-09-04 10:51:21', 0),
(210, 0, 10, 'Váš účet byl odblokován. Pokud si myslíte, že došlo k chybě, můžete vytvořit <a href=\'tickets.php\' class=\'font-weight-bold text-primary\'>nový tiket</a>.', '2019-09-04 16:20:23', 0),
(211, 0, 63, 'Vaše žádost o výběr 95 Kč byla schválena uživatelem <a class=\'text-primary font-weight-bold\' href=\'/testshop/profile_show.php?profile_id=1\'><u>admin</u></a>. Peníze budete mít na účtě do 3 pracovních dní.', '2019-09-04 18:01:40', 0),
(212, 0, 1, 'Vaše žádost o přidání školy  byla <span class=\'text-success font-weight-bold\'>schválena</span> uživatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>.', '2019-09-29 19:19:12', 0),
(213, 0, 1, 'Vaše žádost o změnu školy na Mosíkkuuum234 43 byla <span class=\'text-danger font-weight-bold\'>odmítnuta</span> uživatelem <a class=\'text-primary font-weight-bold\' href=\'profile_show.php?profile_id=1\'><u>admin</u></a>. Důvod: zoryik+ěšč+ěš+ěš.', '2019-09-29 19:20:11', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE `news` (
  `news_id` int(11) NOT NULL,
  `news_createdby` int(11) NOT NULL,
  `news_title` varchar(50) COLLATE utf8_bin NOT NULL,
  `news_content` varchar(1000) COLLATE utf8_bin NOT NULL,
  `news_date` datetime NOT NULL,
  `news_visible` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `news`
--

INSERT INTO `news` (`news_id`, `news_createdby`, `news_title`, `news_content`, `news_date`, `news_visible`) VALUES
(1, 0, 'Hello', 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Maxime, ex, neque ipsam eligendi unde amet saepe hic soluta voluptatem assumenda esse sunt mollitia ducimus, dolores quam quos aspernatur suscipit deserunt voluptate est corrupti asperiores magni. Exercitationem molestias ratione soluta sint neque assumenda distinctio et! Error quam ducimus cum repudiandae consequuntur?', '2019-07-07 00:00:00', 1),
(2, 0, 'How are you', 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Debitis placeat quis exercitationem deserunt quaerat doloremque deleniti aspernatur cupiditate voluptatum officia maxime quam quisquam molestiae, aliquam atque aperiam iste quidem quasi assumenda, possimus eius illo non repudiandae. Vero impedit iste tempore?', '2019-07-06 00:00:00', 1),
(3, 0, 'Lolki', 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Debitis placeat quis exercitationem deserunt quaerat doloremque deleniti aspernatur cupiditate voluptatum officia maxime quam quisquam molestiae, aliquam atque aperiam iste quidem quasi assumenda, possimus eius illo non repudiandae. Vero impedit iste tempore?', '2019-07-05 00:00:00', 1),
(4, 0, 'Nova aktualizace', 'Dobry den, zde muzete najit detailnejsi poodrobnosti!\r\n<button class=\"btn btn-success\">asdasd</button>', '0000-00-00 00:00:00', 0),
(5, 0, 'Test', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Sapiente ipsam enim laboriosam quaerat adipisci. Molestias animi aperiam fuga, expedita nisi sed iste numquam labore ad magni nihil, error debitis repellat eum eaque incidunt iure dolores! Dolorem ex placeat tempora, libero praesentium quae soluta facilis ducimus, voluptate ratione quas animi voluptatum.', '2019-07-07 14:51:38', 1),
(6, 0, 'jhuj', 'jkkjkjk', '2019-07-07 17:24:57', 0),
(7, 0, 'asasd', 'asdasd', '2019-07-18 17:06:19', 1),
(8, 0, 'test', 'asdasd', '2019-07-19 15:27:25', 1),
(9, 1, 'tesd', 'dsd', '2019-07-19 15:44:13', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `referrals`
--

CREATE TABLE `referrals` (
  `id` int(11) NOT NULL,
  `referrals_userid` int(11) NOT NULL,
  `referrals_userby` int(11) NOT NULL,
  `referrals_money` float NOT NULL DEFAULT '0' COMMENT 'Withdraw money for referred by user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `referrals`
--

INSERT INTO `referrals` (`id`, `referrals_userid`, `referrals_userby`, `referrals_money`) VALUES
(1, 62, 1, 0),
(2, 2, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `report`
--

CREATE TABLE `report` (
  `report_id` int(11) NOT NULL,
  `report_from` int(11) NOT NULL,
  `report_on` int(11) NOT NULL,
  `report_item` int(11) NOT NULL,
  `report_message` varchar(200) COLLATE utf8_bin NOT NULL,
  `report_date` datetime NOT NULL,
  `report_description` varchar(200) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `report`
--

INSERT INTO `report` (`report_id`, `report_from`, `report_on`, `report_item`, `report_message`, `report_date`, `report_description`) VALUES
(2, 1, 6, 23, '', '2019-06-29 16:48:12', '1312312313'),
(3, 61, 1, 51, '', '2019-07-18 19:35:09', 'Dassd');

-- --------------------------------------------------------

--
-- Структура таблицы `school`
--

CREATE TABLE `school` (
  `school_id` int(11) NOT NULL,
  `school_name` varchar(40) COLLATE utf8_bin NOT NULL,
  `district_id` int(11) NOT NULL,
  `visible` int(11) NOT NULL DEFAULT '1',
  `school_created` datetime NOT NULL,
  `added_by` int(11) NOT NULL,
  `checked_by` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `school`
--

INSERT INTO `school` (`school_id`, `school_name`, `district_id`, `visible`, `school_created`, `added_by`, `checked_by`) VALUES
(1, 'Gymnázium na Pražačce', 1, 1, '2019-06-15 05:31:25', 0, 0),
(2, 'Dukelska', 2, 1, '0000-00-00 00:00:00', 0, 0),
(5, 'Zakladni skola Mendelova 550', 3, 1, '0000-00-00 00:00:00', 0, 0),
(14, 'pčelovodík', 3, 1, '0000-00-00 00:00:00', 0, 0),
(15, 'asdasdsd', 1, 1, '0000-00-00 00:00:00', 0, 0),
(26, '11111dasd', 2, 1, '2019-06-21 20:22:40', 1, 0),
(28, 'asd', 2, 1, '2019-07-12 21:15:17', 1, 0),
(34, 'Testik', 3, 1, '0000-00-00 00:00:00', 0, 0),
(35, 'Testik', 3, 1, '0000-00-00 00:00:00', 0, 0),
(36, 'Testik', 3, 1, '0000-00-00 00:00:00', 0, 0),
(37, 'Testik', 3, 1, '0000-00-00 00:00:00', 0, 0),
(38, 'Testik', 3, 1, '0000-00-00 00:00:00', 0, 0),
(39, 'Testik', 3, 1, '0000-00-00 00:00:00', 0, 0),
(40, 'Tesasdasdtik', 3, 1, '0000-00-00 00:00:00', 0, 0),
(41, 'Testiksadsddddd', 3, 1, '0000-00-00 00:00:00', 0, 0),
(42, 'Tefffffffffffstik', 3, 1, '0000-00-00 00:00:00', 0, 0),
(43, 'KKKKK', 3, 1, '0000-00-00 00:00:00', 0, 0),
(56, 'Gymn&aacute;zium Otevřen&eacute;ho', 32, 1, '2019-08-10 19:29:18', 1, 1),
(57, 'Středn&iacute; &scaron;kola Kulk&aacute;', 33, 1, '2019-08-10 20:25:24', 1, 1),
(58, 'Mosíkkuuum234 43', 34, 1, '2019-09-29 19:19:12', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `school_add`
--

CREATE TABLE `school_add` (
  `sa_id` int(11) NOT NULL,
  `sa_userid` int(11) NOT NULL,
  `sa_city` varchar(30) COLLATE utf8_bin NOT NULL,
  `sa_district` varchar(30) COLLATE utf8_bin NOT NULL,
  `sa_school` varchar(50) COLLATE utf8_bin NOT NULL,
  `sa_date` datetime NOT NULL,
  `sa_confirmedby` int(11) NOT NULL,
  `sa_confirmed` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `school_add`
--

INSERT INTO `school_add` (`sa_id`, `sa_userid`, `sa_city`, `sa_district`, `sa_school`, `sa_date`, `sa_confirmedby`, `sa_confirmed`) VALUES
(1, 1, 'Pardubice', 'Pardubice - venkov', 'Gymn&aacute;zium BÄ›lohorsk&aacute; 221/', '2019-08-08 15:32:45', 1, '2019-08-09 21:17:56'),
(3, 1, 'Prague', 'Praha test', 'Test', '2019-08-08 19:27:36', 1, '2019-08-09 21:21:22'),
(4, 1, 'Pec&iacute;nov', 'Centrum', 'Gymn&aacute;zium BÄ›lohorsk&aacute; 221/1', '2019-08-10 15:48:58', 1, '2019-08-10 15:49:06'),
(5, 1, 'Test&eacute;', 'asdÅ¡a', 'fffÄ›&scaron; asd', '2019-08-10 16:02:40', 1, '2019-08-10 16:02:51'),
(6, 1, 'Mur&eacute;n', 'KorÃ©n', 'asd&scaron;Ä›', '2019-08-10 16:04:48', 1, '2019-08-10 16:05:00'),
(7, 1, 'Gymn&aacute;zium na Podol&iacu', 'DobrÃ½ den', 'TEst&iacute;k', '2019-08-10 16:18:20', 0, '0000-00-00 00:00:00'),
(8, 1, 'Zkou&scaron;ka', 'AhojÃ¡Ã¡Å¡Ã­', 'Nun&aacute;l', '2019-08-10 16:28:56', 1, '2019-08-10 16:29:35'),
(9, 1, '&yacute;Å¾Å™ÄÄ›', 'Å¾ÄÅ¡Ä', 'Ä›Ä+', '2019-08-10 16:39:28', 1, '2019-08-10 16:44:50'),
(10, 1, 'pÄelovod', 'AjÃ¡kÃ¡kÃ¡', 'K&aacute;ja', '2019-08-10 16:46:20', 0, '0000-00-00 00:00:00'),
(11, 1, 'Soběhrdy', 'Soběhrdy - centrum', 'Gymn&aacute;zium Otevřen&eacute;ho', '2019-08-10 19:28:25', 1, '2019-08-10 19:29:18'),
(12, 1, 'Praha', 'Praha - Měcholupy', 'Středn&iacute; &scaron;kola Kulk&aacute;&aacute;&i', '2019-08-10 20:25:14', 1, '2019-08-10 20:25:24'),
(13, 1, 'Soběhrdy', 'Kos+čěk', 'Mosíkkuuum234 43', '2019-09-29 19:18:58', 1, '2019-09-29 19:19:12');

-- --------------------------------------------------------

--
-- Структура таблицы `school_change`
--

CREATE TABLE `school_change` (
  `id` int(11) NOT NULL,
  `change_school_id_from` int(11) NOT NULL,
  `change_school_id_to` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_date` datetime NOT NULL,
  `last_setdate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `school_change`
--

INSERT INTO `school_change` (`id`, `change_school_id_from`, `change_school_id_to`, `user_id`, `request_date`, `last_setdate`) VALUES
(7, 1, 1, 1, '2019-06-21 21:30:26', '2019-06-12 21:26:45'),
(10, 1, 15, 1, '2019-08-02 15:32:51', '2019-07-13 15:21:19'),
(12, 1, 2, 1, '2019-08-07 19:37:05', '2019-07-13 15:21:19'),
(13, 1, 2, 1, '2019-08-07 19:37:17', '2019-07-13 15:21:19'),
(15, 1, 5, 1, '2019-08-07 19:53:41', '2019-07-13 15:21:19'),
(16, 1, 57, 1, '2019-08-10 20:25:39', '2019-08-10 19:30:24');

-- --------------------------------------------------------

--
-- Структура таблицы `shop`
--

CREATE TABLE `shop` (
  `item_id` int(11) NOT NULL,
  `teacher` varchar(30) COLLATE utf8_bin NOT NULL,
  `item_subject` varchar(5) COLLATE utf8_bin NOT NULL,
  `school_class` int(11) NOT NULL DEFAULT '0',
  `item_name` varchar(70) COLLATE utf8_bin NOT NULL,
  `item_createdby_username` varchar(20) COLLATE utf8_bin NOT NULL,
  `item_description` varchar(200) COLLATE utf8_bin NOT NULL,
  `item_price` int(11) NOT NULL,
  `item_createdby_userid` int(11) DEFAULT NULL,
  `school_id` int(11) NOT NULL,
  `item_answers` int(11) NOT NULL COMMENT '1-excellent answers, 0-just questions without answers',
  `likes` int(11) NOT NULL DEFAULT '0',
  `dislikes` int(11) NOT NULL DEFAULT '0',
  `bought_times` int(11) NOT NULL DEFAULT '0',
  `visible` int(11) NOT NULL DEFAULT '1',
  `item_type` int(11) NOT NULL,
  `checked` int(11) NOT NULL DEFAULT '0',
  `create_date` datetime NOT NULL,
  `confirmed_date` datetime NOT NULL,
  `confirmed_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `shop`
--

INSERT INTO `shop` (`item_id`, `teacher`, `item_subject`, `school_class`, `item_name`, `item_createdby_username`, `item_description`, `item_price`, `item_createdby_userid`, `school_id`, `item_answers`, `likes`, `dislikes`, `bought_times`, `visible`, `item_type`, `checked`, `create_date`, `confirmed_date`, `confirmed_by`) VALUES
(8, '', 'Akv', 0, 'Jnaskjnakdnkjasnds ad asd asdasdalkjsdnjkasnj', 'admin', 'Jasndkansdkjnakjsdn kjans dk', 110, 6, 1, 0, 2, 0, 1, 0, 1, 1, '2019-05-02 21:15:30', '2019-05-02 21:19:05', 0),
(11, '', 'M', 0, 'Asdasd', 'admin', 'Asdasd', 111, 1, 1, 0, 0, 0, 0, 1, 0, 1, '2019-05-13 13:53:17', '2019-05-13 13:53:21', 0),
(13, '', 'Aj', 2, 'Hello', 'admin', 'Asdasd', 100, 1, 1, 0, 0, 0, 1, 1, 0, 1, '2019-05-13 21:24:56', '2019-05-13 21:25:09', 0),
(14, '', 'Cj', 3, 'b bhbhbhhhbh', 'admin', 'iyiu', 111, 1, 1, 0, 0, 0, 0, 1, 1, 1, '2019-05-13 21:45:21', '2019-05-13 21:45:39', 1),
(16, 'Kooook', 'D', 1, 'HUHUUUHHUhuh', 'admin', 'Huhuhuhuuhu', 100, 1, 1, 0, 0, 0, 0, 1, 1, 1, '2019-05-14 15:52:44', '2019-05-14 15:53:03', 1),
(17, 'asdads', 'Zsv', 1, 'Asdjasdad', 'admin', 'Iuahsdiuiasuhdiu', 100, 1, 1, 0, 0, 0, 1, 1, 1, 1, '2019-05-14 15:55:11', '2019-05-14 15:56:27', 1),
(18, 'Marmok', 'On', 1, 'Pitukh', 'admin', 'Hello', 100, 1, 1, 0, 0, 0, 2, 1, 0, 1, '2019-05-14 15:57:20', '2019-05-14 15:57:24', 1),
(19, 'Marmok', 'Pr', 1, 'Testttt', 'admin', 'Asdasd', 111, 1, 1, 0, 0, 0, 0, 1, 1, 1, '2019-05-17 21:38:12', '2019-05-17 21:38:17', 1),
(23, 'Marmok', 'Dt', 2, 'Fafafafafaf', 'kololo', '1312312313m ajsdiasdk oask odkoaksod oasdokoaskd ok asodko kx cozxmkvcmlckmvlkxlkcvmkamsdk aksodkoak spd', 31, 6, 1, 1, 0, 1, 2, 0, 1, 1, '2019-05-27 21:26:39', '2019-06-03 20:30:11', 1),
(27, 'Marmok', 'UuCj', 1, 'Ffaaffa', 'admin', 'Asdasd', 434, 1, 1, 0, 0, 0, 0, 1, 0, 1, '2019-05-29 20:19:12', '2019-05-29 20:19:19', 1),
(28, 'Kooook', 'Vv', 2, 'Bbbbb', 'admin', 'Bbbbbbb', 144, 1, 1, 0, 1, 0, 1, 1, 1, 1, '2019-05-29 20:19:55', '2019-05-29 20:20:01', 1),
(29, 'Konstantinova', 'Cj', 3, 'Konstanknknknk', 'admin', 'Nknknknknnnk', 100, 1, 1, 0, 1, 1, 4, 1, 1, 1, '2019-05-29 21:16:09', '2019-05-29 21:16:19', 1),
(36, 'Konstantinova', 'Zsv', 1, 'Asaasd', 'admin', 'Asdasdasd', 123, 1, 1, 0, 0, 0, 0, 1, 1, 1, '2019-06-16 16:23:40', '0000-00-00 00:00:00', 0),
(37, 'Konstantinova', 'Aa', 2, 'Sad', 'admin', 'Sdasd', 111, 1, 1, 0, 0, 0, 1, 1, 0, 1, '2019-06-16 16:24:36', '2019-07-16 20:01:55', 1),
(39, 'Kooook', 'On', 2, 'Asdasfgfaf', 'admin', 'Asffafcx', 111, 1, 1, 0, 0, 0, 0, 1, 1, 1, '2019-06-16 16:36:16', '0000-00-00 00:00:00', 0),
(40, 'Konstantinova', 'qq', 3, 'Afafga', 'admin', 'Afe', 111, 1, 1, 0, 0, 0, 0, 1, 0, 1, '2019-06-16 16:37:31', '0000-00-00 00:00:00', 0),
(41, 'Kooook', 'bb', 2, 'Asdasdvvv', 'admin', 'Fasf', 111, 1, 1, 0, 0, 0, 0, 1, 1, 1, '2019-06-16 16:38:44', '0000-00-00 00:00:00', 0),
(42, 'Konstantinova', 'On', 3, 'Gvvvv', 'admin', 'Acxcx', 111, 1, 1, 0, 0, 0, 0, 1, 1, 1, '2019-06-16 16:48:18', '0000-00-00 00:00:00', 0),
(43, 'Kooook', 'Zsv', 3, 'Adsasdasdaa', 'admin', 'Asdasdasdasd', 111, 1, 1, 0, 0, 0, 0, 1, 1, 1, '2019-06-16 16:50:03', '0000-00-00 00:00:00', 0),
(44, 'Asdads', 'Zsv', 2, 'Asdasdafaz', 'admin', 'Axcz', 111, 1, 1, 0, 0, 0, 0, 1, 1, 1, '2019-06-16 16:52:53', '0000-00-00 00:00:00', 0),
(46, 'Konstantinova', 'Fy', 2, 'OTHER SCHOOL', 'imagee', 'OTHER SCHOOL', 100, 55, 2, 0, 0, 0, 0, 1, 1, 1, '2019-06-21 18:17:58', '2019-06-21 20:18:43', 1),
(48, '', 'OAO', 0, 'Jnaskjnakdnkjasnds ad asd asdasdalkjsdnjkasnj', 'admin', 'Jasndkansdkjnakjsdn kjans dk', 110, 6, 1, 0, 1, 0, 1, 0, 1, 1, '2019-05-02 21:15:30', '2019-05-02 21:19:05', 0),
(49, '', 'PPP', 0, 'Jnaskjnakdnkjasnds ad asd asdasdalkjsdnjkasnj', 'admin', 'Jasndkansdkjnakjsdn kjans dk', 110, 6, 1, 0, 1, 0, 2, 0, 1, 1, '2019-05-02 21:15:30', '2019-05-02 21:19:05', 0),
(51, 'Konstantinova', 'Asd', 1, 'Asda', 'admin', 'Dassdk joiajic opxokcokzxc kpoqs kpok wpoqkpoksp kx mcmz l,xm c.,mklasmdlkmlkmlkmaklsm dlkm asd', 111, 1, 1, 0, 0, 0, 1, 1, 0, 1, '2019-06-28 12:50:59', '2019-07-17 19:23:10', 2),
(53, 'Kooook', 'Aj', 1, 'Asdafa', 'admin', 'Faf', 111, 1, 1, 0, 0, 0, 1, 1, 1, 1, '2019-06-28 12:56:19', '2019-07-26 18:47:01', 1),
(67, 'Asdads', 'Cj', 2, 'Xjak', 'admin', 'Disk', 167, 1, 1, 0, 0, 0, 0, 0, 0, 0, '2019-06-28 13:26:12', '0000-00-00 00:00:00', 0),
(68, 'Asdads', 'Aj', 2, 'Dhxk', 'admin', 'D&iacute;ky', 167, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-06-28 13:27:20', '0000-00-00 00:00:00', 0),
(69, 'Kooook', 'Nj', 2, 'Sgxb', 'admin', 'Dhab', 150, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-06-28 13:28:40', '0000-00-00 00:00:00', 0),
(70, 'Konstantinova', 'Nj', 2, 'Dhwux', 'admin', 'Chsbx', 100, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-06-28 13:29:31', '0000-00-00 00:00:00', 0),
(71, 'Konstantinova', 'Nj', 2, 'Hdbdj', 'admin', 'Sgbsn', 100, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-06-28 13:30:10', '0000-00-00 00:00:00', 0),
(72, 'Kooook', 'Nj', 1, 'Euhdns', 'admin', 'Nshnd', 100, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-06-28 13:31:42', '0000-00-00 00:00:00', 0),
(73, 'Konstantinova', 'Cj', 2, 'Ghujb', 'admin', 'Xhmsb', 107, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-06-28 13:41:14', '0000-00-00 00:00:00', 0),
(75, 'Konstantinova', 'M', 2, 'Iasdi', 'user02', 'KKKKKN', 50, 57, 1, 0, 0, 0, 0, 1, 0, 0, '2019-06-28 21:04:48', '0000-00-00 00:00:00', 0),
(76, 'Konstantinova', 'Nj', 3, 'HOLOLOLO', 'admin', 'KOKOKO', 100, 1, 1, 0, 0, 0, 0, 0, 0, 0, '2019-07-02 15:55:32', '0000-00-00 00:00:00', 0),
(80, 'Kooook', 'Fj', 1, 'From moblie phone', 'admin', 'Helpme url adresy a kontakty mapa webu prohl&aacute;&scaron;en&iacute; o pÅ™&iacute;stupnosti grafick&aacute; verze textov&aacute; verze vytisknout 5 str&aacute;nku 5 a', 100, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 19:27:29', '0000-00-00 00:00:00', 0),
(81, 'Kooook', 'Fj', 1, 'Help', 'admin', 'Chutna', 200, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 19:29:51', '0000-00-00 00:00:00', 0),
(82, 'Konstantinova', 'Fj', 1, 'L&iacute;stkÅ¯', 'admin', 'Brzd&iacute;', 500, 1, 1, 0, 0, 0, 0, 0, 0, 0, '2019-07-02 19:30:42', '0000-00-00 00:00:00', 0),
(83, 'Konstantinova', 'Fj', 1, 'Ggg', 'admin', 'Brzd&iacute;', 500, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 19:32:55', '0000-00-00 00:00:00', 0),
(84, 'Kooook', 'Fj', 1, 'Hdjs', 'admin', 'Brzd&iacute;', 100, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 19:35:31', '0000-00-00 00:00:00', 0),
(85, 'Konstantinova', 'Fj', 1, 'VraÅ¾dÄ›n&iacute;a', 'admin', 'Helpme', 500, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 19:42:39', '0000-00-00 00:00:00', 0),
(86, 'Konstantinova', 'Fj', 1, 'VraÅ¾dÄ›n&iacute;na', 'admin', 'Chutna', 200, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 19:44:03', '0000-00-00 00:00:00', 0),
(87, 'Kooook', 'Cj', 1, 'Helpmeurl', 'admin', 'Helpme', 55, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 19:49:30', '0000-00-00 00:00:00', 0),
(88, 'Kooook', 'Nj', 1, 'Help metourlurl', 'admin', 'Helpmeurl', 55, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 19:51:04', '0000-00-00 00:00:00', 0),
(89, 'Konstantinova', 'Nj', 1, 'Sibdb', 'admin', 'Helpmeurl', 850, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 19:54:15', '0000-00-00 00:00:00', 0),
(90, 'Kooook', 'Cj', 1, 'Helpmeurla', 'admin', 'VraÅ¾dÄ›n&iacute; a kontakty mapa webu prohl&aacute;&scaron;en&iacute; o 5 pÅ™&iacute;stupnosti grafick&aacute; verze textov&aacute; verze vytisknout str&aacute;nku', 200, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 19:57:29', '0000-00-00 00:00:00', 0),
(91, 'Konstantinova', 'Cj', 3, 'Czxc', 'admin', 'Zxvzvzv', 111, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 20:33:08', '0000-00-00 00:00:00', 0),
(92, 'Kooook', 'Cj', 3, 'Fafsafasf', 'admin', 'Asfasfasf', 111, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 20:35:33', '0000-00-00 00:00:00', 0),
(93, 'Konstantinova', 'Aj', 2, 'Fasfa', 'admin', 'Sfasf', 111, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 20:37:11', '0000-00-00 00:00:00', 0),
(94, 'Konstantinova', 'Cj', 3, 'Oaooop', 'admin', 'VraÅ¾dÄ›n&iacute; a kontakty mapa webu prohl&aacute;&scaron;en&iacute; o 5 pÅ™&iacute;stupnosti grafick&aacute; verze textov&aacute; verze vytisknout str&aacute;nku', 69, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 20:38:46', '0000-00-00 00:00:00', 0),
(95, 'Konstantinova', 'M', 2, 'Fuchs', 'admin', 'Yarick', 500, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 20:52:35', '0000-00-00 00:00:00', 0),
(96, 'Konstantinova', 'Aj', 2, 'Iphonebzjbss', 'admin', 'Dhcjzjd', 500, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 21:02:17', '0000-00-00 00:00:00', 0),
(97, 'Konstantin', 'Ak', 4, 'Dikska', 'admin', 'Chajjs', 500, 1, 1, 0, 0, 0, 0, 0, 0, 0, '2019-07-02 21:05:01', '0000-00-00 00:00:00', 0),
(98, 'Konstantinova', 'Cj', 1, 'VraÅ¾dÄ›n&iacute;naurlaaaa', 'admin', 'VraÅ¾dÄ›n&iacute;naaaaaa', 500, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-02 21:30:54', '0000-00-00 00:00:00', 0),
(99, 'Konstantinova', 'Cj', 2, 'Kokoko', 'admin', 'Asdasdasd', 111, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-05 20:49:40', '0000-00-00 00:00:00', 0),
(100, 'Konstantinova', 'Cj', 3, 'Adadad', 'admin', 'Adadad', 111, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-05 20:50:33', '0000-00-00 00:00:00', 0),
(101, 'Kooook', 'Aj', 2, 'Gaggaag', 'admin', 'Gaag', 111, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-05 20:51:08', '0000-00-00 00:00:00', 0),
(102, 'Afd', 'Zcx', 2, 'Afafsfa', 'admin', 'Xczxczxczxc', 333, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-10 21:48:52', '0000-00-00 00:00:00', 0),
(103, 'Konstantinova', 'Aj', 2, 'MOmomoommoo', 'admin', 'Momomom', 333, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-10 21:50:06', '0000-00-00 00:00:00', 0),
(104, 'Konstantinova', 'Aj', 2, 'Afagxcv', 'admin', 'Cvvs', 234, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-10 21:51:05', '0000-00-00 00:00:00', 0),
(105, 'Konstantinova', 'Aj', 2, 'Try', 'admin', 'OKOKOO', 222, 1, 1, 0, 0, 0, 0, 0, 1, 0, '2019-07-10 21:53:32', '0000-00-00 00:00:00', 0),
(106, 'Kooook', 'Cj', 2, 'Ddsdf', 'admin', 'Fgvfc', 222, 1, 1, 1, 0, 0, 0, 0, 0, 0, '2019-07-10 21:55:01', '0000-00-00 00:00:00', 0),
(107, 'Konstantinova', 'Kknv', 2, 'Otec', 'testpp', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type a', 100, 61, 1, 0, 0, 0, 2, 1, 0, 1, '2019-07-18 19:39:16', '2019-07-19 21:21:56', 1),
(108, 'Konstantinova', 'Cj', 2, 'Item answers_checked', 'admin', 'Asdasdasdasd', 100, 1, 1, 1, 0, 0, 1, 1, 1, 1, '2019-07-28 15:11:49', '2019-07-28 15:22:40', 1),
(110, 'Konstantinova', 'Aj', 2, 'Test answers_not checked', 'admin', 'NOT CHECKED', 50, 1, 1, 0, 0, 0, 0, 1, 1, 0, '2019-07-28 15:14:01', '0000-00-00 00:00:00', 0),
(111, 'Kooook', 'M', 2, 'Zkou&scaron;&iacute;m h&aacute;čky a č&aacute', 'admin', '- pravopis\r\n- slov&iacute;čka\r\n- gramatika', 30, 1, 1, 1, 0, 0, 0, 1, 0, 0, '2019-08-11 14:46:00', '0000-00-00 00:00:00', 0),
(112, 'Konstantinova', 'On', 2, 'Zkou&scaron;&iacute;m h&aacute;čky a č&aacute', 'admin', 'Dobr&yacute; den adiasidkiasd', 31, 1, 1, 1, 0, 0, 0, 1, 0, 0, '2019-08-11 14:48:33', '0000-00-00 00:00:00', 0),
(113, '', '', 0, 'zkouška', '', '', 0, NULL, 1, 0, 0, 0, 0, 1, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(114, 'Konstantinova', 'Bi', 2, 'Zkouška přidávání testů', 'admin', 'Dobrý den, jak se máte? Já se mám dobře!', 33, 1, 1, 1, 0, 0, 0, 1, 1, 0, '2019-08-11 15:13:26', '0000-00-00 00:00:00', 0),
(115, 'Kooook', 'Fy', 1, 'Zkouška druhá o přidávání testů', 'admin', 'TO je dobrý vtípek řčš+', 31, 1, 1, 0, 0, 0, 0, 1, 1, 0, '2019-08-11 15:16:10', '0000-00-00 00:00:00', 0),
(116, 'Konstantinova', 'On', 1, 'čest má je zde', 'admin', '&lt;button&gt;řčžýáěš&lt;/button&gt;', 33, 1, 1, 1, 0, 0, 0, 1, 1, 0, '2019-08-11 15:17:20', '0000-00-00 00:00:00', 0),
(117, 'Konstantinova', 'Fy', 3, 'HHHheee', 'matoustest', 'Ausduasudiauhdiu', 30, 63, 1, 1, 0, 0, 1, 1, 0, 1, '2019-09-04 10:48:15', '2019-09-04 10:51:21', 6);

-- --------------------------------------------------------

--
-- Структура таблицы `shop_earn`
--

CREATE TABLE `shop_earn` (
  `shopearn_id` int(11) NOT NULL,
  `shopearn_itemid` int(11) NOT NULL,
  `shopearn_value` float NOT NULL,
  `shopearn_referralmoney` float NOT NULL DEFAULT '0',
  `shopearn_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `shop_earn`
--

INSERT INTO `shop_earn` (`shopearn_id`, `shopearn_itemid`, `shopearn_value`, `shopearn_referralmoney`, `shopearn_date`) VALUES
(1, 29, 16, 3, '2019-05-15 21:26:21'),
(3, 18, 16, 0, '2019-06-11 21:28:38'),
(4, 13, 22, 0, '2019-06-11 21:30:20'),
(5, 23, 9, 0, '2019-06-12 20:48:14'),
(6, 23, 9, 0, '2019-06-12 20:49:30'),
(13, 49, 29, 3, '2019-06-25 22:11:45'),
(14, 28, 43, 0, '2019-06-28 19:57:30'),
(15, 51, 29, 3, '2019-07-18 19:34:46'),
(16, 107, 27, 3, '2019-07-18 19:42:38'),
(17, 107, 30, 0, '2019-07-23 15:07:06'),
(18, 29, 27, 3, '2019-07-23 18:29:22'),
(19, 18, 25, 5, '2019-07-23 18:35:01'),
(20, 17, 25, 5, '2019-07-23 20:53:55'),
(21, 29, 28, 0, '2019-07-27 19:22:32'),
(22, 108, 28, 0, '2019-07-28 15:26:25'),
(23, 53, 31, 0, '2019-07-28 16:16:13'),
(24, 37, 31, 0, '2019-08-02 19:30:18'),
(25, 117, 8, 0, '2019-09-04 10:52:45');

-- --------------------------------------------------------

--
-- Структура таблицы `shop_remove_log`
--

CREATE TABLE `shop_remove_log` (
  `removed_id` int(11) NOT NULL,
  `removed_item` int(11) NOT NULL,
  `removed_by` int(11) NOT NULL,
  `removed_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `shop_remove_log`
--

INSERT INTO `shop_remove_log` (`removed_id`, `removed_item`, `removed_by`, `removed_time`) VALUES
(11, 23, 1, '2019-06-19 16:27:34');

-- --------------------------------------------------------

--
-- Структура таблицы `statistics`
--

CREATE TABLE `statistics` (
  `shop_earn` int(11) NOT NULL DEFAULT '0',
  `withdraw_earn` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `statistics`
--

INSERT INTO `statistics` (`shop_earn`, `withdraw_earn`) VALUES
(0, 136);

-- --------------------------------------------------------

--
-- Структура таблицы `statuses`
--

CREATE TABLE `statuses` (
  `status_id` int(11) NOT NULL,
  `status_userid` int(11) NOT NULL,
  `status` varchar(10) COLLATE utf8_bin NOT NULL,
  `status_expiration` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL,
  `ticket_createdby` int(11) NOT NULL,
  `ticket_title` varchar(100) COLLATE utf8_bin NOT NULL,
  `ticket_content` varchar(1000) COLLATE utf8_bin NOT NULL,
  `ticket_type` int(11) NOT NULL COMMENT '0 - Question, 1 - Bug, 2 - Suggestion',
  `ticket_answered` int(11) NOT NULL DEFAULT '0',
  `ticket_visible` int(11) NOT NULL DEFAULT '1',
  `ticket_created` datetime NOT NULL,
  `ticket_image` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `tickets`
--

INSERT INTO `tickets` (`ticket_id`, `ticket_createdby`, `ticket_title`, `ticket_content`, `ticket_type`, `ticket_answered`, `ticket_visible`, `ticket_created`, `ticket_image`) VALUES
(1, 1, 'How to asdasdasd?', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s.', 1, 0, 1, '2019-05-25 09:33:00', 'uploads/admin_cake-1.jpeg'),
(2, 2, 'How to asdasda?', '2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word.', 0, 0, 1, '2019-05-25 18:00:00', ''),
(3, 6, 'Fao asdasa?', 'Text, and a search for Lorem ipsum will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometime', 0, 1, 0, '2019-05-25 19:00:00', ''),
(4, 1, 'Really test?', 'Are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isnt anything.', 0, 0, 1, '2019-05-25 20:00:00', ''),
(5, 1, 'test', 'sadsdasdasdasdasd', 0, 1, 0, '2019-06-01 21:21:50', ''),
(44, 1, 'With image', 'Hello :) &lt;button&gt;asdasdasd&lt;/button&gt;', 0, 0, 1, '2019-06-01 22:25:30', 'ticket_images/admin_20190422_173504.jpg'),
(60, 1, 'aggagaoriginal', 'fasd', 1, 0, 1, '2019-06-19 19:46:00', 'ticket_images/admin_default.jpg'),
(61, 1, 'NEW PNG', 'adsasd', 1, 0, 1, '2019-06-19 19:47:48', 'ticket_images/admin_palm-fronds-and-sky.png'),
(62, 1, 'Mobilr phone', 'Testuji nahr&aacute;v&aacute;n&iacute; souborÅ¯. Aby to v&scaron;e bezvadnÄ› fungovalo.', 0, 1, 1, '2019-06-19 20:48:33', 'ticket_images/admin_15609700867891868738935.jpg'),
(63, 1, 'rrr', 'r', 1, 0, 1, '2019-06-20 16:45:37', 'ticket_images/admin_404.png'),
(64, 1, '', '', 1, 0, 0, '2019-06-20 16:47:19', ''),
(65, 1, 'hhbh', 'bhbhb', 0, 0, 1, '2019-06-20 16:52:43', 'ticket_images/admin_facebook.png'),
(66, 57, 'Hooo', 'KOKOKO', 0, 0, 1, '2019-06-28 21:06:01', 'ticket_images/user02_pexels-photo-730803.jpeg'),
(67, 1, 'Test image', 'From phone', 0, 0, 1, '2019-07-02 21:32:05', 'ticket_images/admin_20190701_065621.jpg'),
(68, 1, 'Image test .png', '.png', 0, 0, 1, '2019-07-02 21:33:47', 'ticket_images/admin_Screenshot_20190702-180845.png'),
(69, 57, 'sosik', 'rosik', 0, 0, 1, '2019-07-09 21:00:12', ''),
(70, 1, 'asd', 'Hello', 1, 0, 1, '2019-07-15 21:32:58', ''),
(71, 1, 'asdasdfff', 'asdafaf', 0, 0, 1, '2019-07-15 21:35:34', ''),
(72, 1, 'sdfd', 'fdfdf', 1, 0, 1, '2019-07-15 21:41:57', 'ticket_images/admin_admin_020719_19572920190628_213417(0).jpg'),
(73, 1, 'ASdasd', 'asasfaf', 0, 0, 0, '2019-07-16 15:52:43', 'ticket_images/admin_imagee_20190420_174342.jpg'),
(74, 1, 'ggg', 'fa', 0, 0, 1, '2019-07-16 15:53:32', 'ticket_images/admin_admin_020719_19572920190628_213408.jpg'),
(75, 61, 'Otec', 'sdzsdasdasd', 0, 0, 1, '2019-07-27 19:24:22', 'ticket_images/testpp_pexels-photo-730803.jpeg'),
(76, 1, 'suggestion', 'asdasd', 2, 0, 1, '2019-08-01 20:22:21', 'ticket_images/admin_admin_020719_19572920190628_213408.jpg'),
(77, 1, 'adsasd', 'KOkOOk', 2, 0, 1, '2019-08-01 20:49:06', 'ticket_images/admin_admin_15583750263751064670502.jpg'),
(78, 1, 'fa', 'fa', 1, 0, 1, '2019-08-01 20:49:17', 'ticket_images/admin_admin_020719_205237IMG_20190702_205148.jpg'),
(79, 1, 'afafafa', 'fafafaf', 0, 0, 1, '2019-08-01 20:49:28', '');

-- --------------------------------------------------------

--
-- Структура таблицы `ticket_comments`
--

CREATE TABLE `ticket_comments` (
  `comment_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `comment_createdby` int(11) NOT NULL,
  `comment_content` varchar(600) COLLATE utf8_bin NOT NULL,
  `comment_created` datetime NOT NULL,
  `comment_visible` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `ticket_comments`
--

INSERT INTO `ticket_comments` (`comment_id`, `ticket_id`, `comment_createdby`, `comment_content`, `comment_created`, `comment_visible`) VALUES
(2, 1, 1, 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facere quasi sunt quidem, aperiam dignissimos debitis. Nostrum minus aut commodi repellat impedit doloribus laudantium quo nisi cum asperiores earum assumenda totam repudiandae, perferendis dolore atque eum, sunt sint aperiam illo aliquam repellendus. Nostrum, id molestiae! Cumque iusto numquam ipsum ipsam adipisci.', '0000-00-00 00:00:00', 1),
(3, 1, 6, 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facere quasi sunt quidem, aperiam dignissimos debitis. Nostrum minus aut commodi repellat impedit doloribus lauda...', '0000-00-00 00:00:00', 1),
(4, 2, 1, 'Lorem ipsum dolor...', '0000-00-00 00:00:00', 1),
(5, 2, 1, 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facere quasi sunt quidem, aperiam dignissimos debitis. Nostrum minus aut commodi repellat impedit doloribus laudapsum dolor sit amet, consectetur adipisicing elit. Facere quasi sunt quidem, aperiam dignissimos debitis. Nostrum minus aut commodi repellat impedit doloribus lauda!!!', '0000-00-00 00:00:00', 1),
(6, 1, 1, 'Helloasdasdasdasdasd', '2019-05-26 18:22:41', 1),
(7, 1, 1, 'Hi', '2019-05-26 18:22:53', 0),
(8, 1, 1, 'Helloooaosdsdas asd asd asdasjda sdkLo a. IOIJOIJOIOIJOIJ. Lorem Ipsum i think is very bad', '2019-05-26 18:24:25', 1),
(9, 1, 55, 'Hi guys!!!!!!!!!!!!!', '2019-05-26 18:25:40', 1),
(13, 4, 1, 'Hello,  that\'s short test!', '2019-05-26 21:07:34', 1),
(14, 4, 1, 'V 11. stolet&iacute; za vl&aacute;dy PÅ™emyslovcÅ¯ prob&iacute;hala kolonizace dosud neobydlen&yacute;ch oblast&iacute; jiÅ¾nÄ› od Prahy. Prvn&iacute; p&iacute;semn&aacute; zm&iacute;nka o Bene&scaron;ovÄ› poch&aacute;z&iacute; z roku 1219 a jeho prvn&iacute; os&iacute;dlen&iacute; bylo na m&iacute;stÄ› dnes zvan&eacute;m Na KarlovÄ›, kde vznikl pansk&yacute; dvorec, kostel a okolo nich osada. Pot&eacute; se centrum pÅ™esunulo v&yacute;stavbou mÄ›stsk&eacute;ho trÅ¾i&scaron;tÄ› na dne&scaron;n&iacute; Masarykovo n&aacute;mÄ›st&iacute;. Na KarlovÄ› byl zaloÅ¾en minoritsk&yacute; kl&aacute;&scar', '2019-05-26 21:07:53', 1),
(15, 4, 1, 'V 11. stoletÃ­ za vlÃ¡dy PÅ™emyslovcÅ¯ probÃ­hala kolonizace dosud neobydlenÃ½ch oblastÃ­ jiÅ¾nÄ› od Prahy. PrvnÃ­ pÃ­semnÃ¡ zmÃ­nka o BeneÅ¡ovÄ› pochÃ¡zÃ­ z roku 1219 a jeho prvnÃ­ osÃ­dlenÃ­ bylo na mÃ­stÄ› dnes zvanÃ©m Na KarlovÄ›, kde vznikl panskÃ½ dvorec, kostel a okolo nich osada. PotÃ© se centrum pÅ™esunulo vÃ½stavbou mÄ›stskÃ©ho trÅ¾iÅ¡tÄ› na dneÅ¡nÃ­ Masarykovo nÃ¡mÄ›stÃ­. Na KarlovÄ› byl zaloÅ¾en minoritskÃ½ klÃ¡Å¡ter a majitelem se stal praÅ¾skÃ½ biskup TobiÃ¡Å¡ z BeneÅ¡ova, kterÃ½ si zÃ¡hy vybudoval novÃ© sÃ­dlo â€“ KonopiÅ¡tÄ›. Po vymÅ™enÃ­ BeneÅ¡ovicÅ¯ se majiteli stali Å ternbe', '2019-05-26 21:08:18', 1),
(16, 4, 1, '<b>asdasd</b>', '2019-05-26 21:08:29', 1),
(17, 4, 1, 'ose crest is still the city\'s coat of arms. I\'\'\'asdasd\'as\'d\'asd\'asd\'a\'sda\'sd\'asd\'asd\'as\'d', '2019-05-26 21:34:51', 1),
(18, 4, 1, '\'\'\'\'\'\'\'\'ose crest is still the city\'s coat of arms. I\'sad\'', '2019-05-26 21:34:59', 1),
(19, 4, 1, '<div>asdasda</div>', '2019-05-26 21:35:08', 1),
(20, 4, 1, '<div class=\"font-weight-bold\">asdasdasdasd</div>', '2019-05-26 21:35:29', 1),
(21, 4, 1, '<button>asdasd</button>', '2019-05-26 21:35:41', 1),
(22, 4, 1, '&lt;button&gt;asdasdasdasdasd&lt;/button&gt;', '2019-05-26 21:37:09', 1),
(23, 4, 1, '&lt;div class=&quot;font-weight-bold&quot;&gt;fassaffs&lt;/div&gt;', '2019-05-26 21:37:50', 1),
(24, 2, 1, 'Bene&scaron;ov (nÄ›m. Beneschau) je mÄ›sto ve StÅ™edoÄesk&eacute;m kraji, obec s roz&scaron;&iacute;Å™enou pÅ¯sobnost&iacute; a tak&eacute; nejvÄ›t&scaron;&iacute; mÄ›sto okresu Bene&scaron;ov. LeÅ¾&iacute; v Bene&scaron;ovsk&eacute; pahorkatinÄ› 30 km jihov&yacute;chodnÄ› od Prahy, prot&eacute;k&aacute; j&iacute;m Bene&scaron;ovsk&yacute; a Konopi&scaron;Å¥sk&yacute; potok. Å½ije zde pÅ™ibliÅ¾nÄ› 17 tis&iacute;c[1] obyvatel.', '2019-05-26 21:53:46', 1),
(25, 2, 1, '&lt;button&gt;Asdasd&lt;/button&gt;', '2019-05-26 21:53:59', 1),
(26, 44, 1, 'Hello, test :) &lt;button&gt;asdasdasda&lt;/button&gt;', '2019-06-01 22:27:02', 1),
(27, 44, 55, 'Hello, test of a profile image. &lt;a href=&quot;asda&quot;&gt;asdasdaasd&lt;/a&gt;', '2019-06-16 12:57:38', 1),
(28, 62, 1, 'Komentar', '2019-06-19 20:48:52', 0),
(29, 62, 1, 'asdasd', '2019-06-20 05:58:47', 0),
(31, 62, 1, '', '2019-06-20 06:46:14', 1),
(32, 62, 1, 'Ñ„Ñ„Ð°Ñ„Ð°Ð°Ñ„', '2019-06-20 06:47:14', 1),
(37, 62, 1, '', '2019-06-20 16:40:17', 1),
(38, 62, 1, 'Heeee', '2019-06-20 16:41:19', 1),
(39, 65, 1, 'jkasd', '2019-06-20 16:52:58', 1),
(40, 65, 55, 'Hello', '2019-06-25 19:35:54', 1),
(41, 66, 1, '<button>asdas</button>', '2019-06-28 21:07:50', 1),
(42, 63, 1, 'Test', '2019-07-02 21:20:51', 0),
(43, 69, 1, 'HEllo', '2019-07-09 21:00:32', 1),
(44, 69, 57, 'lol', '2019-07-09 21:05:56', 1),
(45, 69, 1, 'ddd', '2019-07-09 21:06:22', 1),
(46, 69, 57, 'lol', '2019-07-09 21:06:24', 1),
(47, 69, 57, 'ahoj', '2019-07-09 21:08:14', 1),
(48, 69, 57, 'ahoj', '2019-07-09 21:08:14', 1),
(49, 69, 57, 'Test 10.07.2019', '2019-07-10 18:21:29', 1),
(50, 69, 55, 'test', '2019-07-10 18:22:15', 1),
(51, 74, 2, 'Hello', '2019-07-17 19:23:57', 1),
(52, 74, 1, 'Hello', '2019-07-17 20:26:27', 1),
(53, 75, 61, 'bnm,,', '2019-07-27 19:24:37', 1),
(54, 77, 1, 'NIce', '2019-08-01 20:49:35', 1),
(55, 77, 1, 'sdds', '2019-08-01 20:50:32', 1),
(56, 77, 1, '&lt;button&gt;asdasdasd&lt;/button&gt;', '2019-08-01 20:50:44', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `transactions`
--

CREATE TABLE `transactions` (
  `t_id` int(11) NOT NULL,
  `t_from` int(11) NOT NULL,
  `t_sum` int(11) NOT NULL,
  `t_description` varchar(30) COLLATE utf8_bin NOT NULL,
  `t_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `transactions`
--

INSERT INTO `transactions` (`t_id`, `t_from`, `t_sum`, `t_description`, `t_date`) VALUES
(1, 2, 13, 'Referrals reward', '2019-07-25 19:45:16'),
(2, 1, 80, 'Referrals reward', '2019-07-25 19:49:21'),
(3, 1, 50, 'Referrals reward', '2019-07-25 19:50:13'),
(4, 1, 500, 'Checked items reward', '2019-07-25 20:20:03'),
(5, 1, 500, 'Checked items reward', '2019-07-25 20:20:11'),
(6, 1, 500, 'Checked items reward', '2019-07-25 20:20:17'),
(7, 1, 500, 'Checked items reward', '2019-07-25 20:22:36'),
(8, 1, 0, 'Checked items reward', '2019-07-25 21:00:32'),
(9, 1, 10, 'Checked items reward', '2019-07-25 21:29:05'),
(10, 1, 6, 'Checked items reward', '2019-07-28 18:55:34'),
(11, 1, 600, 'Lottery reward', '2019-08-02 17:27:00');

-- --------------------------------------------------------

--
-- Структура таблицы `tutorial`
--

CREATE TABLE `tutorial` (
  `tutorial_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop` int(11) NOT NULL DEFAULT '1',
  `profile` int(11) NOT NULL DEFAULT '1',
  `item_add` int(11) NOT NULL DEFAULT '1',
  `school_change` int(11) NOT NULL DEFAULT '1',
  `deposit` int(11) NOT NULL DEFAULT '1',
  `withdraw` int(11) NOT NULL DEFAULT '1',
  `referrals` int(11) NOT NULL DEFAULT '1',
  `item_check` int(11) NOT NULL DEFAULT '1',
  `selling_items` int(11) NOT NULL DEFAULT '1',
  `bought_items` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `tutorial`
--

INSERT INTO `tutorial` (`tutorial_id`, `user_id`, `shop`, `profile`, `item_add`, `school_change`, `deposit`, `withdraw`, `referrals`, `item_check`, `selling_items`, `bought_items`) VALUES
(1, 1, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0),
(2, 6, 0, 0, 1, 1, 1, 1, 1, 0, 1, 1),
(3, 59, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(5, 61, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0),
(6, 62, 0, 0, 1, 1, 1, 1, 1, 1, 1, 0),
(7, 63, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `unbanned_users`
--

CREATE TABLE `unbanned_users` (
  `id` int(11) NOT NULL,
  `unbanned_id` int(11) NOT NULL,
  `unbanned_by` int(11) NOT NULL,
  `unban_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `unbanned_users`
--

INSERT INTO `unbanned_users` (`id`, `unbanned_id`, `unbanned_by`, `unban_time`) VALUES
(1, 6, 1, '2019-07-08 16:14:07'),
(2, 2, 1, '2000-10-01 10:00:00'),
(3, 2, 1, '2001-05-23 10:00:00'),
(4, 2, 1, '2002-06-13 10:00:00'),
(5, 2, 1, '2000-10-01 10:00:00'),
(6, 2, 1, '2000-10-01 10:00:00'),
(7, 2, 1, '2000-10-01 10:00:00'),
(8, 2, 1, '2000-10-01 10:00:00'),
(9, 2, 1, '2000-10-01 10:00:00'),
(10, 2, 1, '2000-10-01 10:00:00'),
(11, 2, 1, '2000-10-01 10:00:00'),
(12, 6, 1, '2019-07-08 20:22:33'),
(13, 2, 1, '2019-07-25 15:27:06'),
(14, 2, 1, '2019-07-26 19:14:51'),
(15, 2, 1, '2019-07-26 21:56:53'),
(16, 2, 1, '2019-07-27 15:33:17'),
(17, 2, 1, '2019-07-27 15:34:14'),
(18, 2, 1, '2019-07-27 15:46:03'),
(19, 2, 1, '2019-08-02 15:41:58'),
(20, 10, 1, '2019-09-04 16:20:23');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(20) COLLATE utf8_bin NOT NULL,
  `password` varchar(20) COLLATE utf8_bin NOT NULL,
  `balance` int(11) DEFAULT '0',
  `email` varchar(40) COLLATE utf8_bin NOT NULL,
  `first_name` varchar(15) COLLATE utf8_bin NOT NULL,
  `last_name` varchar(30) COLLATE utf8_bin NOT NULL,
  `full_register` int(11) NOT NULL DEFAULT '0',
  `register_ip` varchar(20) COLLATE utf8_bin NOT NULL,
  `last_ip` varchar(20) COLLATE utf8_bin NOT NULL,
  `ref_code` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `ref_multiplier` float NOT NULL DEFAULT '0.03',
  `sell_multiplier` float NOT NULL DEFAULT '0.7',
  `school_id` int(11) NOT NULL,
  `school_setdate` datetime NOT NULL,
  `school_added` int(11) NOT NULL DEFAULT '0',
  `bought_items` int(11) NOT NULL DEFAULT '0',
  `instagram` varchar(40) COLLATE utf8_bin NOT NULL,
  `facebook` varchar(40) COLLATE utf8_bin NOT NULL,
  `activated` int(11) NOT NULL DEFAULT '0',
  `confirmed_items` int(11) NOT NULL DEFAULT '0',
  `declined_items` int(11) NOT NULL DEFAULT '0',
  `declined_reports` int(11) NOT NULL DEFAULT '0',
  `confirmed_reports` int(11) NOT NULL DEFAULT '0',
  `image_path` varchar(255) COLLATE utf8_bin NOT NULL,
  `bank_number` varchar(16) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `debug_mode` int(11) DEFAULT '0',
  `hash` varchar(32) COLLATE utf8_bin NOT NULL,
  `social_show` int(11) NOT NULL DEFAULT '1',
  `removed_items` int(11) NOT NULL DEFAULT '0',
  `register_date` datetime NOT NULL,
  `last_action` datetime NOT NULL,
  `last_session` datetime NOT NULL,
  `last_withdraw` datetime NOT NULL,
  `session_hash` varchar(32) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `balance`, `email`, `first_name`, `last_name`, `full_register`, `register_ip`, `last_ip`, `ref_code`, `ref_multiplier`, `sell_multiplier`, `school_id`, `school_setdate`, `school_added`, `bought_items`, `instagram`, `facebook`, `activated`, `confirmed_items`, `declined_items`, `declined_reports`, `confirmed_reports`, `image_path`, `bank_number`, `debug_mode`, `hash`, `social_show`, `removed_items`, `register_date`, `last_action`, `last_session`, `last_withdraw`, `session_hash`) VALUES
(0, 'System', '', 0, '', 'dss', '', 0, '', '::1', NULL, 0.03, 0.7, 0, '0000-00-00 00:00:00', 0, 0, '1', '', 0, 0, 0, 0, 0, '', '0', 0, '', 1, 0, '0000-00-00 00:00:00', '2019-07-19 14:19:33', '2019-07-18 20:55:23', '0000-00-00 00:00:00', ''),
(1, 'admin', 'adpexzg3FUZAk', 2077, 'admin@admin.net', 'Admin', 'Adminovich', 1, '::1', '::1', 'refferal00', 0.03, 0.7, 1, '2019-08-10 19:30:24', 1, 6, 'michaelberezovskiy', '', 1, 38, 33, 3, 19, 'profile_pictures/admin1563285523.png', '1234567890/2700', 0, 'c4015b7f368e6b4871809f49debe0579', 0, 17, '2019-07-18 21:26:24', '2019-09-29 21:14:02', '2019-09-29 20:59:59', '2019-07-03 00:00:00', '11b921ef080f7736089c757404650e40'),
(2, 'tester', 'teCi1U7ES.EJw', 1100, 'tester@tester.net', 'Tester', 'OAOoaoa', 1, '::1', '::1', NULL, 0.03, 0.7, 1, '0000-00-00 00:00:00', 1, 1, 'ddddd', '', 1, 1, 0, 0, 0, 'profile_pictures/tester1560876805.png', '0', 0, 'b7bb35b9c6ca2aee2df08cf09d7016c2', 1, 0, '2019-06-07 18:41:29', '2019-08-31 16:20:13', '2019-08-31 16:20:15', '0000-00-00 00:00:00', 'e44fea3bec53bcea3b7513ccef5857ac'),
(4, 'asd', 'as0BGbKy7INIE', 0, 'asd@asd.asd', 'asd', 'asd', 1, '::1', '::1', NULL, 0.03, 0.7, 0, '0000-00-00 00:00:00', 0, 0, '', '', 0, 0, 0, 0, 0, '', '0', 0, 'f9b902fc3289af4dd08de5d1de54f68f', 1, 0, '2019-07-17 18:49:12', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(6, 'kololo', 'kot0N7N.yGC/s', 377, 'lol@lol.net', 'Asd', 'Dsdsds', 1, '::1', '::1', 'kololo123', 0.03, 0.7, 1, '2019-05-11 15:20:51', 1, 5, 'bulkinspb', 'asdsad', 1, 1, 0, 0, 0, 'profile_pictures/kololo1560883308.png', '2147483647', 0, '3b5dca501ee1e6d8cd7b905f4e1bf723', 1, 1, '2019-06-11 21:28:59', '2019-09-04 10:51:21', '2019-09-04 10:51:24', '2019-07-15 16:25:17', '470e7a4f017a5476afb7eeb3f8b96f9b'),
(7, 'ukulele', 'ukBpjzivQ3/SA', 500, 'uuu@uuu.uuu', 'ukulele', 'SAadda', 1, '::1', '::1', NULL, 0.03, 0.7, 0, '0000-00-00 00:00:00', 0, 0, '', '', 1, 0, 0, 0, 0, '', '0', 0, 'a64c94baaf368e1840a1324e839230de', 1, 0, '2019-05-15 21:19:36', '2019-05-23 21:57:10', '2019-05-23 21:57:12', '0000-00-00 00:00:00', ''),
(10, 'try2', 'trytsGFB71RYU', 0, 'try2@email.cz', 'Konzerva', 'Konzervovich', 1, '::1', '::1', NULL, 0.03, 0.7, 0, '0000-00-00 00:00:00', 0, 1, '', '', 1, 0, 0, 0, 0, 'profile_pictures/try21567607729.png', '0', 0, '9cc138f8dc04cbf16240daa92d8d50e2', 1, 0, '2019-05-01 19:59:39', '2019-09-04 16:35:29', '2019-09-04 16:24:37', '0000-00-00 00:00:00', '16c222aa19898e5058938167c8ab6c57'),
(11, 'kokoko', 'koNRq82lbo5gc', 0, 'koko@koko.koko', 'Kokook', 'Kokolollo', 1, '::1', '::1', NULL, 0.03, 0.7, 2, '2019-09-04 16:29:55', 0, 0, '', '', 1, 0, 0, 0, 0, 'profile_pictures/kokoko1567607377.png', '0', 0, 'f76a89f0cb91bc419542ce9fa43902dc', 1, 0, '2019-05-01 20:02:19', '2019-09-04 16:34:18', '2019-09-04 16:34:21', '0000-00-00 00:00:00', '02e74f10e0327ad868d138f2b4fdd6f0'),
(46, 'adminfffff', 'adpexzg3FUZAk', 0, 'asdasd@ad.adad', '', '', 0, '::1', '', NULL, 0.03, 0.7, 0, '0000-00-00 00:00:00', 0, 0, '', '', 0, 0, 0, 0, 0, '', '0', 0, 'f4552671f8909587cf485ea990207f3b', 1, 0, '2019-05-14 21:24:38', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(47, 'adminoss', 'adxXHMWJp2/sE', 0, 'asd@ad.ad', '', '', 0, '::1', '', NULL, 0.03, 0.7, 0, '0000-00-00 00:00:00', 0, 0, '', '', 0, 0, 0, 0, 0, '', '0', 0, '289dff07669d7a23de0ef88d2f7129e7', 1, 0, '2019-05-15 16:30:14', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(48, 'adminnjn', 'adxXHMWJp2/sE', 0, 'adad@adad.adad', '', '', 0, '::1', '', NULL, 0.03, 0.7, 0, '0000-00-00 00:00:00', 0, 0, '', '', 0, 0, 0, 0, 0, '', '0', 0, '2b24d495052a8ce66358eb576b8912c8', 1, 0, '2019-05-15 16:34:12', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(49, 'moskak', 'mouEhl1CYjQPs', 0, 'ad@adad.d', '', '', 0, '::1', '', NULL, 0.03, 0.7, 0, '0000-00-00 00:00:00', 0, 0, '', '', 0, 0, 0, 0, 0, '', '0', 0, '63dc7ed1010d3c3b8269faf0ba7491d4', 1, 0, '2019-05-15 20:44:41', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(50, 'bossw', 'boGkyCSmn4v7g', 0, 'asd@ad.adad', '', '', 0, '::1', '', NULL, 0.03, 0.7, 0, '0000-00-00 00:00:00', 0, 0, '', '', 0, 0, 0, 0, 0, '', '0', 0, '9b04d152845ec0a378394003c96da594', 1, 0, '2019-05-15 20:45:48', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(51, 'adminddd', 'adxXHMWJp2/sE', 0, 'dd@dd.dddasd', '', '', 0, '::1', '', NULL, 0.03, 0.7, 0, '0000-00-00 00:00:00', 0, 0, '', '', 0, 0, 0, 0, 0, '', '0', 0, '821fa74b50ba3f7cba1e6c53e8fa6845', 1, 0, '2019-05-15 20:47:37', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(52, 'kokookooko', 'koiv9fcHznYXU', 0, 'ads@ad.ad', '', '', 0, '::1', '', NULL, 0.03, 0.7, 0, '0000-00-00 00:00:00', 0, 0, '', '', 0, 0, 0, 0, 0, '', '0', 0, 'e995f98d56967d946471af29d7bf99f1', 1, 0, '2019-05-15 20:49:11', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(53, 'adminbb', 'adBi7QbIk1m32', 0, 'asdasd@ad.ad', '', '', 0, '::1', '', NULL, 0.03, 0.7, 0, '0000-00-00 00:00:00', 0, 0, '', '', 0, 0, 0, 0, 0, '', '0', 0, '0f49c89d1e7298bb9930789c8ed59d48', 1, 0, '2019-05-15 20:49:55', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(54, 'adminddddd', 'adbio/7hm7Wb2', 0, 'asdad@adad.ad', '', '', 0, '::1', '', NULL, 0.03, 0.7, 0, '0000-00-00 00:00:00', 0, 0, '', '', 0, 0, 0, 0, 0, 'profile_pictures/adminddddd_intro-3.jpeg', '0', 0, '30bb3825e8f631cc6075c0f87bb4978c', 1, 0, '2019-05-15 20:50:52', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(55, 'imagee', 'imePo1t1DY6fc', 500, 'asdasds@adsasd.asd', 'Image', 'Test', 1, '::1', '::1', NULL, 0.03, 0.7, 2, '2019-05-15 21:05:21', 0, 0, '', '', 1, 0, 0, 0, 0, 'profile_pictures/imagee1560682608.png', '0', 0, 'ec8956637a99787bd197eacd77acce5e', 1, 0, '2019-05-15 20:53:03', '2019-07-28 18:48:12', '2019-07-28 18:49:57', '0000-00-00 00:00:00', '0deb1c54814305ca9ad266f53bc82511'),
(56, 'user0', 'usEXWJO18Io6M', 0, 'user@user.net', 'Addidas', 'Tacosino', 1, '::1', '::1', NULL, 0.03, 0.7, 1, '2019-06-26 19:55:00', 0, 0, '', '', 1, 0, 0, 0, 0, 'profile_pictures/user01561576832.png', '0', 0, '5c572eca050594c7bc3c36e7e8ab9550', 1, 0, '2019-06-26 19:47:39', '2019-07-10 20:02:57', '2019-07-10 20:03:03', '0000-00-00 00:00:00', ''),
(57, 'user02', 'usdLQFdFSDnrM', 500, 'haha@haha.haha', 'User', 'Komik', 1, '::1', '::1', 'user02refe', 0.03, 0.7, 0, '2019-06-28 21:03:37', 1, 0, '', '', 1, 0, 0, 0, 0, 'profile_pictures/user021561745224.png', '0', 0, '00411460f7c92d2124a67ea0f4cb5f85', 1, 0, '2019-06-28 20:04:26', '2019-07-13 14:47:21', '2019-07-13 14:47:29', '0000-00-00 00:00:00', ''),
(58, 'burrito', 'bu914akk9PoNE', 0, 'burrito@asd.asd', '', '', 0, '::1', '', NULL, 0.03, 0.7, 0, '0000-00-00 00:00:00', 0, 0, '', '', 1, 0, 0, 0, 0, '', '0', 0, 'd840cc5d906c3e9c84374c8919d2074e', 1, 0, '2019-06-30 16:51:28', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(59, 'kolkkok', 'koosVp7zEaYx2', 0, 'koadsk@a.ads', '', '', 0, '::1', '', NULL, 0.03, 0.7, 0, '0000-00-00 00:00:00', 0, 0, '', '', 0, 0, 0, 0, 0, '', '0', 0, '1cc3633c579a90cfdd895e64021e2163', 1, 0, '2019-06-30 16:54:06', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(61, 'testpp', 'teywDudVMJ86M', 70, 'pp@pp.pp', 'Asdasd', 'Asdasd', 1, '::1', '::1', NULL, 0.03, 0.7, 1, '2019-07-20 12:56:14', 0, 3, '', '', 1, 0, 0, 0, 0, '', '0', 0, 'f5f8590cd58a54e94377e6ae2eded4d9', 1, 0, '2019-07-18 19:28:20', '2019-08-11 14:37:52', '2019-08-11 14:39:57', '0000-00-00 00:00:00', '115f89503138416a242f40fb7d7f338e'),
(62, 'referraltest', 're4i0CpwJrwNg', 100, 'referral@asd.asd', 'Test', 'Referral', 1, '::1', '::1', NULL, 0.03, 0.7, 1, '2019-07-23 21:31:28', 0, 4, '', '', 1, 0, 0, 0, 0, '', '0', 0, '3d8e28caf901313a554cebc7d32e67e5', 1, 0, '2019-07-23 15:04:27', '2019-07-28 18:45:39', '2019-07-28 18:45:42', '0000-00-00 00:00:00', '53e3a7161e428b65688f14b84d61c610'),
(63, 'matoustest', 'mavlOryyAG4ng', 464, 'matous@test.test', 'FAzol', 'Okokook', 1, '::1', '::1', 'reeedfff', 0.03, 0.7, 1, '2019-09-04 10:46:51', 0, 1, '', '', 1, 0, 0, 0, 0, 'profile_pictures/matoustest1567610787.png', '2281337821/23444', 0, '70c639df5e30bdee440e4cdf599fec2b', 1, 0, '2019-09-04 10:44:28', '2019-09-04 18:13:35', '2019-09-04 18:00:59', '2019-09-04 18:01:40', 'd3d9446802a44259755d38e6d163e820');

-- --------------------------------------------------------

--
-- Структура таблицы `users_groups`
--

CREATE TABLE `users_groups` (
  `bridge_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `event_date` datetime NOT NULL,
  `set_by` int(11) NOT NULL,
  `set_method` varchar(10) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `users_groups`
--

INSERT INTO `users_groups` (`bridge_id`, `user_id`, `group_id`, `event_date`, `set_by`, `set_method`) VALUES
(18, 55, 3, '2019-06-20 20:28:29', 0, ''),
(21, 6, 1, '2019-07-09 16:31:00', 1, 'Manually'),
(22, 61, 2, '2019-07-24 20:04:28', 1, 'Manually'),
(23, 1, 4, '0000-00-00 00:00:00', 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `users_log`
--

CREATE TABLE `users_log` (
  `ul_id` int(11) NOT NULL,
  `ul_user_id` int(11) NOT NULL,
  `ul_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `users_log`
--

INSERT INTO `users_log` (`ul_id`, `ul_user_id`, `ul_date`) VALUES
(5, 1, '2019-07-17 13:56:50'),
(6, 1, '2019-07-17 14:01:16'),
(7, 2, '2019-07-17 14:01:32'),
(8, 1, '2019-07-17 14:01:36'),
(9, 1, '2019-07-17 19:07:50'),
(10, 2, '2019-07-17 19:07:59'),
(11, 6, '2019-07-17 19:08:06'),
(12, 2, '2019-07-17 19:19:11'),
(13, 55, '2019-07-17 19:28:59'),
(14, 1, '2019-07-17 19:30:10'),
(15, 55, '2019-07-17 19:31:45'),
(16, 1, '2019-07-17 19:45:23'),
(17, 1, '2019-07-17 20:25:43'),
(18, 1, '2019-07-17 20:37:46'),
(19, 6, '2019-07-17 20:37:52'),
(20, 1, '2019-07-17 20:38:03'),
(21, 1, '2019-07-18 16:57:16'),
(22, 1, '2019-07-18 17:28:29'),
(23, 61, '2019-07-18 19:29:43'),
(24, 1, '2019-07-18 19:33:14'),
(25, 61, '2019-07-18 19:33:55'),
(26, 1, '2019-07-18 19:34:19'),
(27, 1, '2019-07-18 20:27:26'),
(28, 1, '2019-07-18 20:31:17'),
(29, 1, '2019-07-18 20:31:28'),
(30, 1, '2019-07-18 20:40:42'),
(31, 1, '2019-07-18 20:49:16'),
(32, 1, '2019-07-18 20:50:41'),
(33, 1, '2019-07-18 20:51:29'),
(34, 1, '2019-07-18 21:05:58'),
(35, 1, '2019-07-18 21:08:26'),
(36, 1, '2019-07-18 21:11:00'),
(37, 1, '2019-07-18 21:12:00'),
(38, 1, '2019-07-18 21:12:35'),
(39, 1, '2019-07-18 21:24:16'),
(40, 1, '2019-07-18 21:24:21'),
(41, 1, '2019-07-18 21:25:37'),
(42, 1, '2019-07-18 21:31:00'),
(43, 1, '2019-07-18 21:35:30'),
(44, 1, '2019-07-18 21:35:39'),
(45, 1, '2019-07-19 14:18:54'),
(46, 1, '2019-07-19 14:20:11'),
(47, 1, '2019-07-19 14:20:59'),
(48, 1, '2019-07-19 14:21:08'),
(49, 1, '2019-07-19 14:22:07'),
(50, 1, '2019-07-19 14:25:46'),
(51, 1, '2019-07-19 14:26:24'),
(52, 1, '2019-07-19 14:27:21'),
(53, 1, '2019-07-19 14:28:43'),
(54, 1, '2019-07-19 14:30:26'),
(55, 1, '2019-07-19 14:30:43'),
(56, 1, '2019-07-19 14:31:03'),
(57, 1, '2019-07-19 14:32:07'),
(58, 1, '2019-07-19 14:45:34'),
(59, 1, '2019-07-19 14:53:19'),
(60, 1, '2019-07-19 14:54:53'),
(61, 1, '2019-07-19 14:57:54'),
(62, 1, '2019-07-19 14:58:26'),
(63, 1, '2019-07-19 14:59:23'),
(64, 6, '2019-07-19 14:59:30'),
(65, 1, '2019-07-19 15:00:10'),
(66, 1, '2019-07-19 15:00:12'),
(67, 1, '2019-07-19 15:00:19'),
(68, 1, '2019-07-19 15:01:40'),
(69, 1, '2019-07-19 15:01:52'),
(70, 1, '2019-07-19 15:04:17'),
(71, 1, '2019-07-19 15:04:29'),
(72, 1, '2019-07-19 15:27:12'),
(73, 1, '2019-07-19 15:44:07'),
(74, 1, '2019-07-19 15:48:01'),
(75, 1, '2019-07-19 15:58:34'),
(76, 1, '2019-07-19 16:41:39'),
(77, 1, '2019-07-19 16:44:42'),
(78, 1, '2019-07-19 16:44:53'),
(79, 1, '2019-07-19 16:45:11'),
(80, 61, '2019-07-19 16:45:59'),
(81, 1, '2019-07-19 17:06:09'),
(82, 61, '2019-07-19 17:06:27'),
(83, 61, '2019-07-19 17:29:09'),
(84, 6, '2019-07-19 17:33:59'),
(85, 1, '2019-07-19 17:34:21'),
(86, 6, '2019-07-19 17:38:43'),
(87, 1, '2019-07-19 18:57:12'),
(88, 1, '2019-07-19 19:20:45'),
(89, 1, '2019-07-19 20:18:19'),
(90, 1, '2019-07-19 20:20:28'),
(91, 1, '2019-07-19 20:20:55'),
(92, 1, '2019-07-19 20:33:00'),
(93, 1, '2019-07-19 20:51:47'),
(94, 1, '2019-07-19 21:02:41'),
(95, 1, '2019-07-19 21:23:21'),
(96, 1, '2019-07-19 22:07:17'),
(97, 1, '2019-07-20 12:53:44'),
(98, 61, '2019-07-20 12:55:56'),
(99, 1, '2019-07-20 12:58:35'),
(100, 1, '2019-07-20 14:46:04'),
(101, 61, '2019-07-20 14:46:12'),
(102, 6, '2019-07-20 15:01:11'),
(103, 1, '2019-07-20 15:10:18'),
(104, 61, '2019-07-20 15:10:25'),
(105, 1, '2019-07-20 15:13:06'),
(106, 61, '2019-07-20 15:13:13'),
(107, 1, '2019-07-20 15:22:38'),
(108, 61, '2019-07-20 15:22:48'),
(109, 1, '2019-07-20 15:23:17'),
(110, 61, '2019-07-20 15:23:23'),
(111, 61, '2019-07-20 15:30:41'),
(112, 1, '2019-07-20 15:31:00'),
(113, 61, '2019-07-20 15:31:09'),
(114, 61, '2019-07-20 15:54:17'),
(115, 1, '2019-07-20 15:55:03'),
(116, 1, '2019-07-20 16:17:25'),
(117, 61, '2019-07-20 16:17:31'),
(118, 1, '2019-07-20 16:38:15'),
(119, 61, '2019-07-20 16:38:20'),
(120, 61, '2019-07-20 16:58:44'),
(121, 61, '2019-07-20 17:19:36'),
(122, 1, '2019-07-20 17:43:47'),
(123, 61, '2019-07-20 17:43:55'),
(124, 1, '2019-07-20 18:23:01'),
(125, 61, '2019-07-20 18:23:07'),
(126, 1, '2019-07-20 18:31:49'),
(127, 1, '2019-07-20 19:08:28'),
(128, 61, '2019-07-20 19:08:35'),
(129, 1, '2019-07-20 20:11:56'),
(130, 61, '2019-07-20 20:12:03'),
(131, 1, '2019-07-20 20:32:50'),
(132, 61, '2019-07-20 20:43:15'),
(133, 1, '2019-07-20 20:43:46'),
(134, 1, '2019-07-20 21:03:55'),
(135, 1, '2019-07-21 14:17:20'),
(136, 1, '2019-07-21 14:39:18'),
(137, 61, '2019-07-21 17:05:03'),
(138, 1, '2019-07-21 19:26:37'),
(139, 61, '2019-07-21 19:28:51'),
(140, 1, '2019-07-22 16:26:55'),
(141, 1, '2019-07-22 20:09:04'),
(142, 6, '2019-07-22 20:51:07'),
(143, 1, '2019-07-22 20:53:30'),
(144, 61, '2019-07-22 20:53:36'),
(145, 1, '2019-07-22 20:54:13'),
(146, 61, '2019-07-22 20:58:34'),
(147, 1, '2019-07-22 21:15:17'),
(148, 1, '2019-07-22 21:17:31'),
(149, 61, '2019-07-22 21:19:07'),
(150, 61, '2019-07-22 21:19:55'),
(151, 61, '2019-07-22 21:24:27'),
(152, 1, '2019-07-22 21:25:19'),
(153, 1, '2019-07-22 21:28:53'),
(154, 1, '2019-07-22 21:31:31'),
(155, 1, '2019-07-23 14:28:48'),
(156, 1, '2019-07-23 14:51:08'),
(157, 62, '2019-07-23 15:05:18'),
(158, 62, '2019-07-23 15:06:43'),
(159, 1, '2019-07-23 15:07:37'),
(160, 1, '2019-07-23 15:08:21'),
(161, 1, '2019-07-23 18:18:42'),
(162, 62, '2019-07-23 18:29:12'),
(163, 1, '2019-07-23 18:29:26'),
(164, 62, '2019-07-23 18:33:01'),
(165, 1, '2019-07-23 18:53:13'),
(166, 1, '2019-07-23 20:22:43'),
(167, 62, '2019-07-23 20:23:48'),
(168, 1, '2019-07-23 20:42:59'),
(169, 62, '2019-07-23 20:53:00'),
(170, 1, '2019-07-23 20:54:20'),
(171, 62, '2019-07-23 21:22:26'),
(172, 1, '2019-07-23 21:24:44'),
(173, 62, '2019-07-23 21:24:58'),
(174, 62, '2019-07-23 21:29:51'),
(175, 62, '2019-07-23 21:31:04'),
(176, 1, '2019-07-24 19:09:42'),
(177, 62, '2019-07-24 19:09:54'),
(178, 1, '2019-07-24 19:11:38'),
(179, 61, '2019-07-24 20:03:35'),
(180, 1, '2019-07-24 20:03:45'),
(181, 61, '2019-07-24 20:03:53'),
(182, 1, '2019-07-24 20:27:15'),
(183, 1, '2019-07-24 21:15:39'),
(184, 1, '2019-07-25 14:52:13'),
(185, 1, '2019-07-25 15:22:27'),
(186, 1, '2019-07-25 15:25:31'),
(187, 2, '2019-07-25 15:25:58'),
(188, 1, '2019-07-25 15:26:42'),
(189, 2, '2019-07-25 15:27:30'),
(190, 2, '2019-07-25 15:28:00'),
(191, 2, '2019-07-25 15:28:32'),
(192, 2, '2019-07-25 15:28:43'),
(193, 2, '2019-07-25 15:32:24'),
(194, 1, '2019-07-25 17:49:42'),
(195, 1, '2019-07-25 19:08:51'),
(196, 1, '2019-07-25 19:28:57'),
(197, 1, '2019-07-25 19:48:59'),
(198, 1, '2019-07-25 19:49:18'),
(199, 1, '2019-07-25 19:50:08'),
(200, 1, '2019-07-25 20:19:15'),
(201, 1, '2019-07-25 20:46:03'),
(202, 1, '2019-07-25 21:28:31'),
(203, 1, '2019-07-26 16:52:14'),
(204, 1, '2019-07-26 17:12:59'),
(205, 1, '2019-07-26 17:38:39'),
(206, 1, '2019-07-26 17:58:44'),
(207, 1, '2019-07-26 18:24:48'),
(208, 1, '2019-07-26 18:40:31'),
(209, 1, '2019-07-26 18:40:50'),
(210, 1, '2019-07-26 19:00:57'),
(211, 1, '2019-07-26 19:13:46'),
(212, 2, '2019-07-26 19:14:00'),
(213, 1, '2019-07-26 19:14:08'),
(214, 1, '2019-07-26 19:14:18'),
(215, 2, '2019-07-26 19:15:45'),
(216, 1, '2019-07-26 19:21:18'),
(217, 1, '2019-07-26 21:16:27'),
(218, 1, '2019-07-26 21:29:48'),
(219, 1, '2019-07-26 21:30:07'),
(220, 1, '2019-07-26 21:43:14'),
(221, 1, '2019-07-26 21:52:33'),
(222, 1, '2019-07-27 15:06:35'),
(223, 1, '2019-07-27 15:26:45'),
(224, 2, '2019-07-27 15:31:07'),
(225, 1, '2019-07-27 15:31:30'),
(226, 2, '2019-07-27 15:33:23'),
(227, 1, '2019-07-27 15:33:34'),
(228, 2, '2019-07-27 15:34:22'),
(229, 1, '2019-07-27 15:34:30'),
(230, 2, '2019-07-27 15:46:20'),
(231, 1, '2019-07-27 15:46:30'),
(232, 6, '2019-07-27 15:58:02'),
(233, 61, '2019-07-27 15:58:20'),
(234, 1, '2019-07-27 15:58:25'),
(235, 1, '2019-07-27 16:20:48'),
(236, 1, '2019-07-27 16:44:56'),
(237, 2, '2019-07-27 17:04:57'),
(238, 1, '2019-07-27 17:05:31'),
(239, 1, '2019-07-27 18:13:36'),
(240, 2, '2019-07-27 18:23:17'),
(241, 1, '2019-07-27 18:24:15'),
(242, 1, '2019-07-27 19:17:18'),
(243, 61, '2019-07-27 19:22:01'),
(244, 61, '2019-07-27 19:49:17'),
(245, 1, '2019-07-27 20:49:44'),
(246, 1, '2019-07-27 21:11:33'),
(247, 1, '2019-07-28 14:19:40'),
(248, 1, '2019-07-28 14:40:04'),
(249, 1, '2019-07-28 15:00:11'),
(250, 1, '2019-07-28 15:20:14'),
(251, 1, '2019-07-28 15:42:50'),
(252, 1, '2019-07-28 16:08:32'),
(253, 1, '2019-07-28 16:28:41'),
(254, 1, '2019-07-28 17:40:50'),
(255, 6, '2019-07-28 17:56:41'),
(256, 1, '2019-07-28 17:56:50'),
(257, 1, '2019-07-28 18:39:03'),
(258, 61, '2019-07-28 18:39:14'),
(259, 62, '2019-07-28 18:39:46'),
(260, 1, '2019-07-28 18:45:43'),
(261, 2, '2019-07-28 18:45:49'),
(262, 61, '2019-07-28 18:45:57'),
(263, 1, '2019-07-28 18:47:13'),
(264, 55, '2019-07-28 18:47:27'),
(265, 1, '2019-07-28 18:49:59'),
(266, 1, '2019-07-28 20:17:21'),
(267, 1, '2019-07-28 20:44:48'),
(268, 1, '2019-07-29 16:41:47'),
(269, 1, '2019-07-29 17:03:13'),
(270, 1, '2019-07-29 17:23:17'),
(271, 1, '2019-07-29 17:43:39'),
(272, 1, '2019-07-29 20:45:24'),
(273, 1, '2019-07-29 21:09:56'),
(274, 1, '2019-07-30 21:10:32'),
(275, 1, '2019-07-30 21:30:48'),
(276, 1, '2019-07-31 19:22:37'),
(277, 1, '2019-07-31 19:44:48'),
(278, 1, '2019-07-31 20:09:00'),
(279, 1, '2019-07-31 20:30:42'),
(280, 1, '2019-07-31 21:05:49'),
(281, 1, '2019-08-01 07:00:38'),
(282, 1, '2019-08-01 14:47:34'),
(283, 1, '2019-08-01 15:07:38'),
(284, 1, '2019-08-01 15:28:24'),
(285, 1, '2019-08-01 18:44:40'),
(286, 1, '2019-08-01 19:09:39'),
(287, 1, '2019-08-01 19:43:36'),
(288, 1, '2019-08-01 19:48:41'),
(289, 1, '2019-08-01 19:54:44'),
(290, 1, '2019-08-01 20:15:02'),
(291, 1, '2019-08-01 20:40:14'),
(292, 1, '2019-08-01 21:01:05'),
(293, 1, '2019-08-01 21:25:08'),
(294, 6, '2019-08-01 21:28:46'),
(295, 2, '2019-08-01 21:28:52'),
(296, 1, '2019-08-02 14:51:05'),
(297, 2, '2019-08-02 15:09:11'),
(298, 1, '2019-08-02 15:09:59'),
(299, 1, '2019-08-02 15:15:04'),
(300, 1, '2019-08-02 15:32:42'),
(301, 2, '2019-08-02 15:40:59'),
(302, 1, '2019-08-02 15:41:04'),
(303, 2, '2019-08-02 15:41:27'),
(304, 1, '2019-08-02 15:41:45'),
(305, 2, '2019-08-02 15:42:07'),
(306, 1, '2019-08-02 15:42:15'),
(307, 1, '2019-08-02 16:05:10'),
(308, 1, '2019-08-02 16:25:14'),
(309, 1, '2019-08-02 16:45:26'),
(310, 1, '2019-08-02 17:07:18'),
(311, 1, '2019-08-02 17:27:35'),
(312, 1, '2019-08-02 17:54:41'),
(313, 1, '2019-08-02 18:34:37'),
(314, 1, '2019-08-02 19:29:05'),
(315, 1, '2019-08-02 19:49:07'),
(316, 1, '2019-08-02 20:14:08'),
(317, 1, '2019-08-02 20:51:58'),
(318, 1, '2019-08-02 20:52:12'),
(319, 1, '2019-08-02 20:52:36'),
(320, 1, '2019-08-02 20:53:54'),
(321, 1, '2019-08-02 21:00:49'),
(322, 1, '2019-08-02 21:36:40'),
(323, 1, '2019-08-03 15:31:06'),
(324, 1, '2019-08-03 15:51:22'),
(325, 1, '2019-08-03 17:00:32'),
(326, 1, '2019-08-03 17:29:23'),
(327, 1, '2019-08-04 17:44:42'),
(328, 1, '2019-08-05 14:57:18'),
(329, 6, '2019-08-05 15:07:31'),
(330, 1, '2019-08-05 15:11:41'),
(331, 1, '2019-08-05 15:33:19'),
(332, 1, '2019-08-05 18:38:11'),
(333, 1, '2019-08-05 18:38:11'),
(334, 1, '2019-08-05 19:10:36'),
(335, 1, '2019-08-05 19:14:46'),
(336, 1, '2019-08-06 19:53:20'),
(337, 1, '2019-08-06 20:15:27'),
(338, 1, '2019-08-06 20:35:39'),
(339, 1, '2019-08-07 19:14:08'),
(340, 1, '2019-08-07 19:37:08'),
(341, 1, '2019-08-07 20:05:10'),
(342, 1, '2019-08-07 20:26:10'),
(343, 6, '2019-08-07 21:21:12'),
(344, 1, '2019-08-07 21:21:42'),
(345, 1, '2019-08-08 15:08:14'),
(346, 1, '2019-08-08 15:31:59'),
(347, 1, '2019-08-08 18:15:57'),
(348, 1, '2019-08-08 19:23:20'),
(349, 1, '2019-08-08 20:26:04'),
(350, 1, '2019-08-09 21:12:16'),
(351, 1, '2019-08-09 21:32:49'),
(352, 1, '2019-08-10 15:39:26'),
(353, 1, '2019-08-10 15:59:53'),
(354, 1, '2019-08-10 16:28:36'),
(355, 1, '2019-08-10 16:49:44'),
(356, 1, '2019-08-10 16:56:13'),
(357, 1, '2019-08-10 18:23:32'),
(358, 1, '2019-08-10 19:12:01'),
(359, 1, '2019-08-10 19:36:49'),
(360, 1, '2019-08-10 20:00:16'),
(361, 1, '2019-08-10 20:22:55'),
(362, 1, '2019-08-11 14:33:44'),
(363, 61, '2019-08-11 14:37:52'),
(364, 1, '2019-08-11 14:41:49'),
(365, 1, '2019-08-11 14:42:03'),
(366, 1, '2019-08-11 15:12:35'),
(367, 1, '2019-08-11 15:34:22'),
(368, 1, '2019-08-11 15:55:19'),
(369, 1, '2019-08-12 16:14:34'),
(370, 1, '2019-08-15 16:29:04'),
(371, 1, '2019-08-31 15:41:31'),
(372, 1, '2019-08-31 15:41:44'),
(373, 1, '2019-08-31 15:43:09'),
(374, 2, '2019-08-31 15:43:32'),
(375, 2, '2019-08-31 15:44:58'),
(376, 2, '2019-08-31 15:47:17'),
(377, 2, '2019-08-31 15:50:25'),
(378, 2, '2019-08-31 15:55:11'),
(379, 2, '2019-08-31 15:55:19'),
(380, 2, '2019-08-31 15:55:29'),
(381, 1, '2019-08-31 16:08:53'),
(382, 1, '2019-08-31 16:17:22'),
(383, 1, '2019-08-31 16:17:57'),
(384, 1, '2019-08-31 16:19:34'),
(385, 2, '2019-08-31 16:20:13'),
(386, 1, '2019-08-31 16:20:22'),
(387, 1, '2019-08-31 16:22:39'),
(388, 1, '2019-09-04 10:43:25'),
(389, 63, '2019-09-04 10:46:04'),
(390, 1, '2019-09-04 10:49:57'),
(391, 6, '2019-09-04 10:50:22'),
(392, 63, '2019-09-04 10:51:33'),
(393, 63, '2019-09-04 10:52:24'),
(394, 1, '2019-09-04 10:56:59'),
(395, 1, '2019-09-04 16:08:27'),
(396, 1, '2019-09-04 16:14:43'),
(397, 10, '2019-09-04 16:20:03'),
(398, 1, '2019-09-04 16:20:10'),
(399, 10, '2019-09-04 16:23:47'),
(400, 11, '2019-09-04 16:26:25'),
(401, 10, '2019-09-04 16:34:31'),
(402, 1, '2019-09-04 16:59:34'),
(403, 1, '2019-09-04 17:00:12'),
(404, 1, '2019-09-04 17:04:33'),
(405, 1, '2019-09-04 17:17:07'),
(406, 1, '2019-09-04 17:22:00'),
(407, 63, '2019-09-04 17:22:28'),
(408, 63, '2019-09-04 17:26:00'),
(409, 63, '2019-09-04 17:55:29'),
(410, 1, '2019-09-04 18:01:01'),
(411, 63, '2019-09-04 18:02:05'),
(412, 1, '2019-09-04 18:22:39'),
(413, 1, '2019-09-08 16:22:15'),
(414, 1, '2019-09-29 14:40:11'),
(415, 1, '2019-09-29 15:07:46'),
(416, 1, '2019-09-29 15:29:48'),
(417, 1, '2019-09-29 15:49:56'),
(418, 1, '2019-09-29 18:49:16'),
(419, 1, '2019-09-29 19:01:28'),
(420, 1, '2019-09-29 19:01:28'),
(421, 1, '2019-09-29 19:23:14'),
(422, 1, '2019-09-29 20:47:59'),
(423, 1, '2019-09-29 20:59:35'),
(424, 1, '2019-09-29 21:00:06');

-- --------------------------------------------------------

--
-- Структура таблицы `validators_requests`
--

CREATE TABLE `validators_requests` (
  `request_id` int(11) NOT NULL,
  `request_from` int(11) NOT NULL,
  `request_biography` varchar(1000) COLLATE utf8_bin NOT NULL,
  `checked` int(11) NOT NULL DEFAULT '0',
  `checked_by` int(11) NOT NULL,
  `request_date` datetime NOT NULL,
  `request_school` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `validators_requests`
--

INSERT INTO `validators_requests` (`request_id`, `request_from`, `request_biography`, `checked`, `checked_by`, `request_date`, `request_school`) VALUES
(6, 55, 'Try', 1, 1, '2019-05-19 21:00:40', 2),
(13, 1, 'Triad aoisj doias d', 0, 0, '2019-06-20 14:00:40', 2),
(14, 4, 'Kokoko', 1, 1, '2019-06-21 21:00:40', 1),
(15, 2, 'Kokokoadasdjasdkn', 0, 0, '2019-06-04 21:00:40', 2),
(16, 2, 'LOlololl', 0, 0, '2019-06-19 21:00:40', 2),
(17, 2, 'Kokoko', 1, 1, '2019-06-21 21:00:40', 1),
(19, 2, 'dfsdf sf sfsdf sdf sd fsdf sdfsdfsdfsdfsdfsdf', 0, 0, '2019-08-02 15:09:53', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `withdraw`
--

CREATE TABLE `withdraw` (
  `withdraw_id` int(11) NOT NULL,
  `withdraw_from` int(11) NOT NULL,
  `withdraw_sum` int(11) NOT NULL,
  `withdraw_description` varchar(255) COLLATE utf8_bin NOT NULL,
  `withdraw_status` int(11) NOT NULL DEFAULT '0' COMMENT '0 - Pending, 1 - Completed, 2 - Denied',
  `withdraw_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `withdraw`
--

INSERT INTO `withdraw` (`withdraw_id`, `withdraw_from`, `withdraw_sum`, `withdraw_description`, `withdraw_status`, `withdraw_date`) VALUES
(1, 1, 4, 'Withdraw referral bonuses.', 2, '2019-05-01 00:00:00'),
(2, 1, 1, 'Withdraw referral bonuses.', 2, '0000-00-00 00:00:00'),
(3, 1, 1, 'Withdraw referral bonuses.', 2, '2019-05-11 21:07:20'),
(4, 6, 100, 'asdasdasd', 2, '2019-06-11 00:00:00'),
(11, 1, 100000, 'Withdraw referral bonuses.', 2, '2019-05-02 21:50:12'),
(12, 1, 38, 'Default balance withdraw', 2, '2019-06-02 20:56:44'),
(13, 1, 28, 'Default balance withdraw', 2, '2019-06-02 20:57:15'),
(14, 1, 38, 'Default balance withdraw', 2, '2019-06-02 20:59:46'),
(15, 1, 28, 'Default balance withdraw', 0, '2019-06-02 21:01:30'),
(17, 1, 100, 'Withdraw referral bonuses.', 2, '2019-07-03 14:32:06'),
(18, 1, 47, 'StandardnÃ­ vÃ½bÄ›r z konta', 2, '2019-07-15 16:28:40'),
(19, 1, 47, 'StandardnÃ­ vÃ½bÄ›r z konta', 2, '2019-07-15 16:31:55'),
(21, 6, 95, 'StandardnÃ­ vÃ½bÄ›r z konta', 1, '2019-07-16 16:24:45'),
(22, 1, 105, 'StandardnÃ­ vÃ½bÄ›r z konta', 1, '2019-07-16 16:47:00'),
(25, 1, 6, 'VÃ½bÄ›r z pozvanÃ½ch lidÃ­.', 0, '2019-07-19 15:56:49'),
(29, 63, 95, 'Standardní výběr z konta', 1, '2019-09-04 18:00:30'),
(30, 63, 390, 'Standardní výběr z konta', 0, '2019-09-04 18:00:34'),
(31, 1, 202, 'Standardní výběr z konta', 0, '2019-09-29 21:08:35'),
(32, 1, 95, 'Standardní výběr z konta', 0, '2019-09-29 21:09:01');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `banned_users`
--
ALTER TABLE `banned_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `banned_by` (`banned_by`),
  ADD KEY `banned_id` (`banned_id`);

--
-- Индексы таблицы `black_points`
--
ALTER TABLE `black_points`
  ADD PRIMARY KEY (`bp_id`),
  ADD KEY `bp_userid` (`bp_userid`),
  ADD KEY `bp_givenby` (`bp_givenby`);

--
-- Индексы таблицы `buy_events`
--
ALTER TABLE `buy_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Индексы таблицы `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`city_id`);

--
-- Индексы таблицы `codes`
--
ALTER TABLE `codes`
  ADD PRIMARY KEY (`code_id`),
  ADD KEY `code_generatedby` (`code_generatedby`),
  ADD KEY `code_activatedby` (`code_activatedby`);

--
-- Индексы таблицы `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `district`
--
ALTER TABLE `district`
  ADD PRIMARY KEY (`district_id`),
  ADD KEY `city_id` (`city_id`);

--
-- Индексы таблицы `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`faq_id`),
  ADD KEY `faq_createdby` (`faq_createdby`);

--
-- Индексы таблицы `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`group_id`);

--
-- Индексы таблицы `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`image_id`);

--
-- Индексы таблицы `lottery`
--
ALTER TABLE `lottery`
  ADD PRIMARY KEY (`lottery_id`),
  ADD KEY `lottery_userid` (`lottery_userid`);

--
-- Индексы таблицы `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `message_from` (`message_from`),
  ADD KEY `message_to` (`message_to`);

--
-- Индексы таблицы `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`),
  ADD KEY `news_createdby` (`news_createdby`);

--
-- Индексы таблицы `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referrals_userid` (`referrals_userid`),
  ADD KEY `referrals_userby` (`referrals_userby`);

--
-- Индексы таблицы `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`report_id`);

--
-- Индексы таблицы `school`
--
ALTER TABLE `school`
  ADD PRIMARY KEY (`school_id`),
  ADD KEY `district_id` (`district_id`),
  ADD KEY `added_by` (`added_by`),
  ADD KEY `checked_by` (`checked_by`);

--
-- Индексы таблицы `school_add`
--
ALTER TABLE `school_add`
  ADD PRIMARY KEY (`sa_id`),
  ADD KEY `sa_userid` (`sa_userid`),
  ADD KEY `sa_confirmedby` (`sa_confirmedby`);

--
-- Индексы таблицы `school_change`
--
ALTER TABLE `school_change`
  ADD PRIMARY KEY (`id`),
  ADD KEY `change_school_id_from` (`change_school_id_from`),
  ADD KEY `change_school_id_to` (`change_school_id_to`);

--
-- Индексы таблицы `shop`
--
ALTER TABLE `shop`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `confirmed_by` (`confirmed_by`),
  ADD KEY `item_createdby_userid` (`item_createdby_userid`);

--
-- Индексы таблицы `shop_earn`
--
ALTER TABLE `shop_earn`
  ADD PRIMARY KEY (`shopearn_id`),
  ADD KEY `shopearn_itemid` (`shopearn_itemid`);

--
-- Индексы таблицы `shop_remove_log`
--
ALTER TABLE `shop_remove_log`
  ADD PRIMARY KEY (`removed_id`),
  ADD KEY `removed_item` (`removed_item`),
  ADD KEY `removed_by` (`removed_by`);

--
-- Индексы таблицы `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`status_id`);

--
-- Индексы таблицы `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `ticked_createdby` (`ticket_createdby`);

--
-- Индексы таблицы `ticket_comments`
--
ALTER TABLE `ticket_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Индексы таблицы `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`t_id`),
  ADD KEY `t_userid` (`t_from`);

--
-- Индексы таблицы `tutorial`
--
ALTER TABLE `tutorial`
  ADD PRIMARY KEY (`tutorial_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `unbanned_users`
--
ALTER TABLE `unbanned_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unbanned_id` (`unbanned_id`),
  ADD KEY `unbanned_by` (`unbanned_by`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Индексы таблицы `users_groups`
--
ALTER TABLE `users_groups`
  ADD PRIMARY KEY (`bridge_id`),
  ADD KEY `set_by` (`set_by`);

--
-- Индексы таблицы `users_log`
--
ALTER TABLE `users_log`
  ADD PRIMARY KEY (`ul_id`),
  ADD KEY `ul_user_id` (`ul_user_id`);

--
-- Индексы таблицы `validators_requests`
--
ALTER TABLE `validators_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `request_from` (`request_from`),
  ADD KEY `checked_by` (`checked_by`);

--
-- Индексы таблицы `withdraw`
--
ALTER TABLE `withdraw`
  ADD PRIMARY KEY (`withdraw_id`),
  ADD KEY `withdraw_from` (`withdraw_from`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `banned_users`
--
ALTER TABLE `banned_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT для таблицы `black_points`
--
ALTER TABLE `black_points`
  MODIFY `bp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `buy_events`
--
ALTER TABLE `buy_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT для таблицы `city`
--
ALTER TABLE `city`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT для таблицы `codes`
--
ALTER TABLE `codes`
  MODIFY `code_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `district`
--
ALTER TABLE `district`
  MODIFY `district_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT для таблицы `faq`
--
ALTER TABLE `faq`
  MODIFY `faq_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT для таблицы `groups`
--
ALTER TABLE `groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `images`
--
ALTER TABLE `images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT для таблицы `lottery`
--
ALTER TABLE `lottery`
  MODIFY `lottery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT для таблицы `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=214;

--
-- AUTO_INCREMENT для таблицы `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `report`
--
ALTER TABLE `report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `school`
--
ALTER TABLE `school`
  MODIFY `school_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT для таблицы `school_add`
--
ALTER TABLE `school_add`
  MODIFY `sa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `school_change`
--
ALTER TABLE `school_change`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `shop`
--
ALTER TABLE `shop`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT для таблицы `shop_earn`
--
ALTER TABLE `shop_earn`
  MODIFY `shopearn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT для таблицы `shop_remove_log`
--
ALTER TABLE `shop_remove_log`
  MODIFY `removed_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `statuses`
--
ALTER TABLE `statuses`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT для таблицы `ticket_comments`
--
ALTER TABLE `ticket_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT для таблицы `transactions`
--
ALTER TABLE `transactions`
  MODIFY `t_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `tutorial`
--
ALTER TABLE `tutorial`
  MODIFY `tutorial_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `unbanned_users`
--
ALTER TABLE `unbanned_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT для таблицы `users_groups`
--
ALTER TABLE `users_groups`
  MODIFY `bridge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT для таблицы `users_log`
--
ALTER TABLE `users_log`
  MODIFY `ul_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=425;

--
-- AUTO_INCREMENT для таблицы `validators_requests`
--
ALTER TABLE `validators_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `withdraw`
--
ALTER TABLE `withdraw`
  MODIFY `withdraw_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `banned_users`
--
ALTER TABLE `banned_users`
  ADD CONSTRAINT `banned_users_ibfk_1` FOREIGN KEY (`banned_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `banned_users_ibfk_2` FOREIGN KEY (`banned_id`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `black_points`
--
ALTER TABLE `black_points`
  ADD CONSTRAINT `black_points_ibfk_1` FOREIGN KEY (`bp_userid`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `black_points_ibfk_2` FOREIGN KEY (`bp_givenby`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `buy_events`
--
ALTER TABLE `buy_events`
  ADD CONSTRAINT `buy_events_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `buy_events_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `buy_events_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `shop` (`item_id`);

--
-- Ограничения внешнего ключа таблицы `codes`
--
ALTER TABLE `codes`
  ADD CONSTRAINT `codes_ibfk_1` FOREIGN KEY (`code_generatedby`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `codes_ibfk_2` FOREIGN KEY (`code_activatedby`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `district`
--
ALTER TABLE `district`
  ADD CONSTRAINT `district_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `city` (`city_id`);

--
-- Ограничения внешнего ключа таблицы `faq`
--
ALTER TABLE `faq`
  ADD CONSTRAINT `faq_ibfk_1` FOREIGN KEY (`faq_createdby`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `lottery`
--
ALTER TABLE `lottery`
  ADD CONSTRAINT `lottery_ibfk_1` FOREIGN KEY (`lottery_userid`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`message_from`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`message_to`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`news_createdby`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `referrals`
--
ALTER TABLE `referrals`
  ADD CONSTRAINT `referrals_ibfk_1` FOREIGN KEY (`referrals_userid`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `referrals_ibfk_2` FOREIGN KEY (`referrals_userby`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `school`
--
ALTER TABLE `school`
  ADD CONSTRAINT `school_ibfk_1` FOREIGN KEY (`district_id`) REFERENCES `district` (`district_id`),
  ADD CONSTRAINT `school_ibfk_2` FOREIGN KEY (`added_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `school_ibfk_3` FOREIGN KEY (`checked_by`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `school_add`
--
ALTER TABLE `school_add`
  ADD CONSTRAINT `school_add_ibfk_1` FOREIGN KEY (`sa_userid`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `school_add_ibfk_2` FOREIGN KEY (`sa_confirmedby`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `school_change`
--
ALTER TABLE `school_change`
  ADD CONSTRAINT `school_change_ibfk_1` FOREIGN KEY (`change_school_id_from`) REFERENCES `school` (`school_id`),
  ADD CONSTRAINT `school_change_ibfk_2` FOREIGN KEY (`change_school_id_to`) REFERENCES `school` (`school_id`);

--
-- Ограничения внешнего ключа таблицы `shop`
--
ALTER TABLE `shop`
  ADD CONSTRAINT `shop_ibfk_1` FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `shop_ibfk_2` FOREIGN KEY (`item_createdby_userid`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `shop_earn`
--
ALTER TABLE `shop_earn`
  ADD CONSTRAINT `shop_earn_ibfk_1` FOREIGN KEY (`shopearn_itemid`) REFERENCES `shop` (`item_id`);

--
-- Ограничения внешнего ключа таблицы `shop_remove_log`
--
ALTER TABLE `shop_remove_log`
  ADD CONSTRAINT `shop_remove_log_ibfk_1` FOREIGN KEY (`removed_item`) REFERENCES `shop` (`item_id`),
  ADD CONSTRAINT `shop_remove_log_ibfk_2` FOREIGN KEY (`removed_by`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`ticket_createdby`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `ticket_comments`
--
ALTER TABLE `ticket_comments`
  ADD CONSTRAINT `ticket_comments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`);

--
-- Ограничения внешнего ключа таблицы `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`t_from`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `tutorial`
--
ALTER TABLE `tutorial`
  ADD CONSTRAINT `tutorial_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `unbanned_users`
--
ALTER TABLE `unbanned_users`
  ADD CONSTRAINT `unbanned_users_ibfk_1` FOREIGN KEY (`unbanned_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `unbanned_users_ibfk_2` FOREIGN KEY (`unbanned_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `unbanned_users_ibfk_3` FOREIGN KEY (`unbanned_by`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `users_groups`
--
ALTER TABLE `users_groups`
  ADD CONSTRAINT `users_groups_ibfk_1` FOREIGN KEY (`set_by`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `users_log`
--
ALTER TABLE `users_log`
  ADD CONSTRAINT `users_log_ibfk_1` FOREIGN KEY (`ul_user_id`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `validators_requests`
--
ALTER TABLE `validators_requests`
  ADD CONSTRAINT `validators_requests_ibfk_1` FOREIGN KEY (`request_from`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `validators_requests_ibfk_2` FOREIGN KEY (`request_from`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `validators_requests_ibfk_3` FOREIGN KEY (`request_from`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `validators_requests_ibfk_4` FOREIGN KEY (`checked_by`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `withdraw`
--
ALTER TABLE `withdraw`
  ADD CONSTRAINT `withdraw_ibfk_1` FOREIGN KEY (`withdraw_from`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
