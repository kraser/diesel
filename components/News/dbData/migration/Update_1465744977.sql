-- update for timestamp 1465744977
ALTER TABLE `prefix_news`
ADD COLUMN `alias` VARCHAR (255) COMMENT 'Алиас новости' AFTER `name`;