-- update for timestamp 1461080844
CREATE TABLE IF NOT EXISTS `prefix_menu`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `parentId` INTEGER(11) NOT NULL DEFAULT 0,
    `alias` VARCHAR(64) NOT NULL DEFAULT '',
    `title`  VARCHAR(128) NOT NULL DEFAULT '',
    `module` VARCHAR(32) NOT NULL,
    `template` VARCHAR(63) NOT NULL DEFAULT 'page.php',
    `root` VARCHAR(64) NOT NULL DEFAULT '',
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;