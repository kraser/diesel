CREATE TABLE IF NOT EXISTS `prefix_shop_paymethods`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `order` INTEGER(11) NOT NULL,
    `type` VARCHAR(32) NOT NULL,
    `name` VARCHAR(128) NOT NULL,
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `text` TEXT NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;


