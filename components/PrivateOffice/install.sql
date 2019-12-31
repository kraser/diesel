CREATE TABLE IF NOT EXISTS `users`
(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `login` varchar(45) NOT NULL,
    `passwd` varchar(45) NOT NULL,
    `status` enum('super','admin','manager','client') NOT NULL,
    `firstName` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `lastName` VARCHAR(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `company` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `phone` VARCHAR(255)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `city` VARCHAR(225) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `address` VARCHAR(255)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `zoneId` INT(11) NOT NULL,
    `fax` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `deleted` BOOLEAN  DEFAULT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;