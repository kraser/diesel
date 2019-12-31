-- update for timestamp 1471933220
ALTER TABLE `prefix_tags_values`
MODIFY `order` INT(10) NOT NULL DEFAULT 0,
MODIFY `value` VARCHAR(1024) NOT NULL DEFAULT '',
MODIFY `unit` VARCHAR(10)  CHARACTER SET utf8 COLLATE utf8_general_ci,
MODIFY `created` DATETIME,
MODIFY `modified` DATETIME;

