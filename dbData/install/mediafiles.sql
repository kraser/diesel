CREATE TABLE IF NOT EXISTS `prefix_mediafiles`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `src` varchar(255) NOT NULL,
    `md5` varchar(32) NOT NULL,
    `filetype` varchar(4) NOT NULL,
    `fileinfo` text NOT NULL,
    `name` varchar(255) NOT NULL,
    `text` text NOT NULL,
    `module` varchar(64) NOT NULL,
    `module_id` int(11) NOT NULL,
    `order` int(11) NOT NULL,
    `date` datetime NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;

