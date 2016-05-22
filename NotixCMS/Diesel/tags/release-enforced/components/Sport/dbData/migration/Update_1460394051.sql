-- update for timestamp 1460394051
ALTER TABLE `prefix_players`
ADD COLUMN `height` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Рост' AFTER `weight`;