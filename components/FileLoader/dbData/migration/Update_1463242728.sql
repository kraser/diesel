-- update for timestamp 1463242728
ALTER TABLE `prefix_files`
ADD COLUMN `order` INTEGER (11) NOT NULL DEFAULT 0 COMMENT 'Порядковый номер документа в отображении' AFTER `id`,
ADD COLUMN `alias` VARCHAR (255) NOT NULL DEFAULT '' COMMENT 'Категория документа' AFTER `order`;