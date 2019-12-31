CREATE TABLE IF NOT EXISTS `prefix_images_storage`
(
    `id` INTEGER(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `path` VARCHAR(255) NOT NULL,
    `del` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `name_idx` (`name`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;
