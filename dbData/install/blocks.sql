CREATE TABLE IF NOT EXISTS `prefix_blocks`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `order` INTEGER(11) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `callname` VARCHAR(128) NOT NULL,
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    `text` TEXT NOT NULL,
    PRIMARY KEY (`id`),
    KEY `callname` (`callname`),
    KEY `show` (`show`),
    KEY `deleted` (`deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
