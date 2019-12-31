-- update for timestamp 1459404713
ALTER TABLE `prefix_keeperStat` ADD COLUMN `multy` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Коффициент' AFTER `playTime`;