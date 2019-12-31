CREATE TABLE IF NOT EXISTS `prefix_products_brands`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `order` INTEGER(11) NOT NULL,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `nav` VARCHAR(255) NOT NULL DEFAULT '',
    `text` TEXT NOT NULL,
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `nav` (`nav`),
    KEY `name` (`name`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;
