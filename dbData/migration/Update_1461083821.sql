-- update for timestamp 1461083821
ALTER TABLE `prefix_menu`
ADD COLUMN `link` VARCHAR(64) NOT NULL DEFAULT '' AFTER `alias`;