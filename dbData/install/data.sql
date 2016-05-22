CREATE TABLE IF NOT EXISTS `prefix_data`
(
    `id` INTEGER(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id variable',
    `key` VARCHAR(64) NOT NULL COMMENT 'Variable name',
    `data` LONGTEXT NOT NULL COMMENT 'Variable value',
    PRIMARY KEY (`id`),
    UNIQUE KEY `key` (`key`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT 'Table of variables DEPRECATED';
