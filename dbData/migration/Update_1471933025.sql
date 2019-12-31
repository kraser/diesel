-- update for timestamp 1471933025
ALTER TABLE `prefix_properties_tags`
MODIFY `alias` VARCHAR(100) NOT NULL DEFAULT '',
MODIFY `name` VARCHAR(100) NOT NULL DEFAULT '',
MODIFY `created` DATETIME,
MODIFY `modified` DATETIME;