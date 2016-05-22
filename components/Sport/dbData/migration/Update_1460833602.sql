-- update for timestamp 1460833602
CREATE TABLE IF NOT EXISTS `prefix_tourney`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `teamId` INTEGER(11) NOT NULL COMMENT 'ID команды',
    `matches` INTEGER(11) NOT NULL COMMENT 'Количество игр',
    `wins` INTEGER(11) NOT NULL COMMENT 'Количество побед',
    `winsByBullit` INTEGER(11) NOT NULL COMMENT 'Количество побед по буллитам',
    `lossByBullit` INTEGER(11) NOT NULL COMMENT 'Количество проигрышей по буллитам',
    `loss` INTEGER(11) NOT NULL COMMENT 'Количество проигрышей',
    `goals` INTEGER(11) NOT NULL COMMENT 'Количество забитых шайб',
    `misses` INTEGER(11) NOT NULL COMMENT 'Количество пропущенных шайб',
    `scores` INTEGER(11) NOT NULL COMMENT 'Количество очков',
    `здфсу` INTEGER(11) NOT NULL COMMENT 'Место',
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `team_idx` (`teamId`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT 'Таблица регулярного чемпионата';