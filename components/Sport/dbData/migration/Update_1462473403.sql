-- update for timestamp 1462473403
ALTER TABLE `prefix_keeperStat`
ADD COLUMN `tourneyId` INTEGER (11) NOT NULL COMMENT 'ID турнира к которому относятся данные' AFTER `date`,
ADD UNIQUE INDEX `period_player_idx` (`tourneyId`, `playerId`);