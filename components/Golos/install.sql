CREATE TABLE IF NOT EXISTS `prfix_golos`
(
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` TEXT NOT NULL,
    `golos_quest` TEXT NOT NULL,
    `golos_answer` TEXT NOT NULL,
    `enabled` INT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=UTF8;
