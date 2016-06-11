-- update for timestamp 1464369428
CREATE TABLE IF NOT EXISTS `prefix_games`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'ID записи',
    `host` INTEGER(11) NOT NULL COMMENT 'ID домашней команды',
    `quest` INTEGER(11) NOT NULL COMMENT 'ID гостевой команды',
    `hostGoals` INTEGER(11) NOT NULL COMMENT 'Голы домашней команды',
    `questGoals` INTEGER(11) NOT NULL COMMENT 'Голы гостевой команды',
    `tourneyId` INTEGER(11) NOT NULL COMMENT 'ID турнира, в рамках которого проходит матч',
    `holdDate` DATE COMMENT 'Время проведения',
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT 'Таблица игр';