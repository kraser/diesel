-- update for timestamp 1462986102
CREATE TABLE IF NOT EXISTS `prefix_clickjacking`
(
    `uid` INTEGER (11) NOT NULL AUTO_INCREMENT,
    `id` INTEGER (11) NOT NULL,
    `name` VARCHAR (255) NOT NULL,
    `reffer` VARCHAR (255) NOT NULL,
    `date` DATETIME NOT NULL,
    `modered` ENUM('Y','N') NOT NULL DEFAULT 'N',
    PRIMARY KEY (`uid`)
)
ENGINE=MyISAM DEFAULT CHARSET=UTF8