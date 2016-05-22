-- upadte for timestamp 1429249218
ALTER TABLE `prefix_prices`
    ADD COLUMN `include` ENUM( 'Y',  'N' ) NOT NULL DEFAULT 'Y' COMMENT 'Флаг включения файла в общий прайс';