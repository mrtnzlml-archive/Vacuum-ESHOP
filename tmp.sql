DROP TABLE IF EXISTS `product_variant`;
CREATE TABLE `product_variant` (
	`id`         INT(11) NOT NULL AUTO_INCREMENT,
	`variant_id` INT(11) NOT NULL,
	`product_id` INT(11) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `variants_id_products_id` (`variant_id`, `product_id`),
	KEY `products_id` (`product_id`),
	CONSTRAINT `product_variant_ibfk_5` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	CONSTRAINT `product_variant_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `variant` (`id`)
)
	ENGINE =InnoDB
	DEFAULT CHARSET =utf8
	COLLATE =utf8_czech_ci;

DROP TABLE IF EXISTS `variant`;
CREATE TABLE `variant` (
	`id`   INT(11)               NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(200)
				 COLLATE utf8_czech_ci NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `name_lc` (`name`)
)
	ENGINE =InnoDB
	DEFAULT CHARSET =utf8
	COLLATE =utf8_czech_ci;


DROP TABLE IF EXISTS `variant_item`;
CREATE TABLE `variant_item` (
	`id`           INT(11)               NOT NULL AUTO_INCREMENT,
	`name`         VARCHAR(200)
								 COLLATE utf8_czech_ci NOT NULL,
	`price`        DOUBLE                NOT NULL,
	`price_status` ENUM('abs', 'price', 'rel')
								 COLLATE utf8_czech_ci NOT NULL,
	`priority`     INT(11)               NOT NULL DEFAULT '0',
	`variant_id`   INT(11)               NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `name_variants_id` (`name`, `variant_id`),
	KEY `variants_id` (`variant_id`),
	CONSTRAINT `variant_item_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `variant` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
)
	ENGINE =InnoDB
	DEFAULT CHARSET =utf8
	COLLATE =utf8_czech_ci;


DROP TABLE IF EXISTS `variants_items_individual`;
CREATE TABLE `variants_items_individual` (
	`id`           INT(11)               NOT NULL AUTO_INCREMENT,
	`variants_id`  INT(11)               NOT NULL,
	`name`         VARCHAR(200)
								 COLLATE utf8_czech_ci NOT NULL,
	`price`        DOUBLE                NOT NULL,
	`price_status` ENUM('abs', 'rel')
								 COLLATE utf8_czech_ci NOT NULL,
	`priority`     INT(11)               NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `variants_id` (`variants_id`),
	CONSTRAINT `variants_items_individual_ibfk_1` FOREIGN KEY (`variants_id`) REFERENCES `variant` (`id`)
)
	ENGINE =InnoDB
	DEFAULT CHARSET =utf8
	COLLATE =utf8_czech_ci;