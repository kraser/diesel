CREATE TABLE IF NOT EXISTS `prefix_players`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'ID игрока',
    `alias` VARCHAR(64) NOT NULL COMMENT 'Системный псевдоним игрока',
    `teamId` INTEGER(11) NOT NULL COMMENT 'ID команды игрока',
    `amplua` ENUM('GOALKEEPER', 'DEFENDER', 'FORWARD') NOT NULL DEFAULT 'DEFENDER' COMMENT 'Амплуа игрока',
    `name` VARCHAR(255) NOT NULL COMMENT 'Имя игрока',
    `ratting` INTEGER(11) NOT NULL COMMENT 'Рейтинг игрока',
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT 'Таблица игроков';
