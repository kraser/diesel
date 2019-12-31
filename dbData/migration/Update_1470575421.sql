-- update for timestamp 1470575421
ALTER TABLE `prefix_users`
ADD COLUMN `lastEntrance` DATETIME COMMENT 'Время последнего входа в систему';
