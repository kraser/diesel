CREATE TABLE IF NOT EXISTS `prefix_keeperStat`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'ID записи',
    `playerId` INTEGER(11) NOT NULL COMMENT 'ID игрока',
    `matches` INTEGER(11) NOT NULL COMMENT 'Количество проведенных игр',
    `wins` INTEGER(11) NOT NULL COMMENT 'Выигрыши',
    `loss` INTEGER(11) NOT NULL COMMENT 'Проигрыши',
    `missed` INTEGER(11) NOT NULL COMMENT 'Пропущено шайб',
    `shots` INTEGER(11) NOT NULL COMMENT 'Броски по воротам',
    `saved` INTEGER(11) NOT NULL COMMENT 'Отраженные броски',
    `zero` INTEGER(11) NOT NULL COMMENT 'Сухие игры',
    `playTime` INTEGER(11) NOT NULL COMMENT 'Время на площадке',
    `date` DATE COMMENT 'Статистический период',
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT 'Таблица статистики вратарей';
