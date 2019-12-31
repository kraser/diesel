CREATE TABLE IF NOT EXISTS `prefix_slider`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `date` DATETIME NOT NULL,
    `link` VARCHAR(255) NOT NULL,
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    `images_ids` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Ссылки (через запятую) на ИД привязанных к записи изображений в таблице xx_images_storage, первый ид - "главной" картинки',
    `text` TEXT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
