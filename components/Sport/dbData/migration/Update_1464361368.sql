-- update for timestamp 1464361368
ALTER TABLE `prefix_tourneys`
ADD COLUMN `type` ENUM('playoff', 'regular', 'mixed') NOT NULL DEFAULT 'regular' COMMENT 'Тип турнира Регулярныйчемпионат/Плэйофф/Смешанный' AFTER `alias`;
