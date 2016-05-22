CREATE TABLE IF NOT EXISTS `prefix_forms_fields`
(
    `id` INTEGER(11) unsigned NOT NULL AUTO_INCREMENT,
    `form` INTEGER(11) unsigned NOT NULL,
    `type` ENUM('text','textarea','checkbox') NOT NULL DEFAULT 'text',
    `label` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `regex` VARCHAR(255) NOT NULL,
    `regex_error` VARCHAR(255) NOT NULL,
    `default` VARCHAR(255) NOT NULL,
    `required` ENUM('Y','N') NOT NULL DEFAULT 'N',
    `order` INTEGER(11) NOT NULL,
    `show` ENUM('Y','N') NOT NULL DEFAULT 'Y',
    `created` DATETIME NOT NULL,
    `modified` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `form` (`form`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
