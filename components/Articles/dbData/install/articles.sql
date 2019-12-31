CREATE TABLE IF NOT EXISTS `prefix_articles`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'ID статьи',
    `alias` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Алиас статьи',
    `title` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Заголовок статьи',
    `anons` TEXT NOT NULL DEFAULT '' COMMENT 'Анонс статьи',
    `text` TEXT NOT NULL DEFAULT '' COMMENT 'Текст статьи',
    `date` DATETIME NOT NULL COMMENT 'Дата публикации статьи',
    `authorId` INTEGER(11) NOT NULL COMMENT 'ID автора',
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;
