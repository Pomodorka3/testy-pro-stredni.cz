-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Сен 30 2019 г., 18:48
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

-- --------------------------------------------------------

--
-- Структура таблицы `city`
--

CREATE TABLE `city` (
  `city_id` int(11) NOT NULL,
  `city_name` varchar(30) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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

-- --------------------------------------------------------

--
-- Структура таблицы `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `MAINTAIN_MODE` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `district`
--

CREATE TABLE `district` (
  `district_id` int(11) NOT NULL,
  `district_name` varchar(30) COLLATE utf8_bin NOT NULL,
  `city_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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

-- --------------------------------------------------------

--
-- Структура таблицы `groups`
--

CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(30) COLLATE utf8_bin NOT NULL,
  `group_description` varchar(200) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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

-- --------------------------------------------------------

--
-- Структура таблицы `statistics`
--

CREATE TABLE `statistics` (
  `shop_earn` int(11) NOT NULL DEFAULT '0',
  `withdraw_earn` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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

-- --------------------------------------------------------

--
-- Структура таблицы `users_log`
--

CREATE TABLE `users_log` (
  `ul_id` int(11) NOT NULL,
  `ul_user_id` int(11) NOT NULL,
  `ul_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `black_points`
--
ALTER TABLE `black_points`
  MODIFY `bp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `buy_events`
--
ALTER TABLE `buy_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `city`
--
ALTER TABLE `city`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `codes`
--
ALTER TABLE `codes`
  MODIFY `code_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `district`
--
ALTER TABLE `district`
  MODIFY `district_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `faq`
--
ALTER TABLE `faq`
  MODIFY `faq_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `groups`
--
ALTER TABLE `groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `images`
--
ALTER TABLE `images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `lottery`
--
ALTER TABLE `lottery`
  MODIFY `lottery_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `report`
--
ALTER TABLE `report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `school`
--
ALTER TABLE `school`
  MODIFY `school_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `school_add`
--
ALTER TABLE `school_add`
  MODIFY `sa_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `school_change`
--
ALTER TABLE `school_change`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `shop`
--
ALTER TABLE `shop`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `shop_earn`
--
ALTER TABLE `shop_earn`
  MODIFY `shopearn_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `shop_remove_log`
--
ALTER TABLE `shop_remove_log`
  MODIFY `removed_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `statuses`
--
ALTER TABLE `statuses`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `ticket_comments`
--
ALTER TABLE `ticket_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `transactions`
--
ALTER TABLE `transactions`
  MODIFY `t_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tutorial`
--
ALTER TABLE `tutorial`
  MODIFY `tutorial_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `unbanned_users`
--
ALTER TABLE `unbanned_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users_groups`
--
ALTER TABLE `users_groups`
  MODIFY `bridge_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users_log`
--
ALTER TABLE `users_log`
  MODIFY `ul_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `validators_requests`
--
ALTER TABLE `validators_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `withdraw`
--
ALTER TABLE `withdraw`
  MODIFY `withdraw_id` int(11) NOT NULL AUTO_INCREMENT;

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
