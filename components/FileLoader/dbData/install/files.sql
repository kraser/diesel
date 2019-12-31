CREATE TABLE IF NOT EXISTS `prefix_files` 
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `link` VARCHAR(255) NOT NULL,
    `date` DATETIME NOT NULL,
    `show` enum('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
    `created` datetime NOT NULL,
    `modified` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

