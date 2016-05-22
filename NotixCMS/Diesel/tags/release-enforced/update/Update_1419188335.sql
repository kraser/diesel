-- upadte for timestamp 1419188335
CREATE TABLE IF NOT EXISTS `prefix_prices`
(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `link` varchar(500) NOT NULL,
    `date` datetime NOT NULL,
    `show` enum('Y','N') NOT NULL DEFAULT 'Y',
    `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
    `created` datetime NOT NULL,
    `modified` datetime NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8;

