-- update for timestamp 1462473926
ALTER TABLE `prefix_tourneyResults`
ADD COLUMN `tourneyId` INTEGER (11) NOT NULL COMMENT 'ID турнира к которому относятся данные' AFTER `teamId`,
ADD UNIQUE INDEX `team_idx` (`tourneyId`, `teamId`);