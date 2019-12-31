CREATE TABLE IF NOT EXISTS `prefix_settings`
(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `module` varchar(32) NOT NULL,
    `name` varchar(255) NOT NULL,
    `callname` varchar(128) NOT NULL,
    `value` varchar(255) NOT NULL,
    `created` datetime NOT NULL,
    `modified` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `module` (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

