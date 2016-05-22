-- upadte for timestamp 1419191505
CREATE TABLE IF NOT EXISTS `prefix_question`
(
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `question` VARCHAR(255) NOT NULL,
    `firstName` VARCHAR(255) NOT NULL,
    `lastName` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `person` INT(11) NOT NULL,
    `date` DATETIME NOT NULL,
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    `anons` TEXT NOT NULL,
    `answer` TEXT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;