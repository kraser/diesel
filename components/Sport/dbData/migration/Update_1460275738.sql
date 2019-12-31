-- update for timestamp 1460275738
ALTER TABLE `prefix_byPeriodStat`
ADD COLUMN `matches` INTEGER(11) NOT NULL DEFAULT 0 COMMENT 'Количество матчей' AFTER `teamId`;