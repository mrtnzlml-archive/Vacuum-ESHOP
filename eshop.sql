SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = '+02:00';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXIST `categories`;
CREATE TABLE `categories` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(200) COLLATE utf8_general_ci NOT NULL,
	`slug` varchar(200) COLLATE utf8_general_ci NOT NULL,
	`priority` int(11) NOT NULL DEFAULT '0',
	`parent` int(11) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `fullname` varchar(200) COLLATE utf8_general_ci NOT NULL,
  `street` varchar(200) COLLATE utf8_general_ci NOT NULL,
  `city` varchar(200) COLLATE utf8_general_ci NOT NULL,
  `zip` varchar(200) COLLATE utf8_general_ci NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('new','complete') COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXIST `products`;
CREATE TABLE `products` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(200) COLLATE utf8_general_ci NOT NULL,
	`slug` varchar(200) COLLATE utf8_general_ci NOT NULL,
	`priority` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(200) COLLATE utf8_general_ci NOT NULL,
  `value` varchar(200) COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `settings` (`id`, `key`, `value`) VALUES
(1,	'dph',	'21'),
(2,	'show_empty_in_menu',	'0'),
(3,	'show_numbers_in_menu',	'0'),
(4,	'title_prefix',	'eShop'),
(5,	'title_sufix',	''),
(6,	'title_separator',	'|'),
(7,	'items_per_page',	'18');

DROP TABLE IF EXIST `users`;
CREATE TABLE `users` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`username` varchar(200) COLLATE utf8_general_ci NOT NULL,
	`password` varchar(200) COLLATE utf8_general_ci NOT NULL,
	`role` varchar(100) COLLATE utf8_general_ci NOT NULL,
	`fullname` varchar(200) COLLATE utf8_general_ci NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `users` (`id`, `username`, `password`, `role`, `fullname`) VALUES
(1,	'admin',	'$2a$07$972m0h23wjwoenzqfo379ezvNXlGWHjYfjPrdEvWp3qfvL.uJPVtO',	'admin', 'John Admin'), ---admin/admin
(2,	'moderator',	'$2a$07$mtxxd5usykkz3dtvg7e9keCwhnT3y3jJ2TjtJpD5ylSGlgW9Yl6Ua',	'moderator', 'John Mod'), ---moderator/moderator
(3,	'waiting',	'$2a$07$5n2ymz04jo1olto9oybi8e7RaYBm8kXT/7FcnzrJOX8eZxPUsJdWa',	'waiting', 'John Wait'), ---waiting/waiting
(4,	'approved',	'$2a$07$pwekk6rpjuxhy0alrs58yevfHq.ymO/ZWCNTWFvb0o7C/LJ6X.ozi',	'approved', 'John Approve'); ---approved/approved

------------------------------------------------------------
------------------------------------------------------------
------------------------------------------------------------

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `configuration` varchar(500) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

DROP TABLE IF EXISTS `pictures`;
CREATE TABLE `pictures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_czech_ci NOT NULL,
  `product_id` int(11) NOT NULL,
  `promo` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `pictures_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_czech_ci NOT NULL,
  `description` text COLLATE utf8_czech_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `event_date` datetime NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  `active` enum('y','x','n') COLLATE utf8_czech_ci NOT NULL,
  `category_id` int(11) NOT NULL,
  `promo` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `role` enum('guest','waiting','approved','moderator','admin') COLLATE utf8_czech_ci NOT NULL,
  `company_name` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `seat` text COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `tel` varchar(25) COLLATE utf8_czech_ci NOT NULL,
  `web` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `IC` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `DIC` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `account` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `represented_by` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

DROP TABLE IF EXISTS `variants`;
CREATE TABLE `variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

DROP TABLE IF EXISTS `variants_items`;
CREATE TABLE `variants_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variants_id` int(11) NOT NULL,
  `name` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `price` double NOT NULL,
  `price_status` enum('abs','price','rel') COLLATE utf8_czech_ci NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_variants_id` (`name`,`variants_id`),
  KEY `variants_id` (`variants_id`),
  CONSTRAINT `variants_items_ibfk_1` FOREIGN KEY (`variants_id`) REFERENCES `variants` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

DROP TABLE IF EXISTS `variants_items_individual`;
CREATE TABLE `variants_items_individual` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variants_id` int(11) NOT NULL,
  `name` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `price` double NOT NULL,
  `price_status` enum('abs','rel') COLLATE utf8_czech_ci NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `variants_id` (`variants_id`),
  CONSTRAINT `variants_items_individual_ibfk_1` FOREIGN KEY (`variants_id`) REFERENCES `variants` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

DROP TABLE IF EXISTS `variants_products`;
CREATE TABLE `variants_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variants_id` int(11) NOT NULL,
  `products_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `variants_id_products_id` (`variants_id`,`products_id`),
  KEY `products_id` (`products_id`),
  CONSTRAINT `variants_products_ibfk_1` FOREIGN KEY (`variants_id`) REFERENCES `variants` (`id`),
  CONSTRAINT `variants_products_ibfk_2` FOREIGN KEY (`products_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;