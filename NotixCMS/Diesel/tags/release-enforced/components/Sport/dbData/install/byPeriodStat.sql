CREATE TABLE IF NOT EXISTS `prefix_byPeriodStat`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'ID записи',
    `teamId` INTEGER(11) NOT NULL COMMENT 'ID команды',
    `period` ENUM('1', '2', '3') NOT NULL DEFAULT '1' COMMENT 'Период',
    `majority` INTEGER(11) NOT NULL COMMENT 'Количество раз, которое команда играла в большинстве',
    `majorityGoals` INTEGER(11) NOT NULL COMMENT 'Шайбы, забитые в большинстве',
    `majorityMisses` INTEGER(11) NOT NULL COMMENT 'Шайбы пропущенные в большинстве',
    `minority` INTEGER(11) NOT NULL COMMENT 'Количество раз, которое команда играла в меньшинстве',
    `minotityGoals` INTEGER(11) NOT NULL COMMENT 'Шайбы забитые в меньшинстве',
    `minotityMisses` INTEGER(11) NOT NULL COMMENT 'Шайбы пропущенные в меньшинстве',
    `penaltyTime` INTEGER(11) NOT NULL COMMENT 'Штрафное время',
    `contestPenaltyTime` INTEGER(11) NOT NULL COMMENT 'Штрафное время соперника',
    `lossBullit` INTEGER(11) NOT NULL COMMENT 'Проигрыши в послематчевых буллитах',
    `scores` INTEGER(11) NOT NULL COMMENT 'Количество набранных очков',
    `goalless` INTEGER(11) NOT NULL COMMENT 'Игры без забитых шайб',
    `missless` INTEGER(11) NOT NULL COMMENT 'Игры без пропущенных шайб',
    `win` INTEGER(11) NOT NULL COMMENT 'Победы',
    `draw` INTEGER(11) NOT NULL COMMENT 'Ничьи',
    `loss` INTEGER(11) NOT NULL COMMENT 'Поражения',
    `date` DATE COMMENT 'Статистический период',
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT 'Таблица статистики команд по периодам';
