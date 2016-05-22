-- update for timestamp 1460865033
ALTER TABLE `prefix_playerStat`
CHANGE COLUMN `minotityGoals` `minorityGoals` INTEGER(11) NOT NULL COMMENT 'Шайбы забитые в меньшинстве';