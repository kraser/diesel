-- upadte for timestamp 1419189886
CREATE TABLE IF NOT EXISTS `prefix_portfolio_topics`
(
    `id` INT(8) unsigned NOT NULL AUTO_INCREMENT,
    `top` INT(8) unsigned NOT NULL DEFAULT '0',
    `order` INT(11) NOT NULL,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `nav` VARCHAR(100) NOT NULL DEFAULT '',
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    `text` TEXT NOT NULL,
    `types` LONGTEXT NOT NULL,
    `cases` TEXT NOT NULL,
    `rate` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `top` (`top`),
    KEY `order` (`order`),
    KEY `deleted` (`deleted`),
    KEY `show` (`show`,`deleted`),
    KEY `show_2` (`show`),
    KEY `rate` (`rate`)
)
ENGINE=MyISAM  DEFAULT CHARSET=utf8;