-- Adminer 4.7.6 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_number` (`account_number`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `accounts` (`id`, `user_id`, `account_number`, `balance`) VALUES
(1,	1,	'ALICE_1000001',	600),
(2,	1,	'ALICE_1000002',	200),
(3,	2,	'BOB_1000003',	200),
(4,	3,	'JOHN_1000004',	0);

DROP TABLE IF EXISTS `accounts_balance`;
CREATE TABLE `accounts_balance` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `gmtime` datetime NOT NULL,
  `account_id` bigint(20) NOT NULL,
  `value` double NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_id_gmtime` (`account_id`,`gmtime`),
  KEY `gmtime` (`gmtime`),
  CONSTRAINT `accounts_balance_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `accounts_balance` (`id`, `gmtime`, `account_id`, `value`) VALUES
(2,	'2022-01-22 12:00:00',	1,	0),
(3,	'2022-01-22 15:00:00',	1,	300),
(4,	'2022-01-22 15:00:00',	2,	200),
(5,	'2022-01-22 15:00:00',	3,	200),
(6,	'2022-01-22 15:00:00',	4,	300),
(7,	'2022-01-22 15:30:00',	1,	600),
(8,	'2022-01-22 15:30:00',	2,	200),
(9,	'2022-01-22 15:30:00',	3,	200),
(10,	'2022-01-22 15:30:00',	4,	0),
(11,	'2022-01-22 16:00:00',	1,	600),
(12,	'2022-01-22 16:00:00',	2,	200),
(13,	'2022-01-22 16:00:00',	3,	200),
(14,	'2022-01-22 16:00:00',	4,	0);

DROP TABLE IF EXISTS `accounts_history`;
CREATE TABLE `accounts_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `gmtime` datetime NOT NULL,
  `account_id` bigint(20) NOT NULL,
  `from_account_id` bigint(20) DEFAULT NULL,
  `money` double NOT NULL,
  `description` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`),
  KEY `from_account_id` (`from_account_id`),
  CONSTRAINT `accounts_history_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `accounts_history_ibfk_3` FOREIGN KEY (`from_account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `accounts_history` (`id`, `gmtime`, `account_id`, `from_account_id`, `money`, `description`) VALUES
(55,	'2022-01-22 14:01:51',	1,	NULL,	1000,	'Add 1000'),
(58,	'2022-01-22 14:04:47',	1,	2,	-500,	'Transfer 500'),
(59,	'2022-01-22 14:04:47',	2,	1,	500,	'Transfer 500'),
(60,	'2022-01-22 14:07:24',	1,	3,	-200,	'Transfer 500'),
(61,	'2022-01-22 14:07:24',	3,	1,	200,	'Transfer 500'),
(62,	'2022-01-22 14:08:05',	1,	4,	-300,	'Transfer 300'),
(63,	'2022-01-22 14:08:05',	4,	1,	300,	'Transfer 300'),
(64,	'2022-01-22 14:12:19',	2,	4,	-300,	'Transfer 300'),
(65,	'2022-01-22 14:12:19',	4,	2,	300,	'Transfer 300'),
(66,	'2022-01-22 14:13:48',	4,	1,	-300,	'Transfer 300'),
(67,	'2022-01-22 14:13:48',	1,	4,	300,	'Transfer 300'),
(68,	'2022-01-22 15:25:56',	4,	1,	-300,	'123'),
(69,	'2022-01-22 15:25:56',	1,	4,	300,	'123');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`) VALUES
(1,	'Alice'),
(2,	'Bob'),
(3,	'John');

-- 2022-01-22 16:01:50