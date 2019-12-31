CREATE TABLE IF NOT EXISTS `prefix_shop_orders`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `userId` INTEGER(11) DEFAULT NULL,
    `name` VARCHAR(255) NOT NULL,
    `mail` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `comment` TEXT NOT NULL,
    `paymethod` INTEGER(11) NOT NULL,
    `orderSum` DECIMAL (10, 2) NOT NULL DEFAULT 0 COMMENT 'Сумма заказа',
    `shopId` INT(11) NOT NULL DEFAULT 0,
    `status` INTEGER(11) NOT NULL,
    `date` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;
