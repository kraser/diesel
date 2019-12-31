CREATE TABLE IF NOT EXISTS `prefix_products_currencies`
(
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(5) NOT NULL,
    `symbol` VARCHAR(10) NOT NULL DEFAULT '',
    `value` DECIMAL(10,4) NOT NULL DEFAULT '1',
    `symbol_position` ENUM('left','right') NOT NULL DEFAULT 'right',
    `is_main` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;
