-- upadte for timestamp 1419189644
CREATE TABLE IF NOT EXISTS `prefix_portfolio`
(
    `id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
    `top` INT(11) NOT NULL,
    `order` INT(11) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `nav` VARCHAR(100) NOT NULL,
    `date` DATETIME NOT NULL,
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    `anons` TEXT NOT NULL,
    `text` TEXT NOT NULL,
    `worked` TEXT NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM  DEFAULT CHARSET=utf8;