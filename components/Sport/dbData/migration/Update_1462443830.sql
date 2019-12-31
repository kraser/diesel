-- update for timestamp 1462443830
CREATE TABLE IF NOT EXISTS `prefix_tourneys`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'ID турнира',
    `seasonId` INTEGER(11) NOT NULL COMMENT 'ID сезона в котором проходит турнир',
    `alias` VARCHAR (255) NOT NULL DEFAULT '' COMMENT 'Системное (Url) турнира',
    `title` VARCHAR (255) NOT NULL DEFAULT '' COMMENT 'Наименование турнира',
    `description` TEXT NOT NULL DEFAULT '' COMMENT 'Описание турнира',
    `startDate` DATE COMMENT 'Дата начала турнира',
    `endDate` DATE COMMENT 'Дата завершения турнира',
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT 'Таблица турниров';