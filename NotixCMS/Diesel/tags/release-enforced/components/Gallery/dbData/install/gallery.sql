-- upadte for timestamp 1419186182
CREATE TABLE IF NOT EXISTS `prefix_gallery`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'ID галереи',
    `name` VARCHAR(255) NOT NULL COMMENT 'Наименование галереи',
    `text` TEXT NOT NULL,
    `date` DATETIME NOT NULL,
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    `images_ids` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Ссылки (через запятую) на ИД привязанных к записи изображений в таблице xx_images_storage, первый ид - "главной" картинки',
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;

