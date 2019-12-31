-- upadte for timestamp 1419191591
CREATE TABLE IF NOT EXISTS `prefix_sorting`
(
    `id` INT NOT NULL AUTO_INCREMENT,
    `type` VARCHAR(64) NOT NULL,
    `name` VARCHAR(64) NOT NULL,
    `direction` ENUM('Y', 'N') NOT NULL DEFAULT 'Y',
    `order` INT NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;;