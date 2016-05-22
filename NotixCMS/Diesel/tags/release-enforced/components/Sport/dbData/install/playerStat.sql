CREATE TABLE IF NOT EXISTS `prefix_playerStat`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'ID записи',
    `playerId` INTEGER(11) NOT NULL COMMENT 'ID игрока',
    `matches` INTEGER(11) NOT NULL COMMENT 'Количество проведенных игр',
    `goals` INTEGER(11) NOT NULL COMMENT 'Заброшенные шайбы',
    `pass` INTEGER(11) NOT NULL COMMENT 'Передачи',
    `utility` INTEGER(11) NOT NULL COMMENT 'Показатель полезности',
    `penaltyTime` INTEGER(11) NOT NULL COMMENT 'Штрафное время',
    `winGoals` INTEGER(11) NOT NULL COMMENT 'Победные шайбы',
    `equalGoals` INTEGER(11) NOT NULL COMMENT 'Шайбы, забитые в равенстве',
    `majorityGoals` INTEGER(11) NOT NULL COMMENT 'Шайбы, забитые в большинстве',
    `minotityGoals` INTEGER(11) NOT NULL COMMENT 'Шайбы, забитые в меньшинстве',
    `overtimeGoals` INTEGER(11) NOT NULL COMMENT 'Шайбы, забитые в овертайме',
    `winBullit` INTEGER(11) NOT NULL COMMENT 'Решающие буллиты',
    `shots` INTEGER(11) NOT NULL COMMENT 'Броски по воротам',
    `throw` INTEGER(11) NOT NULL COMMENT 'Вбрасывания',
    `winThrow` INTEGER(11) NOT NULL COMMENT 'Выигранные вбрасывания',
    `date` DATE COMMENT 'Статистический период',
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT 'Таблица статистики полевых игроков';
