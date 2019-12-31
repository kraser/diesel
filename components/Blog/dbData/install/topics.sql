CREATE TABLE IF NOT EXISTS `prefix_blog_topics`
(
    `id` INTEGER(11) unsigned NOT NULL AUTO_INCREMENT,
    `top` INTEGER(11) unsigned NOT NULL DEFAULT '0',
    `order` INTEGER(11) NOT NULL,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `nav` VARCHAR(100) NOT NULL DEFAULT '',
    `text` TEXT NOT NULL,
    `types` TEXT NOT NULL,
    `cases` TEXT NOT NULL,
    `rate` INTEGER(11) NOT NULL,
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `top` (`top`),
    KEY `order` (`order`),
    KEY `deleted` (`deleted`),
    KEY `show` (`show`,`deleted`),
    KEY `show_2` (`show`),
    KEY `rate` (`rate`)
)
ENGINE=MyISAM  DEFAULT CHARSET=utf8;