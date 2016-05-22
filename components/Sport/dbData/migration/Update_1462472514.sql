-- update for timestamp 1462472514
ALTER TABLE `prefix_playerStat`
ADD COLUMN `tourneyId` INTEGER (11) NOT NULL COMMENT 'ID турнира к которому относятся данные',
ADD UNIQUE INDEX `period_player_idx` (`tourneyId`, `date`, `playerId`);