CREATE TABLE IF NOT EXISTS `prefix_blog`
(
    `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `date` DATETIME NOT NULL,
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    `anons` TEXT NOT NULL,
    `text` TEXT NOT NULL,
    `top` INT(11) NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM  DEFAULT CHARSET=utf8;
