-- update for timestamp 1459682297
CREATE TABLE IF NOT EXISTS `prefix_users`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `login` VARCHAR(64) NOT NULL DEFAULT '',
    `passwd` VARCHAR(128) NOT NULL,
    `status` ENUM('super','admin','manager','client', 'guest') NOT NULL DEFAULT 'guest',
    `firstName` VARCHAR(255) NOT NULL DEFAULT '',
    `lastName` VARCHAR(255) NOT NULL DEFAULT '',
    `address` VARCHAR(255) DEFAULT NULL,
    `phone` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `anons` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `company` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;