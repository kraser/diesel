CREATE TABLE IF NOT EXISTS `prefix_seo`
(
    `id` INTEGER(11) unsigned NOT NULL AUTO_INCREMENT,
    `module` VARCHAR(64) NOT NULL,
    `module_id` INTEGER(11) NOT NULL,
    `module_table` VARCHAR(64) NOT NULL,
    `title` VARCHAR(1024) NOT NULL,
    `keywords` VARCHAR(2048) NOT NULL,
    `description` VARCHAR(2048) NOT NULL,
    `tagH1` VARCHAR(1024) NOT NULL COMMENT 'Значение тэга H1 для текущей страницы',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COMMENT 'Данные для СЕО';