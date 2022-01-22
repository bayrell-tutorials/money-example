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
(1,	1,	'ALICE_1000001',	0),
(2,	1,	'ALICE_1000002',	0),
(3,	2,	'BOB_1000003',	0),
(4,	3,	'JOHN_1000004',	0);

DROP TABLE IF EXISTS `accounts_balance`;
CREATE TABLE `accounts_balance` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `gmtime` datetime NOT NULL,
  `account_id` bigint(20) NOT NULL,
  `value` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id_gmtime` (`account_id`,`gmtime`),
  KEY `gmtime` (`gmtime`),
  CONSTRAINT `accounts_balance_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `accounts_history`;
CREATE TABLE `accounts_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `gmtime` datetime NOT NULL,
  `account_id` bigint(20) NOT NULL,
  `money` double NOT NULL,
  `description` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`),
  CONSTRAINT `accounts_history_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


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

-- 2022-01-19 14:20:24