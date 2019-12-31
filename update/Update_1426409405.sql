-- upadte for timestamp 1426409405
CREATE TABLE IF NOT EXISTS `prefix_module_to_region`
(
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `region_id` INT(11) NOT NULL,
    `module` VARCHAR(100) NOT NULL,
    `module_id` INT(11) NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8 ;