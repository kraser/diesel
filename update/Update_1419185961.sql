-- upadte for timestamp 1419185961
CREATE TABLE IF NOT EXISTS `prefix_golos`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'ID опроса',
    `name` text NOT NULL COMMENT 'Наименование опроса',
    `enabled` INTEGER(1) NOT NULL DEFAULT '0' COMMENT 'Флаг активации опроса',
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;
