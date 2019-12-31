-- update for timestamp 1464370666
CREATE TABLE IF NOT EXISTS `prefix_pairs`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'ID записи',
    `tourneyId` INTEGER(11) NOT NULL COMMENT 'ID турнира',
    `host` INTEGER(11) NOT NULL COMMENT 'ID домашней команды',
    `quest` INTEGER(11) NOT NULL COMMENT 'ID гостевой команды',
    `stage` INTEGER(11) NOT NULL COMMENT 'Этап турнира',
    `winnerId` INTEGER(11) NOT NULL COMMENT 'ID победителя',
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT 'Таблица игр';