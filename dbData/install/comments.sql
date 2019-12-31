CREATE TABLE IF NOT EXISTS `prefix_comments`
(
    `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `parent_id` INTEGER(11) NOT NULL DEFAULT '0',
    `hash` VARCHAR(32) NOT NULL,
    `author` VARCHAR(64) NOT NULL,
    `email` VARCHAR(64) NOT NULL,
    `text` text NOT NULL,
    `module` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `element_id` INTEGER(11) NOT NULL,
    `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted` ENUM('Y','N') NOT NULL DEFAULT 'N',
    PRIMARY KEY (`id`),
    KEY `hash` (`hash`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;
