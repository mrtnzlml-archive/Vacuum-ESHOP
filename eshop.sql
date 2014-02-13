SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';


-- -- -- PRODUCTS -- -- --


DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
	`id`          INT(11)                  NOT NULL AUTO_INCREMENT,
	`name`        TEXT                     NOT NULL,
	`description` TEXT                     NOT NULL,
	`slug`        VARCHAR(255)             NOT NULL,
	`price`       DECIMAL(10, 2)           NOT NULL,
	`stock`       INT(11)                  NOT NULL DEFAULT '0',
	`priority`    INT(11)                  NOT NULL DEFAULT '0',
	`active`      ENUM('y', 'x', 'n', 'd') NOT NULL DEFAULT 'n',
	`category_id` INT(11) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `slug` (`slug`),
	KEY `category_id` (`category_id`),
	CONSTRAINT `product_ibfk` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
		ON DELETE SET NULL -- ON CATEGORY DELETE SET NULL
		ON UPDATE CASCADE -- ON CATEGORY UPDATE BE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8;


DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
	`id`       INT(11)      NOT NULL AUTO_INCREMENT,
	`name`     TEXT         NOT NULL,
	`slug`     VARCHAR(255) NOT NULL,
	`priority` INT(11)      NOT NULL DEFAULT '0',
	`parent`   INT(11) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `slug` (`slug`),
	KEY `parent` (`parent`),
	CONSTRAINT `category_ibfk` FOREIGN KEY (`parent`) REFERENCES `category` (`id`)
		ON DELETE SET NULL -- ON PARENT CATEGORY DELETE SET NULL
		ON UPDATE CASCADE -- ON PARENT CATEGORY UPDATE BE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8;


DROP TABLE IF EXISTS `picture`;
CREATE TABLE `picture` (
	`id`         INT(11)    NOT NULL AUTO_INCREMENT,
	`name`       TEXT       NOT NULL,
	`promo`      TINYINT(1) NOT NULL,
	`product_id` INT(11)    NOT NULL,
	PRIMARY KEY (`id`),
	KEY `product_id` (`product_id`),
	CONSTRAINT `picture_ibfk` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
		ON DELETE CASCADE -- ON PRODUCT DELETE BE CASCADE
		ON UPDATE CASCADE -- ON PRODUCT UPDATE BE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8;


-- -- -- ORDERS -- -- --


DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
	`id`       INT(11)                            NOT NULL AUTO_INCREMENT,
	`created`  DATETIME                           NOT NULL,
	`username` VARCHAR(255)                       NOT NULL,
	`ic`       VARCHAR(255)                       NOT NULL,
	`dic`      VARCHAR(255)                       NOT NULL,
	`total`    DECIMAL(10, 2)                     NOT NULL,
	`status`   ENUM('new', 'complete', 'deleted') NOT NULL,
	PRIMARY KEY (`id`)
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8;


DROP TABLE IF EXISTS `order_product`;
CREATE TABLE `order_product` (
	`id`         INT(11)        NOT NULL AUTO_INCREMENT,
	`order_id`   INT(11)        NOT NULL,
	`product_id` INT(11)        NOT NULL,
	`price`      DECIMAL(10, 2) NOT NULL,
	`quantity`   INT(11)        NOT NULL,
-- `configuration` VARCHAR(500) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `order_id` (`order_id`),
	KEY `product_id` (`product_id`),
	CONSTRAINT `order_product_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
		ON DELETE RESTRICT -- RESTRICT PRODUCT DELETE
		ON UPDATE CASCADE, -- ON PRODUCT UPDATE BE CASCADE
	CONSTRAINT `order_product_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`)
		ON DELETE CASCADE -- ON ORDER DELETE BE CASCADE
		ON UPDATE CASCADE -- ON ORDER UPDATE BE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8;


-- -- -- OTHER -- -- --


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
	`id`           INT(11)                                NOT NULL AUTO_INCREMENT,
	`username`     VARCHAR(255)                           NOT NULL,
	`password`     VARCHAR(60)                            NOT NULL,
	`role`         ENUM('guest', 'waiting',
											'approved', 'moderator', 'admin') NOT NULL,
	`company_name` VARCHAR(255)                           NOT NULL,
	`email`        VARCHAR(255)                           NOT NULL,
	`tel`          VARCHAR(25)                            NOT NULL,
	`web`          VARCHAR(255)                           NOT NULL,
	`ic`           VARCHAR(255)                           NOT NULL,
	`dic`          VARCHAR(255)                           NOT NULL,
	`account`      VARCHAR(255)                           NOT NULL,
	`note`         TEXT                                   NOT NULL,
	`created`      DATETIME DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `username` (`username`)
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8;


DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
	`id`    INT(11)      NOT NULL AUTO_INCREMENT,
	`key`   VARCHAR(255) NOT NULL,
	`value` TEXT         NOT NULL,
	PRIMARY KEY (`id`)
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8;
INSERT INTO `setting` (`id`, `key`, `value`) VALUES
	(1, 'dph', '21'),
	(2, 'show_empty_in_menu', '1'),
	(3, 'show_numbers_in_menu', '0'),
	(4, 'title_prefix', 'eShop'),
	(5, 'title_sufix', ''),
	(6, 'title_separator', '|'),
	(7, 'items_per_page', '18');


-- ---------------------------------------------------------------------------------
-- ---------------------------------------------------------------------------------
-- ---------------------------------------------------------------------------------


INSERT INTO `category` (id, name, slug, priority, parent) VALUES
	(1, 'Category1', 'category-1', '', NULL),
	(2, 'Category2', 'category-2', '', 1);
INSERT INTO `product` (id, name, description, slug, price, priority, active, category_id) VALUES
	(1, 'Product1', 'Description1', 'product-1', 100.00, '', 'y', 1),
	(2, 'Product2', 'Description2', 'product-2', 100.00, '', 'y', 2);