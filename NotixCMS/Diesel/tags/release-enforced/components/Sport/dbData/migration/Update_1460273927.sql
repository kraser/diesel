-- update for timestamp 1460273927
ALTER TABLE `prefix_byPeriodStat`
CHANGE COLUMN `minotityGoals` `minorityGoals` INTEGER(11) NOT NULL COMMENT 'Шайбы забитые в меньшинстве',
CHANGE COLUMN `minotityMisses` `minorityMisses` INTEGER(11) NOT NULL COMMENT 'Шайбы пропущенные в меньшинстве';