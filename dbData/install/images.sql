CREATE TABLE IF NOT EXISTS `prefix_images`
(
    `id` INTEGER(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID изображения',
    `src` VARCHAR(255) NOT NULL COMMENT 'Полноое имя файла',
    `md5` VARCHAR(32) NOT NULL COMMENT 'Хэш имени файла',
    `module` VARCHAR(64) NOT NULL COMMENT 'Алиас модуля',
    `module_id` INTEGER(11) NOT NULL COMMENT 'ID записи ',
    `alter_key` VARCHAR(64) NOT NULL COMMENT 'Текст для аттрибута alter',
    `main` ENUM('Y','N') NOT NULL COMMENT 'Флаг главного изображения',
    PRIMARY KEY (`id`),
    KEY `module` (`module`,`module_id`)
)
ENGINE=MyISAM DEFAULT CHARSET=UTF8  COMMENT 'Хранилище изображений';
