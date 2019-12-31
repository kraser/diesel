CREATE TABLE IF NOT EXISTS `prefix_content`
(
    `id` INTEGER(11) unsigned NOT NULL AUTO_INCREMENT,
    `top` INTEGER(11) unsigned NOT NULL,
    `order` INTEGER(11) NOT NULL,
    `nav` VARCHAR(64) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `text` TEXT NOT NULL,
    `module` VARCHAR(32) NOT NULL,
    `template` VARCHAR(63) NOT NULL DEFAULT 'page.php',
    `showmenu` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `show` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
