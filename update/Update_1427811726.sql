-- upadte for timestamp 1427811726
ALTER TABLE  `prefix_regions` ADD COLUMN `deleted` ENUM( 'Y',  'N' ) NOT NULL DEFAULT  'N';
