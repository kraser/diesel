-- update for timestamp 1465916147
ALTER TABLE `prefix_blocks`
ADD COLUMN `template` VARCHAR (255) COMMENT 'Шаблон для блока' AFTER `callname`;