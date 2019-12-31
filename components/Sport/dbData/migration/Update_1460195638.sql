-- update for timestamp 1460195638
ALTER TABLE `prefix_players`
ADD COLUMN `birthDate` DATE NOT NULL DEFAULT '0000-00-00' COMMENT 'Дата рождения' AFTER `name`,
ADD COLUMN `num` INTEGER(11) NOT NULL DEFAULT 0 COMMENT 'Номер' AFTER `birthdate`,
ADD COLUMN `weight` DECIMAL(6,2) NOT NULL DEFAULT 0 COMMENT 'Вес' AFTER `num`,
ADD COLUMN `status` ENUM('Любитель', 'СШК', 'Мастер13+') NOT NULL DEFAULT 'Любитель' COMMENT 'Статус игрока' AFTER `weight`,
ADD COLUMN `grip` ENUM('левый', 'правый') NOT NULL DEFAULT 'правый' COMMENT 'Хват клюшки' AFTER `status`,
ADD COLUMN `description` TEXT NOT NULL DEFAULT '' COMMENT 'Сопроводительный текст' AFTER `grip`;