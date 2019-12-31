-- update for timestamp 1462443303
CREATE TABLE IF NOT EXISTS `prefix_seasons`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'ID сезона',
    `alias` VARCHAR (255) NOT NULL DEFAULT '' COMMENT 'Системное (Url) сезона',
    `title` VARCHAR (255) NOT NULL DEFAULT '' COMMENT 'Наименование сезона',
    `description` TEXT NOT NULL DEFAULT '' COMMENT 'Описание сезона',
    `startDate` DATE COMMENT 'Дата начала сезона',
    `endDate` DATE COMMENT 'Дата завершения сезона',
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT 'Таблица сезонов';