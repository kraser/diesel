CREATE TABLE IF NOT EXISTS `prefix_forms`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `order` INTEGER(11) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `callname` VARCHAR(128) NOT NULL,
    `template` VARCHAR(64)  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
    `email` VARCHAR(255) NOT NULL,
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;
