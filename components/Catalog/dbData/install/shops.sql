CREATE TABLE IF NOT EXISTS `prefix_shops`
(
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL,
    `address` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL,
    `text` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL,
    `show` ENUM('Y', 'N') NOT NULL DEFAULT 'Y',
    `deletetd` ENUM('Y', 'N') NOT NULL DEFAULT 'N',
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;
