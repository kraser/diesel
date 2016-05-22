CREATE TABLE IF NOT EXISTS `prefix_teams`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'ID команды',
    `alias` VARCHAR(64) NOT NULL COMMENT 'Системный псевдоним команды',
    `order` INTEGER(11) NOT NULL COMMENT 'Место команды',
    `name` VARCHAR(255) NOT NULL COMMENT 'Название команды',
    `city` VARCHAR(255) NOT NULL COMMENT 'Город',
    `rating` INTEGER(11) NOT NULL COMMENT 'Рейтинг команды',
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT 'Таблица комманд';
