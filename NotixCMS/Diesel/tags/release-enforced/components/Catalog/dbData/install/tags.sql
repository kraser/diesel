CREATE TABLE IF NOT EXISTS `prefix_catalog_tags`
(
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `alias` VARCHAR(100) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `tagType` ENUM ('INTERVAL', 'SET', 'ENUM') NOT NULL default 'SET',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `alias` (`alias`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;
