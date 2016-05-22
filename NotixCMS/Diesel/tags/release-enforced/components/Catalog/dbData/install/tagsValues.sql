CREATE TABLE IF NOT EXISTS `prefix_tags_values`
(
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `module` VARCHAR(100) NOT NULL,
    `moduleId` INT(10) NOT NULL,
    `order` INT(10) NOT NULL,
    `tagId` INT(10) NOT NULL,
    `value` VARCHAR(1024) NOT NULL,
    `unit` VARCHAR(10)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    `show` ENUM('Y', 'N')  NOT NULL DEFAULT 'Y',
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;

