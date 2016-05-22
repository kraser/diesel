CREATE TABLE IF NOT EXISTS `prefix_teamStat`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'ID записи',
    `teamId` INTEGER(11) NOT NULL COMMENT 'ID команды',
    `matches` INTEGER(11) NOT NULL COMMENT 'Количество проведенных игр',
    `winsMaintime` INTEGER(11) NOT NULL COMMENT 'Выигрыши в основное время',
    `lossMaintime` INTEGER(11) NOT NULL COMMENT 'Проигрыши в основное время',
    `winsBullit` INTEGER(11) NOT NULL COMMENT 'Выигрыши в послематчевых буллитах',
    `lossBullit` INTEGER(11) NOT NULL COMMENT 'Проигрыши в послематчевых буллитах',
    `scores` INTEGER(11) NOT NULL COMMENT 'Количество набранных очков',
    `goals` INTEGER(11) NOT NULL COMMENT 'Заброшенные шайбы',
    `missed` INTEGER(11) NOT NULL COMMENT 'Пропущенные шайбы',
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
ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT 'Таблица статистики команд';
